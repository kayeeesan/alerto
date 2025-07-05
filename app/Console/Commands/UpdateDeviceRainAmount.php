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
    protected $description = 'Fetch device data from API and update rain amount and water level for sensors with latest values';

    public function handle()
    {
        $this->info('Starting device data update process...');
        Log::info('Starting device data update process');

        try {
            $this->info('Fetching latest device data from API...');
            Log::info('Making API request to fetch device data');

            $response = Http::get('https://alertofews.com/api/index.php?ep=saka');

            if (!$response->successful()) {
                $errorMessage = 'API request failed with status: ' . $response->status();
                $this->error($errorMessage);
                Log::error($errorMessage);
                return 1;
            }

            $responseData = $response->json();

            $this->info('Processing API response...');
            Log::info('Processing API response', [
                'device_count' => count($responseData),
                'response_status' => $response->status()
            ]);

            // Group by unique DevEUI and take latest per device
            $latestDeviceEvents = collect($responseData)
                ->filter(function ($data) {
                    return isset($data['msg']['DevEUI']);
                })
                ->groupBy(function ($item) {
                    return (string) $item['msg']['DevEUI']; // normalize DevEUI to string
                })
                ->map(function ($deviceEvents) {
                    return collect($deviceEvents)->sortByDesc(function ($event) {
                        return $event['metadata']['ts'] ?? 0;
                    })->first();
                });

            $this->info('Found ' . $latestDeviceEvents->count() . ' unique devices with recent data');
            Log::info('Device processing stats', [
                'unique_devices_found' => $latestDeviceEvents->count()
            ]);

            foreach ($latestDeviceEvents as $deviceEvent) {
                $deviceId = (string) $deviceEvent['msg']['DevEUI'];
                $deviceName = $deviceEvent['metadata']['deviceName'] ?? 'Unknown';

                $timestamp = $deviceEvent['msg']['Time']
                    ?? (isset($deviceEvent['metadata']['ts']) ? Carbon::createFromTimestampMs($deviceEvent['metadata']['ts'])->toDateTimeString() : null);

                $eventTime = $timestamp ? Carbon::parse($timestamp)->format('Y-m-d H:i:s') : 'Unknown time';

                $this->info("Processing device: {$deviceName} (ID: {$deviceId}) - Last update: {$eventTime}");
                Log::info("Processing device", [
                    'device_id' => $deviceId,
                    'device_name' => $deviceName,
                    'last_update_time' => $eventTime
                ]);

                // Process rainfall
                if (isset($deviceEvent['msg']['EventAcc'])) {
                    $rainAmount = $deviceEvent['msg']['EventAcc'];

                    $this->info("Device {$deviceName} recorded rainfall: {$rainAmount}mm at {$eventTime}");
                    Log::info("Rainfall data", [
                        'device_id' => $deviceId,
                        'rain_amount' => $rainAmount,
                        'recorded_at' => $eventTime
                    ]);

                    if ($alerto = SensorUnderAlerto::where('device_id', $deviceId)->first()) {
                        $alerto->device_rain_amount = $rainAmount;
                        $alerto->save();
                        $this->info("Updated SensorUnderAlerto ID {$alerto->id} with rain amount: {$rainAmount}mm");
                        Log::info("Updated SensorUnderAlerto", [
                            'sensor_id' => $alerto->id,
                            'rain_amount' => $rainAmount,
                            'updated_at' => now()
                        ]);
                    }

                    if ($ph = SensorUnderPh::where('device_id', $deviceId)->first()) {
                        $ph->device_rain_amount = $rainAmount;
                        $ph->save();
                        $this->info("Updated SensorUnderPh ID {$ph->id} with rain amount: {$rainAmount}mm");
                        Log::info("Updated SensorUnderPh", [
                            'sensor_id' => $ph->id,
                            'rain_amount' => $rainAmount,
                            'updated_at' => now()
                        ]);
                    }
                }

                // Process water level
                if (isset($deviceEvent['msg']['decoded_payload']['distance'])) {
                    $waterLevel = $deviceEvent['msg']['decoded_payload']['distance'];

                    $this->info("Device {$deviceName} recorded water level: {$waterLevel}mm at {$eventTime}");
                    Log::info("Water level data", [
                        'device_id' => $deviceId,
                        'water_level' => $waterLevel,
                        'recorded_at' => $eventTime
                    ]);

                    if ($alerto = SensorUnderAlerto::where('device_id', $deviceId)->first()) { //2025-05-31T03:36:52.914+08:00
                        $alerto->device_water_level = $waterLevel;
                        $alerto->save();
                        $this->info("Updated SensorUnderAlerto ID {$alerto->id} with water level: {$waterLevel}mm");
                        Log::info("Updated SensorUnderAlerto", [
                            'sensor_id' => $alerto->id,
                            'water_level' => $waterLevel,
                            'updated_at' => now()
                        ]);
                    }

                    if ($ph = SensorUnderPh::where('device_id', $deviceId)->first()) {
                        $ph->device_water_level = $waterLevel;
                        $ph->save();
                        $this->info("Updated SensorUnderPh ID {$ph->id} with water level: {$waterLevel}mm");
                        Log::info("Updated SensorUnderPh", [
                            'sensor_id' => $ph->id,
                            'water_level' => $waterLevel,
                            'updated_at' => now()
                        ]);
                    }
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
 