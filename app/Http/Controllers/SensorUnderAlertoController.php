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

            $sensor_under_alerto->forceDelete(); 

            $this->logService->logAction('Sensor Under Alerto', $id, 'delete');

            return response()->json(['message' => 'Sensor has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
