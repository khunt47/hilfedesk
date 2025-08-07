<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\Projects;
use App\Models\Users;
use App\Models\ProjectMappedUsers;
use App\Models\Tickets;
use App\Models\Customers;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create_project(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'name' => 'bail|required|max:255',
                'email' => 'bail|required|unique:projects',
                'code' => 'bail|required|unique:projects,project_code|max:3',
                'project_code' => Rule::unique('projects')->where(fn ($query) => $query->where('company_id', $company_id))
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            do {
                $bytes = random_bytes(5);
                $random_string = bin2hex($bytes);
                $forward_email = $random_string.'@daysupport-email.geedesk.com';
                $forward_email_exists = Projects::where('forward_email', $forward_email)->where('status', 'active')->count();
            }while($forward_email_exists > 0);

            $new_project_data = array(
                'company_id' => $company_id,
                'name' => $request->input('name'),
                'project_code' => $request->input('code'),
                'email' => $request->input('email'),
                'forward_email' => $forward_email,
                'created_by' => $user_id,
                'created_on' => Carbon::now()
            );
            $project_created = DB::table('projects')->insert($new_project_data);
            if ($project_created) {
                return response()->json(["success" => true, 'message' => 'Project created successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Project was not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Get project
     *
     * @param INT $project_id
     * @return void
     */
    public function get_project($project_id, Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $project_id = (int)$project_id;
            if(!is_numeric($project_id) || $project_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Project id is of invalid type'], 400);
            }
            else {
                $project_id_exists = Projects::where('id', $project_id)->where('status', 'active')->where('company_id', $company_id)->exists();
                if(!$project_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Project id does not exist'], 400);
                }
            }
            $project_details = Projects::where('id', $project_id)->where('status', 'active')->where('company_id', $company_id)->first();
            if($project_details) {
                return response()->json(["success" => true, "data" => $project_details], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No active project available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Get projects
     *
     * @return json
     */
    public function get_projects(Request $request)
    {
        try{
            $company_id = $request->get('company_id');
            $all_projects = Projects::where('status', 'active')->where('company_id', $company_id)->get();
            if($all_projects) {
                return response()->json(["success" => true, "data" => $all_projects], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No projects active available'], 404);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Map users to projects
     *
     * @param Request $request
     * @return void
     */
    public function map_users(Request $request)
    {
        try {
            $user_ids_added = false;
            $project_id = 0;
            $user_ids = [];
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'project_id' => 'bail|required|numeric|exists:projects,id',
                'id' => Rule::unique('projects')->where(fn ($query) => $query->where('company_id', $company_id)),
                'user_ids' => 'bail|required'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $user_ids = $request->user_ids;
                $user_ids = json_decode($user_ids, TRUE);
                if(!is_array($user_ids)) {
                    return response()->json(["success" => false, "errors" => 'User ids should be an array'], 400);
                }

                $projects = new Projects();
                $project_id = $request->project_id;
                /*
                Checking if the project is active
                */
                $project_status = $projects->select('status')->where('id', $project_id)->where('company_id', $company_id)->first();
                if($project_status->status === Projects::STATUS_Deleted) {
                    return response()->json(["success" => false, "errors" => 'Related tickets cannot be added for merged ticket'], 400);
                }
                elseif($project_status->status === Projects::STATUS_Archived) {
                    return response()->json(["success" => false, "errors" => 'Related tickets cannot be added for deleted ticket'], 400);
                }

                $mapped_user_ids_exists = Users::whereIn('id', $user_ids)->where('company_id', $company_id)->exists();
                if(!$mapped_user_ids_exists) {
                    return response()->json(["success" => false, "errors" => 'Mapping user id array value has invalid user ids'], 400);
                }
                else {
                    $mapped_user_ids_mapped = ProjectMappedUsers::whereIn('user_id', $user_ids)->where('project_id', $project_id)->where('company_id', $company_id)->exists();
                    if($mapped_user_ids_mapped) {
                        return response()->json(["success" => false, "errors" => 'Mapping user ids are already mapped'], 400);
                    }
                }
            }

            $temp_loop_counter = 0;
            for($i = 0; $i < count($user_ids); $i++) {
                $new_mapped_users_data = array(
                    'company_id' => $company_id,
                    'project_id' => $project_id,
                    'user_id' => $user_ids[$i]
                );
                $mapped_user_inserted = DB::table('project_users_mapping')->insert($new_mapped_users_data);

                if($mapped_user_inserted) {
                    $user_ids_added = true;
                }
                else {
                    /*
                    Rolling back recent updates in case of a failure
                    */
                    ProjectMappedUsers::whereIn('user_id',$user_ids)
                    ->where('company_id', $company_id)
                    ->where('project_id', $project_id)->delete();
                }
            }

            if ($user_ids_added) {
                return response()->json(["success" => true, 'message' => 'Users mapped to the project successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Users not mapped to the project successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    public function mapped_users($project_id, Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $project_id = (int)$project_id;
            if(!is_numeric($project_id) || $project_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Project id is of invalid type'], 400);
            }
            else {
                $project_id_exists = Projects::where('id', $project_id)->where('status', 'active')->where('company_id', $company_id)->exists();
                if(!$project_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Project id does not exist'], 400);
                }
            }

            $sql_query = "SELECT project_users_mapping.user_id AS mapped_user_id, users.fname AS user_fname, users.lname AS user_lname 
            FROM project_users_mapping 
            JOIN users ON project_users_mapping.user_id = users.id 
            WHERE project_users_mapping.project_id = ".$project_id." AND project_users_mapping.company_id = ".$company_id;
            
            $sql_query = $sql_query." ORDER BY project_users_mapping.id ASC";
            $mapped_users = DB::select($sql_query);

            if($mapped_users) {
                return response()->json(["success" => true, "data" => $mapped_users], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No users mapped to the project'], 404);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    /**
     * Get project tickets
     *
     * @param Request $request
     * @return void
     */
    public function project_tickets($project_id, Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $project_id = (int)$project_id;
            $ticket_status = '';
            $ticket_priority = '';
            $customer_id = 0;
            $project_tickets = [];
            if(!is_numeric($project_id) || $project_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Project id is of invalid type'], 400);
            }
            else {
                $project_id_exists = Projects::where('id', $project_id)->where('status', 'active')->where('company_id', $company_id)->exists();
                if(!$project_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Project id does not exist'], 400);
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
            WHERE tickets.project_id = ".$project_id." AND tickets.status NOT IN ('resolved', 'deleted', 'merged') AND tickets.company_id = ".$company_id;
            
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



    /**
     * Get project tickets
     *
     * @param Request $request
     * @return void
     */
    public function project_filter_tickets($project_id, Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $project_id = (int)$project_id;
            $ticket_status = '';
            $ticket_priority = '';
            $customer_id = 0;
            $project_tickets = [];
            if(!is_numeric($project_id) || $project_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Project id is of invalid type'], 400);
            }
            else {
                $project_id_exists = Projects::where('id', $project_id)->where('status', 'active')->where('company_id', $company_id)->exists();
                if(!$project_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Project id does not exist'], 400);
                }
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
            WHERE tickets.project_id = ".$project_id." AND tickets.company_id = ".$company_id;

            
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

            //tickets.status NOT IN ('resolved', 'deleted')

            /*if(!is_numeric($customer_id)) {
                return response()->json(["success" => false, "errors" => 'Customer id is of invalid type'], 400);
            }
            elseif($customer_id > 0) {
                $customer_id_exists = Customers::where('id', $customer_id)->where('company_id', $company_id)->exists();
                if(!$customer_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Customer id does not exist'], 400);
                }
                else {
                    $sql_query = $sql_query." AND tickets.customer_id = ".$customer_id;
                }
            }*/
            
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


    //Methods below will be done in future

    public function edit_project(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            $validated = Validator($request->all(), [
                'project_id'    => 'bail|required|numeric|exists:projects,id',
                'project_name'  => 'bail|required|max:255',
                'project_code'  => 'bail|required|max:3',
                'project_email' => 'bail|required',
            ]);

            if($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $project_id = $request->project_id;
            $project_name = $request->project_name;
            $project_code = $request->project_code;
            $project_email = $request->project_email;

            $code_exists = DB::table('projects')
                            ->where('project_code', $project_code)
                            ->where('id', '!=', $project_id)
                            ->exists();

            if ($code_exists) {
                    return response()->json(['success' => false, 'error' => 'Code is already taken by another project.'], 400);
                }

            $email_exists = DB::table('projects')
                            ->where('email', $project_email)
                            ->where('id', '!=', $project_id)
                            ->exists();

            if ($email_exists) {
                    return response()->json(['success' => false, 'error' => 'Email is already taken by another project.'], 400);
                }    

            $update_details = array(
                'name' => $project_name,
                'project_code' => $project_code,
                'email' => $project_email
            );
                
            $update_project = DB::table('projects')
                             ->where('id', $project_id)
                             ->where('company_id', $company_id)
                             ->update($update_details);

            if($update_project) {
                return response()->json(["success" => true, 'message' => "Project updated successfully"], 200);
            }
            else {
                return response()->json(["success" => false, 'error' => 'Project not updated successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }

    public function archive_project(Request $request)
    {
        $company_id = $request->get('company_id');
        $user_id = $request->get('user_id');

    }

    public function delete_project(Request $request)
    {
        $company_id = $request->get('company_id');
        $user_id = $request->get('user_id');

    }

}
