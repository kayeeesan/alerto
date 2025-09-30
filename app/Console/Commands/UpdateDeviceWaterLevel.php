<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SensorUnderAlerto;
use App\Models\SensorUnderPh;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\SensorsHistory;
use App\Services\AlertService;
use App\Events\SensorUpdated;
use Illuminate\Support\Str;

class UpdateDeviceWaterLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:update-water';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch device data from API and update water amount for sensors with latest values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting water device data update process...');

        try {

        } catch (\Exception $e) {
            $errorMessage = 'Error during water device data update: ' . $e->getMessage();
            $this->error($errorMessage);
            return 1;
        }
    }
}
