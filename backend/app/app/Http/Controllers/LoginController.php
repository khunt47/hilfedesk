<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\JWT;
use App\Libraries\MailgunEmail;
use App\Models\Users;
use App\Models\UserToken;
use App\Models\Companies;
use App\Models\Apikeys;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Cache;

class LoginController extends Controller
{

    public function __construct()
    {

    }

    /**
     * User login  api
     *
     * @param Request $request
     * @return void
     */
    public function auth_user(Request $request)
    {
        try {
            $company_id = 0;
            $user_id = 0;
            $validated = validator($request->all(), [
                'user_name' => 'bail|required|email|exists:users,email',
                'user_password' => 'bail|required|min:8'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $user_name    = $request->user_name;
                $user_password = $request->user_password;
                $device_id = $request->device_id;
                if ($device_id === '' || $device_id === null) {
                    $random_string = rand(1000000000,9999999999);
                    $device_id = md5($random_string);
                }

                $user_details = Users::select('id as user_id', 'company_id', 'fname as user_fname',
                            'lname as user_lname', 'email as user_email', 'password', 'role as user_role', 'salt')
                        ->where('email', $user_name)
                        ->where('status', Users::STATUS_ACTIVE)
                        ->first();

                if(!$user_details) {
                    return response()->json(["success" => false,  'message' => 'User details does not exist'], 401);
                }

                $salt = $user_details->salt;
                $pepper = env('PASSWORD_PEPPER');
                $salted_preppered_password = $salt.$user_password.$pepper;

                if(Hash::check($salted_preppered_password, $user_details->password)) {
                    $user_id = $user_details->user_id;
                    $user_role = $user_details->user_role;
                    if ($user_role === 'admin') {
                        $is_admin = 'yes';
                    }
                    else {
                        $is_admin = 'no';
                    }
                    $company_id = $user_details->company_id;

                    $company_exists = Companies::where('id', $company_id)->where('status', Companies::STATUS_ACTIVE)->exists();
                    if (!$company_exists) {
                        return response()->json(["success" => false,  'message' => 'Your organization is not active'], 401);
                    }


                    $user_fname = $user_details->user_fname;
                    $user_lname = $user_details->user_lname;
                    $user_email = $user_details->user_email;
                    $user_role = $user_details->user_role;

                    $generated_on = time();
                    // jwt valid for 60 days (60 seconds * 60 minutes * 24 hours * 60 days)
                    $expiration_time = $generated_on + 60 * 60 * 24 * 60;

                    $jwt = new JWT();
                    $token = array();
                    $token['user_id'] = $user_id;
                    $token['company_id'] = $company_id;
                    $token['user_fname'] = $user_fname;
                    $token['user_lname'] = $user_lname;
                    $token['user_email'] = $user_email;
                    $token['user_role'] = $user_role;
                    //$token['company_api_key'] = $company_api_key;
                    $token['user_token'] = 'yes';
                    $token['expiration_time'] = $expiration_time;
                    $server_key = env('JWT_SECRET');
                    $user_token = $jwt->encode($token, $server_key);
                    $data = array(
                        'access_token' => $user_token, 
                        'is_admin' => $is_admin,
                        'device_id' => $device_id
                    );

                    $user_token_exists = UserToken::where('user_id', $user_id)->where('company_id', $company_id)->exists();

                    if($user_token_exists) {
                        $api_key_created = UserToken::where('user_id', $user_id)->where('company_id', $company_id)
                        ->update(['user_token' => $user_token]);
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

                    if ($api_key_created) {
                        return response()->json(["success" => true, 'message' => 'User authenticated successfully', 'data' => $data], 200);
                    }
                    else {
                        return response()->json(["success" => false,  'message' => 'There was an error please try again'], 400);
                    }
                }
                else {
                    return response()->json(["success" => false,  'message' => 'User was not authenticated successfully'], 401);
                }
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }

    }


    /**
     * User logout api
     *
     * @param Request $request
     * @return void
     */
    public function logout_user(Request $request)
    {
        try {
            $validated = validator($request->all(), [
                'device_id' => 'bail|required'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $device_id = $request->device_id;

            $clear_user_token = UserToken::where('user_id', $user_id)->where('company_id', $company_id)
            ->where('device_id', $device_id)->delete();
            if($clear_user_token) {
                return response()->json(["success" => true, 'message' => 'User logged out successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'User was not logged out successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    public function login_with_google(Request $request) 
    {
        try {
            $attempt = 0;
            $is_admin = 'no';
            $company_id = 0;
            $user_id = 0;
            $validated = validator($request->all(), [
                'user_name' => 'bail|required|email|exists:users,email',
                'device_id' => 'bail|required|string'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            $user_email = $request->user_name;
            $user_email_valid = Users::where('email', $user_email)->where('status', Users::STATUS_ACTIVE)->exists();
            if (!$user_email_valid) {
                return response()->json(["success" => false,  'message' => 'User email is invalid'], 400);
            }
            else {
                $user_details = Users::select('id as user_id', 'company_id', 'fname as user_fname',
                    'lname as user_lname', 'email as user_email', 'password', 'salt AS passowrd_salt', 
                    'role as user_role')->where('email', $user_email)->where('status', Users::STATUS_ACTIVE)
                ->first();
                $user_id = $user_details->user_id;
                $company_id = $user_details->company_id;

                $company_exists = Companies::where('id', $company_id)->where('status', Companies::STATUS_ACTIVE)->exists();
                if (!$company_exists) {
                    return response()->json(["success" => false,  'message' => 'Your organization is not active'], 401);
                }
                else {
                    $company_api_key_exists = Apikeys::where('company_id', $company_id)->exists();
                    if(!$company_api_key_exists) {
                        return response()->json(["success" => false,  'message' => 'Your organization needs to have an API key before you can login'], 401);
                    }
                    else {
                        $company_api_key_details = Apikeys::select('api_key')->where('company_id', $company_id)->first();
                        $company_api_key = $company_api_key_details->api_key;
                    }
                }

                $user_fname = $user_details->user_fname;
                $user_lname = $user_details->user_lname;
                $user_role = $user_details->user_role;
                $generated_on = time();
                // jwt valid for 60 days (60 seconds * 60 minutes * 24 hours * 60 days)
                $expiration_time = $generated_on + 60 * 60 * 24 * 60;
                $jwt = new JWT();
                $token = array();

                $token['user_id'] = $user_id;
                $token['company_id'] = $company_id;
                $token['user_fname'] = $user_fname;
                $token['user_lname'] = $user_lname;
                $token['user_email'] = $user_email;
                $token['user_role'] = $user_role;
                $token['company_api_key'] = $company_api_key;
                $token['user_token'] = 'yes';
                $token['expiration_time'] = $expiration_time;

                $server_key = env('JWT_SECRET');
                $user_token = $jwt->encode($token, $server_key);
                $is_admin = 'no';
                $device_id = $request->device_id;
                if($device_id === 'new') {
                    $random_string = rand(1000000000,9999999999);
                    $device_id = md5($random_string);
                }
                $attempt = 0;
                $data = array(
                    'attempt' => $attempt,
                    'access_token' => $user_token, 
                    'is_admin' => $is_admin, 
                    'device_id' => $device_id
                );
                $user_token_exists = UserToken::where('user_id', $user_id)->where('company_id', $company_id)
                        ->where('device_id', $device_id)->exists();
                if($user_token_exists) {
                    $api_key_created = UserToken::where('user_id', $user_id)->where('company_id', $company_id)
                            ->update(['user_token' => $user_token]);
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
                if ($api_key_created) {
                    return response()->json(["success" => true, 'message' => 'User authenticated successfully', 'data' => $data], 200);
                }
                else {
                    return response()->json(["success" => false,  'message' => 'There was an error please try again', 'data' => $data], 400);
                }
            }
        } 
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }
}
