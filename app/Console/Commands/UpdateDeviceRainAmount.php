<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SensorUnderAlerto;
use App\Models\SensorUnderPh;

class UpdateDeviceRainAmount extends Command
{
    protected $signature = 'devices:update-rain';
    protected $description = 'Fetch device data from API and update rain amount for sensors';

    public function handle()
    {
        $this->info('Fetching device data from API...');

        try {
            $response = Http::get('https://alertofews.com/api/index.php?ep=saka');

            if (!$response->successful()) {
                $this->error('API request failed.');
                return 1;
            }

            $devices = collect($response->json())->filter(function ($data) {
                return isset($data['msg']['DevEUI']) && isset($data['msg']['EventAcc']);
            });

            foreach ($devices as $device) {
                $deviceId = $device['msg']['DevEUI'];
                $rainAmount = $device['msg']['EventAcc'];

                // Update in SensorUnderAlerto
                $alerto = SensorUnderAlerto::where('device_id', $deviceId)->first();
                if ($alerto) {
                    $alerto->device_rain_amount = $rainAmount;
                    $alerto->save();
                    $this->info("Updated SensorUnderAlerto ID {$alerto->id} with rain amount: $rainAmount");
                }

                // Update in SensorUnderPh
                $ph = SensorUnderPh::where('device_id', $deviceId)->first();
                if ($ph) {
                    $ph->device_rain_amount = $rainAmount;
                    $ph->save();
                    $this->info("Updated SensorUnderPh ID {$ph->id} with rain amount: $rainAmount");
                }
            }

            $this->info('Device rain amounts updated successfully.');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
