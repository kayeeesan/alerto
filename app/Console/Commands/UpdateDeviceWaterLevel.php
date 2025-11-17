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
    protected $signature = 'devices:update-water';
    protected $description = 'Fetch device data from API and update water level for sensors with latest values';

    public function handle()
    {
        $this->info('Starting water device data update process...');

        try {
            $this->info('Fetching latest water level data from API...');
            Log::info('Fetching water level device data');

            $response = Http::retry(3, 500)->timeout(10)->get('https://alertofews.com/api/api-awls/get_awls_data.php');

            if (!$response->successful()) {
                $this->error('API request failed: '.$response->status());
                Log::error('API request failed', ['status' => $response->status()]);
                return 1;
            }

            $responseData = $response->json();

            if (!isset($responseData['data'])) {
                $this->error('Invalid API response format: missing data key');
                Log::error('API response missing data key');
                return 1;
            }

            $this->info("Processing API response...");
            Log::info('API water data count', [
                'count' => count($responseData['data'])
            ]);

            // ---------------------------------------------------
            // GROUP BY SENSOR & GET LATEST READING PER SENSOR
            // ---------------------------------------------------
            $latestWaterEvents = collect($responseData['data'])
                ->filter(fn($e) => isset($e['sensor_id']) && isset($e['distance_cm']))
                ->groupBy('sensor_id')
                ->map(fn($events) => $events->sortByDesc(fn($e) => strtotime($e['created_at']))->first());

            $this->info("Found {$latestWaterEvents->count()} unique sensors");
            Log::info('Unique water sensors found', [
                'unique_count' => $latestWaterEvents->count()
            ]);

            $alertService = app(AlertService::class);

            // preload sensors
            $alertoSensors = SensorUnderAlerto::whereIn('device_id', $latestWaterEvents->keys())
                ->get()
                ->keyBy('device_id');

            $phSensors = SensorUnderPh::whereIn('device_id', $latestWaterEvents->keys())
                ->get()
                ->keyBy('device_id');

            foreach ($latestWaterEvents as $event) {
                try {
                    $sensorId = $event['sensor_id'];
                    $waterLevel = $event['distance_cm']; // THIS IS THE WATER LEVEL
                    $recordedAt = Carbon::parse($event['created_at']);

                    Log::info("Processing water level sensor", [
                        'sensor_id' => $sensorId,
                        'water_level' => $waterLevel,
                        'recorded_at' => $recordedAt
                    ]);

                    $alerto = $alertoSensors->get($sensorId);
                    $ph = $phSensors->get($sensorId);

                    // ----------------------------------------
                    // UPDATE ALERTO SENSOR
                    // ----------------------------------------
                    if ($alerto) {
                        $alerto->device_water_level = $waterLevel;
                        $alerto->api_last_updated_at = $recordedAt;
                        $alerto->save();

                        SensorsHistory::create([
                            'uuid' => Str::uuid(),
                            'sensor_uuid' => $alerto->uuid,
                            'device_water_level' => $waterLevel,
                            'device_rain_amount' => $alerto->device_rain_amount,
                            'recorded_at' => $recordedAt,
                        ]);

                        event(new SensorUpdated($alerto, $ph ?? new SensorUnderPh()));

                        if ($alerto->threshold) {
                            $alertService->createAlertIfNeeded($alerto->threshold);
                        }

                        $this->info("Updated SensorUnderAlerto ID {$alerto->id} with water level: {$waterLevel} cm");
                    }

                    // ----------------------------------------
                    // UPDATE PH SENSOR
                    // ----------------------------------------
                    if ($ph) {
                        $ph->device_water_level = $waterLevel;
                        $ph->api_last_updated_at = $recordedAt;
                        $ph->save();

                        SensorsHistory::create([
                            'uuid' => Str::uuid(),
                            'sensor_uuid' => $ph->uuid,
                            'device_water_level' => $waterLevel,
                            'device_rain_amount' => $ph->device_rain_amount,
                            'recorded_at' => $recordedAt,
                        ]);

                        event(new SensorUpdated($alerto ?? new SensorUnderAlerto(), $ph));

                        if ($ph->threshold) {
                            $alertService->createAlertIfNeeded($ph->threshold);
                        }

                        $this->info("Updated SensorUnderPh ID {$ph->id} with water level: {$waterLevel} cm");
                    }

                } catch (\Exception $e) {
                    Log::error("Error processing water sensor {$event['sensor_id']}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    $this->error("Failed to process sensor {$event['sensor_id']}... Skipping.");
                    continue;
                }
            }

            Log::info('Water level update completed', [
                'processed' => $latestWaterEvents->count(),
                'time' => now(),
            ]);

            $this->info("Water device update completed. Total processed: {$latestWaterEvents->count()}");

            return 0;

        } catch (\Exception $e) {
            $error = "Error during water update: " . $e->getMessage();
            $this->error($error);
            Log::error($error, [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
