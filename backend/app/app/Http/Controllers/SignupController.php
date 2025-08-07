<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Companies;
use App\Models\Users;
use App\Models\UserToken;
use Illuminate\Validation\Rule;
use Validator;
use App\Libraries\JWT;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendSignupConfirmationEmail;


class SignupController extends Controller
{
    
    public function register(Request $request) 
    {
        try {
            
            $validated = validator($request->all(), [
                'first_name' => 'bail|required|string',
                'last_name' => 'bail|required|string',
                'org_name' => 'bail|required|string',
                'email_addr' => 'bail|required|email|unique:companies,admin_email',
                'password' => 'bail|required|string|min:8'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            
            $first_name = $request->first_name;
            $last_name = $request->last_name;
            $comp_name = $request->org_name;
            $email_addr = $request->email_addr;

            $confirm_code = rand(1000000000,9999999999);
            $confirm_code = md5($confirm_code);

            $new_comp_data = array(
                'admin_fname' => $first_name,
                'admin_lname' => $last_name,
                'comp_name' => $comp_name,
                'admin_email' => $email_addr,
                'confirm_code' => $confirm_code,
                'created_on' => Carbon::now(),
                'status' => Companies::STATUS_ACTIVE
            );

            $signup_created = DB::table('companies')->insertGetId($new_comp_data);
            if (is_numeric($signup_created) && $signup_created > 0) {
                $company_id = $signup_created;
                /*
                create user
                */
                $salt = rand(1000,9999);
                $salt = md5($salt);
                $pepper = env('PASSWORD_PEPPER');
                $salted_preppered_password = $salt.$request->password.$pepper;
                $hashed_password = Hash::make($salted_preppered_password);
                $user_role = 'admin';

                $new_user_data = array(
                    'company_id' => $company_id,
                    'fname' => $first_name,
                    'lname' => $last_name,
                    'email' => $email_addr,
                    'password' => $hashed_password,
                    'role' => $user_role,
                    'created_on' => Carbon::now(), 
                    'salt' => $salt
                );
                $user_created = DB::table('users')->insertGetId($new_user_data);
                if (is_numeric($user_created) && $user_created > 0) {
                    $user_id = $user_created;
                    $generated_on = time();
                    // jwt valid for 60 days (60 seconds * 60 minutes * 24 hours * 60 days)
                    $expiration_time = $generated_on + 60 * 60 * 24 * 60;

                    $jwt = new JWT();
                    $token = array();
                    $token['user_id'] = $user_id;
                    $token['company_id'] = $company_id;
                    $token['user_fname'] = $first_name;
                    $token['user_lname'] = $last_name;
                    $token['user_email'] = $email_addr;
                    $token['user_role'] = $user_role;
                    $token['expiration_time'] = $expiration_time;
                    $server_key = env('JWT_SECRET');
                    $user_token = $jwt->encode($token, $server_key);
                    $is_admin = 'yes';
                    $random_string = rand(1000000000,9999999999);
                    $device_id = md5($random_string);
                    
                    $data = array(
                        'attempt' => 0,
                        'access_token' => $user_token, 
                        'is_admin' => $is_admin, 
                        'device_id' => $device_id
                    );

                    $user_token_exists = UserToken::where('user_id', $user_id)->where('company_id', $company_id)
                    ->where('device_id', $device_id)->exists();

                    if($user_token_exists) {
                        $api_key_created = UserToken::where('user_id', $user_id)->where('company_id', $company_id)->where('device_id', $device_id)->update(['user_token' => $user_token]);
                    }
                    else {
                        $new_insert_data = array(
                            'company_id' => $company_id,
                            'user_id' => $user_id,
                            'user_token' => $user_token,
                            'device_id' => $device_id              
                        );
                        $api_key_created = DB::table('user_token')->insert($new_insert_data);
                    }
                }
                // $queue_data = array(
                //     'company_id' => $company_id,
                //     'user_email' => $email_addr
                // );
                // $json_queue_data = json_encode($queue_data);
                // SendSignupConfirmationEmail::dispatch($json_queue_data);
                return response()->json(["success" => true, 'message' => 'Signup completed successfully', 'data' => $data], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Signup not completed successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    public function confirm_email() 
    {
        //do the confirmation of email api

    }
    
}
