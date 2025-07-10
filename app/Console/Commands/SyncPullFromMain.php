<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Helpers\NetworkHelper;
use Illuminate\Support\Facades\Log;

class SyncPullFromMain extends Command
{
    protected $signature = 'sync:pull-main';
    protected $description = 'Pull updated records from main server to LGU';

    protected array $models = [
        \App\Models\User::class,
        \App\Models\Role::class,
        \App\Models\Region::class,
        \App\Models\Province::class,
        \App\Models\Municipality::class,
        \App\Models\River::class,
        \App\Models\SensorUnderAlerto::class,
        \App\Models\SensorUnderPh::class,
        \App\Models\Response::class,
        \App\Models\Threshold::class,
        \App\Models\Alert::class,
        \App\Models\UserLog::class,
        \App\Models\Staff::class,
        \App\Models\ContactMessage::class,
        \App\Models\Notification::class,
    ];

    public function handle()
    {
         if (!NetworkHelper::isOnline()){
            $this->error('No internet conenction. Cannot push updates.');
            return;
        }
        Log::info('[SyncPullFromMain] Starting fetching data from main server...');
        $mainServerUrl = config('services.main_server.url', 'https://alertofews.com/api/sync/updates');

        foreach ($this->models as $model) {
            $lastUpdated = $model::max('updated_at');

            $response = Http::post($mainServerUrl, [
                'model' => $model,
                'since' => $lastUpdated,
            ]);

            if ($response->failed()) {
                $this->error("Failed to fetch for model: $model");
                continue;
            }

            $records = $response->json('records');

            foreach ($records as $record) {
                $model::updateOrCreate(['uuid' => $record['uuid']], $record);
            }

            $this->info("Pulled " . count($records) . " records for model $model");
        }
    }
}

