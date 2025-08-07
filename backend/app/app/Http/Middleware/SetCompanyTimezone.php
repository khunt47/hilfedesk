<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Settings;

class SetCompanyTimezone
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
        $company_id = $request->get('company_id');
        $timezone_setting_exists = Settings::select('value')->where('company_id', $company_id)->where('key', 'timezone')->exists();
        if (!$timezone_setting_exists) {
            $timezone = 'Asia/Kolkata';
        }
        else {
            $timezone_settings = Settings::select('value')->where('company_id', $company_id)->where('key', 'timezone')->first();
            $timezone = $timezone_settings->value;
        }
        $request->attributes->add(['timezone' => $timezone]);
        date_default_timezone_set($timezone);
        return $next($request);
    }
}
