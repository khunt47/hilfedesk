<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tickets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportsController extends Controller
{
    /**
    * Get Ticket Metrics API
    *
    * Method : GET
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] [ Get Ticket Metrics ]
    */
    public function get_ticket_metrics(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fromDate' => 'nullable|date|before_or_equal:toDate',
                'toDate'   => 'nullable|date|after_or_equal:fromDate'
            ]);

            if ($validator->fails()) {
                return response()->json(["success" => false, "errors" => $validator->errors()], 422);
            }

            $company_id = $request->get('company_id');

            $from_date = $request->fromDate;
            $to_date   = $request->toDate;  

            if (!$from_date || !$to_date) {
                $from_date = now()->subMonths(3)->toDateString();  
                $to_date   = now()->toDateString();                 
            }   

            $total = DB::table('tickets')
                     ->where('company_id', $company_id)
                     ->whereDate('created_on', '>=', $from_date)
                     ->whereDate('created_on', '<=', $to_date)
                     ->count();

            $status_counts = DB::table('tickets')
                             ->select('status', DB::raw('COUNT(*) as count'))
                             ->where('company_id', $company_id)
                             ->whereDate('created_on', '>=', $from_date)
                             ->whereDate('created_on', '<=', $to_date)
                             ->groupBy('status')
                             ->pluck('count', 'status'); 

            return response()->json(["success" => true, "data" => ["total" => $total, "counts" => $status_counts]], 200);
        } 
        catch(\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'error' => $message], 400);
        }
    } 
    

    /**
    * Get Agenet Workload API
    *
    * Method : GET
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] [ Get Workload ]
    */
    public function get_agent_workload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fromDate' => 'nullable|date|before_or_equal:toDate',
                'toDate'   => 'nullable|date|after_or_equal:fromDate'
            ]);

            if ($validator->fails()) {
                return response()->json(["success" => false, "errors" => $validator->errors()], 422);
            }

            $company_id = $request->get('company_id');

            $from_date = $request->fromDate;
            $to_date   = $request->toDate;

            if (!$from_date || !$to_date) {
                $from_date = now()->subMonths(3)->toDateString();  
                $to_date   = now()->toDateString();                 
            }

            $required_status = [
                Tickets::STATUS_New,
                Tickets::STATUS_Inprogress,
                Tickets::STATUS_Resolved
            ];

            $data = DB::table('tickets')
                    ->join('users', 'users.id', '=', 'tickets.created_by')
                    ->select('users.fname', 'users.lname', 'users.id as agent_id', DB::raw('COUNT(tickets.id) as ticket_count'))
                    ->whereIn('tickets.status', $required_status)
                    ->where('tickets.company_id', $company_id)
                    ->whereDate('tickets.created_on', '>=', $from_date)
                    ->whereDate('tickets.created_on', '<=', $to_date)
                    ->groupBy('tickets.created_by', 'users.fname', 'users.lname', 'users.id')
                    ->get();

            return response()->json(["success" => true, "data" => $data], 200);
        } 
        catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false, "error" => $message], 400);
        }
    }


    /**
    * Get Ticket Trends API
    *
    * Method : GET
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] [ Get Ticket Trends ]
    */
    public function get_ticket_trends(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fromDate' => 'nullable|date|before_or_equal:toDate',
                'toDate'   => 'nullable|date|after_or_equal:fromDate'
            ]);

            if ($validator->fails()) {
                return response()->json(["success" => false, "errors" => $validator->errors()], 422);
            }

            $company_id = $request->get('company_id');

            $from_date = $request->fromDate;
            $to_date   = $request->toDate;

            if (!$from_date || !$to_date) {
                $from_date = now()->subDays(6)->toDateString();  
                $to_date   = now()->toDateString();                 
            }

            $data = DB::table('tickets')
                    ->select(DB::raw("DATE(created_on) as date"), DB::raw("COUNT(*) as count"))
                    ->where('company_id', $company_id)
                    ->whereDate('tickets.created_on', '>=', $from_date)
                    ->whereDate('tickets.created_on', '<=', $to_date)
                    ->groupBy(DB::raw("DATE(created_on)"))
                    ->orderBy('date', 'asc')
                    ->get();

            return response()->json(["success" => true, "data" => $data], 200);
        } 
        catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false, "error" => $message], 400);
        }
    }

}
