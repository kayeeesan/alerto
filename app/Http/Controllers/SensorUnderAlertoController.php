<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\SensorUnderAlertoRequest;
use App\Http\Resources\SensorUnderAlerto as ResourcesSensorUnderAlerto;
use App\Models\SensorUnderAlerto;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class SensorUnderAlertoController extends Controller
{

    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index()
    {
        $sensors_under_alerto = [];
        if (isset($request->search)) {
            $sensors_under_alerto = SensorUnderAlerto::where('name', 'like', '%' . $request->search . '%');
        }

        $sensors_under_alerto = isset($request->search) && $request->search ? $sensors_under_alerto->paginate(10) : SensorUnderAlerto::paginate(10);
        return ResourcesSensorUnderAlerto::collection($sensors_under_alerto);
    }

    public function store(SensorUnderAlertoRequest $request)
    {
        try {
            $sensor_under_alerto = new SensorUnderAlerto();
            $sensor_under_alerto->name = ucwords($request->name);
            $sensor_under_alerto->baseline = $request->baseline;
            $sensor_under_alerto->sixty_percent = $request->sixty_percent;
            $sensor_under_alerto->eighty_percent = $request->eighty_percent;
            $sensor_under_alerto->one_hundred_percent = $request->one_hundred_percent;
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
            $sensor_under_alerto->baseline = $request->baseline;
            $sensor_under_alerto->sixty_percent = $request->sixty_percent;
            $sensor_under_alerto->eighty_percent = $request->eighty_percent;
            $sensor_under_alerto->one_hundred_percent = $request->one_hundred_percent;
            $sensor_under_alerto->update();

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

            $sensor_under_alerto->forceDelete(); 

            $this->logService->logAction('Sensor Under Alerto', $id, 'delete');

            return response()->json(['message' => 'Sensor has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
