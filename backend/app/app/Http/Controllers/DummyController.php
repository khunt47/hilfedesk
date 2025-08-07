<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\JWT;
use App\Models\Apikeys;
use Illuminate\Support\Facades\DB;


class DummyController extends Controller
{
    public function index(Request $request) 
    {
        /*$company_id = 1;
        $generated_on = time();

        // jwt valid for 60 days (60 seconds * 60 minutes * 24 hours * 36500 days)
        $expiration_time = $generated_on + 60 * 60 * 24 * 36500;

                    $jwt = new JWT();
                    $token = array();
                    
                    $token['company_id'] = $company_id;
                    $token['expiration_time'] = $expiration_time;
                    $server_key = env('JWT_SECRET');
        return $user_token = $jwt->encode($token, $server_key);*/


    }
}
