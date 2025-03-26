<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StaffRequest;
use App\Http\Resources\Staff as ResourcesStaff;
use App\Models\Staff;
use App\Models\User;
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
            
            $staff = new Staff();
            $staff->username = ucwords($request->username);
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->first_name = $request->first_name;
            $staff->mobile_number = $request->mobile_number;
            $staff->role_id = $request->input('role.id');
            $staff->region_id = $request->input('region.id');
            $staff->province_id = $request->input('province.id');
            $staff->municipality_id = $request->input('municipality.id');
            $staff->river_id = $request->input('river.id');
            $staff->save();

          

            return response()->json(['message' => 'Staff has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, StaffRequest $request)
    {
        try 
        {
            $staff = Staff::findOrFail($id);
            $staff->username = ucwords($request->username);
            $staff->first_name = $request->first_name;
            $staff->last_name = $request->last_name;
            $staff->first_name = $request->first_name;
            $staff->mobile_number = $request->mobile_number;
            $staff->role_id = $request->input('role.id');
            $staff->region_id = $request->input('region.id');
            $staff->province_id = $request->input('province.id');
            $staff->municipality_id = $request->input('municipality.id');
            $staff->river_id = $request->input('river.id');
            $staff->update();

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