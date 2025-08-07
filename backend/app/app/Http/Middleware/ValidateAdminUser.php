<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Users;


class ValidateAdminUser
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
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $request->attributes->add(['company_id' => $company_id]);
            $request->attributes->add(['user_id' => $user_id]);

            $is_admin = Users::select('id')
            ->where('id', $user_id)->where('company_id', $company_id)
            ->where('role', Users::ADMIN_ROLE)->where('status', Users::STATUS_ACTIVE)->first();

            if ($is_admin === '' || $is_admin === null || empty($is_admin)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }
            return $next($request);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid api key',
            ], 401);
        }
    }
}
