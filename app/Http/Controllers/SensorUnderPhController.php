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
            $response = Http::get('https://alertofews.com/api/index.php?ep=saka');


            if ($response->successful()) {
                $devices = collect($response->json())
                    ->filter(function ($data) {
                        return isset($data['msg']['LrrLON']) &&
                            isset($data['msg']['LrrLAT']) &&
                            isset($data['metadata']['deviceName']) &&
                            isset($data['msg']['DevEUI']);
                    })
                    ->map(function ($data) {
                        $deviceType = $data['metadata']['deviceType'] ?? '';
                        $deviceName = $data['metadata']['deviceName'] ?? '';
                        $DevEUI = $data['msg']['DevEUI'];


                        return [
                            'name' => $deviceName,
                            'device_id' => $DevEUI,
                            'device_type' => $deviceType,
                            'device_rain_amount' => $data['msg']['EventAcc'] ?? null,
                            'device_water_level' => $data['decoded_payload']['distance'] ?? null,
                            'long' => $data['msg']['LrrLON'],
                            'lat' => $data['msg']['LrrLAT'],
                        ];
                    })
                    ->unique('device_id') // still keeps only one per DevEUI
                    ->values();


                return response()->json($devices);
            }


            return response()->json(['message' => 'Failed to fetch devices.'], 500);


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

            $sensor_under_ph->forceDelete(); 

            $this->logService->logAction('Sensor Under Ph', $id, 'delete');

            return response()->json(['message' => 'Sensor has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
