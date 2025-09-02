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

class UpdateDeviceRainAmount extends Command
{
    protected $signature = 'devices:update-rain';
    protected $description = 'Fetch device data from API and update rain amount for sensors with latest values';

    public function handle()
    {
        $this->info('Starting device data update process...');
        Log::info('Starting device data update process');

        try {
            $this->info('Fetching latest device data from API...');
            Log::info('Making API request to fetch device data');
            
            $response = Http::retry(3, 500)->timeout(10)->get('https://alertofews.com/api/api-awls/get_arg_data.php');

            if (!$response->successful()) {
                $errorMessage = 'API request failed with status: ' . $response->status();
                $this->error($errorMessage);
                Log::error($errorMessage);
                return 1;
            }

            $responseData = $response->json();
            
            if (!isset($responseData['data'])) {
                $errorMessage = 'Invalid API response format - missing data key';
                $this->error($errorMessage);
                Log::error($errorMessage);
                return 1;
            }

            $this->info('Processing API response...');
            Log::info('Processing API response', [
                'device_count' => count($responseData['data']),
                'response_status' => $response->status()
            ]);

            // Group devices by sensor_id and keep only the latest event for each
            $latestDeviceEvents = collect($responseData['data'])
                ->filter(function ($data) {
                    return isset($data['sensor_id']) && isset($data['event_acc']);
                })
                ->groupBy('sensor_id')
                ->map(function ($deviceEvents) {
                    return $deviceEvents->sortByDesc(function ($event) {
                        return strtotime($event['created_at']);
                    })->first();
                });

            $this->info('Found ' . $latestDeviceEvents->count() . ' unique devices with recent data');
            Log::info('Device processing stats', [
                'unique_devices_found' => $latestDeviceEvents->count()
            ]);

            $alertService = app(AlertService::class);

            $alertoSensors = SensorUnderAlerto::whereIn('device_id', $latestDeviceEvents->keys())
                ->get()
                ->keyBy('device_id');

            $phSensors = SensorUnderPh::whereIn('device_id', $latestDeviceEvents->keys())
                ->get()
                ->keyBy('device_id');

            foreach ($latestDeviceEvents as $deviceEvent) {
                try {
                    $sensorId = $deviceEvent['sensor_id'];
                    $eventTime = $deviceEvent['created_at'] ?? 'Unknown time';
                    $rainAmount = $deviceEvent['event_acc'];
                    
                    $this->info("Processing sensor: {$sensorId} - Last update: {$eventTime}");
                    Log::info("Processing sensor", [
                        'sensor_id' => $sensorId,
                        'last_update_time' => $eventTime,
                        'rain_amount' => $rainAmount
                    ]);

                    $recordedAt = !empty($deviceEvent['created_at'])
                        ? Carbon::parse($deviceEvent['created_at'])
                        : now();

                    $alerto = $alertoSensors->get($sensorId);
                    $ph = $phSensors->get($sensorId);
                    // Update in SensorUnderAlerto
                    // $alerto = SensorUnderAlerto::where('device_id', $sensorId)->first();
                    $alerto = $alertoSensors->get($sensorId);
                    if ($alerto) {
                        $alerto->device_rain_amount = $rainAmount;
                        $alerto->save();

                        SensorsHistory::create([
                            'uuid' => \Illuminate\Support\Str::uuid(),
                            'sensor_uuid' => $alerto->uuid,
                            'device_rain_amount' => $rainAmount,
                            'device_water_level' => $alerto->device_water_level,
                            'recorded_at' => $recordedAt,
                        ]);

                        event(new SensorUpdated($alerto, $ph ?? new SensorUnderPh()));

                        Log::info("Calling AlertService for SensorUnderAlerto", [
                            'sensor_id' => $alerto->id,
                            'threshold' => $alerto->threshold,
                            'rain_amount' => $rainAmount
                        ]);

                        if ($alerto && $alerto->threshold) {
                            $alertService->createAlertIfNeeded($alerto->threshold);
                        }
                        
                        Log::info("Finished calling AlertService for SensorUnderAlerto", [
                            'sensor_id' => $alerto->id
                        ]);

                        $this->info("Updated SensorUnderAlerto ID {$alerto->id} with latest rain amount: {$rainAmount}mm");
                        Log::info("Updated SensorUnderAlerto", [
                            'sensor_id' => $alerto->id,
                            'rain_amount' => $rainAmount,
                            'updated_at' => now()
                        ]);
                    }

                    // Update in SensorUnderPh
                    // $ph = SensorUnderPh::where('device_id', $sensorId)->first();
                    $ph = $phSensors->get($sensorId);
                    if ($ph) {
                        $ph->device_rain_amount = $rainAmount;
                        $ph->save();

                        SensorsHistory::create([
                            'uuid' => Str::uuid(),
                            'sensor_uuid' => $ph->uuid,
                            'device_rain_amount' => $rainAmount,
                            'device_water_level' => $ph->device_water_level,
                            'recorded_at' => $recordedAt,
                        ]);

                        event(new SensorUpdated($alerto ?? new SensorUnderAlerto(), $ph));

                        Log::info("Calling AlertService for SensorUnderPh", [
                            'sensor_id' => $ph->id,
                            'threshold' => $ph->threshold,
                            'rain_amount' => $rainAmount
                        ]);
                        if ($ph && $ph->threshold) {
                            $alertService->createAlertIfNeeded($ph->threshold);
                        }
                        Log::info("Finished calling AlertService for SensorUnderPh", [
                            'sensor_id' => $ph->id
                        ]);

                        $this->info("Updated SensorUnderPh ID {$ph->id} with latest rain amount: {$rainAmount}mm");
                        Log::info("Updated SensorUnderPh", [
                            'sensor_id' => $ph->id,
                            'rain_amount' => $rainAmount,
                            'updated_at' => now()
                        ]);
                    }

                    } catch (\Exception $e) {
                        Log::error("Error processing sensor {$deviceEvent['sensor_id']}", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);

                        $this->error("Failed to process sensor {$deviceEvent['sensor_id']}. Skipping...");
                        continue; // move to the next sensor
                    }
                }

            $successMessage = 'Device data update completed successfully. Processed ' . $latestDeviceEvents->count() . ' devices.';
            $this->info($successMessage);
            Log::info($successMessage, [
                'processed_devices' => $latestDeviceEvents->count(),
                'completed_at' => now()
            ]);

            return 0;

        } catch (\Exception $e) {
            $errorMessage = 'Error during device data update: ' . $e->getMessage();
            $this->error($errorMessage);
            Log::error($errorMessage, [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'occurred_at' => now()
            ]);
            return 1;
        }
    }
}