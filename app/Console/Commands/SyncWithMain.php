<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Helpers\NetworkHelper;
use App\Models\User;
use App\Models\Role;
use App\Models\Region;
use App\Models\Province;
use App\Models\Municipality;
use App\Models\River;
use App\Events\UserCreated;
use App\Events\AlertUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SyncWithMain extends Command
{
    protected $signature = 'sync:main {model?}';
    protected $description = 'Synchronize local data with main server';

    public function handle()
    {
        if (!NetworkHelper::isOnline()) {
            $this->error('No internet connection. Skipping sync.');
            Log::warning('[Sync] Skipped due to no internet connection.');
            return 0;
        }

        $models = config('sync.models');
        $mainUrl = 'https://alerto.adzu.edu.ph/api/sync';
        $specificModel = $this->argument('model');

        if ($specificModel) {
            if (!isset($models[$specificModel])) {
                $this->error("Model '{$specificModel}' not found in sync config.");
                return 0;
            }

            $models = [$specificModel => $models[$specificModel]];
            Log::info("[Sync] Running partial sync for model: {$specificModel}");
        }

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

                if ($key === 'staffs') {
                    return [
                        'uuid' => $record->uuid,
                        'mobile_number' => $record->mobile_number,
                        'fb_lgu' => $record->fb_lgu,
                        'user_uuid' => $record->user?->uuid,
                        'role_uuid' => $record->role?->uuid,
                        'region_uuid' => $record->region?->uuid,
                        'province_uuid' => $record->province?->uuid,
                        'municipality_uuid' => $record->municipality?->uuid,
                        'river_uuid' => $record->river?->uuid,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at,
                        'deleted_at' => $record->deleted_at,
                    ];
                }

                if ($key === 'thresholds') {
                    return array_merge($record->toArray(), [
                        'sensorable_uuid' => $record->sensorable?->uuid,
                    ]);
                }

                return $record->toArray();
            })->toArray();

            $pushedCount = 0;

            if (!empty($payload)) {
                $response = Http::timeout(60)->post("{$mainUrl}/{$key}", $payload);

                if ($response->failed()) {
                    $this->error("❌ Failed to push $key");
                    Log::error("[Sync] Push failed for $key. Status: {$response->status()}");
                    continue;
                }

                $toPush->each->update(['synced_at' => now()]);
                $pushedCount = count($payload);
                $this->info("⬆️ Pushed {$pushedCount} record(s) for $key");
                Log::info("[Sync] Pushed {$pushedCount} record(s) for $key");
            } else{
                $this->line("⬆️ No records to push for $key");
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

            $pulledCount = count($dataArray);
            $this->info("⬇️ Pulled {$pulledCount} record(s) for $key");
            Log::info("[Sync] Pulled {$pulledCount} record(s) for $key");

            if ($pulledCount === 0) {
                Log::warning("[Sync] No data returned for $key. Response: " . $response->body());
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

                // Delete local roles not present in main (guard when empty to avoid wiping table)
                if (!empty($mainPairs)) {
                    $modelClass::whereNot(function ($q) use ($mainPairs) {
                        foreach ($mainPairs as [$uid, $rid]) {
                            $q->orWhere(function ($sub) use ($uid, $rid) {
                                $sub->where('user_id', $uid)->where('role_id', $rid);
                            });
                        }
                    })->delete();
                }

                continue; // skip generic model sync
            }

            /** Special case for staffs */
            if ($key === 'staffs') {
                foreach ($dataArray as $data) {
                    $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass));

                    $query = $modelClass::query();
                    if ($usesSoftDeletes) {
                        $query = $query->withTrashed();
                    }
                    $existing = $query->where('uuid', $data['uuid'])->first();

                    $user = isset($data['user_uuid']) ? User::where('uuid', $data['user_uuid'])->first() : null;
                    $role = isset($data['role_uuid']) ? Role::where('uuid', $data['role_uuid'])->first() : null;
                    $region = isset($data['region_uuid']) ? Region::where('uuid', $data['region_uuid'])->first() : null;
                    $province = isset($data['province_uuid']) ? Province::where('uuid', $data['province_uuid'])->first() : null;
                    $municipality = isset($data['municipality_uuid']) ? Municipality::where('uuid', $data['municipality_uuid'])->first() : null;
                    $river = isset($data['river_uuid']) ? River::where('uuid', $data['river_uuid'])->first() : null;

                    if (!$user || !$role || !$region || !$province || !$municipality || !$river) {
                        Log::warning("[Sync] Skipping staff {$data['uuid']}: missing relations");
                        continue;
                    }

                    $attributes = [
                        'user_id' => $user->id,
                        'mobile_number' => $data['mobile_number'] ?? '',
                        'role_id' => $role->id,
                        'region_id' => $region->id,
                        'province_id' => $province->id,
                        'municipality_id' => $municipality->id,
                        'river_id' => $river->id,
                        'fb_lgu' => $data['fb_lgu'] ?? '',
                    ];

                    if (!$existing) {
                        $createData = array_merge(['uuid' => $data['uuid']], $attributes, ['synced_at' => now()]);
                        $modelClass::create($createData);
                    } else {
                        $remoteUpdatedAt = isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null;
                        $needsUpdate = $remoteUpdatedAt ? $remoteUpdatedAt->gt($existing->updated_at) : false;

                        // Force update when domain data differs (main is authoritative)
                        $domainDiffers = false;
                        foreach ($attributes as $k => $v) {
                            if ($existing->{$k} != $v) { $domainDiffers = true; break; }
                        }

                        if ($needsUpdate || $domainDiffers) {
                            $existing->update(array_merge($attributes, ['synced_at' => now()]));
                        }

                        if ($usesSoftDeletes) {
                            $remoteDeletedAt = $data['deleted_at'] ?? null;
                            $remoteDeletedAt = $remoteDeletedAt ? Carbon::parse($remoteDeletedAt) : null;
                            $existingDeletedAt = $existing->deleted_at;
                            $deletedChanged = $remoteDeletedAt != $existingDeletedAt;

                            if ($deletedChanged) {
                                if (!is_null($remoteDeletedAt) && !$existing->trashed()) {
                                    $existing->delete();
                                } elseif (is_null($remoteDeletedAt) && method_exists($existing, 'restore') && $existing->trashed()) {
                                    $existing->restore();
                                }
                            }
                        }
                    }
                }

                Log::info("[Sync] Model OK: $key");
                continue;
            }

            /** Generic model sync (for everything except user_roles) */
            foreach ($dataArray as $data) {
                $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass));

                // Resolve sensorable linkage for thresholds using sensorable_uuid from main
                if ($key === 'thresholds') {
                    $sensorableUuid = $data['sensorable_uuid'] ?? null;
                    $sensorableType = $data['sensorable_type'] ?? null;
                    if ($sensorableUuid && $sensorableType) {
                        try {
                            if (class_exists($sensorableType)) {
                                $sensor = $sensorableType::where('uuid', $sensorableUuid)->first();
                                if ($sensor) {
                                    $data['sensorable_id'] = $sensor->id;
                                } else {
                                    Log::warning("[Sync] Threshold {$data['uuid']} linking skipped: missing sensor {$sensorableUuid}");
                                }
                            } else {
                                Log::warning("[Sync] Threshold {$data['uuid']} has unknown sensorable_type {$sensorableType}");
                            }
                        } catch (\Throwable $e) {
                            Log::error("[Sync] Threshold {$data['uuid']} link error: {$e->getMessage()}");
                        }
                    }
                    unset($data['sensorable_uuid']);
                }

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

                    if ($key === 'alerts') {
                        event(new AlertUpdated($newRecord));
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

                    // Force update when domain data differs (main is authoritative)
                    $ignore = ['id','synced_at','created_at','updated_at','deleted_at'];
                    $localComparable = Arr::except($existing->getAttributes(), $ignore);
                    $remoteComparable = Arr::except($data, $ignore);
                    ksort($localComparable);
                    ksort($remoteComparable);
                    $domainDiffers = $localComparable != $remoteComparable;

                    if ($needsUpdate || $deletedChanged || $domainDiffers) {
                        $updateData = array_merge($data, ['synced_at' => now()]);
                        if (!$usesSoftDeletes) {
                            unset($updateData['deleted_at']);
                        }
                        $existing->update($updateData);

                        if ($key === 'users') {
                            event(new UserCreated($existing));
                        }

                        if ($key === 'alerts') {
                            event(new AlertUpdated($existing));
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

        // Second pull-only pass to catch cross-device updates pushed during this run
        foreach ($models as $key => $modelClass) {
            Log::info("[Sync] Starting second pull-only pass for model: $key");

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

            $pulledCount = count($dataArray);
            $this->info("⬇️ Pulled {$pulledCount} record(s) for $key");
            Log::info("[Sync] Pulled {$pulledCount} record(s) for $key");

            if ($pulledCount === 0) {
                Log::warning("[Sync] No data returned for $key. Response: " . $response->body());
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

                // Delete local roles not present in main (guard when empty to avoid wiping table)
                if (!empty($mainPairs)) {
                    $modelClass::whereNot(function ($q) use ($mainPairs) {
                        foreach ($mainPairs as [$uid, $rid]) {
                            $q->orWhere(function ($sub) use ($uid, $rid) {
                                $sub->where('user_id', $uid)->where('role_id', $rid);
                            });
                        }
                    })->delete();
                }

                continue; // skip generic model sync
            }

            /** Special case for staffs */
            if ($key === 'staffs') {
                foreach ($dataArray as $data) {
                    $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass));

                    $query = $modelClass::query();
                    if ($usesSoftDeletes) {
                        $query = $query->withTrashed();
                    }
                    $existing = $query->where('uuid', $data['uuid'])->first();

                    $user = isset($data['user_uuid']) ? User::where('uuid', $data['user_uuid'])->first() : null;
                    $role = isset($data['role_uuid']) ? Role::where('uuid', $data['role_uuid'])->first() : null;
                    $region = isset($data['region_uuid']) ? Region::where('uuid', $data['region_uuid'])->first() : null;
                    $province = isset($data['province_uuid']) ? Province::where('uuid', $data['province_uuid'])->first() : null;
                    $municipality = isset($data['municipality_uuid']) ? Municipality::where('uuid', $data['municipality_uuid'])->first() : null;
                    $river = isset($data['river_uuid']) ? River::where('uuid', $data['river_uuid'])->first() : null;

                    if (!$user || !$role || !$region || !$province || !$municipality || !$river) {
                        Log::warning("[Sync] Skipping staff {$data['uuid']}: missing relations");
                        continue;
                    }

                    $attributes = [
                        'user_id' => $user->id,
                        'mobile_number' => $data['mobile_number'] ?? '',
                        'role_id' => $role->id,
                        'region_id' => $region->id,
                        'province_id' => $province->id,
                        'municipality_id' => $municipality->id,
                        'river_id' => $river->id,
                        'fb_lgu' => $data['fb_lgu'] ?? '',
                    ];

                    if (!$existing) {
                        $createData = array_merge(['uuid' => $data['uuid']], $attributes, ['synced_at' => now()]);
                        $modelClass::create($createData);
                    } else {
                        $remoteUpdatedAt = isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null;
                        $needsUpdate = $remoteUpdatedAt ? $remoteUpdatedAt->gt($existing->updated_at) : false;

                        // Force update when domain data differs (main is authoritative)
                        $domainDiffers = false;
                        foreach ($attributes as $k => $v) {
                            if ($existing->{$k} != $v) { $domainDiffers = true; break; }
                        }

                        if ($needsUpdate || $domainDiffers) {
                            $existing->update(array_merge($attributes, ['synced_at' => now()]));
                        }

                        if ($usesSoftDeletes) {
                            $remoteDeletedAt = $data['deleted_at'] ?? null;
                            $remoteDeletedAt = $remoteDeletedAt ? Carbon::parse($remoteDeletedAt) : null;
                            $existingDeletedAt = $existing->deleted_at;
                            $deletedChanged = $remoteDeletedAt != $existingDeletedAt;

                            if ($deletedChanged) {
                                if (!is_null($remoteDeletedAt) && !$existing->trashed()) {
                                    $existing->delete();
                                } elseif (is_null($remoteDeletedAt) && method_exists($existing, 'restore') && $existing->trashed()) {
                                    $existing->restore();
                                }
                            }
                        }
                    }
                }

                Log::info("[Sync] Model OK: $key");
                continue;
            }

            /** Generic model sync (for everything except user_roles) */
            foreach ($dataArray as $data) {
                $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass));

                // Resolve sensorable linkage for thresholds using sensorable_uuid from main
                if ($key === 'thresholds') {
                    $sensorableUuid = $data['sensorable_uuid'] ?? null;
                    $sensorableType = $data['sensorable_type'] ?? null;
                    if ($sensorableUuid && $sensorableType) {
                        try {
                            if (class_exists($sensorableType)) {
                                $sensor = $sensorableType::where('uuid', $sensorableUuid)->first();
                                if ($sensor) {
                                    $data['sensorable_id'] = $sensor->id;
                                } else {
                                    Log::warning("[Sync] Threshold {$data['uuid']} linking skipped: missing sensor {$sensorableUuid}");
                                }
                            } else {
                                Log::warning("[Sync] Threshold {$data['uuid']} has unknown sensorable_type {$sensorableType}");
                            }
                        } catch (\Throwable $e) {
                            Log::error("[Sync] Threshold {$data['uuid']} link error: {$e->getMessage()}");
                        }
                    }
                    unset($data['sensorable_uuid']);
                }

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

                    if ($key === 'alerts') {
                        event(new AlertUpdated($newRecord));
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

                    // Force update when domain data differs (main is authoritative)
                    $ignore = ['id','synced_at','created_at','updated_at','deleted_at'];
                    $localComparable = Arr::except($existing->getAttributes(), $ignore);
                    $remoteComparable = Arr::except($data, $ignore);
                    ksort($localComparable);
                    ksort($remoteComparable);
                    $domainDiffers = $localComparable != $remoteComparable;

                    if ($needsUpdate || $deletedChanged || $domainDiffers) {
                        $updateData = array_merge($data, ['synced_at' => now()]);
                        if (!$usesSoftDeletes) {
                            unset($updateData['deleted_at']);
                        }
                        $existing->update($updateData);

                        if ($key === 'users') {
                            event(new UserCreated($existing));
                        }

                        if ($key === 'alerts') {
                            event(new AlertUpdated($existing));
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

        // Second pull-only pass to catch cross-device updates pushed during this run
        foreach ($models as $key => $modelClass) {
            Log::info("[Sync] Starting second pull-only pass for model: $key");

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

            $pulledCount = count($dataArray);
            $this->info("⬇️ Pulled {$pulledCount} record(s) for $key");
            Log::info("[Sync] Pulled {$pulledCount} record(s) for $key");

            if ($pulledCount === 0) {
                Log::warning("[Sync] No data returned for $key. Response: " . $response->body());
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

                // Delete local roles not present in main (guard when empty to avoid wiping table)
                if (!empty($mainPairs)) {
                    $modelClass::whereNot(function ($q) use ($mainPairs) {
                        foreach ($mainPairs as [$uid, $rid]) {
                            $q->orWhere(function ($sub) use ($uid, $rid) {
                                $sub->where('user_id', $uid)->where('role_id', $rid);
                            });
                        }
                    })->delete();
                }

                continue; // skip generic model sync
            }

            /** Special case for staffs */
            if ($key === 'staffs') {
                foreach ($dataArray as $data) {
                    $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass));

                    $query = $modelClass::query();
                    if ($usesSoftDeletes) {
                        $query = $query->withTrashed();
                    }
                    $existing = $query->where('uuid', $data['uuid'])->first();

                    $user = isset($data['user_uuid']) ? User::where('uuid', $data['user_uuid'])->first() : null;
                    $role = isset($data['role_uuid']) ? Role::where('uuid', $data['role_uuid'])->first() : null;
                    $region = isset($data['region_uuid']) ? Region::where('uuid', $data['region_uuid'])->first() : null;
                    $province = isset($data['province_uuid']) ? Province::where('uuid', $data['province_uuid'])->first() : null;
                    $municipality = isset($data['municipality_uuid']) ? Municipality::where('uuid', $data['municipality_uuid'])->first() : null;
                    $river = isset($data['river_uuid']) ? River::where('uuid', $data['river_uuid'])->first() : null;

                    if (!$user || !$role || !$region || !$province || !$municipality || !$river) {
                        Log::warning("[Sync] Skipping staff {$data['uuid']}: missing relations");
                        continue;
                    }

                    $attributes = [
                        'user_id' => $user->id,
                        'mobile_number' => $data['mobile_number'] ?? '',
                        'role_id' => $role->id,
                        'region_id' => $region->id,
                        'province_id' => $province->id,
                        'municipality_id' => $municipality->id,
                        'river_id' => $river->id,
                        'fb_lgu' => $data['fb_lgu'] ?? '',
                    ];

                    if (!$existing) {
                        $createData = array_merge(['uuid' => $data['uuid']], $attributes, ['synced_at' => now()]);
                        $modelClass::create($createData);
                    } else {
                        $remoteUpdatedAt = isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null;
                        $needsUpdate = $remoteUpdatedAt ? $remoteUpdatedAt->gt($existing->updated_at) : false;

                        // Force update when domain data differs (main is authoritative)
                        $domainDiffers = false;
                        foreach ($attributes as $k => $v) {
                            if ($existing->{$k} != $v) { $domainDiffers = true; break; }
                        }

                        if ($needsUpdate || $domainDiffers) {
                            $existing->update(array_merge($attributes, ['synced_at' => now()]));
                        }

                        if ($usesSoftDeletes) {
                            $remoteDeletedAt = $data['deleted_at'] ?? null;
                            $remoteDeletedAt = $remoteDeletedAt ? Carbon::parse($remoteDeletedAt) : null;
                            $existingDeletedAt = $existing->deleted_at;
                            $deletedChanged = $remoteDeletedAt != $existingDeletedAt;

                            if ($deletedChanged) {
                                if (!is_null($remoteDeletedAt) && !$existing->trashed()) {
                                    $existing->delete();
                                } elseif (is_null($remoteDeletedAt) && method_exists($existing, 'restore') && $existing->trashed()) {
                                    $existing->restore();
                                }
                            }
                        }
                    }
                }

                Log::info("[Sync] Model OK: $key");
                continue;
            }

            /** Generic model sync (for everything except user_roles) */
            foreach ($dataArray as $data) {
                $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass));

                // Resolve sensorable linkage for thresholds using sensorable_uuid from main
                if ($key === 'thresholds') {
                    $sensorableUuid = $data['sensorable_uuid'] ?? null;
                    $sensorableType = $data['sensorable_type'] ?? null;
                    if ($sensorableUuid && $sensorableType) {
                        try {
                            if (class_exists($sensorableType)) {
                                $sensor = $sensorableType::where('uuid', $sensorableUuid)->first();
                                if ($sensor) {
                                    $data['sensorable_id'] = $sensor->id;
                                } else {
                                    Log::warning("[Sync] Threshold {$data['uuid']} linking skipped: missing sensor {$sensorableUuid}");
                                }
                            } else {
                                Log::warning("[Sync] Threshold {$data['uuid']} has unknown sensorable_type {$sensorableType}");
                            }
                        } catch (\Throwable $e) {
                            Log::error("[Sync] Threshold {$data['uuid']} link error: {$e->getMessage()}");
                        }
                    }
                    unset($data['sensorable_uuid']);
                }

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

                    if ($key === 'alerts') {
                        event(new AlertUpdated($newRecord));
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

                    // Force update when domain data differs (main is authoritative)
                    $ignore = ['id','synced_at','created_at','updated_at','deleted_at'];
                    $localComparable = Arr::except($existing->getAttributes(), $ignore);
                    $remoteComparable = Arr::except($data, $ignore);
                    ksort($localComparable);
                    ksort($remoteComparable);
                    $domainDiffers = $localComparable != $remoteComparable;

                    if ($needsUpdate || $deletedChanged || $domainDiffers) {
                        $updateData = array_merge($data, ['synced_at' => now()]);
                        if (!$usesSoftDeletes) {
                            unset($updateData['deleted_at']);
                        }
                        $existing->update($updateData);

                        if ($key === 'users') {
                            event(new UserCreated($existing));
                        }

                        if ($key === 'alerts') {
                            event(new AlertUpdated($existing));
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
