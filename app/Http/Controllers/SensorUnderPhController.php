<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\SensorUnderPhRequest;
use App\Http\Resources\SensorUnderPh as ResourcesSensorUnderPh;
use App\Models\SensorUnderPh;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class SensorUnderPhController extends Controller
{
    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

    // public function index()
    // {
    //     $sensors_under_ph = [];
    //     if (isset($request->search)) {
    //         $sensors_under_ph = SensorUnderPh::where('name', 'like', '%' . $request->search . '%');
    //     }

    //     $sensors_under_ph = isset($request->search) && $request->search ? $sensors_under_ph->paginate(10) : SensorUnderPh::paginate(10);
    //     return ResourcesSensorUnderPh::collection($sensors_under_ph);
    // }

    public function index(Request $request)
    {
        $query = SensorUnderPh::with(['river', 'municipality']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sensors_under_ph = $query->paginate(10);
        return ResourcesSensorUnderPh::collection($sensors_under_ph);
    }

    public function store(SensorUnderPhRequest $request)
    {
        try {
            $sensor_under_ph = new SensorUnderPh();
            $sensor_under_ph->name = ucwords($request->name);
            $sensor_under_ph->river_id = $request->input('river.id');
            $sensor_under_ph->municipality_id = $request->input('municipality.id');
            $sensor_under_ph->long = $request->long;
            $sensor_under_ph->lat = $request->lat;
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
            $sensor_under_ph->river_id = $request->river['id'];
            $sensor_under_ph->municipality_id = $request->municipality['id'];
            $sensor_under_ph->long = $request->long;
            $sensor_under_ph->lat = $request->lat;
            $sensor_under_ph->update();

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

            $sensor_under_ph->forceDelete(); 

            $this->logService->logAction('Sensor Under Ph', $id, 'delete');

            return response()->json(['message' => 'Sensor has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
