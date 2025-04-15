<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StaffRequest;
use App\Http\Resources\Staff as ResourcesStaff;
use App\Models\Staff;
use App\Models\User;
use App\Models\Role; 
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $staffs = [];
        if (isset($request->search)) {
            $staffs = Staff::where('name', 'like', '%' . $request->search . '%');
        }

        $staffs = isset($request->search) && $request->search ? $staffs->paginate(10) : Staff::paginate(10);
        return ResourcesStaff::collection($staffs);
    }

    public function store(StaffRequest $request)
    {
        try {
            // Validate if the role exists
            $role = Role::findOrFail($request->input('role.id'));  // Ensure role exists
    
            // Create the Staff entry
            $staff = new Staff();
            $staff->username = ucwords($request->username);
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->mobile_number = $request->mobile_number;
            $staff->role_id = $role->id;  // Store the role id from the request
            $staff->region_id = $request->input('region.id');
            $staff->province_id = $request->input('province.id');
            $staff->municipality_id = $request->input('municipality.id');
            $staff->river_id = $request->input('river.id');
            $staff->fb_lgu = $request->fb_lgu;
            $staff->save();
    
            // Now create the User
            $default_password = "*1234#";
            $user = new User();
            $user->username = $request->username;
            $user->first_name = ucwords($request->first_name);
            $user->middle_name = null;
            $user->last_name = ucwords($request->last_name);
            $user->password = bcrypt($default_password);
            $user->status = 'pending';
            $user->save();
    
            // Assign role to user
            $user->roles()->sync([$role->id]);  // Sync the role for the user
    
            return response()->json(['message' => 'Staff has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function storeWalkinStaff(StaffRequest $request)
    {
        try {
            // Validate if the role exists
            $role = Role::findOrFail($request->input('role.id'));  // Ensure role exists
    
            // Create the Staff entry
            $staff = new Staff();
            $staff->username = ucwords($request->username);
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->mobile_number = $request->mobile_number;
            $staff->role_id = $role->id;  // Store the role id from the request
            $staff->region_id = $request->input('region.id');
            $staff->province_id = $request->input('province.id');
            $staff->municipality_id = $request->input('municipality.id');
            $staff->river_id = $request->input('river.id');
            $staff->fb_lgu = $request->fb_lgu;
            $staff->save();
    
            // Now create the User
            $default_password = "*1234#";
            $user = new User();
            $user->username = $request->username;
            $user->first_name = ucwords($request->first_name);
            $user->middle_name = null;
            $user->last_name = ucwords($request->last_name);
            $user->password = bcrypt($default_password);
            $user->status = 'pending';
            $user->save();
    
            // Assign role to user
            $user->roles()->sync([$role->id]);  // Sync the role for the user
    
            return response()->json(['message' => 'Staff has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function update($id, StaffRequest $request)
    {
        try {
            $staff = Staff::findOrFail($id);
            $user = User::where('username', $staff->username)->first();
    
            // Check if the new username already exists in users (excluding current user)
            $existingUser = User::where('username', $request->username)->where('id', '!=', $user->id)->first();
            if ($existingUser) {
                return response()->json(['message' => 'Username already exists. Please choose another one.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            // Update Staff details
            $staff->username = ucwords($request->username);
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->mobile_number = $request->mobile_number;
            $staff->role_id = $request->input('role.id');
            $staff->region_id = $request->input('region.id');
            $staff->province_id = $request->input('province.id');
            $staff->municipality_id = $request->input('municipality.id');
            $staff->river_id = $request->input('river.id');
            $staff->fb_lgu = $request->fb_lgu;
            $staff->update();
    
            // Update User details
            if ($user) {
                $user->username = $request->username;
                $user->first_name = ucwords($request->first_name);
                $user->middle_name = null;
                $user->last_name = ucwords($request->last_name);
                $user->update();
            }
    
            return response(['message' => 'Staff has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    

    public function destroy($id)
    {
        Staff::findOrFail($id)->delete();
        return response(['message' => 'Staff has been successfully deleted!']);
    }
}
