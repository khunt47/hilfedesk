<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\Contacts;
use Validator;

class ContactsController extends Controller
{

    /**
     * Create contact
     *
     * @param Request $request
     * @return void
     */
    public function create_contact(Request $request)
    {
        try {
            $customer_id = 0;
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'customer_id' => 'bail|required|numeric|exists:customers,id',
                'id' => Rule::unique('customers')->where(fn ($query) => $query->where('company_id', $company_id)),
                'cust_fname' => 'bail|required|string',
                'cust_lname' => 'bail|required|string',
                'cust_email' => 'bail|required|string|unique:contacts,email',
                'email' => Rule::unique('contacts')->where(fn ($query) => $query->where('company_id', $company_id)),
                'cust_phone' => 'bail|string',
                'cust_mobile' => 'bail|string'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $customer_id = $request->customer_id;
                $cust_fname = $request->cust_fname;
                $cust_lname = $request->cust_lname;
                $cust_email = $request->cust_email;
                $cust_phone = $request->cust_phone;
                $cust_mobile = $request->cust_mobile;
            }

            $new_contact_data = array(
                'company_id' => $company_id,
                'customer_id' => $customer_id,
                'fname' => $cust_fname,
                'lname' => $cust_lname,
                'email' => $cust_email,
                'phone' => $cust_phone,
                'mobile' => $cust_mobile
            );

            $contact_created = DB::table('contacts')->insert($new_contact_data);
            if ($contact_created) {
                return response()->json(["success" => true, 'message' => 'Contact created successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Contact was not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Get contacts
     *
     * @return void
     */
    public function get_contacts(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $contacts = Contacts::select('id', 'customer_id', 'fname', 'lname', 'email', 'phone', 'mobile')->where('company_id', $company_id)->get();

            $sql_query = "SELECT contacts.id, contacts.fname, contacts.lname, contacts.email, contacts.phone, contacts.mobile, contacts.customer_id, 
            customers.cust_name
            FROM contacts
            JOIN customers ON contacts.customer_id = customers.id
            WHERE contacts.company_id = ".$company_id;

            $contacts = DB::select($sql_query);

            if($contacts) {
                return response()->json(["success" => true, "data" => $contacts], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No contacts available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }


    /**
     * Get specific contact details
     *
     * @param INT $contact_id
     * @return void
     */
    public function get_contact($contact_id, Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $contact_id = (int)$contact_id;
            if(!is_numeric($contact_id) || $contact_id <= 0) {
                return response()->json(["success" => false, "errors" => 'Contact id is of invalid type'], 400);
            }
            else {
                $contact_id_exists = Contacts::where('id', $contact_id)->where('company_id', $company_id)->exists();
                if(!$contact_id_exists) {
                    return response()->json(["success" => false, "errors" => 'Contact id does not exist'], 400);
                }
            }
            $contact_details = Contacts::select('id', 'customer_id', 'fname', 'lname', 'email', 'phone', 'mobile')->where('id', $contact_id)->where('company_id', $company_id)->first();
            if($contact_details) {
                return response()->json(["success" => true, "data" => $contact_details], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Contact details not available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }


    public function create_contacts(Request $request)
    {
        //create contacts in bulk
    }
}
