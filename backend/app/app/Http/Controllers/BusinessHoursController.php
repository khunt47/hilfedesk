<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\BusinessHours;
use App\Models\Holidays;
use App\Libraries\BusinessHoursLib;
use Validator;


class BusinessHoursController extends Controller
{

    public function create(Request $request) 
    {
        try {
            $sunday_start = '00:00:00';
            $sunday_end = '00:00:00';
            $monday_start = '00:00:00';
            $monday_end = '00:00:00';
            $tuesday_start = '00:00:00';
            $tuesday_end = '00:00:00';
            $wednesday_start = '00:00:00';
            $wednesday_end = '00:00:00';
            $thursday_start = '00:00:00';
            $thursday_end = '00:00:00';
            $friday_start = '00:00:00';
            $friday_end = '00:00:00';
            $saturday_start = '00:00:00';
            $saturday_end = '00:00:00';
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            $validated = validator($request->all(), [
                'business_hour_name' => 'bail|required|string',
                'two47' => 'bail|required|string',
                'sunday_start' => 'bail|required_if:two47,no'

            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $business_hour_name = $request->input('business_hour_name');
            $two47 = $request->input('two47');

            if ($two47 === 'yes') {
                $two47 = BusinessHours::BUSINESS_HOURS_247_YES;
            }
            elseif ($two47 === 'no' || ($two47 === '' && $two47 === null)) {
                $two47 = BusinessHours::BUSINESS_HOURS_247_NO;
                $sunday_start = $request->input('sunday_start');
                $sunday_end = $request->input('sunday_end');
                $monday_start = $request->input('monday_start');
                $monday_end = $request->input('monday_end');
                $tuesday_start = $request->input('tuesday_start');
                $tuesday_end = $request->input('tuesday_end');
                $wednesday_start = $request->input('wednesday_start');
                $wednesday_end = $request->input('wednesday_end');
                $thursday_start = $request->input('thursday_start');
                $thursday_end = $request->input('thursday_end');
                $friday_start = $request->input('friday_start');
                $friday_end = $request->input('friday_end');
                $saturday_start = $request->input('saturday_start');
                $saturday_end = $request->input('saturday_end');

                $business_hour = new BusinessHoursLib();
                /*
                Validating the business hour time
                */
                if (!$business_hour->validate_start_time_end_time($sunday_start, $sunday_end)) {
                    return response()->json(["success" => false,  'message' => 'Sunday start time and end time are not correct.'], 400);
                }
                elseif (!$business_hour->validate_start_time_end_time($monday_start, $monday_end)) {
                    return response()->json(["success" => false,  'message' => 'Monday start time and end time are not correct.'], 400);
                }
                elseif (!$business_hour->validate_start_time_end_time($tuesday_start, $tuesday_end)) {
                    return response()->json(["success" => false,  'message' => 'Tuesday start time and end time are not correct.'], 400);
                }
                elseif (!$business_hour->validate_start_time_end_time($wednesday_start, $wednesday_end)) {
                    return response()->json(["success" => false,  'message' => 'Wednesday start time and end time are not correct.'], 400);
                }
                elseif (!$business_hour->validate_start_time_end_time($thursday_start, $thursday_end)) {
                    return response()->json(["success" => false,  'message' => 'Thursday start time and end time are not correct.'], 400);
                }
                elseif (!$business_hour->validate_start_time_end_time($friday_start, $friday_end)) {
                    return response()->json(["success" => false,  'message' => 'Friday start time and end time are not correct.'], 400);
                }
                elseif (!$business_hour->validate_start_time_end_time($saturday_start, $saturday_end)) {
                    return response()->json(["success" => false,  'message' => 'Saturday start time and end time are not correct.'], 400);
                }
            }

            $new_biz_hrs_data = array(
                'company_id' => $company_id,
                'name' => $business_hour_name,
                '247' => $two47,
                'sunday_start' => $sunday_start,
                'sunday_end' => $sunday_end,
                'monday_start' => $monday_start,
                'monday_end' => $monday_end,
                'tuesday_start' => $tuesday_start,
                'tuesday_end' => $tuesday_end,
                'wednesday_start' => $wednesday_start,
                'wednesday_end' => $wednesday_end,
                'thursday_start' => $thursday_start,
                'thursday_end' => $thursday_end,
                'friday_start' => $friday_start,
                'friday_end' => $friday_end,
                'saturday_start' => $saturday_start,
                'saturday_end' => $saturday_end
            );
            $biz_hrs_created = DB::table('business_hours')->insert($new_biz_hrs_data);
            if ($biz_hrs_created) {
                return response()->json(["success" => true, 'message' => 'Business hour created successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Business hour was not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    public function get(Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $business_hours = BusinessHours::select('id', 'name', '247 AS two47')->where('company_id', $company_id)->get();
            if(count($business_hours) > 0) {
                return response()->json(["success" => true, "data" => $business_hours], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No business hours available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    public function delete(Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'business_hour_id' => 'bail|required|numeric|exists:business_hours,id'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $business_hour_id = $request->input('business_hour_id');

            /*
            If mapped to sla do not delete
            has mapped holidays do not delete
            */
            
            $business_hours = BusinessHours::find($business_hour_id);
            $business_hour_deleted = $business_hours->delete();

            if ($business_hour_deleted) {
                return response()->json(["success" => true, 'message' => 'Business hours deleted successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Business hour was not deleted successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    public function details($business_hour_id, Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $business_hour = BusinessHours::select('name', '247 AS two47', 'sunday_start', 'sunday_end', 'monday_start', 'monday_end', 'tuesday_start', 'tuesday_end', 
                'wednesday_start', 'wednesday_end', 'thursday_start', 'thursday_end', 
                'friday_start', 'friday_end', 'saturday_start', 'saturday_end')
            ->where('id', $business_hour_id)->where('company_id', $company_id)->first();
            if($business_hour) {
                return response()->json(["success" => true, "data" => $business_hour], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No business hours available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    public function holiday_mapping(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');

            $validated = validator($request->all(), [
                'business_hour_id' => 'bail|required|numeric|exists:business_hours,id',
                'holiday_ids' => 'bail|required'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $business_hour_id = $request->input('business_hour_id');
            $holiday_ids = $request->input('holiday_ids');
            $holiday_ids = json_decode($holiday_ids, TRUE);
            if(!is_array($holiday_ids)) {
                return response()->json(["success" => false, "errors" => 'Holiday ids should be an array'], 400);
            }

            for ($i=0; $i < count($holiday_ids) ; $i++) { 
                $holiday_id = $holiday_ids[$i];

                //Check if provided holiday id is valid
                $holiday_id_valid = Holidays::where('id', $holiday_id)
                ->where('company_id', $company_id)->exists();

                if ($holiday_id_valid) {
                    $new_holiday_mapping_data = array(
                        'company_id' => $company_id,
                        'business_hour_id' => $business_hour_id,
                        'holiday_id' => $holiday_id
                    );
                    $holiday_mapping = DB::table('holiday_business_hours_mapping')->insert($new_holiday_mapping_data);
                }
                else {
                    //Log the error
                    $holiday_mapping = true;
                }
            }
            if ($holiday_mapping) {
                return response()->json(["success" => true, 'message' => 'Holidays mapped to business hour successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Holidays not mapped to business hour successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    public function edit() 
    {
        
    }

}
