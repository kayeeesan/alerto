<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Region;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\River;
use App\Models\Staff;
use App\Events\UserCreated;
use App\Events\AlertUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;


class SyncController extends Controller
{
    //main receiving data from local server
    public function receive(Request $request, $model)
    {
        $modelClass = config('sync.models')[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid model'], 400);
        }


        foreach ($request->all() as $data) {
            try {
                if ($model === 'user_roles') {
                    $user = User::where('uuid', $data['user_uuid'])->first();
                    $role = Role::where('uuid', $data['role_uuid'])->first();

                    if (!$user || !$role) {
                        \Log::warning("[Sync] Skipped user_roles. Missing UUIDs.");
                        continue;
                    }

                    $modelClass::updateOrCreate([
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                    ], [
                        'created_at' => $data['created_at'],
                        'updated_at' => $data['updated_at'],
                    ]);

                    continue;
                }

                // Normal model with UUID
                // $modelClass::updateOrCreate(['uuid' => $data['uuid']], $data);

                if ($model === 'staffs') {
                    $user = User::where('uuid', $data['user_uuid'] ?? null)->first();
                    $role = Role::where('uuid', $data['role_uuid'] ?? null)->first();
                    $region = Region::where('uuid', $data['region_uuid'] ?? null)->first();
                    $province = Province::where('uuid', $data['province_uuid'] ?? null)->first();
                    $municipality = Municipality::where('uuid', $data['municipality_uuid'] ?? null)->first();
                    $river = River::where('uuid', $data['river_uuid'] ?? null)->first();

                    if (!$user || !$role || !$region || !$province || !$municipality || !$river) {
                        \Log::warning("[Sync] Skipped staffs. Missing related UUIDs.");
                        continue;
                    }

                    $modelClass::updateOrCreate(
                        ['uuid' => $data['uuid']],
                        [
                            'user_id' => $user->id,
                            'mobile_number' => $data['mobile_number'] ?? '',
                            'role_id' => $role->id,
                            'region_id' => $region->id,
                            'province_id' => $province->id,
                            'municipality_id' => $municipality->id,
                            'river_id' => $river->id,
                            'fb_lgu' => $data['fb_lgu'] ?? '',
                            'created_at' => $data['created_at'] ?? null,
                            'updated_at' => $data['updated_at'] ?? null,
                            'deleted_at' => $data['deleted_at'] ?? null,
                        ]
                    );

                    continue;
                }

                $record = $modelClass::updateOrCreate(['uuid' => $data['uuid']], $data);

                if ($model === 'users') {
                    event(new UserCreated($record));
                }

                if ($model === 'alerts') {
                    event(new AlertUpdated($record));
                }

            } catch (\Exception $e) {
                \Log::error("Failed syncing $model: " . $e->getMessage());
                continue;
            }
        }

        return response()->json(['status' => 'ok']);
    }

    //local server fetching data from main server
    public function fetch(Request $request, $model)
    {
        $modelClass = config('sync.models')[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid model'], 400);
        }

        if ($model === 'user_roles') {
            $data = $modelClass::with(['user', 'role'])->get()->map(function ($item) {
                return [
                    'user_uuid' => $item->user->uuid ?? null,
                    'role_uuid' => $item->role->uuid ?? null,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'synced_at' => $item->synced_at ?? null,
                ];
            });
        } else if ($model === 'staffs') {
            $data = $modelClass::withTrashed()->with(['user','role','region','province','municipality','river'])->get()->map(function ($item) {
                return [
                    'uuid' => $item->uuid,
                    'mobile_number' => $item->mobile_number,
                    'fb_lgu' => $item->fb_lgu,
                    'user_uuid' => $item->user->uuid ?? null,
                    'role_uuid' => $item->role->uuid ?? null,
                    'region_uuid' => $item->region->uuid ?? null,
                    'province_uuid' => $item->province->uuid ?? null,
                    'municipality_uuid' => $item->municipality->uuid ?? null,
                    'river_uuid' => $item->river->uuid ?? null,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'deleted_at' => $item->deleted_at,
                    'synced_at' => $item->synced_at ?? null,
                ];
            });
        } else if (in_array(SoftDeletes::class, class_uses_recursive($modelClass))) {
            $data = $modelClass::withTrashed()->get();
        } else {
            $data = $modelClass::get();
        }


        if ($model === 'users') {
            $data->makeVisible(['password']);
        }

        return $data;
    }
}
