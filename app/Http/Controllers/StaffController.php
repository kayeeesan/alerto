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
            $existingUser = User::where('username', $request->username)->first();
            if ($existingUser) {
                return response()->json(['message' => 'Username already exists.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $role = Role::where('slug', 'project-staff')->firstOrFail();

            $user = User::create([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'password' => bcrypt('*1234#'),
                'status' => 'pending',
            ]);
    
            $role = Role::where('slug', 'project-staff')->firstOrFail();
            $user->roles()->sync([$role->id]);
    
            $staff = Staff::create([
                'user_id' => $user->id,
                'mobile_number' => $request->mobile_number,
                'role_id' => $role->id,
                'region_id' => $request->input('region.id'),
                'province_id' => $request->input('province.id'),
                'municipality_id' => $request->input('municipality.id'),
                'river_id' => $request->input('river.id'),
                'fb_lgu' => $request->fb_lgu,
            ]);
    
            return response()->json(['message' => "Successfully saved, use your username {$user->username} and default password *1234# to login."]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function storeWalkinStaff(StaffRequest $request)
    {
        try {
            $existingUser = User::where('username', $request->username)->first();
            if ($existingUser) {
                return response()->json(['message' => 'Username already exists.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $role = Role::where('slug', 'project-staff')->firstOrFail();
    
            $user = User::create([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'password' => bcrypt('*1234#'),
                'status' => 'pending',
            ]);
    
            $role = Role::where('slug', 'project-staff')->firstOrFail();
            $user->roles()->sync([$role->id]);
    
            $staff = Staff::create([
                'user_id' => $user->id,
                'mobile_number' => $request->mobile_number,
                'role_id' => $role->id,
                'region_id' => $request->input('region.id'),
                'province_id' => $request->input('province.id'),
                'municipality_id' => $request->input('municipality.id'),
                'river_id' => $request->input('river.id'),
                'fb_lgu' => $request->fb_lgu,
            ]);
    
            return response()->json(['message' => 'Successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function update($id, StaffRequest $request)
    {
        try {
            $staff = Staff::findOrFail($id);
            $user = $staff->user;
    
            $existingUser = User::where('username', $request->username)
            ->where('id', '!=', $user->id)
            ->first();

            if ($existingUser) {
            return response()->json(['message' => 'Username already exists.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            $user->update([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
            ]);
    
            $staff->update([
                'mobile_number' => $request->mobile_number,
                'role_id' => $request->input('role.id'),
                'region_id' => $request->input('region.id'),
                'province_id' => $request->input('province.id'),
                'municipality_id' => $request->input('municipality.id'),
                'river_id' => $request->input('river.id'),
                'fb_lgu' => $request->fb_lgu,
            ]);
    
            return response()->json(['message' => 'Staff successfully updated.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    

    public function destroy($id)
    {
        try {
            $staff = Staff::findOrFail($id);
            $user = $staff->user;

            $staff->delete();

            if ($user) {
                $user->delete();
            }

            return response(['message' => 'Staff and associated User have been successfully deleted!']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
