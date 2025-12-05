<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AlertService;
use Illuminate\Support\Facades\Http;

use App\Http\Requests\SensorUnderAlertoRequest;
use App\Http\Resources\SensorUnderAlerto as ResourcesSensorUnderAlerto;
use App\Models\SensorUnderAlerto;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class SensorUnderAlertoController extends Controller
{

    protected $logService;
    protected $alertService; 

    public function __construct(UserLogService $logService,  AlertService $alertService)
    {
        $this->logService = $logService;
        $this->alertService = $alertService; 
    }

  


    public function index(Request $request)
        {
        $query = SensorUnderAlerto::with(['river', 'municipality.province', 'threshold']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sensors_under_alerto = $query->paginate(10);

        return ResourcesSensorUnderAlerto::collection($sensors_under_alerto);
    }

    public function fetchDevices()
    {
        try {
            $sensorData = collect();

            // First API - alertofews.com (sequential)
            try {
                $response1 = Http::get('https://alertofews.com/api/api-awls/get_arg_data.php');
                
                if ($response1->successful()) {
                    $alertofewsData = collect($response1->json()['data'] ?? [])
                        ->sortByDesc('created_at')
                        ->unique('sensor_id')
                        ->map(function ($item) {
                            return [
                                'name' => 'ESN ' . $item['sensor_id'],
                                'device_id' => $item['sensor_id'],
                                'device_rain_amount' => $item['rain_int'] ?? null,
                                'device_water_level' => $item['distance'] ?? null,
                                'created_at' => $item['created_at'] ?? null,
                                'acc' => $item['acc'] ?? null,
                                'event_acc' => $item['event_acc'] ?? null,
                                'recent_acc' => $item['event_acc'] ?? null,
                                'total_acc' => $item['total_acc'] ?? null,
                                'source' => 'alertofews',
                            ];
                        });

                    $sensorData = $sensorData->merge($alertofewsData);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch from alertofews.com: ' . $e->getMessage());
            }

            // Second API - weatherlink.com (sequential)
            try {
                $response2 = Http::get('https://api.weatherlink.com/v1/NoaaExt.json?user=001D0A002457&pass=t@b1ngd@gat&apiToken=3BFA895305E34127A232DF10508F51E6');
                
                if ($response2->successful()) {
                    $weatherlinkData = $response2->json();
                    $davisData = $weatherlinkData['davis_current_observation'] ?? [];
                    
                    if (!empty($davisData)) {
                        $weatherlinkDevice = [
                            'name' => $davisData['station_name'] ?? 'WeatherLink Station',
                            'device_id' => $davisData['DID'] ?? 'weatherlink_001',
                            'device_rain_amount' => isset($davisData['rain_day_in']) ? floatval($davisData['rain_day_in']) : null,
                            'device_water_level' => null,
                            'created_at' => $weatherlinkData['observation_time_rfc822'] ?? now()->toISOString(),
                            'acc' => null,
                            'event_acc' => isset($davisData['rain_storm_in']) ? floatval($davisData['rain_storm_in']) : null,
                            'total_acc' => isset($davisData['rain_year_in']) ? floatval($davisData['rain_year_in']) : null,
                            'source' => 'weatherlink',
                            'lat' => $weatherlinkData['latitude'] ?? null,
                            'long' => $weatherlinkData['longitude'] ?? null,
                            'additional_data' => [
                                'pressure' => $weatherlinkData['pressure_in'] ?? null,
                                'temperature' => $davisData['temp_in_f'] ?? null,
                                'humidity' => $davisData['relative_humidity_in'] ?? null,
                                'location' => $weatherlinkData['location'] ?? null,
                                'latitude' => $weatherlinkData['latitude'] ?? null,
                                'longitude' => $weatherlinkData['longitude'] ?? null,
                            ]
                        ];

                        $sensorData->push($weatherlinkDevice);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch from weatherlink.com: ' . $e->getMessage());
            }

            // Final processing
            $sensorData = $sensorData->sortByDesc('created_at')
                ->unique('device_id')
                ->values();

            return response()->json($sensorData);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(SensorUnderAlertoRequest $request)
    {
        try {
           if (
                SensorUnderAlerto::where('device_id', $request->device_id)->exists() ||
                \App\Models\SensorUnderPh::where('device_id', $request->device_id)->exists()
            ) {
                return response()->json(['message' => 'Device ID already exists in the system.'], Response::HTTP_CONFLICT);
            }

            $sensor_under_alerto = new SensorUnderAlerto();
            $sensor_under_alerto->name = ucwords($request->name);
            $sensor_under_alerto->device_id = $request->device_id;
            $sensor_under_alerto->device_rain_amount = $request->device_rain_amount;
            $sensor_under_alerto->device_water_level = $request->device_water_level;
            $sensor_under_alerto->river_id = $request->input('river.id');
            $sensor_under_alerto->municipality_id = $request->input('municipality.id');
            $sensor_under_alerto->long = $request->long;
            $sensor_under_alerto->lat = $request->lat;
            $sensor_under_alerto->sensor_type = $request->sensor_type;
            $sensor_under_alerto->save();

            $this->logService->logAction('Sensor Under Alerto', $sensor_under_alerto->id, 'create', $sensor_under_alerto->toArray());


            return response()->json(['message' => 'Sensor has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, SensorUnderAlertoRequest $request)
    {
        try {
            $sensor_under_alerto = SensorUnderAlerto::findOrFail($id);
            $oldData = $sensor_under_alerto->toArray();
            $sensor_under_alerto->name = ucwords($request->name);
            $sensor_under_alerto->device_id = $request->device_id;
            $sensor_under_alerto->device_rain_amount = $request->device_rain_amount;
            $sensor_under_alerto->device_water_level = $request->device_water_level;
            $sensor_under_alerto->river_id = $request->river['id'];
            $sensor_under_alerto->municipality_id = $request->municipality['id'];
            $sensor_under_alerto->long = $request->long;
            $sensor_under_alerto->lat = $request->lat;
            $sensor_under_alerto->sensor_type = $request->sensor_type;
            $sensor_under_alerto->update();

            $threshold = $sensor_under_alerto->threshold;
                if ($threshold) {
                    $this->alertService->createAlertIfNeeded($threshold);
                }

            $this->logService->logAction('Sensor Under Alerto', $sensor_under_alerto->id, 'update', [
                'old' => $oldData,
                'new' => $sensor_under_alerto->toArray(),
            ]);

            return response(['message' => 'Sensor has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    
    public function destroy($id)
    {
        try {
            $sensor_under_alerto = SensorUnderAlerto::find($id);

            if (!$sensor_under_alerto) {
                return response()->json(['message' => 'Sensor not found.'], Response::HTTP_NOT_FOUND);
            }

            if ($sensor_under_alerto->threshold) {
                $sensor_under_alerto->threshold->delete();
            }

            $sensor_under_alerto->delete(); 

            $this->logService->logAction('Sensor Under Alerto', $id, 'delete');

            return response()->json(['message' => 'Sensor has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
