<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\Groups;
use App\Models\GroupMembers;
use App\Models\Users;
use Validator;


class GroupsController extends Controller
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
                'group_name' => 'bail|required|string'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }

            $group_name = $request->input('group_name');

            $new_group_data = array(
                'company_id' => $company_id,
                'name' => $group_name,
                'created_on' => Carbon::now()
            );
            $group_created = DB::table('groups')->insert($new_group_data);
            if ($group_created) {
                return response()->json(["success" => true, 'message' => 'Group created successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Group was not created successfully'], 400);
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
            $groups = Groups::select('name AS group_name')->where('company_id', $company_id)->get();
            if(count($groups) > 0) {
                return response()->json(["success" => true, "data" => $groups], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No groups available'], 400);
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }


    /**
     * Map users to group
     *
     * @param Request $request
     * @return void
     */
    public function map_users($group_id, Request $request)
    {
        try {
            $user_ids_added = false;
            $user_ids = [];
            $company_id = $request->get('company_id');
            $user_id = $request->get('user_id');
            $validated = validator($request->all(), [
                // 'group_id' => 'bail|required|numeric|exists:groups,id',
                // 'id' => Rule::unique('groups')->where(fn ($query) => $query->where('company_id', $company_id)),
                'user_ids' => 'bail|required'
            ]);
            if ($validated->fails()) {
                return response()->json(["success" => false, "errors" => $validated->errors()->first()], 400);
            }
            else {
                $user_ids = $request->user_ids;
                $user_ids = json_decode($user_ids, TRUE);
                if(!is_array($user_ids)) {
                    return response()->json(["success" => false, "errors" => 'User ids should be an array'], 400);
                }

                $groups = new Groups();

                $mapped_user_ids_exists = Users::whereIn('id', $user_ids)->where('company_id', $company_id)->exists();
                if(!$mapped_user_ids_exists) {
                    return response()->json(["success" => false, "errors" => 'Mapping user id array value has invalid user ids'], 400);
                }
                else {
                    $mapped_user_ids_mapped = GroupMembers::whereIn('user_id', $user_ids)->where('group_id', $group_id)->where('company_id', $company_id)->exists();
                    if($mapped_user_ids_mapped) {
                        return response()->json(["success" => false, "errors" => 'Mapping user ids are already mapped'], 400);
                    }
                }
            }

            $temp_loop_counter = 0;
            for($i = 0; $i < count($user_ids); $i++) {
                $new_mapped_users_data = array(
                    'company_id' => $company_id,
                    'group_id' => $group_id,
                    'user_id' => $user_ids[$i]
                );
                $mapped_user_inserted = DB::table('group_members')->insert($new_mapped_users_data);

                if($mapped_user_inserted) {
                    $user_ids_added = true;
                }
                else {
                    /*
                    Rolling back recent updates in case of a failure
                    */
                    GroupMembers::whereIn('user_id',$user_ids)
                    ->where('company_id', $company_id)
                    ->where('group_id', $group_id)->delete();
                }
            }

            if ($user_ids_added) {
                return response()->json(["success" => true, 'message' => 'Users mapped to the group successfully'], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'Users not mapped to the group successfully'], 400);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }



    public function mapped_users($group_id, Request $request) 
    {
        try {
            $company_id = $request->get('company_id');

            $sql_query = "SELECT group_members.user_id AS mapped_user_id, users.fname AS user_fname, users.lname AS user_lname 
            FROM group_members  
            JOIN users ON group_members.user_id = users.id 
            WHERE group_members.group_id = ".$group_id." AND group_members.company_id = ".$company_id;
            
            $sql_query = $sql_query." ORDER BY group_members.id ASC";
            $mapped_users = DB::select($sql_query);

            if($mapped_users) {
                return response()->json(["success" => true, "data" => $mapped_users], 200);
            }
            else {
                return response()->json(["success" => false,  'message' => 'No users mapped to the group'], 404);
            }
        }
        catch(\Throwable $th) {
            //Bring logging here via Beanstalkd
            $message = $th->getMessage();
            return response()->json(["success" => false,  'message' => $message], 400);
        }
    }
    
}
