<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Http\Resources\Staff as StaffResource;
use App\Models\Staff;
use App\Models\User;
use App\Services\UserLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StaffController extends Controller
{
    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $staffs = Staff::when($request->search, function ($query, $search) {
            return $query->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('middle_name', 'like', "%$search%");
        })->paginate(10);

        return StaffResource::collection($staffs);
    }

    public function store(StaffRequest $request)
    {
        try {
            $default_password = "*1234#";
            $hashedPassword = Hash::make($default_password);

            $staff = new Staff();
            $staff->username = $request->username;
            $staff->first_name = ucwords($request->first_name);
            $staff->middle_name = ucwords($request->middle_name);
            $staff->last_name = ucwords($request->last_name);
            $staff->mobile_number = $request->mobile_number;
            $staff->role_id = $request->role_id;
            $staff->government_agency = $request->government_agency;
            $staff->region_id = $request->region_id;
            $staff->province_id = $request->province_id;
            $staff->municipality_id = $request->municipality_id;
            $staff->river_id = $request->river_id;
            $staff->lgu_fb = $request->lgu_fb;
            $staff->password = $hashedPassword;
            $staff->status = 'pending';
            $staff->save();

            User::create([
                'username' => $staff->username,
                'first_name' => $staff->first_name,
                'middle_name' => $staff->middle_name,
                'last_name' => $staff->last_name,
                'role_id' => $staff->role_id,
                'password' => $hashedPassword,
                'status' => 'pending',
            ]);

            $this->logService->logAction('Staff', $staff->id, 'create', $staff->toArray());

            return response()->json(['message' => 'Staff successfully registered.'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(StaffRequest $request, $id)
    {
        try {
            $staff = Staff::findOrFail($id);
            $oldData = $staff->toArray();

            $staff->username = $request->username;
            $staff->first_name = ucwords($request->first_name);
            $staff->middle_name = ucwords($request->middle_name);
            $staff->last_name = ucwords($request->last_name);
            $staff->mobile_number = $request->mobile_number;
            $staff->role_id = $request->role_id;
            $staff->government_agency = $request->government_agency;
            $staff->region_id = $request->region_id;
            $staff->province_id = $request->province_id;
            $staff->municipality_id = $request->municipality_id;
            $staff->river_id = $request->river_id;
            $staff->lgu_fb = $request->lgu_fb;
            $staff->status = $request->status;
            $staff->save();

            $user = User::where('username', $oldData['username'])->first();
            if ($user) {
                $user->update([
                    'username' => $staff->username,
                    'first_name' => $staff->first_name,
                    'middle_name' => $staff->middle_name,
                    'last_name' => $staff->last_name,
                    'role_id' => $staff->role_id,
                    'status' => $staff->status,
                ]);
            }

            $this->logService->logAction('Staff', $staff->id, 'update', [
                'old' => $oldData,
                'new' => $staff->toArray(),
            ]);

            return response()->json(['message' => 'Staff successfully updated.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        try {
            $staff = Staff::findOrFail($id);
            $staff->delete();

            $this->logService->logAction('Staff', $id, 'delete');

            return response()->json(['message' => 'Staff successfully deleted.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong. Please try again.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
