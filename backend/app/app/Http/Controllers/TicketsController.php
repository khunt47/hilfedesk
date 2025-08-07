<?php

/**
 * @author krishnan <ks@geeedesk.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Projects;
use App\Models\Tickets;
use App\Models\Comments;
use App\Models\BlockingTickets;
use App\Models\RelatedTickets;
use App\Models\Customers;
use App\Models\Contacts;
use App\Models\TicketCommentFiles;
use App\Models\Users;
use App\Jobs\SendTicketCreationAlert;
use App\Jobs\SendTicketCreationAlertToRequestor;
use App\Jobs\SendCommentCreationAlertToRequestor;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Validator;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;


class TicketsController extends Controller
{
    /**
     * Api to create ticket
     * @author krishnan <ks@geedesk.com>
     * @param int $project_id
     * @param string $heading
     * @return json
     */
    public function create_ticket(Request $request)
    {
        /*
        Do not get contact id instead get customer id
        In addition to that get their phone number and email address
        based on that create a contact id here and use it to create ticket from helpdesk api
        */
        try {
            $ticket_files = [];
            $contact_exists = 0;
            $created_user = Tickets::USER_CREATED;
            $project_id = 0;
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            if ($user_id === 0) {
                $created_user = Tickets::CUSTOMER_CREATED;
            }

            /*
            Modify validations so that it also validates for customer id and contact id
            */
            $rules = ['web', 'email', 'android'];
            $validated = validator($request->all(), [
                'project_id' => 'bail|required|numeric|exists:projects,id|min:1',
                'id' => Rule::unique('projects')->where(fn ($query) => $query->where('company_id', $company_id)),
                'heading' => 'bail|required',
                'from_email' => 'bail|email',
                'customer_id' => 'bail|numeric|exists:customers,id|min:1',
                'source' => 'bail|required|in:'. implode(",",$rules)
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                /*
                Checking if project id belongs to this company and is active
                */
                $project_id = $request->project_id;
                $project_id_exists = Projects::where('id', $project_id)->where('status', 'active')->where('company_id', $company_id)->exists();
                if(!$project_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Project id does not exist'], 400);
                }
                $source = $request->source;

                /*Processing the contact received*/
                $from_email = $request->from_email;
                $customer_id = $request->customer_id;

                $contact_fname = $request->contact_fname;
                $contact_lname = $request->contact_lname;
                $contact_name = $contact_fname.' '.$contact_lname;

                if (!isset($request->contact_id)) {
                    
                    $contact_email = $request->contact_email;

                    if ($contact_email !== '' || $contact_email !== null) {
                        $contact_exists = Contacts::where('email', $contact_email)->where('company_id', $company_id)->exists();
                    }

                    if ($contact_exists) {
                        $contact_detail = Contacts::select('id')->where('email', $contact_email)->where('company_id', $company_id)->first();
                        $contact_id = $contact_detail->id;
                    }
                    else {
                        $new_contact_data = array(
                            'company_id' => $company_id,
                            'customer_id' => $customer_id,
                            'fname' => $contact_fname,
                            'lname' => $contact_lname,
                            'email' => $contact_email
                        );
                        $contact_id = DB::table('contacts')->insertGetId($new_contact_data);
                    }
                }
                else {
                    $contact_id = $request->contact_id;
                }
                /*Processing the contact received*/
            }

            $project_details = Projects::where('id', $project_id)->where('status', 'active')->first();
            $project_code = $project_details->project_code;
            $last_ticket_id = $project_details->last_ticket_id;
            $ticket_id = (int)$last_ticket_id + 1;
            $easy_ticket_id = $project_code.'-'.(string)$ticket_id;

            $description = $request->input('description');
            $description = nl2br($description);

            if (count($_FILES) > 0) {
                $attachment_present = Tickets::ATTACHMENT_YES;
            }
            else {
                $attachment_present = Tickets::ATTACHMENT_NO;
            }

            $ticket_heading = $request->input('heading');

            $new_ticket_data = array(
                'easy_ticket_id' => $ticket_id,
                'display_ticket_id' => $easy_ticket_id,
                'company_id' => $company_id,
                'project_id' => $project_id,
                'created_by' => $user_id,
                'created_on' => Carbon::now(),
                'heading' => $ticket_heading,
                'description' => $description,
                'priority' => $request->input('priority'),
                'status' => 'new',
                'merged_ticket_id' => 0,
                'from_email' => $from_email,
                'created_user' => $created_user,
                'customer_id' => $customer_id, 
                'contact_id' => $contact_id,
                'attachment_present' => $attachment_present
            );

            $ticket_created = DB::table('tickets')->insertGetId($new_ticket_data);
            if ($ticket_created > 0) {
                $new_ticket_id = $ticket_created;
                Projects::where('status', 'active')->where('company_id', $company_id)->where('id', $project_id)->update(['last_ticket_id' => $ticket_id]);
                $ticket_details = array(
                    'id' => $ticket_created,
                    'ticket_id' => $easy_ticket_id
                );
                /*
                    Upload files to ticket
                */

                    $key = env('LINODE_BUCKET_KEY');
                    $secret     = env('LINODE_BUCKET_SECRET');
                    $space_name = env('LINODE_BUCKET_NAME');
                    $region     = env('LINODE_BUCKET_REGION');
                    $end_point  = env('LINODE_BUCKET_ENDPOINT');

                    $s3Client = new S3Client([
                        'version'     => 'latest',
                        'region'      => $region,
                        'endpoint' => $end_point,
                        'credentials' => [
                            'key'         => $key,
                            'secret'      => $secret,
                        ],
                    ]);

                foreach($_FILES as $file) {
                    $file_name = $file['name'];
                    $file_size = $file['size'];
                    $file_tmp = $file['tmp_name'];

                    $random_string = rand(1000,9999);
                    $file_name = $random_string.$file_name;
                    $file_name = 'daysupport-files/'.$file_name;
                    $file_mime_type = mime_content_type($file_tmp);

                        $file_uploaded = $s3Client->putObject([
                            'ContentType' => $file_mime_type,
                            'Bucket'     => $space_name,
                            'Key'        => $file_name,
                            'SourceFile' => $file_tmp,
                            'ACL'        => 'public-read'
                        ]);

                        array_push($ticket_files, $file_name);
                }

                for ($i=0; $i < count($ticket_files) ; $i++) { 
                    $new_ticket_file = [];
                    $file_name = $ticket_files[$i];
                    $new_ticket_file = array(
                        'company_id' => $company_id,
                        'ticket_id' => $new_ticket_id,
                        'linked_to' => TicketCommentFiles::TICKET,
                        'created_on' => Carbon::now(),
                        'file_name' => $file_name
                    );
                    DB::table('ticket_files')->insert($new_ticket_file);
                }

                /*
                Upload file to ticket
                */

                //SendTicketCreationAlert::dispatch($ticket_created);
                /*
                After a ticket is created we send a confirmation to the requestor
                */
                $requestor_data = array(
                    'project_id' => $project_id,
                    'to_email' => $contact_email,
                    'to_name' => $contact_name, 
                    'ticket_id' => $ticket_created,
                    'ticket_heading' => $ticket_heading,
                    'company_id' => $company_id
                );

                $requestor_data_json = json_encode($requestor_data);
                SendTicketCreationAlertToRequestor::dispatch($requestor_data_json)
                ->onQueue('send_email_to_requestor');
                
                return response()->json(["success" => true, 'message' => 'Ticket created successfully', 'data' => $ticket_details], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Ticket was not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Get ticket details
     *
     * @param [type] $ticket_id
     * @return void
     */
    public function get_ticket($ticket_id, Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $ticket_id = (int)$ticket_id;
            if(!is_numeric($ticket_id) || $ticket_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Ticket id is of invalid type'], 400);
            }
            else {
                $ticket_id_exists = Tickets::where('id', $ticket_id)->where('company_id', $company_id)->exists();
                if(!$ticket_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Ticket id does not exist'], 400);
                }
            }

            $sql_query = "SELECT tickets.id, tickets.easy_ticket_id, tickets.display_ticket_id, tickets.created_on,
            tickets.heading, tickets.description, tickets.priority, tickets.status, tickets.taken_on, tickets.resolved_on,
            tickets.time_worked, tickets.attachment_present, tickets.merged_ticket_id, tickets.from_email, tickets.email_cc,
            tickets.customer_id, tickets.created_by, tickets.owned_by,tickets.resolved_by, tickets.project_id, 
            projects.name AS project_name, 
            created_by_user.fname AS created_by_fname, created_by_user.lname AS created_by_lname,
            owned_by_user.fname AS owned_by_fname, owned_by_user.lname AS owned_by_lname,
            resolved_by_user.fname AS resolved_by_fname, resolved_by_user.lname AS resolved_by_lname,
            customers.cust_name, contacts.fname AS contact_fname, contacts.lname AS contact_lname
            FROM tickets 
            JOIN projects ON tickets.project_id = projects.id 
            LEFT JOIN users AS created_by_user ON tickets.created_by = created_by_user.id
            LEFT JOIN users AS owned_by_user ON tickets.owned_by = owned_by_user.id
            LEFT JOIN users AS resolved_by_user ON tickets.resolved_by = resolved_by_user.id 
            LEFT JOIN customers ON tickets.customer_id = customers.id
            LEFT JOIN contacts ON tickets.contact_id = contacts.id  
            WHERE tickets.id = ".$ticket_id." AND tickets.company_id = ".$company_id;
            $ticket_details = DB::select($sql_query);
            $ticket_details = $ticket_details[0];

            if ($ticket_details->attachment_present === Tickets::ATTACHMENT_YES) {
                $sql_query = "SELECT files.id, files.file_name FROM ticket_files AS files 
                WHERE files.ticket_id = ".$ticket_id." AND files.linked_to = ".TicketCommentFiles::TICKET;
                $ticket_attachments = DB::select($sql_query);
                $ticket_details->attachments = $ticket_attachments;
            }

            if($ticket_details) {
                return response()->json(["success" => true, "data" => $ticket_details], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No ticket available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }


    /**
     * API to take ticket
     *
     * @author krishnan <ks@geedesk.com>
     *
     * @param Request $request
     * @return void
     */
    public function take_ticket(Request $request)
    {
        try {
            $ticket_id = 0;
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'ticket_id' => 'bail|required|numeric|exists:tickets,id',
                'id' => Rule::unique('tickets')->where(fn ($query) => $query->where('company_id', $company_id))
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $tickets = new Tickets();
                /*
                Check if ticket id status is new
                */
                $ticket_id = $request->ticket_id;
                $new_ticket = $tickets->where('id', $ticket_id)->where('status', Tickets::STATUS_New)->where('company_id', $company_id)->exists();
                if(!$new_ticket) {
                    return response()->json(["success" => false, "errors" => 'Ticket id is already assigned to someone and hence it cannot be taken'], 400);
                }
            }

            $update_details = array(
                'status' => 'inprogress',
                'taken_on' => Carbon::now(),
                'owned_by' => $user_id
            );
            $ticket_taken = DB::table('tickets')->where('id', $ticket_id)
            ->where('company_id', $company_id)->update($update_details);
            if ($ticket_taken) {
                return response()->json(["success" => true, 'message' => 'Ticket taken successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Ticket was not taken successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Change ticket status
     *
     * @param Request $request
     * @return void
     */
    public function change_ticket_status(Request $request)
    {
        try {
            $ticket_id = 0;
            $status = '';
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'ticket_id' => 'bail|required|numeric|exists:tickets,id',
                'id' => Rule::unique('tickets')->where(fn ($query) => $query->where('company_id', $company_id))
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $tickets = new Tickets();

                $ticket_id = $request->ticket_id;
                $status = $request->status;

                /*
                Check if ticket existing status and provided status are different
                */
                if($status === Tickets::STATUS_New) {
                    return response()->json(["success" => false, "errors" => 'Ticket status cannot be changed to new'], 400);
                }
                else {
                    $existing_status = $tickets->where('id', $ticket_id)->where('status', $status)->where('company_id', $company_id)->exists();
                    if($existing_status) {
                        return response()->json(["success" => false, "errors" => 'Ticket status is same as provided status'], 400);
                    }
                    else {
                        /*
                        Checking if the ticket is merged or deleted
                        */
                        $ticket_status = Tickets::select('status')->where('id', $ticket_id)->where('company_id', $company_id)->first();
                        if($ticket_status->status === Tickets::STATUS_Merged) {
                            return response()->json(["success" => false, "errors" => 'Status of merged ticket cannot be changed'], 400);
                        }
                        elseif($ticket_status->status === Tickets::STATUS_Deleted) {
                            return response()->json(["success" => false, "errors" => 'Status of deleted ticket cannot be changed'], 400);
                        }
                    }
                }
            }

            $update_details = array(
                'status' => $status,
            );
            $ticket_status_changed = DB::table('tickets')->where('id', $ticket_id)
            ->where('company_id', $company_id)->update($update_details);
            if ($ticket_status_changed) {
                if($status === 'resolved') {
                    //write a library to calculate time worked
                    $update_details = array(
                        'resolved_on' => Carbon::now(),
                        'resolved_by' => $user_id,
                        'time_worked' => '100'
                    );
                    DB::table('tickets')->where('id', $ticket_id)
                    ->where('company_id', $company_id)->update($update_details);
                }
                return response()->json(["success" => true, 'message' => 'Ticket status changed successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Ticket status not changed successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Change ticket priority
     *
     * @param Request $request
     * @return void
     */
    public function change_ticket_priority(Request $request)
    {
        try {
            $ticket_id = 0;
            $priority = '';
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'ticket_id' => 'bail|required|numeric|exists:tickets,id',
                'id' => Rule::unique('tickets')->where(fn ($query) => $query->where('company_id', $company_id))
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $tickets = new Tickets();

                $ticket_id = $request->ticket_id;
                $priority = $request->priority;

                /*
                Check if ticket existing priority and provided priority are different
                */
                $existing_priority = $tickets->where('id', $ticket_id)->where('priority', $priority)->where('company_id', $company_id)->exists();
                if($existing_priority) {
                    return response()->json(["success" => false, "errors" => 'Ticket priority is same as provided priority'], 400);
                }
                else {
                    /*
                    Checking if the ticket is merged or deleted
                    */
                    $ticket_status = Tickets::select('status')->where('id', $ticket_id)->where('company_id', $company_id)->first();
                    if($ticket_status->status === Tickets::STATUS_Merged) {
                        return response()->json(["success" => false, "errors" => 'Priority of merged ticket cannot be changed'], 400);
                    }
                    elseif($ticket_status->status === Tickets::STATUS_Deleted) {
                        return response()->json(["success" => false, "errors" => 'Priority of deleted ticket cannot be changed'], 400);
                    }
                }
            }

            $update_details = array(
                'priority' => $priority,
            );
            $ticket_priorty_changed = DB::table('tickets')->where('id', $ticket_id)
            ->where('company_id', $company_id)->update($update_details);
            if ($ticket_priorty_changed) {
                return response()->json(["success" => true, 'message' => 'Ticket priority changed successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Ticket priority not changed successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 500);
        }
    }


    /**
     * Create new comment
     *
     * @param Request $request
     * @return json
     */
    public function new_ticket_comment(Request $request)
    {
        try {
            $comment_files = [];
            $created_user = Comments::USER_CREATED;
            $ticket_id = 0;
            $priority = '';
            $file_name = '';
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            if ($user_id === 0) {
                $created_user = Comments::CUSTOMER_CREATED;
                $customer_id = $request->input('customer_id');
                $contact_id = $request->input('contact_id');
            }
            else {
                $customer_id = 0;
                $contact_id = 0;
            }
            /*
            Modify validations so that it also validates for customer id and contact id
            */
            $validated = validator($request->all(), [
                'ticket_id' => 'bail|required|numeric|exists:tickets,id',
                'id' => Rule::unique('tickets')->where(fn ($query) => $query->where('company_id', $company_id)),
                'new_comment' => 'bail|required',
                'public' => 'nullable|in:yes,no'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $tickets = new Tickets();
                $ticket_id = $request->ticket_id;

                /*
                If ticket not taken throw an error
                */
                $ticket_status_new = Tickets::where('id', $ticket_id)
                ->where('company_id', $company_id)->where('status', Tickets::STATUS_New)->exists();

                if ($ticket_status_new) {
                    return response()->json(["success" => false,  'message' => 'You need to first take the ticket. Only after that you can add comment.'], 400);
                }

                /*
                Checking if the ticket is merged or deleted
                */
                $ticket_status = Tickets::select('status')->where('id', $ticket_id)->where('company_id', $company_id)->first();
                if($ticket_status->status === Tickets::STATUS_Merged) {
                    return response()->json(["success" => false, "errors" => 'Ticket comment cannot be created for merged ticket'], 400);
                }
                elseif($ticket_status->status === Tickets::STATUS_Deleted) {
                    return response()->json(["success" => false, "errors" => 'Ticket comment cannot be created for deleted ticket'], 400);
                }

                $comments = new Comments();
                $public = $request->public;
                if(empty($public) || $public === '') {
                    $public = 'yes';
                }
            }

            if (count($_FILES) > 0) {
                $attachment_present = Comments::ATTACHMENT_YES;
            }
            else {
                $attachment_present = Comments::ATTACHMENT_NO;
            }
            $new_comment = $request->input('new_comment');

            $new_comment_data = array(
                'company_id' => $company_id,
                'ticket_id' => $ticket_id,
                'created_by' => $user_id,
                'created_on' => Carbon::now(),
                'public' => $public,
                'comment' => nl2br($new_comment),
                'created_user' => $created_user,
                'customer_id' => $customer_id, 
                'contact_id' => $contact_id,
                'attachment' => $attachment_present
            );

            $comment_created = DB::table('comments')->insertGetId($new_comment_data);
            if ($comment_created > 0) {
                $new_comment_id = $comment_created;

                if ($attachment_present === Comments::ATTACHMENT_YES) {
                    /*
                    Upload files to ticket
                    */

                        $key = env('LINODE_BUCKET_KEY');
                        $secret     = env('LINODE_BUCKET_SECRET');
                        $space_name = env('LINODE_BUCKET_NAME');
                        $region     = env('LINODE_BUCKET_REGION');
                        $end_point  = env('LINODE_BUCKET_ENDPOINT');

                        $s3Client = new S3Client([
                            'version'     => 'latest',
                            'region'      => $region,
                            'endpoint' => $end_point,
                            'credentials' => [
                                'key'         => $key,
                                'secret'      => $secret,
                            ],
                        ]);

                    foreach($_FILES as $file) {
                        $file_name = $file['name'];
                        $file_size = $file['size'];
                        $file_tmp = $file['tmp_name'];

                        $random_string = rand(1000,9999);
                        $file_name = $random_string.$file_name;
                        $file_name = 'daysupport-files/'.$file_name;
                        $file_mime_type = mime_content_type($file_tmp);

                            $file_uploaded = $s3Client->putObject([
                                'ContentType' => $file_mime_type,
                                'Bucket'     => $space_name,
                                'Key'        => $file_name,
                                'SourceFile' => $file_tmp,
                                'ACL'        => 'public-read'
                            ]);

                            array_push($comment_files, $file_name);
                    }

                    for ($i=0; $i < count($comment_files) ; $i++) { 
                        $new_comment_file = [];
                        $file_name = $comment_files[$i];
                        $new_comment_file = array(
                            'company_id' => $company_id,
                            'ticket_id' => $ticket_id,
                            'comment_id' => $new_comment_id,
                            'linked_to' => TicketCommentFiles::COMMENT,
                            'created_on' => Carbon::now(),
                            'file_name' => $file_name
                        );
                        DB::table('ticket_files')->insert($new_comment_file);
                    }
                }

                /*
                Upload file to ticket
                */

                /*
                Send email to the ticket creator or requestor if the comment is public
                */
                if ($public === 'yes' && $user_id > 0) {
                    $ticket_contact = Tickets::select('contact_id')->where('id', $ticket_id)->where('company_id', $company_id)->first();
                    $contact_id = $ticket_contact->contact_id;
                    if ($contact_id > 0 && $contact_id !== null && $contact_id !== '') {
                        $requestor = Contacts::select('email', 'fname', 'lname')
                                    ->where('id', $contact_id)->where('company_id', $company_id)->first();
                        $contact_email = $requestor->email;
                        $contact_fname = $requestor->fname;
                        $contact_lname = $requestor->lname;
                        $contact_name = $contact_fname.' '.$contact_lname;

                        $commentor_data = array(
                            'ticket_id' => $ticket_id,
                            'to_email' => $contact_email,
                            'to_name' => $contact_name,
                            'new_comment' => $new_comment,
                            'company_id' => $company_id,
                            'attachment_present' => $attachment_present,
                            'attachment' => $file_name
                        );

                        $commentor_data_json = json_encode($commentor_data);
                        SendCommentCreationAlertToRequestor::dispatch($commentor_data_json)
                        ->onQueue('send_commentor_to_requestor');
                    }
                }

                return response()->json(["success" => true, 'message' => 'Ticket comment created successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Ticket comment not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 500);
        }
    }



    /**
     * View ticket comments
     *
     * @param [int] $ticket_id
     * @param [string] $list_type
     * @return json
     */
    public function ticket_comments($ticket_id, $list_type = null, Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $ticket_id = (int)$ticket_id;
            $list_type = strtolower($list_type);
            $ticket_comments = [];
            if(!is_numeric($ticket_id) || $ticket_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Ticket id is of invalid type'], 400);
            }
            elseif($list_type === '' && $list_type === 'asc' && $list_type === 'desc') {
                return response()->json(["success" => false, "errors" => 'List type value is invalid'], 400);
            }
            else {
                $ticket_id_exists = Tickets::where('id', $ticket_id)->where('company_id', $company_id)->exists();
                if(!$ticket_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Ticket id does not exist'], 400);
                }
                else {
                    if($list_type === '') {
                        $list_type = 'asc';
                    }
                }
            }

            $sql_query = "SELECT comments.id, comments.created_on, comments.public, comments.attachment, comments.comment, 
            created_by_user.fname AS created_by_fname, created_by_user.lname AS created_by_lname
            FROM comments
            LEFT JOIN users AS created_by_user ON comments.created_by = created_by_user.id 
            WHERE comments.ticket_id = ".$ticket_id." AND comments.company_id = ".$company_id." 
            ORDER BY comments.id DESC";
            $ticket_comments = DB::select($sql_query);

            foreach ($ticket_comments as $ticket_comment) {
                $comment_id = $ticket_comment->id;
                if ($ticket_comment->attachment === 'yes') {
                    $sql_query = "SELECT files.id, files.file_name FROM ticket_files AS files 
                    WHERE files.comment_id = ".$comment_id." AND files.linked_to = ".TicketCommentFiles::COMMENT;
                    $comment_attachments = DB::select($sql_query);
                    $ticket_comment->attachments = $comment_attachments;
                }
            }

            if($ticket_comments) {
                return response()->json(["success" => true, "data" => $ticket_comments], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No ticket comments available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Change ticket owner
     *
     * @param Request $request
     * @return void
     */
    public function change_ticket_owner(Request $request)
    {
        try {
            $ticket_id = 0;
            $new_owner_id = 0;
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            /*
            Logic for this validation is in the following link, comment by user - Wreigh
            https://stackoverflow.com/questions/49211988/laravel-unique-validation-where-clause
            */
            $validated = validator($request->all(), [
                'ticket_id' => 'bail|required|numeric|exists:tickets,id,company_id,' . $company_id,
                'new_owner_id' => 'bail|required|numeric|exists:users,id,company_id,' . $company_id
            ]);

            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $tickets = new Tickets();
                $ticket_id = $request->ticket_id;
                $new_owner_id = $request->new_owner_id;

                /*
                Check if ticket status, it should not be new and deleted and merged
                */
                $ticket_status_new = $tickets->where('id', $ticket_id)->where('status', Tickets::STATUS_New)->where('company_id', $company_id)->exists();
                if($ticket_status_new) {
                    return response()->json(["success" => false, "errors" => 'Existing ticket status is new. Take the ticket first'], 400);
                }
                else {
                    /*
                    Checking if the ticket is merged or deleted
                    */
                    $ticket_status = Tickets::select('status')->where('id', $ticket_id)->where('company_id', $company_id)->first();
                    if($ticket_status->status === Tickets::STATUS_Merged) {
                        return response()->json(["success" => false, "errors" => 'Ticket owner cannot be changed for merged ticket'], 400);
                    }
                    elseif($ticket_status->status === Tickets::STATUS_Deleted) {
                        return response()->json(["success" => false, "errors" => 'Ticket owner cannot be changed for deleted ticket'], 400);
                    }
                    else {
                        /*
                        Check if ticket existing owner and provided owner are different
                        */
                        $existing_owner = $tickets->where('id', $ticket_id)->where('owned_by', $new_owner_id)->where('company_id', $company_id)->exists();
                        if($existing_owner) {
                            return response()->json(["success" => false, "errors" => 'Existing ticket owner is same as provided ticket owner'], 400);
                        }
                    }
                }
            }

            $update_details = array(
                'owned_by' => $new_owner_id
            );
            $ticket_owner_changed = DB::table('tickets')->where('id', $ticket_id)
            ->where('company_id', $company_id)->update($update_details);
            if ($ticket_owner_changed) {
                /*
                Send email to the new owner
                */
                return response()->json(["success" => true, 'message' => 'Ticket owner changed successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Ticket owner not changed successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Merge ticket
     *
     * @param Request $request
     * @return void
     */
    public function merge_tickets(Request $request)
    {
        /*
        Merge is a one time activity and cannot be undone
        */
        try {
            $ticket_id = 0;
            $merge_ticket_id = 0;
            $ticket_comment = '';
            $merge_ticket_comment = '';
            $public = 'yes';
            $ticket_merged = 'false';
            $ticket_status_changed = 'false';
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            /*
            Logic for this validation is in the following link, comment by user - Wreigh
            https://stackoverflow.com/questions/49211988/laravel-unique-validation-where-clause
            */
            $validated = validator($request->all(), [
                'ticket_id' => 'bail|required|numeric|exists:tickets,id,company_id,' . $company_id,
                'ticket_comment' => 'bail|required',
                'merge_ticket_id' => 'bail|required|numeric|exists:tickets,id,company_id,' . $company_id,
                'merge_ticket_comment' => 'bail|required'
            ]);

            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                /*
                Checking if the ticket is merged or deleted
                */
                $ticket_status = Tickets::select('status')->where('id', $ticket_id)->where('company_id', $company_id)->first();
                if($ticket_status->status === Tickets::STATUS_Merged) {
                    return response()->json(["success" => false, "errors" => 'Ticket cannot be merged into merged ticket'], 400);
                }
                elseif($ticket_status->status === Tickets::STATUS_Deleted) {
                    return response()->json(["success" => false, "errors" => 'Ticket cannot be merged into deleted ticket'], 400);
                }

                $tickets = new Tickets();
                $ticket_id = $request->ticket_id;
                $ticket_comment = $request->ticket_comment;
                $merge_ticket_id = $request->merge_ticket_id;
                $merge_ticket_comment = $request->merge_ticket_comment;
            }

            /*
            Update ticket comments
            */
            $ticket_comment_data = array(
                'company_id' => $company_id,
                'ticket_id' => $ticket_id,
                'created_by' => $user_id,
                'created_on' => Carbon::now(),
                'public' => $public,
                'comment' => $ticket_comment
            );
            $comment_ticket_created = DB::table('comments')->insert($ticket_comment_data);

            if($comment_ticket_created) {
                /*
                Change ticket status of ticket
                */
                $update_details = array(
                    'status' => Tickets::STATUS_Merged,
                );
                $ticket_status_changed = DB::table('tickets')->where('id', $ticket_id)
                ->where('company_id', $company_id)->update($update_details);
            }

            if($ticket_status_changed) {
                $merge_ticket_comment_data = array(
                    'company_id' => $company_id,
                    'ticket_id' => $merge_ticket_id,
                    'created_by' => $user_id,
                    'created_on' => Carbon::now(),
                    'public' => $public,
                    'comment' => $merge_ticket_comment
                );
                $comment_merge_ticket_created = DB::table('comments')->insert($merge_ticket_comment_data);
                $ticket_merged = true;
            }

            if ($ticket_merged) {
                $message = 'Ticket Id: '.$ticket_id.' merged to Ticket Id: '.$merge_ticket_id.' successfully';
                return response()->json(["success" => true, 'message' => $message], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Tickets could not be merged successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Add blocked tickets
     *
     * @param Request $request
     * @return void
     */
    public function add_blocking_tickets(Request $request)
    {
        try {
            $blocked_tickets_added = false;
            $ticket_id = 0;
            $blocking_ticket_ids = [];
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'ticket_id' => 'bail|required|numeric|exists:tickets,id',
                'id' => Rule::unique('tickets')->where(fn ($query) => $query->where('company_id', $company_id)),
                'blocking_ticket_ids' => 'bail|required'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $blocking_ticket_ids = $request->blocking_ticket_ids;
                $blocking_ticket_ids = json_decode($blocking_ticket_ids, TRUE);
                if(!is_array($blocking_ticket_ids)) {
                    return response()->json(["success" => false, "errors" => 'Blocking tickets should be an array'], 400);
                }

                $tickets = new Tickets();
                $ticket_id = $request->ticket_id;
                /*
                Checking if the ticket is merged or deleted
                */
                $ticket_status = Tickets::select('status')->where('id', $ticket_id)->where('company_id', $company_id)->first();
                if($ticket_status->status === Tickets::STATUS_Merged) {
                    return response()->json(["success" => false, "errors" => 'Blocking tickets cannot be added for merged ticket'], 400);
                }
                elseif($ticket_status->status === Tickets::STATUS_Deleted) {
                    return response()->json(["success" => false, "errors" => 'Blocking tickets cannot be added for deleted ticket'], 400);
                }

                $blocking_ticket_ids_exists = Tickets::whereIn('id', $blocking_ticket_ids)->where('company_id', $company_id)->exists();
                if(!$blocking_ticket_ids_exists) {
                    return response()->json(["success" => false, "errors" => 'Blocking ticket id array value has invalid ticket ids'], 400);
                }
            }

            $temp_loop_counter = 0;
            for($i = 0; $i < count($blocking_ticket_ids); $i++) {
                $new_blocking_ticket_data = array(
                    'company_id' => $company_id,
                    'ticket_id' => $ticket_id,
                    'blocking_ticket_id' => $blocking_ticket_ids[$i]
                );
                $blocked_ticket_inserted = DB::table('blocking_tickets')->insert($new_blocking_ticket_data);

                if($blocked_ticket_inserted) {
                    $blocked_tickets_added = true;
                }
                else {
                    /*
                    Rolling back recent updates in case of a failure
                    */
                    BlockingTickets::whereIn('blocking_ticket_id',$blocking_ticket_ids)
                    ->where('company_id', $company_id)
                    ->where('ticket_id', $ticket_id)->delete();
                }
            }

            if ($blocked_tickets_added) {
                return response()->json(["success" => true, 'message' => 'Ticket updated with blocking tickets successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Ticket not updated with blocking tickets successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Add related tickets
     *
     * @param Request $request
     * @return void
     */
    public function add_related_tickets(Request $request)
    {
        try {
            $related_tickets_added = false;
            $ticket_id = 0;
            $related_ticket_ids = [];
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'ticket_id' => 'bail|required|numeric|exists:tickets,id',
                'id' => Rule::unique('tickets')->where(fn ($query) => $query->where('company_id', $company_id)),
                'related_ticket_ids' => 'bail|required'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $related_ticket_ids = $request->related_ticket_ids;
                $related_ticket_ids = json_decode($related_ticket_ids, TRUE);
                if(!is_array($related_ticket_ids)) {
                    return response()->json(["success" => false, "errors" => 'Related tickets should be an array'], 400);
                }

                $tickets = new Tickets();
                $ticket_id = $request->ticket_id;
                /*
                Checking if the ticket is merged or deleted
                */
                $ticket_status = Tickets::select('status')->where('id', $ticket_id)->where('company_id', $company_id)->first();
                if($ticket_status->status === Tickets::STATUS_Merged) {
                    return response()->json(["success" => false, "errors" => 'Related tickets cannot be added for merged ticket'], 400);
                }
                elseif($ticket_status->status === Tickets::STATUS_Deleted) {
                    return response()->json(["success" => false, "errors" => 'Related tickets cannot be added for deleted ticket'], 400);
                }

                $related_ticket_ids_exists = Tickets::whereIn('id', $related_ticket_ids)->where('company_id', $company_id)->exists();
                if(!$related_ticket_ids_exists) {
                    return response()->json(["success" => false, "errors" => 'Related ticket id array value has invalid ticket ids'], 400);
                }
            }

            $temp_loop_counter = 0;
            for($i = 0; $i < count($related_ticket_ids); $i++) {
                $new_related_ticket_data = array(
                    'company_id' => $company_id,
                    'ticket_id' => $ticket_id,
                    'related_ticket_id' => $related_ticket_ids[$i]
                );
                $related_ticket_inserted = DB::table('related_tickets')->insert($new_related_ticket_data);

                if($related_ticket_inserted) {
                    $related_tickets_added = true;
                }
                else {
                    /*
                    Rolling back recent updates in case of a failure
                    */
                    RelatedTickets::whereIn('related_ticket_id',$related_ticket_ids)
                    ->where('company_id', $company_id)
                    ->where('ticket_id', $ticket_id)->delete();
                }
            }

            if ($related_tickets_added) {
                return response()->json(["success" => true, 'message' => 'Ticket updated with related tickets successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Ticket not updated with related tickets successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    /**
     * Get all tickets
     *
     * @param Request $request
     * @return void
     */
    public function all_tickets(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $ticket_status = '';
            $ticket_priority = '';
            $customer_id = 0;
            $all_tickets = [];
            $paginated_tickets = [];
            $fetch_all = $request->get('fetch');

            $user_details = Users::select('role AS user_role')->where('id', $user_id)->first();
            if (empty($user_details)) {
                //throw error;
            }

            $user_role = $user_details->user_role;

            $sql_query = "SELECT tickets.id, tickets.easy_ticket_id, tickets.display_ticket_id, tickets.created_on,
            tickets.heading, tickets.description, tickets.priority, tickets.status, tickets.taken_on, tickets.resolved_on,
            tickets.time_worked, tickets.attachment_present, tickets.merged_ticket_id, tickets.from_email, tickets.email_cc,
            tickets.customer_id, tickets.created_by, tickets.owned_by,tickets.resolved_by, tickets.project_id, tickets.escalated, 
            projects.name AS project_name, 
            created_by_user.fname AS created_by_fname, created_by_user.lname AS created_by_lname,
            owned_by_user.fname AS owned_by_fname, owned_by_user.lname AS owned_by_lname,
            resolved_by_user.fname AS resolved_by_fname, resolved_by_user.lname AS resolved_by_lname, 
            customers.cust_name, contacts.fname AS contact_fname, contacts.lname AS contact_lname
            FROM tickets
            JOIN projects ON tickets.project_id = projects.id
            LEFT JOIN users AS created_by_user ON tickets.created_by = created_by_user.id
            LEFT JOIN users AS owned_by_user ON tickets.owned_by = owned_by_user.id
            LEFT JOIN users AS resolved_by_user ON tickets.resolved_by = resolved_by_user.id
            LEFT JOIN customers ON tickets.customer_id = customers.id
            LEFT JOIN contacts ON tickets.contact_id = contacts.id  
            WHERE tickets.status NOT IN ('resolved', 'deleted', 'merged') AND tickets.company_id = ".$company_id;

            if ($user_role !== Users::ADMIN_ROLE) {
                $condition_query = 'SELECT project_id FROM project_users_mapping WHERE user_id = '.$user_id;
                $sql_query = $sql_query.' AND tickets.project_id IN ('.$condition_query.')';
            }
            
            $sql_query = $sql_query." ORDER BY tickets.id DESC";

            $results = DB::select($sql_query);

            if ($fetch_all === 'all') {
                $paginated_tickets = $results;
            }
            else {
                $page = Paginator::resolveCurrentPage('page');
                //$perPage = env('PER_PAGE_LIMIT', 20);
                $perPage = 20;
                $all_tickets = collect($results);
                $paginated_tickets = new LengthAwarePaginator(
                    $all_tickets->forPage($page, $perPage),
                    $all_tickets->count(),
                    $perPage,
                    $page,
                    ['path' => Paginator::resolveCurrentPath()]
                );
            }

            if($paginated_tickets) {
                return response()->json(["success" => true, "data" => $paginated_tickets], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No tickets available for you'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    public function filter_tickets(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $project_id = 0;
            $ticket_status = '';
            $ticket_priority = '';
            $customer_id = 0;
            $project_tickets = [];
            
            /*if(!is_numeric($project_id) || $project_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Project id is of invalid type'], 400);
            }
            else {
                $project_id_exists = Projects::where('id', $project_id)->where('status', 'active')->where('company_id', $company_id)->exists();
                if(!$project_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Project id does not exist'], 400);
                }
            }*/

            if(isset($_GET['project_id'])) {
                $project_id = $_GET['project_id'];
            }
            if(isset($_GET['status'])) {
                $ticket_status = $_GET['status'];
            }
            if(isset($_GET['priority'])) {
                $ticket_priority = $_GET['priority'];
            }
            if(isset($_GET['customer_id'])) {
                $customer_id = $_GET['customer_id'];
            }

            $sql_query = "SELECT tickets.id, tickets.easy_ticket_id, tickets.display_ticket_id, tickets.created_on,
            tickets.heading, tickets.description, tickets.priority, tickets.status, tickets.taken_on, tickets.resolved_on,
            tickets.time_worked, tickets.attachment_present, tickets.merged_ticket_id, tickets.from_email, tickets.email_cc,
            tickets.customer_id, tickets.created_by, tickets.owned_by,tickets.resolved_by, tickets.project_id, 
            projects.name AS project_name, 
            created_by_user.fname AS created_by_fname, created_by_user.lname AS created_by_lname,
            owned_by_user.fname AS owned_by_fname, owned_by_user.lname AS owned_by_lname,
            resolved_by_user.fname AS resolved_by_fname, resolved_by_user.lname AS resolved_by_lname, 
            customers.cust_name, contacts.fname AS contact_fname, contacts.lname AS contact_lname
            FROM tickets
            JOIN projects ON tickets.project_id = projects.id
            LEFT JOIN users AS created_by_user ON tickets.created_by = created_by_user.id
            LEFT JOIN users AS owned_by_user ON tickets.owned_by = owned_by_user.id
            LEFT JOIN users AS resolved_by_user ON tickets.resolved_by = resolved_by_user.id
            LEFT JOIN customers ON tickets.customer_id = customers.id
            LEFT JOIN contacts ON tickets.contact_id = contacts.id  
            WHERE tickets.company_id = ".$company_id;

            
            $valid_ticket_status = array(
                Tickets::STATUS_ALL,
                Tickets::STATUS_New,
                Tickets::STATUS_Inprogress,
                Tickets::STATUS_Onhold,
                Tickets::STATUS_Resolved,
                Tickets::STATUS_Deleted,
                Tickets::STATUS_Merged
            );
            if(!in_array($ticket_status, $valid_ticket_status)) {
                return response()->json(["success" => false, "errors" => 'Invalid ticket status'], 400);
            }
            elseif ($ticket_status !== Tickets::STATUS_ALL) {
                $sql_query = $sql_query." AND tickets.status = '".$ticket_status."'";
            }

            $valid_ticket_priorities = array(
                Tickets::PRIORITY_ALL,
                Tickets::PRIORITY_CRITICAL,
                Tickets::PRIORITY_HIGH,
                Tickets::PRIORITY_MEDIUM,
                Tickets::PRIORITY_LOW
            );
            if(!in_array($ticket_priority, $valid_ticket_priorities)) {
                return response()->json(["success" => false, "errors" => 'Invalid ticket priority'], 400);
            }
            elseif ($ticket_priority !== Tickets::PRIORITY_ALL) {
                $sql_query = $sql_query." AND tickets.priority = '".$ticket_priority."'";
            }

            if ($ticket_status !== Tickets::STATUS_Resolved && $ticket_status !== Tickets::STATUS_Deleted 
                && $ticket_status !== Tickets::STATUS_Merged) 
            {
                $sql_query = $sql_query." AND tickets.status NOT IN ('resolved', 'deleted', 'merged')";
            }

            if ($project_id > 0) 
            {
                $sql_query = $sql_query." AND tickets.project_id = ".$project_id;
            }
            
            $sql_query = $sql_query." ORDER BY tickets.id DESC";

            $project_tickets = DB::select($sql_query);

            if($project_tickets) {
                return response()->json(["success" => true, "data" => $project_tickets], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No tickets available in the project'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


}
