<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Users;
use Validator;

class UserApiController extends Controller
{

    /**
     * Change password
     *
     * @return void
     */
    public function change_password(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            $current_password = '';
            $new_password = '';

            /*
            Logic for this validation is in the following link, comment by user - Wreigh
            https://stackoverflow.com/questions/49211988/laravel-unique-validation-where-clause
            */
            $validated = validator($request->all(), [
                'current_password' => 'bail|required|string|min:8', 
                'new_password' => 'bail|required|string|min:8'
            ]);

            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $users = new Users();

                $user_detail = $users->select('password', 'salt')
                        ->where('id', $user_id)
                        ->where('status', Users::STATUS_ACTIVE)
                        ->first();

                $current_password = $request->current_password;
                $current_password = trim($current_password);

                $new_password = $request->new_password;
                $new_password = trim($new_password);

                $salt = rand(1000,9999);
                $salt = md5($salt);
                $pepper = env('PASSWORD_PEPPER');

                $existing_salt = $user_detail->salt;

                $saled_peppered_current_password = $existing_salt.$current_password.$pepper; 

                $salted_preppered_new_password = $salt.$new_password.$pepper;
                $hashed_password = Hash::make($salted_preppered_new_password);
            }

            if (!Hash::check($saled_peppered_current_password, $user_detail->password)) {
                return response()->json(["success" => false,  'message' => 'Current password is wrong'], 409);
            }
            elseif (Hash::check($new_password, $user_detail->password) || $new_password === $current_password) {
                return response()->json(["success" => false,  'message' => 'New and current password cannot be same'], 409);
            }

            $update_details = array(
                'password' => $hashed_password, 
                'salt' => $salt
            );
            $password_changed = DB::table('users')->where('id', $user_id)->where('company_id', $company_id)
            ->update($update_details);
            if ($password_changed) {
                return response()->json(["success" => true, 'message' => 'Password changed successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Password not changed successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    
    public function user_profile(Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            $user_profile_details = Users::select('fname AS user_fname', 'lname AS user_lname', 'email AS user_email')->where('id', $user_id)->where('company_id', $company_id)->where('status', Users::STATUS_ACTIVE)->first();

            if($user_profile_details) {
                return response()->json(["success" => true, "data" => $user_profile_details], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No ticket comments available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }

}
