<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Libraries\JWT;
use App\Models\Apikeys;
use App\Models\Companies;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $company_id = $request->get('company_id');
            $api_key = $request->get('api_key');
            if(isset($api_key)) {
                $server_key = env('JWT_SECRET');
                $token_data = JWT::decode($api_key, $server_key);
                $company_id = $token_data->company_id;
                $company_exists = Companies::where('id', $company_id)->where('status', Companies::STATUS_ACTIVE)->exists();
                if($company_exists) {
                    $valid_key = Apikeys::where('company_id', $company_id)->where('api_key', $api_key)->exists();
                    if($valid_key) {
                        $token_valid = 'yes';
                    }
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
