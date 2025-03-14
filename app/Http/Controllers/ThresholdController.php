<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ThresholdRequest;
use App\Http\Resources\Threshold as ResourcesThreshold;
use App\Models\Threshold;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ThresholdController extends Controller
{

    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

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

            $this->logService->logAction('Threshold', $threshold->id, 'create', $threshold->toArray());

            return response()->json(['message' => 'Threshold has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, ThresholdRequest $request)
    {
        try {

            $threshold = Threshold::findOrFail($id);
            $oldData = $threshold->toArray();
            $threshold->river_id = $request->input('river.id');
            $threshold->sensor_id = $request->input('sensor.id');
            $threshold->municipality_id = $request->input('municipality.id');
            $threshold->xs_date = $request->xs_date;
            $threshold->update();

            $this->logService->logAction('Threshold', $threshold->id, 'update', [
                'old' => $oldData,
                'new' => $threshold->toArray(),
            ]);

            return response()->json(['message' => 'Threshold has been successfully updated.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $threshold = Threshold::find($id);

            if (!$threshold) {
                return response()->json(['message' => 'Threshold not found.'], Response::HTTP_NOT_FOUND);
            }

            $threshold->forceDelete(); 

            $this->logService->logAction('Threshold', $id, 'delete');

            return response()->json(['message' => 'Threshold has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}
