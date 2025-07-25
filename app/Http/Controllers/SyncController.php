<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class SyncController extends Controller
{
    //main receiving data from local server
    public function receive(Request $request, $model)
    {
        $modelClass = config('sync.models')[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid model'], 400);
        }

        // foreach ($request->all() as $data) {
        //       try {
        //         $modelClass::updateOrCreate(
        //             ['uuid' => $data['uuid']],
        //             $data
        //         );
        //     } catch (\Exception $e) {
        //         \Log::error("Failed syncing $model: " . $e->getMessage());
        //         continue;
        //     }
        // }
        // return response()->json(['status' => 'ok']);

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
                $modelClass::updateOrCreate(['uuid' => $data['uuid']], $data);

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

        return $modelClass::withTrashed()->get();

    }
}
