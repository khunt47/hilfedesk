<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\Holidays;
use Validator;


class HolidaysController extends Controller
{

    /**
     * Create holiday
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'holiday_name' => 'bail|required|string',
                'state_date' => 'bail|required|date',
                'end_date' => 'bail|required|date',
                'recurring' => 'bail|required|in:yes,no'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $holiday_name = $request->input('holiday_name');
            $state_date = $request->input('state_date');
            $end_date = $request->input('end_date');
            $recurring = $request->input('recurring');

            if ($recurring === 'yes') {
                $recurring = Holidays::HOLIDAY_RECURRING;
            }
            elseif ($recurring === 'no') {
                $recurring = Holidays::HOLIDAY_NO_RECURRING;
            }
            else {
                $recurring = Holidays::HOLIDAY_NO_RECURRING;
            }

            $new_holiday_data = array(
                'company_id' => $company_id,
                'name' => $holiday_name,
                'holiday_start_dt' => $state_date,
                'holiday_end_dt' => $end_date,
                'recurring' => $recurring
            );
            $holiday_created = DB::table('holidays')->insert($new_holiday_data);
            if ($holiday_created) {
                return response()->json(["success" => true, 'message' => 'Holiday created successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Holiday was not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Get holidays
     *
     * @param Request $request
     * @return void
     */
    public function get(Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $holidays = Holidays::select('id AS holiday_id', 'name', 'holiday_start_dt', 'holiday_end_dt', 'recurring')
            ->where('company_id', $company_id)->get();
            if(count($holidays) > 0) {
                return response()->json(["success" => true, "data" => $holidays], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No holidays available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Edit holiday
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request) 
    {
        
    }


    /**
     * Delete holiday
     *
     * @param Request $request
     * @return void
     */    
    public function delete(Request $request) 
    {
        try {
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                'holiday_id' => 'bail|required|numeric|exists:holidays,id'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $holiday_id = $request->input('holiday_id');
            $holiday = Holidays::find($holiday_id);
            $holiday_deleted = $holiday->delete();

            if ($holiday_deleted) {
                return response()->json(["success" => true, 'message' => 'Holiday deleted successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Holiday was not deleted successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


}
