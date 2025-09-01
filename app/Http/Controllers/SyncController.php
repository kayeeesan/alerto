<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Events\UserCreated;
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

                $record = $modelClass::updateOrCreate(['uuid' => $data['uuid']], $data);

                if ($model === 'users') {
                    event(new UserCreated($record));
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
