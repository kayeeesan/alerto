<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Helpers\NetworkHelper;

class SyncWithMain extends Command
{
    protected $signature = 'sync:main';
    protected $description = 'Synchronize local data with main server';

    public function handle()
    {
        if (!NetworkHelper::isOnline()) {
            $this->error('No internet connection. Skipping sync.');
            return 0;
        }

        $models = config('sync.models');
        $mainUrl = env('MAIN_SYNC_URL'); 

        foreach ($models as $key => $modelClass) {
            $this->info("⏫ Syncing local → main: $key");

            $toPush = $modelClass::where(function ($q) {
                $q->whereNull('synced_at')
                  ->orWhereColumn('updated_at', '>', 'synced_at');
            })->get();

            if ($toPush->isNotEmpty()) {
                $response = Http::post("{$mainUrl}/{$key}", $toPush->toArray());

                if ($response->failed()) {
                    $this->error("❌ Failed to push data for model: $key");
                    continue;
                }

                foreach ($toPush as $record) {
                    $record->update(['synced_at' => now()]);
                }
            }


            $this->info("✅ Pulling from main → local: $key");

            $response = Http::get("{$mainUrl}/{$key}");
            if ($response->failed()) {
                $this->error("❌ Failed to pull data for model: $key");
                continue;
            }
            if ($response->ok()) {
                foreach ($response->json() as $data) {
                    $existing = $modelClass::where('uuid', $data['uuid'])->first();

                    if (!$existing) {
                        $modelClass::create(array_merge($data, ['synced_at' => now()]));
                    } elseif ($data['updated_at'] > $existing->updated_at) {
                        $existing->update(array_merge($data, ['synced_at' => now()]));
                    }
                }
            }
        }

        $this->info('✅ Sync finished.');
    }
}

