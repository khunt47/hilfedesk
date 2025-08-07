<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Projects;
use App\Models\Tickets;
use App\Models\Customers;
use App\Models\Contacts;
use App\Models\GeedeskCustomerTickets;
use App\Models\TicketCommentFiles;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendTicketFromGeedeskEmailToAgents;
use Validator;


class GeedeskCustomerTicketsController extends Controller
{

    public function get($cust_id, Request $request) 
    {
        try {
            $company_id = $request->get('company_id'); //Daysupport company
            $cust_id = (int)$cust_id;
            if(!is_numeric($cust_id) || $cust_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Customer id is of invalid type'], 400);
            }

            $company_cust_mapping = DB::table('customers')
                                        ->select('id')
                                        ->where('geedesk_company_id', $cust_id)
                                        ->where('company_id', $company_id)
                                        ->where('status', Customers::STATUS_ACTIVE)
                                        ->first();

            if (empty($company_cust_mapping)) {
                return response()->json(["success" => false,  'message' => 'No customer available'], 400);
            }
            
            $actual_cust_id = $company_cust_mapping->id;

            $customer_tickets = DB::table('tickets')
                ->select(
                    'tickets.id as ticket_id', 
                    'tickets.display_ticket_id', 
                    'tickets.heading', 
                    'tickets.priority', 
                    'tickets.created_on', 
                    'tickets.status',
                    'users.fname'
                )
                ->leftJoin('users', 'users.id', '=', 'tickets.owned_by')
                ->where('tickets.customer_id', $actual_cust_id)
                ->where('tickets.company_id', $company_id)
                ->whereNotIn('tickets.status', ['resolved', 'deleted', 'merged'])
                ->orderBy('tickets.id', 'desc')
                ->get();

            if($customer_tickets) {
                return response()->json(["success" => true, "data" => $customer_tickets], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No ticket available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }



    public function get_closed_tickets($cust_id, Request $request) 
    {
        try {
            $company_id = $request->get('company_id'); //Daysupport company
            $cust_id = (int)$cust_id;
            if(!is_numeric($cust_id) || $cust_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Customer id is of invalid type'], 400);
            }

            $company_cust_mapping = DB::table('customers')
                                        ->select('id')
                                        ->where('geedesk_company_id', $cust_id)
                                        ->where('company_id', $company_id)
                                        ->where('status', Customers::STATUS_ACTIVE)
                                        ->first();

            if (empty($company_cust_mapping)) {
                return response()->json(["success" => false,  'message' => 'No customer available'], 400);
            }
            
            $actual_cust_id = $company_cust_mapping->id;

            $customer_tickets = DB::table('tickets')
                ->select(
                    'tickets.id as ticket_id', 
                    'tickets.display_ticket_id', 
                    'tickets.heading', 
                    'tickets.priority', 
                    'tickets.created_on', 
                    'tickets.status',
                    'users.fname'
                )
                ->leftJoin('users', 'users.id', '=', 'tickets.owned_by')
                ->where('tickets.customer_id', $actual_cust_id)
                ->where('tickets.company_id', $company_id)
                ->whereIn('tickets.status', ['resolved'])
                ->orderBy('tickets.id', 'desc')
                ->limit(50)
                ->get();

            if($customer_tickets) {
                return response()->json(["success" => true, "data" => $customer_tickets], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No ticket available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }



    public function show($cust_id, $ticket_id, Request $request) 
    {
        try {
            $company_id = $request->get('company_id'); //Daysupport company
            $cust_id = (int)$cust_id;
            $ticket_id = (int)$ticket_id;
            if(!is_numeric($cust_id) || $cust_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Customer id is of invalid type'], 400);
            }
            elseif(!is_numeric($ticket_id) || $ticket_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Ticket id is of invalid type'], 400);
            }

            $company_cust_mapping = DB::table('customers')
                                        ->select('id')
                                        ->where('geedesk_company_id', $cust_id)
                                        ->where('company_id', $company_id)
                                        ->where('status', Customers::STATUS_ACTIVE)
                                        ->first();

            if (empty($company_cust_mapping)) {
                return response()->json(["success" => false,  'message' => 'No customer available'], 400);
            }
            
            $actual_cust_id = $company_cust_mapping->id;

            $customer_tickets = DB::table('tickets')
                ->select(
                    'tickets.id as ticket_id', 
                    'tickets.display_ticket_id', 
                    'tickets.heading', 
                    'tickets.description', 
                    'tickets.priority', 
                    'tickets.created_on', 
                    'tickets.status',
                    'users.fname'
                )
                ->leftJoin('users', 'users.id', '=', 'tickets.owned_by')
                ->where('tickets.id', $ticket_id)
                ->where('tickets.customer_id', $actual_cust_id)
                ->where('tickets.company_id', $company_id)
                ->first();

            if($customer_tickets) {
                return response()->json(["success" => true, "data" => $customer_tickets], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No ticket available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }



    public function create(Request $request) 
    {
        try {
            $ticket_files = [];
            $contact_exists = 0;
            $created_user = Tickets::CUSTOMER_CREATED;
            $project_id = 0;
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            /*
            Modify validations so that it also validates for customer id and contact id
            */
            $rules = ['web', 'email', 'geedesk'];
            $validated = validator($request->all(), [
                'subject' => 'bail|required',
                'queue_email' => 'bail|required|email',
                'customer_id' => 'bail|numeric|required|exists:customers,id|min:1',
                'user_fname' => 'bail|required',
                'user_lname' => 'bail|required',
                'user_email' => 'bail|required|email',
                'source' => 'bail|in:'. implode(", ",$rules)
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                /*
                Checking if project id belongs to this company and is active
                */
                $customer_id = $request->customer_id;
                $queue_email = $request->queue_email;
                $user_fname = $request->user_fname;
                $user_lname = $request->user_lname;
                $user_email = $request->user_email;
                $source = $request->source;
                $ticket_heading = $request->input('subject');
                $file_attached = $request->file_attached;
                $file_attachment = $request->file_attachment;

                $systems_user = env('SYSTEMS_USER'); //later on make it dynamic like the task field so that created by can be user and customer

                $company_cust_mapping = DB::table('customers')
                                        ->select('id')
                                        ->where('geedesk_company_id', $customer_id)
                                        ->where('company_id', $company_id)
                                        ->where('status', Customers::STATUS_ACTIVE)
                                        ->first();
                
                $actual_cust_id = $company_cust_mapping->id;


                $contact_email = $request->contact_email;

                if ($contact_email !== '' || $contact_email !== null) {
                    $contact_exists = Contacts::where('email', $user_email)->where('company_id', $company_id)->exists();
                }

                if ($contact_exists) {
                        $contact_detail = Contacts::select('id')->where('email', $user_email)->where('company_id', $company_id)->first();
                        $contact_id = $contact_detail->id;
                }
                else {
                    $new_contact_data = array(
                        'company_id' => $company_id,
                        'customer_id' => $actual_cust_id,
                        'fname' => $user_fname,
                        'lname' => $user_lname,
                        'email' => $user_email
                    );

                    $contact_id = DB::table('contacts')->insertGetId($new_contact_data);
                }
            }

            $project_details = Projects::where('email', $queue_email)->where('status', 'active')->first();
            
            $project_id = $project_details->id;
            $project_code = $project_details->project_code;
            $last_ticket_id = $project_details->last_ticket_id;
            $ticket_id = (int)$last_ticket_id + 1;
            $easy_ticket_id = $project_code.'-'.(string)$ticket_id;

            $description = $request->input('description');
            $description = nl2br($description);

            

            $new_ticket_data = array(
                'easy_ticket_id' => $ticket_id,
                'display_ticket_id' => $easy_ticket_id,
                'company_id' => $company_id,
                'project_id' => $project_id,
                'created_by' => $systems_user,
                'created_on' => Carbon::now(),
                'heading' => $ticket_heading,
                'description' => $description,
                'status' => 'new',
                'merged_ticket_id' => 0,
                'from_email' => $user_email,
                'created_user' => $created_user,
                'customer_id' => $actual_cust_id, 
                'contact_id' => $contact_id,
                'attachment_present' => $file_attached
            );

            $ticket_created = DB::table('tickets')->insertGetId($new_ticket_data);
            if ($ticket_created > 0) {
                $new_ticket_id = $ticket_created;
                Projects::where('status', 'active')->where('company_id', $company_id)->where('id', $project_id)->update(['last_ticket_id' => $ticket_id]);
                $ticket_details = array(
                    'id' => $ticket_created,
                    'ticket_id' => $easy_ticket_id
                );

                if ($file_attached === Tickets::ATTACHMENT_YES) {
                    $new_ticket_file = array(
                        'company_id' => $company_id,
                        'ticket_id' => $new_ticket_id,
                        'linked_to' => TicketCommentFiles::TICKET,
                        'created_on' => Carbon::now(),
                        'file_name' => $file_attachment
                    );
                    DB::table('ticket_files')->insert($new_ticket_file);
                }

                $new_ticket_data = array(
                    'project_id' => $project_id,
                    'ticket_id' => $new_ticket_id,
                    'queue_email' => $queue_email
                );
                $new_ticket_data_json = json_encode($new_ticket_data);

                SendTicketFromGeedeskEmailToAgents::dispatch($new_ticket_data_json)
                ->onQueue('new_ticket_alert_from_geedesk_to_agents');
                
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


}
