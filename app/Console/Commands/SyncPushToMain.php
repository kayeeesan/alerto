<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Helpers\NetworkHelper;
use Illuminate\Support\Facades\Log;

class SyncPushToMain extends Command
{
    protected $signature = 'sync:push-main';
    protected $description = 'Push updated local LGU records to main server';

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

        Log::info('[SyncPushToMain] Starting sync to main server...');
        $mainServerUrl = config('services.main_server.url', 'https://alertofews.com/api/sync/push');

        foreach ($this->models as $model) {
            $records = $model::where('updated_at', '>=', now()->subMinutes(5))->get()->toArray();

            if (empty($records)) {
                $this->info("No recent records to push for model {$model}");
                continue;
            }

            $response = Http::post($mainServerUrl, [
                'model' => $model,
                'data' => $records,
            ]);

            $this->info("Pushed " . count($records) . " records of {$model}: " . $response->body());
        }
    }
}

