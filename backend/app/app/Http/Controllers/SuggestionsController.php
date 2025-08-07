<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\Suggestions;
use Validator;


class SuggestionsController extends Controller
{
    /**
    * Post Suggestion API
    *
    * Method : POST
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] 
    */

    public function create_suggestion(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id    = $request->get('user_id');

            $validated = validator($request->all(), [
                'suggestion'  => 'bail|required',
                'customer_id' => 'bail|numeric|exists:customers,id|min:1',
            ]);

            if ($validated->fails()) {
                return response()->json(["success" => false, "error" => $validated->errors()->first()], 400);
            }
            
            $suggestion  = $request->suggestion;
            $customer_id = $request->customer_id;

            $new_suggestion_data = array(
                'company_id'  => $company_id,
                'suggestion'  => $suggestion,
                'customer_id' => $customer_id,
                'user_id'     => $user_id,
                'created_on'  => Carbon::now()
            );

            $suggestion_created = DB::table('suggestions')->insert($new_suggestion_data);
            
            if ($suggestion_created) {
                return response()->json(["success" => true, 'message' => 'Suggestion created successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'error' => 'Suggestion was not created successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'error' => $message], 400);
        }
    }

    /**
    * Get Suggestions API
    *
    * Method : GET
    * 
    * @author Jayesoorya jayesoorya.p@geedesk.com
    *
    * @return [json] 
    */

    public function get_suggestions(Request $request)
    {
        try {
            $company_id = $request->get('company_id');
            $user_id    = $request->get('user_id');
            
            $suggestions = DB::table('suggestions')
                           ->select('suggestions.id', 'suggestions.suggestion', 'users.fname', 'users.lname',
                                    'customers.cust_name as customer_name', 'suggestions.created_on')
                           ->join('users', 'suggestions.user_id', '=', 'users.id')
                           ->join('customers', 'suggestions.customer_id', '=', 'customers.id')                           
                           ->orderBy('suggestions.id', 'desc')
                           ->get();

            if ($suggestions) {
                return response()->json([ "success" => true, "data" => $suggestions], 200);
            }
            else {
                return response()->json(["success" => false, "error" => 'No Suggestions found' ], 404);
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);

        }
    }

}
