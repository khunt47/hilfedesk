<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\Customers;
use App\Models\Contacts;
use App\Models\Tickets;
use Validator;

class CustomerController extends Controller
{

    /**
     * Create customer
     *
     * @param Request $request
     * @return void
     */
    public function create_customer(Request $request)
    {
        try {
            $project_id = 0;
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'cust_name' => 'bail|required',
                'ltcrm_id' => 'bail|integer',
                'geedesk_id' => 'bail|integer',
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "error" => $validated->errors()->first()], 400);
            }
            else {
                $cust_name = $request->cust_name;
                $crm_customer_id = $request->ltcrm_id;
                $geedesk_company_id = $request->geedesk_id;
            }

            $new_customer_data = array(
                'company_id' => $company_id,
                'cust_name' => $cust_name,
                'crm_customer_id' => $crm_customer_id,
                'geedesk_company_id' => $geedesk_company_id
            );

            $customer_created = DB::table('customers')->insert($new_customer_data);
            if ($customer_created) {
                return response()->json(["success" => true, 'message' => 'Customer created successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'error' => 'Customer was not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'error' => $message], 400);
        }
    }


    /**
     * Get customers
     *
     * @return void
     */
    public function get_customers(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $customers = Customers::select('id', 'cust_name', 'crm_customer_id', 'geedesk_company_id')
            ->where('status', Customers::STATUS_ACTIVE)->where('company_id', $company_id)->get();
            if($customers) {
                return response()->json(["success" => true, "data" => $customers], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No customers available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }


    /**
     * Get specific customer
     *
     * @param INT $customer_id
     * @return void
     */
    public function get_customer($customer_id, Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $customer_id = (int)$customer_id;
            if(!is_numeric($customer_id) || $customer_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Customer id is of invalid type'], 400);
            }
            else {
                $customer_id_exists = Customers::where('id', $customer_id)->where('company_id', $company_id)->exists();
                if(!$customer_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Customer id does not exist'], 400);
                }
            }
            $customer_details = Customers::select('id', 'cust_name', 'crm_customer_id', 'geedesk_company_id')
            ->where('id', $customer_id)->where('company_id', $company_id)->first();
            if($customer_details) {
                return response()->json(["success" => true, "data" => $customer_details], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Customer details not available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }


    /**
     * Customer contacts
     *
     * @param INT $customer_id
     * @return void
     */
    public function get_customer_contacts($customer_id, Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $customer_id = (int)$customer_id;
            if(!is_numeric($customer_id) || $customer_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Customer id is of invalid type'], 400);
            }
            else {

                $customer_id_exists = Customers::where('id', $customer_id)
                ->where('status', Customers::STATUS_ACTIVE)
                ->where('company_id', $company_id)->exists();
                
                if(!$customer_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Customer id does not exist'], 400);
                }
            }
            $customer_contacts = Contacts::select('id', 'fname', 'lname', 'email', 'phone', 'mobile')->where('customer_id', $customer_id)->where('company_id', $company_id)->get();
            if($customer_contacts) {
                return response()->json(["success" => true, "data" => $customer_contacts], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Customer contacts not available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }



    public function customer_tickets($customer_id, Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $customer_id = (int)$customer_id;
            $ticket_status = '';
            $ticket_priority = '';
            //$customer_id = 0;
            $project_tickets = [];
            if(!is_numeric($customer_id) || $customer_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Customer id is of invalid type'], 400);
            }
            else {
                $customer_id_exists = Customers::where('id', $customer_id)->where('company_id', $company_id)->exists();
                if(!$customer_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Customer id does not exist'], 400);
                }
            }

            if(isset($_GET['status'])) {
                $ticket_status = $_GET['status'];
            }
            if(isset($_GET['priority'])) {
                $ticket_priority = $_GET['priority'];
            }

            $sql_query = "SELECT tickets.id, tickets.easy_ticket_id, tickets.display_ticket_id, tickets.created_on,
            tickets.heading, tickets.description, tickets.priority, tickets.status, tickets.taken_on, tickets.resolved_on,
            tickets.time_worked, tickets.attachment_present, tickets.merged_ticket_id, tickets.from_email, tickets.email_cc,
            tickets.customer_id, tickets.created_by, tickets.owned_by,tickets.resolved_by, tickets.project_id, 
            projects.name AS project_name, 
            created_by_user.fname AS created_by_fname, created_by_user.lname AS created_by_lname,
            owned_by_user.fname AS owned_by_fname, owned_by_user.lname AS owned_by_lname,
            resolved_by_user.fname AS resolved_by_fname, resolved_by_user.lname AS resolved_by_lname
            FROM tickets
            JOIN projects ON tickets.project_id = projects.id
            LEFT JOIN users AS created_by_user ON tickets.created_by = created_by_user.id
            LEFT JOIN users AS owned_by_user ON tickets.owned_by = owned_by_user.id
            LEFT JOIN users AS resolved_by_user ON tickets.resolved_by = resolved_by_user.id
            WHERE tickets.company_id = ".$company_id." AND tickets.customer_id = ".$customer_id;

            
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
            
            $sql_query = $sql_query." ORDER BY tickets.id DESC";

            $customer_tickets = DB::select($sql_query);

            if($customer_tickets) {
                return response()->json(["success" => true, "data" => $customer_tickets], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No tickets available for this customer'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    public function create_customers(Request $request)
    {
        //create customers in bulk
    }
    
}
