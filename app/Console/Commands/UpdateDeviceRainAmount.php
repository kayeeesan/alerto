<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SensorUnderAlerto;
use App\Models\SensorUnderPh;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
            
            $response = Http::get('https://alertofews.com/api/api-awls/get_arg_data.php');

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

            foreach ($latestDeviceEvents as $deviceEvent) {
                $sensorId = $deviceEvent['sensor_id'];
                $eventTime = $deviceEvent['created_at'] ?? 'Unknown time';
                $rainAmount = $deviceEvent['event_acc'];
                
                $this->info("Processing sensor: {$sensorId} - Last update: {$eventTime}");
                Log::info("Processing sensor", [
                    'sensor_id' => $sensorId,
                    'last_update_time' => $eventTime,
                    'rain_amount' => $rainAmount
                ]);

                // Update in SensorUnderAlerto
                $alerto = SensorUnderAlerto::where('device_id', $sensorId)->first();
                if ($alerto) {
                    $alerto->device_rain_amount = $rainAmount;
                    $alerto->save();
                    $this->info("Updated SensorUnderAlerto ID {$alerto->id} with latest rain amount: {$rainAmount}mm");
                    Log::info("Updated SensorUnderAlerto", [
                        'sensor_id' => $alerto->id,
                        'rain_amount' => $rainAmount,
                        'updated_at' => now()
                    ]);
                }

                // Update in SensorUnderPh
                $ph = SensorUnderPh::where('device_id', $sensorId)->first();
                if ($ph) {
                    $ph->device_rain_amount = $rainAmount;
                    $ph->save();
                    $this->info("Updated SensorUnderPh ID {$ph->id} with latest rain amount: {$rainAmount}mm");
                    Log::info("Updated SensorUnderPh", [
                        'sensor_id' => $ph->id,
                        'rain_amount' => $rainAmount,
                        'updated_at' => now()
                    ]);
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