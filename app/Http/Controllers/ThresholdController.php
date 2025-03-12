<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ThresholdRequest;
use App\Http\Resources\Threshold as ResourcesThreshold;
use App\Models\Threshold;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ThresholdController extends Controller
{
    public function index(Request $request)
    {
        $thresholds = [];
        if (isset($request->search)) {
            $thresholds = Threshold::where('name', 'like', '%' . $request->search . '%');
        }

        $thresholds = isset($request->search) && $request->search ? $thresholds->paginate(10) : Threshold::paginate(10);
        return ResourcesThreshold::collection(Threshold::paginate(10));
    }

    public function store(ThresholdRequest $request)
    {
        try {
            $threshold = new Threshold();
            $threshold->river_id = $request->input('river.id');
            $threshold->sensor_id = $request->input('sensor.id');
            $threshold->municipality_id = $request->input('municipality.id');
            $threshold->xs_date = $request->xs_date;
            $threshold->save();

            return response()->json(['message' => 'Threshold has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, ThresholdRequest $request)
    {
        try {

            $threshold = Threshold::findOrFail($id);
            $threshold->river_id = $request->input('river.id');
            $threshold->sensor_id = $request->input('sensor.id');
            $threshold->municipality_id = $request->input('municipality.id');
            $threshold->xs_date = $request->xs_date;
            $threshold->update();

            return response()->json(['message' => 'Threshold has been successfully updated.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function destroy($id){
        try {
            $threshold= Threshold::findOrFail($id);
            $threshold->delete();
            return response(['message' => 'Threshold has been successfully deleted'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
     }
    
}
