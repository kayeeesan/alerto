<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\NetworkHelper;

class SyncWithMain extends Command
{
    protected $signature = 'sync:main';
    protected $description = 'Synchronize local data with main server';

    public function handle()
    {
        Log::info('[Sync] Starting sync process...');

        if (!NetworkHelper::isOnline()) {
            $this->error('No internet connection. Skipping sync.');
            Log::warning('[Sync] No internet connection. Sync skipped.');
            return 0;
        }

        $models = config('sync.models');
        $mainUrl = env('MAIN_SYNC_URL', 'https://alertofews.com/api/sync');

        foreach ($models as $key => $modelClass) {
            Log::info("[Sync][$key] Starting sync...");

            // Push local → main
            $this->info("⏫ Syncing local → main: $key");
            $toPush = $modelClass::where(function ($q) {
                $q->whereNull('synced_at')
                  ->orWhereColumn('updated_at', '>', 'synced_at');
            })->get();

            if ($toPush->isNotEmpty()) {
                $response = Http::post("{$mainUrl}/{$key}", $toPush->toArray());

                if ($response->failed()) {
                    $this->error("❌ Failed to push data for model: $key");
                    Log::error("[Sync][$key] Push failed. Response: " . $response->body());
                    continue;
                }

                foreach ($toPush as $record) {
                    $record->update(['synced_at' => now()]);
                }

                Log::info("[Sync][$key] Pushed " . count($toPush) . " record(s) to main.");
            } else {
                Log::info("[Sync][$key] No new data to push.");
            }

            // Pull main → local
            $this->info("✅ Pulling from main → local: $key");
            $response = Http::get("{$mainUrl}/{$key}");

            if ($response->failed()) {
                $this->error("❌ Failed to pull data for model: $key");
                Log::error("[Sync][$key] Pull failed. Response: " . $response->body());
                continue;
            }

            if ($response->ok()) {
                $fetched = 0;
                foreach ($response->json() as $data) {
                    $existing = $modelClass::withTrashed()->where('uuid', $data['uuid'])->first();

                    if (!$existing) {
                        $modelClass::create(array_merge($data, ['synced_at' => now()]));
                        $fetched++;
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

                            $fetched++;
                        }
                    }
                }
                Log::info("[Sync][$key] Pulled and processed {$fetched} record(s).");
            } else {
                $this->error("❌ Failed to process response for model: $key");
                Log::error("[Sync][$key] Invalid response format.");
                continue;
            }
        }

        Log::info('[Sync] All models synchronized.');
        $this->info('✅ Sync finished.');
    }
}
