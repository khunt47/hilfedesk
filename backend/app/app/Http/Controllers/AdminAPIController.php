<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Settings;
use App\Models\Users;

class AdminAPIController extends Controller
{
    
    /**
     * Validate if user is admin
     *
     * @return void
     */
    public function is_user_admin(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $is_admin = 'no';
            $user_details = Users::select('role')->where('id', $user_id)->where('company_id', $company_id)->first();
            $user_role = $user_details->role;
            if ($user_role === 'admin') {
                $is_admin = 'yes';
            }
            $data = array(
                'is_admin' => $is_admin
            );

            return response()->json(["success" => true, "data" => $data], 200);
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }


   /**
    * Get All Timezones API
    *
    * Method : GET
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] [ Get All timezones ]
    */
    public function get_all_timezones(Request $request)
    {
        try {
            $timezones = DB::table('timezones')
                         ->select('timezone')
                         ->orderBy('timezone', 'asc')
                         ->get();

            if ($timezones->isEmpty()) {
                return response()->json(["success" => false, "message" => "No timezones found."], 404);
            }

            return response()->json(["success" => true, "data" => $timezones], 200);

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }



    /**
     * Get timezone
     *
     * @return void
     */
    public function get_timezone(Request $request)
    {
        try {
            $company_id = $request->get('company_id');

            $timezone = Settings::select('value as timezone')
                        ->where('key', 'timezone')
                        ->where('company_id', $company_id)
                        ->first();

            if($timezone) {
                return response()->json(["success" => true, "data" => $timezone], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Timezone setting not available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }


    /**
     * Update timezone setting
     *
     * @param Request $request
     * @return void
     */
    public function update_timezone(Request $request)
    {
        try {
            $company_id = $request->get('company_id');

            $validated = validator($request->all(), [
                'timezone' => 'bail|required'
            ]);

            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
           
            $timezone = $request->timezone;

            $timezone_exists = Settings::where('key', 'timezone')
                              ->where('company_id', $company_id)
                              ->exists();

            if(!$timezone_exists) {

                $insert_details = array(
                    'key'        => 'timezone',
                    'value'      => $timezone,
                    'company_id' => $company_id
                );

                $timezone_inserted = DB::table('settings')->insert($insert_details);

                if($timezone_inserted) {
                   return response()->json(["success" => true, 'message' => 'Timezone inserted successfully'], 200);
                }
                else {
                   return response()->json(["success" => false,  'message' => 'Timezone was not inserted successfully'], 400);
                }
            }

            $update_details = array(
                'value' => $timezone
            );

            $timezone_updated = DB::table('settings')
                                ->where('key', 'timezone')
                                ->where('company_id', $company_id)
                                ->update($update_details);

            if ($timezone_updated) {
                return response()->json(["success" => true, 'message' => 'Timezone updated successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Timezone was not updated successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }

    /**
     * View users
     *
     * @return void
     */
    public function users(Request $request)
    {
        try{
            $company_id = $request->get('company_id');
            $loggedin_user_id = $request->get('user_id');

            $users = Users::select('id AS user_id', 'fname', 'lname', 'email', 'role')
                     ->where('company_id', $company_id)
                     ->get();

            if($users) {
                return response()->json(["success" => true, "data" => $users, "loggedin_user_id" => $loggedin_user_id], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No users available'], 404);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Create user
     *
     * @param Request $request
     * @return void
     */
    public function create_user(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'first_name' => 'bail|required|string',
                'last_name' => 'bail|required|string',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'bail|required|string',
                'role' => 'bail|required|string'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            
            $first_name = $request->first_name;
            $last_name = $request->last_name;
            $email = $request->email;
            
            $password = $request->password;

            $pepper = env('PASSWORD_PEPPER');
            $salt = rand(1000,9999);
            $salt = md5($salt);
            $salted_preppered_password = $salt.$password.$pepper;
            $hashed_password = Hash::make($salted_preppered_password);

            $role = $request->role;

            $new_user_data = array(
                'company_id' => $company_id,
                'fname' => $first_name,
                'lname' => $last_name,
                'email' => $email,
                'password' => $hashed_password,
                'role' => $role,
                'created_on' => Carbon::now(),
                'salt' => $salt
            );

            $new_user_id = DB::table('users')->insertGetId($new_user_data);
            if (is_numeric($new_user_id) && $new_user_id > 0) {
                $new_user_data = array(
                    'user_id' => $new_user_id
                );
                return response()->json(["success" => true, 'message' => 'User created successfully', 'data' => $new_user_data], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'User was not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    public function create_users(Request $request)
    {

    }

     /**
    * Update user API
    *
    * Method : POST
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] 
    */

    public function update_user(Request $request)
    {
        try {
            $validated = validator($request->all(), [
                'user_id' => 'bail|required|numeric|exists:users,id',
                'fname'   => 'bail|required|string',
                'lname'   => 'bail|required|string',
                'email'   => 'required|email|max:255',
                'role'    => 'bail|required|string'
            ]);

            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $company_id = $request->get('company_id');

            $user_id    = $request->input('user_id');
            $first_name = $request->input('fname');
            $last_name  = $request->input('lname');
            $email      = $request->input('email');
            $role       = $request->input('role');

            $emailExists = DB::table('users')
                           ->where('email', $email)
                           ->where('id', '!=', $user_id)
                           ->exists();

            if ($emailExists) {
                return response()->json(['success' => false, 'message' => 'Email is already taken by another user.'], 400);
            }

            $updateUser = DB::table('users')
                         ->where('id', $user_id)
                         ->update([
                            'fname' => $first_name,
                            'lname' => $last_name,
                            'email' => $email,
                            'role'  => $role
                         ]);

            if ($updateUser) {
                return response()->json(["success" => true, 'message' => 'User updated successfully'], 200);
            }
            else {
                return response()->json(["success" => false, 'message' => 'User not updated successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false, 'message' => $message], 400);
        }
    }


    /**
    * Change user password API
    *
    * Method : POST
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] 
    */
    public function change_user_password(Request $request)
    {
        try {
            $user_id    = $request->get('user_id');
            $company_id = $request->get('company_id');

            $validated = Validator($request->all(), [
                'user_id'          => 'bail|required|numeric|exists:users,id',
                'new_password'     => 'bail|required|string|min:8',
                'confirm_password' => 'bail|required|string|min:8']);

            if ($validated->fails()) {
                return response()->json(["success" => false, 'error' => $validated->errors()->first()], 400);
            }
        
            $logged_user_id = $user_id;
            $user_id        = $request->user_id;

            if ($logged_user_id === $user_id) {
                return response()->json(["success" => false, 'error' => "You cannot change your own password"], 400);
            }

            $new_password = $request->new_password;
            $new_password = trim($new_password);

            $confirm_password = $request->confirm_password;
            $confirm_password = trim($confirm_password);

            if($new_password !== $confirm_password) {
                return response()->json(["success" => false, 'error' => 'New password and confirm password does not match'], 400);
            }
            
            $user_detail = DB::table('users')->select('password', 'salt')
                           ->where('id', $user_id)
                           ->where('status', Users::STATUS_ACTIVE)
                           ->first();

            $existing_salt = $user_detail->salt;
            $pepper = env('PASSWORD_PEPPER');
            $new_password_check = $existing_salt.$new_password.$pepper;

            if (Hash::check($new_password_check, $user_detail->password)) {
                return response()->json(["success" => false, 'error' => 'New password and current password cannot be same'], 409);
            }

            $salt = rand(1000,9999);
            $salt = md5($salt);
            
            $salted_preppered_new_password = $salt.$new_password.$pepper;
            $hashed_password = Hash::make($salted_preppered_new_password);

            $update_details = array(
                'password' => $hashed_password, 
                'salt'     => $salt
            );

            $password_changed = DB::table('users')
                                ->where('id', $user_id)
                                ->where('company_id', $company_id)
                                ->update($update_details);

            if ($password_changed) {
                return response()->json(["success" => true, 'message' => 'Password changed successfully'], 200);
            }
            else {
                return response()->json(["success" => false, 'error' => 'Password not changed successfully'], 400);
            }      
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false, 'error' => $message], 400);
        }
    }


     /**
    * Delete user API
    *
    * Method : POST
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] 
    */
    public function delete_user(Request $request) {


        $company_id=  $request->get('company_id');

        $user_id = $request->input('user_id');
       
         $validated = validator($request->all(), [
                'user_id' => 'bail|required|numeric|exists:users,id',
            ]);

            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

        DB::table('users')
        ->where('id', $user_id)
        ->where('company_id', $company_id)
        ->delete();

        return response()->json(["success" => true, 'message' => 'User deleted successfully'], 200);

    }

    public function timezones(Request $request) {

        $company_id = $request->get('company_id');

        $validated = Validator($request->all(),[
        'timezone' => 'bail|required|',
        ]);

        if(!$company_id) {
            return response()->json(["success" => false, "error" => 'Company Id is required'], 400);
        }

        if(!$validated->fails()) {
            return response()->json(["success" => 'false', 'error' => $validated->errors()->first()], 400);
        }


    }

}
