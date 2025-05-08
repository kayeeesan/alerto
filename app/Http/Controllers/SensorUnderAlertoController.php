<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AlertService;

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


    public function store(SensorUnderAlertoRequest $request)
    {
        try {
            $sensor_under_alerto = new SensorUnderAlerto();
            $sensor_under_alerto->name = ucwords($request->name);
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

    // public function destroy($id)
    // {
    //     try {
    //         $sensor_under_alerto = SensorUnderAlerto::findOrFail($id);
    //         $sensor_under_alerto->forceDelete();
    //         return response(['message' => 'Sensor has been successfully deleted'], Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

    
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
