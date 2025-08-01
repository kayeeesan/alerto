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
use Illuminate\Support\Facades\Hash;

class RegisterStaffController extends Controller
{
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
                'password' => Hash::make($request->password),
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
    
            return response()->json(['message' => "Successfully saved"]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
