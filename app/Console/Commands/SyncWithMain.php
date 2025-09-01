<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\NetworkHelper;
use App\Models\User;
use App\Models\Role;
use App\Events\UserCreated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SyncWithMain extends Command
{
    protected $signature = 'sync:main';
    protected $description = 'Synchronize local data with main server';

    public function handle()
    {
        if (!NetworkHelper::isOnline()) {
            $this->error('No internet connection. Skipping sync.');
            Log::warning('[Sync] Skipped due to no internet connection.');
            return 0;
        }

        $models = config('sync.models');
        $mainUrl = 'https://alertofews.com/api/sync';

        foreach ($models as $key => $modelClass) {
            Log::info("[Sync] Starting model: $key");

            /** --------------------------------
             *  PUSH: Local → Main
             * --------------------------------*/
            $toPush = $modelClass::where(function ($q) {
                $q->whereNull('synced_at')
                  ->orWhereColumn('updated_at', '>', 'synced_at');
            })->get();

            $payload = $toPush->map(function ($record) use ($key) {
                if ($key === 'users') {
                    return $record->makeVisible(['password'])->toArray();
                }

                if ($key === 'user_roles') {
                    return [
                        'user_uuid' => $record->user?->uuid,
                        'role_uuid' => $record->role?->uuid,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at
                    ];
                }

                return $record->toArray();
            })->toArray();

            if (!empty($payload)) {
                $response = Http::timeout(60)->post("{$mainUrl}/{$key}", $payload);

                if ($response->failed()) {
                    $this->error("❌ Failed to push $key");
                    Log::error("[Sync] Push failed for $key. Status: {$response->status()}");
                    continue;
                }

                $toPush->each->update(['synced_at' => now()]);
            }

            /** --------------------------------
             *  PULL: Main → Local
             * --------------------------------*/
            $response = Http::timeout(60)->get("{$mainUrl}/{$key}");

            if ($response->failed()) {
                $this->error("❌ Failed to pull $key");
                Log::error("[Sync] Pull failed for $key. Status: {$response->status()}");
                continue;
            }

            $dataArray = $response->json();
            if (!is_array($dataArray)) {
                $this->error("❌ Invalid response for $key");
                Log::error("[Sync] Invalid JSON for $key: " . $response->body());
                continue;
            }

            /** Special case for user_roles */
            if ($key === 'user_roles') {
                $mainPairs = [];

                foreach ($dataArray as $data) {
                    $user = User::where('uuid', $data['user_uuid'])->first();
                    $role = Role::where('uuid', $data['role_uuid'])->first();

                    if (!$user || !$role) {
                        Log::warning("[Sync] Skipping user_role: missing user/role");
                        continue;
                    }

                    $mainPairs[] = [$user->id, $role->id];

                    $modelClass::updateOrCreate(
                        ['user_id' => $user->id, 'role_id' => $role->id],
                        ['created_at' => $data['created_at'], 'updated_at' => $data['updated_at']]
                    );
                }

                // Delete local roles not present in main
                $modelClass::whereNot(function ($q) use ($mainPairs) {
                    foreach ($mainPairs as [$uid, $rid]) {
                        $q->orWhere(function ($sub) use ($uid, $rid) {
                            $sub->where('user_id', $uid)->where('role_id', $rid);
                        });
                    }
                })->delete();

                continue; // skip generic model sync
            }

            /** Generic model sync (for everything except user_roles) */
            foreach ($dataArray as $data) {
                $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass));

                $query = $modelClass::query();
                if ($usesSoftDeletes) {
                    $query = $query->withTrashed();
                }
                $existing = $query->where('uuid', $data['uuid'])->first();

                if (!$existing) {
                    $createData = array_merge($data, ['synced_at' => now()]);
                    if (!$usesSoftDeletes) {
                        unset($createData['deleted_at']);
                    }
                    $newRecord = $modelClass::create($createData);

                    if ($key === 'users') {
                        event(new UserCreated($newRecord));
                    }
                } else {
                    $remoteUpdatedAt = isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null;
                    $needsUpdate = $remoteUpdatedAt ? $remoteUpdatedAt->gt($existing->updated_at) : false;

                    $deletedChanged = false;
                    if ($usesSoftDeletes) {
                        $remoteDeletedAt = $data['deleted_at'] ?? null;
                        $remoteDeletedAt = $remoteDeletedAt ? Carbon::parse($remoteDeletedAt) : null;
                        $existingDeletedAt = $existing->deleted_at;
                        $deletedChanged = $remoteDeletedAt != $existingDeletedAt;
                    }

                    if ($needsUpdate || $deletedChanged) {
                        $updateData = array_merge($data, ['synced_at' => now()]);
                        if (!$usesSoftDeletes) {
                            unset($updateData['deleted_at']);
                        }
                        $existing->update($updateData);

                        if ($key === 'users') {
                            event(new UserCreated($existing));
                        }

                        if ($usesSoftDeletes) {
                            if (!is_null($data['deleted_at']) && !$existing->trashed()) {
                                $existing->delete();
                            }

                            if (is_null($data['deleted_at']) && method_exists($existing, 'restore') && $existing->trashed()) {
                                $existing->restore();
                            }
                        }
                    }
                }
            }

            Log::info("[Sync] Model OK: $key");
        }

        $this->info('✅ Sync finished.');
        Log::info('[Sync] All models processed.');
    }
}
