<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AlertService;
use Illuminate\Support\Facades\Http;

use App\Http\Requests\SensorUnderPhRequest;
use App\Http\Resources\SensorUnderPh as ResourcesSensorUnderPh;
use App\Models\SensorUnderPh;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class SensorUnderPhController extends Controller
{
    protected $logService;
    protected $alertService; 

    public function __construct(UserLogService $logService, AlertService $alertService)
    {
        $this->logService = $logService;
        $this->alertService = $alertService; 
    }

    public function index(Request $request)
    {
        $query = SensorUnderPh::with(['river', 'municipality.province', 'threshold']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sensors_under_ph = $query->paginate(10);
        return ResourcesSensorUnderPh::collection($sensors_under_ph);
    }

    public function fetchDevices()
    {
        try {
            $response = Http::get('https://alertofews.com/api/api-awls/get_arg_data.php');

            if ($response->successful()) {
                $sensorData = collect($response->json()['data'] ?? [])
                    ->sortByDesc('created_at')
                    ->unique('sensor_id')
                    ->map(function ($item) {
                        return [
                            'name' => 'ESN ' . $item['sensor_id'],
                            'device_id' => $item['sensor_id'],
                            'device_rain_amount' => $item['event_acc'] ?? null,  // map like old EventAcc
                            'device_water_level' => $item['distance'] ?? null,   // use if available
                            'created_at' => $item['created_at'] ?? null,
                            'acc' => $item['acc'] ?? null,
                            'event_acc' => $item['event_acc'] ?? null,
                            'total_acc' => $item['total_acc'] ?? null,
                        ];
                    })
                    ->values();

                return response()->json($sensorData);
            }

            return response()->json(['message' => 'Failed to fetch sensor data.'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }



    public function store(SensorUnderPhRequest $request)
    {
        try {

             if (
                SensorUnderPh::where('device_id', $request->device_id)->exists() ||
                \App\Models\SensorUnderAlerto::where('device_id', $request->device_id)->exists()
            ) {
                return response()->json(['message' => 'Device ID already exists in the system.'], Response::HTTP_CONFLICT);
            }

            $sensor_under_ph = new SensorUnderPh();
            $sensor_under_ph->name = ucwords($request->name);
            $sensor_under_ph->device_id = $request->device_id;
            $sensor_under_ph->device_rain_amount = $request->device_rain_amount;
            $sensor_under_ph->device_water_level = $request->device_water_level;
            $sensor_under_ph->river_id = $request->input('river.id');
            $sensor_under_ph->municipality_id = $request->input('municipality.id');
            $sensor_under_ph->long = $request->long;
            $sensor_under_ph->lat = $request->lat;
            $sensor_under_ph->sensor_type = $request->sensor_type;
            $sensor_under_ph->save();

            $this->logService->logAction('Sensor Under Ph', $sensor_under_ph->id, 'create', $sensor_under_ph->toArray());

            return response()->json(['message' => 'Sensor has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, SensorUnderPhRequest $request)
    {
        try {
            $sensor_under_ph = SensorUnderPh::findOrFail($id);
            $oldData = $sensor_under_ph->toArray();
            $sensor_under_ph->name = ucwords($request->name);
            $sensor_under_ph->device_id = $request->device_id;
            $sensor_under_ph->device_rain_amount = $request->device_rain_amount;
            $sensor_under_ph->device_water_level = $request->device_water_level;
            $sensor_under_ph->river_id = $request->river['id'];
            $sensor_under_ph->municipality_id = $request->municipality['id'];
            $sensor_under_ph->long = $request->long;
            $sensor_under_ph->lat = $request->lat;
            $sensor_under_ph->sensor_type = $request->sensor_type;
            $sensor_under_ph->update();

            $threshold = $sensor_under_ph->threshold;
            if ($threshold) {
                $this->alertService->createAlertIfNeeded($threshold);
            }

            $this->logService->logAction('Sensor Under Ph', $sensor_under_ph->id, 'update', [
                'old' => $oldData,
                'new' => $sensor_under_ph->toArray(),
            ]);

            return response(['message' => 'Sensor has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        try {
            $sensor_under_ph = SensorUnderPh::find($id);

            if (!$sensor_under_ph) {
                return response()->json(['message' => 'Sensor not found.'], Response::HTTP_NOT_FOUND);
            }

            if ($sensor_under_ph->threshold) {
                $sensor_under_ph->threshold->delete();
            }

            $sensor_under_ph->delete(); 

            $this->logService->logAction('Sensor Under Ph', $id, 'delete');

            return response()->json(['message' => 'Sensor has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
