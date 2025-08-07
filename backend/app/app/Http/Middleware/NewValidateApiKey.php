<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Libraries\JWT;
use App\Models\Users;
use App\Models\Companies;
use Illuminate\Support\Facades\Log;


class NewValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token_valid = 'no';
            $token = $request->bearerToken();
            if(isset($token)) {
                $server_key = env('JWT_SECRET');
                $token_data = JWT::decode($token, $server_key);
                if (!$token_data) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid api key',
                    ], 401);
                }
                $expiration_time = $token_data->expiration_time;
                $current_time = time();
                if($current_time > $expiration_time) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User token expired',
                    ], 401);
                }
                $company_id = $token_data->company_id;
                $company_exists = Companies::where('id', $company_id)->where('status', Companies::STATUS_ACTIVE)->exists();
                if($company_exists) {
                    $token_valid = 'yes';
                }
                else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unauthorized',
                    ], 401);
                }

                if ($token_valid === 'yes') {
                    $request->attributes->add(['company_id' => $company_id]);
                    return $next($request);
                }
                else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Unauthorized',
                    ], 401);
                }
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid api key',
            ], 401);
        }
    }
}
