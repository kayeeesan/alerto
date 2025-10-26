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

class UpdateWeatherLink extends Command
{
    protected $signature = 'devices:update-weatherlink';
    protected $description = 'Fetch WeatherLink data from API and update sensors with latest values';

    public function handle()
    {
        $this->info('Starting WeatherLink data update process...');

        try {
            $this->info('Fetching latest WeatherLink data from API...');
            Log::info('Making API request to fetch WeatherLink data');

            $response = Http::retry(3, 500)->timeout(10)->get(
                'https://api.weatherlink.com/v1/NoaaExt.json?user=001D0A002457&pass=t@b1ngd@gat&apiToken=3BFA895305E34127A232DF10508F51E6'
            );

            if (!$response->successful()) {
                $errorMessage = 'API WeatherLink request failed with status: ' . $response->status();
                $this->error($errorMessage);
                Log::error($errorMessage);
                return 1;
            }

            $data = $response->json();
            $station = $data['davis_current_observation'] ?? null;

            if (!$station) {
                $this->error('No station data found in WeatherLink response.');
                return 1;
            }

            $deviceId   = $station['DID'] ?? 'weatherlink_001';
            $stationName = $station['station_name'] ?? 'WeatherLink Station';

            // Rainfall values (in inches, converting to mm if needed)
            $rainDay    = isset($station['rain_day_in']) ? floatval($station['rain_day_in']) : null;
            $rainStorm  = isset($station['rain_storm_in']) ? floatval($station['rain_storm_in']) : null;
            $rainYear   = isset($station['rain_year_in']) ? floatval($station['rain_year_in']) : null;

            $recordedAt = isset($data['observation_time_rfc822'])
                ? Carbon::parse($data['observation_time_rfc822'])
                : now();

            // Lookup in both tables
            $alerto = SensorUnderAlerto::where('device_id', $deviceId)->first();
            $ph     = SensorUnderPh::where('device_id', $deviceId)->first();

            $alertService = app(AlertService::class);

            foreach ([$alerto, $ph] as $sensor) {
                if ($sensor) {
                    $sensor->device_rain_amount = $rainDay;
                    $sensor->api_last_updated_at = $recordedAt;
                    $sensor->save();

                    SensorsHistory::create([
                        'uuid' => Str::uuid(),
                        'sensor_uuid' => $sensor->uuid,
                        'device_rain_amount' => $rainDay,
                        'device_water_level' => $sensor->device_water_level,
                        'recorded_at' => $recordedAt,
                    ]);

                    event(new SensorUpdated($alerto ?? new SensorUnderAlerto(), $ph ?? new SensorUnderPh()));

                    if ($sensor->threshold) {
                        $alertService->createAlertIfNeeded($sensor->threshold);
                    }

                    $this->info("Updated {$sensor->name} (ID {$sensor->id}) with latest rain: {$rainDay} in");
                    Log::info("Updated WeatherLink sensor", [
                        'sensor_id' => $sensor->id,
                        'rain_day_in' => $rainDay,
                        'rain_storm_in' => $rainStorm,
                        'rain_year_in' => $rainYear,
                        'api_last_updated_at' => $recordedAt,
                    ]);
                }
            }

            $this->info('WeatherLink update complete.');
            return 0;

        } catch (\Exception $e) {
            $errorMessage = 'Error during WeatherLink data update: ' . $e->getMessage();
            $this->error($errorMessage);
            Log::error($errorMessage, [
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }
}
