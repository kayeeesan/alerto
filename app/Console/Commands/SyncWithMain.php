<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\NetworkHelper;
use App\Models\User;
use App\Models\Role;

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
            $this->info("⏫ Syncing local → main: $key");

            // Push local → main
            $toPush = $modelClass::where(function ($q) {
                $q->whereNull('synced_at')
                  ->orWhereColumn('updated_at', '>', 'synced_at');
            })->get();

            $data = $toPush->map(function ($record) use ($key) {
                if ($key === 'users') {
                    return $record->makeVisible(['password'])->toArray();
                }

                if ($key === 'user_roles') {
                    $user = $record->user;
                    $role = $record->role;

                    return [
                        'user_uuid' => $user?->uuid,
                        'role_uuid' => $role?->uuid,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at
                    ];
                }

                return $record->toArray();
            })->toArray();

            //Send to main
            if ($toPush->isNotEmpty()) {
                // $response = Http::timeout(60)->post("{$mainUrl}/{$key}", $toPush->toArray());
                $response = Http::timeout(60)->post("{$mainUrl}/{$key}", $data);

                if ($response->failed()) {
                    $this->error("❌ Failed to push data for model: $key");
                    Log::error("[Sync] Push failed for model: $key. Status: {$response->status()}, Body: {$response->body()}");
                    continue;
                }

                foreach ($toPush as $record) {
                    $record->update(['synced_at' => now()]);
                }
            }

            // Pull main → local
            $this->info("✅ Pulling from main → local: $key");

            $response = Http::timeout(60)->get("{$mainUrl}/{$key}");

            if ($response->failed()) {
                $this->error("❌ Failed to pull data for model: $key");
                Log::error("[Sync] Pull failed for model: $key. Status: {$response->status()}, Body: {$response->body()}");
                continue;
            }

            $dataArray = $response->json();

            if (!is_array($dataArray)) {
                $this->error("❌ Invalid response for model: $key — not an array.");
                Log::error("[Sync] Invalid JSON response for model: $key. Raw: " . $response->body());
                continue;
            }

            foreach ($dataArray as $data) {

                 if ($key === 'user_roles') {
                    $user = User::where('uuid', $data['user_uuid'])->first();
                    $role = Role::where('uuid', $data['role_uuid'])->first();

                    if (!$user || !$role) {
                        Log::warning("[Sync] Skipping user_roles pull. User or role not found.");
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

                $existing = $modelClass::withTrashed()->where('uuid', $data['uuid'])->first();

                if (!$existing) {
                    $modelClass::create(array_merge($data, ['synced_at' => now()]));
                } else {
                    $needsUpdate = $data['updated_at'] > $existing->updated_at;
                    $deletedChanged = $data['deleted_at'] !== $existing->deleted_at;

                    if ($needsUpdate || $deletedChanged) {
                        $existing->update(array_merge($data, ['synced_at' => now()]));

                        if (!is_null($data['deleted_at']) && !$existing->trashed()) {
                            $existing->delete();
                        }

                        if (is_null($data['deleted_at']) && method_exists($existing, 'restore') && $existing->trashed()) {
                            $existing->restore();
                        }
                    }
                }
            }

            $this->info("✅ Model synced successfully: $key");
            Log::info("[Sync] Model OK: $key");
        }

        $this->info('✅ Sync finished.');
        Log::info('[Sync] All models processed.');
    }
}
