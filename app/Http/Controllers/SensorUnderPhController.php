<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\SensorUnderPhRequest;
use App\Http\Resources\SensorUnderPh as ResourcesSensorUnderPh;
use App\Models\SensorUnderPh;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class SensorUnderPhController extends Controller
{
    public function index()
    {
        $sensors_under_ph = [];
        if (isset($request->search)) {
            $sensors_under_ph = SensorUnderPh::where('name', 'like', '%' . $request->search . '%');
        }

        $sensors_under_ph = isset($request->search) && $request->search ? $sensors_under_ph->paginate(10) : SensorUnderPh::paginate(10);
        return ResourcesSensorUnderPh::collection($sensors_under_ph);
    }

    public function store(SensorUnderPhRequest $request)
    {
        try {
            $sensor_under_ph = new SensorUnderPh();
            $sensor_under_ph->name = ucwords($request->name);
            $sensor_under_ph->baseline = $request->baseline;
            $sensor_under_ph->sixty_percent = $request->sixty_percent;
            $sensor_under_ph->eighty_percent = $request->eighty_percent;
            $sensor_under_ph->one_hundred_percent = $request->one_hundred_percent;
            $sensor_under_ph->save();

            return response()->json(['message' => 'Sensor has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, SensorUnderPhRequest $request)
    {
        try {
            $sensor_under_ph = SensorUnderPh::findOrFail($id);
            $sensor_under_ph->name = ucwords($request->name);
            $sensor_under_ph->baseline = $request->baseline;
            $sensor_under_ph->sixty_percent = $request->sixty_percent;
            $sensor_under_ph->eighty_percent = $request->eighty_percent;
            $sensor_under_ph->one_hundred_percent = $request->one_hundred_percent;
            $sensor_under_ph->update();
            return response(['message' => 'Sensor has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        try {
            $sensor_under_ph = SensorUnderPh::findOrFail($id);
            $sensor_under_ph->forceDelete();
            return response(['message' => 'Sensor has been successfully deleted'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
