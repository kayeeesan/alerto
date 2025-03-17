<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ThresholdRequest;
use App\Http\Resources\Threshold as ResourcesThreshold;
use App\Models\Threshold;
use App\Models\Alert; 
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

    // public function index()
    // {
    //     $thresholds = [];
    //     if (isset($request->search)) {
    //         $thresholds = Threshold::where('sensor_id', 'like', '%' . $request->search . '%');
    //     }

    //     $thresholds = isset($request->search) && $request->search ? $thresholds->paginate(10) : Threshold::paginate(10);
    //     return ResourcesThreshold::collection($thresholds);
    // }
    public function index(Request $request)
    {
        $query = Threshold::with('sensor');

        if ($request->has('search')) {
            $query->where('sensor_id', 'like', '%' . $request->search . '%');
        }

        $thresholds = $query->paginate(10);
        return ResourcesThreshold::collection($thresholds);
    }

    public function store(ThresholdRequest $request)
    {
        try {
            $threshold = new Threshold();
            $threshold->sensor_id = $request->input('sensor.id');
            $threshold->baseline = $request->baseline;
            $threshold->sixty_percent = $request->sixty_percent;
            $threshold->eighty_percent = $request->eighty_percent;
            $threshold->one_hundred_percent = $request->one_hundred_percent;
            $threshold->xs_date = $request->xs_date;
            $threshold->water_level = $request->water_level;
            $threshold->save();

           // Check if the water level exceeds any threshold value and create an alert
        if ($threshold->water_level > $threshold->one_hundred_percent) {
            $alert = new Alert();
            $alert->threshold_id = $threshold->id;
            $alert->details = 'Water is critical: ' . $threshold->water_level;
            $alert->status = 'pending';
            $alert->expired_at = now()->addMinutes(2);
            $alert->save();
        } elseif ($threshold->water_level > $threshold->eighty_percent) {
            $alert = new Alert();
            $alert->threshold_id = $threshold->id;
            $alert->details = 'Water is on alert: ' . $threshold->water_level;
            $alert->status = 'pending';
            $alert->expired_at = now()->addMinutes(2);
            $alert->save();
        } elseif ($threshold->water_level > $threshold->sixty_percent) {
            $alert = new Alert();
            $alert->threshold_id = $threshold->id;
            $alert->details = 'Please monitor water level: ' . $threshold->water_level;
            $alert->status = 'pending';
            $alert->expired_at = now()->addMinutes(2);
            $alert->save();
        }

            $this->logService->logAction('Threshold', $threshold->id, 'create', $threshold->toArray());

            return response()->json(['message' => 'threshold has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, ThresholdRequest $request)
    {
        try {
            $threshold = Threshold::findOrFail($id);
            $oldData = $threshold->toArray();
            $threshold->sensor_id = $request->sensor['id'];
            $threshold->baseline = $request->baseline;
            $threshold->sixty_percent = $request->sixty_percent;
            $threshold->eighty_percent = $request->eighty_percent;
            $threshold->one_hundred_percent = $request->one_hundred_percent;
            $threshold->xs_date = $request->xs_date;
            $threshold->water_level = $request->water_level;
            $threshold->update();

              // Check if the updated water level exceeds any threshold value and create an alert
        if ($threshold->water_level > $threshold->one_hundred_percent) {
            $alert = new Alert();
            $alert->threshold_id = $threshold->id;
            $alert->details = 'Water is critical: ' . $threshold->water_level;
            $alert->status = 'pending';
            $alert->expired_at = now()->addMinutes(2);
            $alert->save();
        } elseif ($threshold->water_level > $threshold->eighty_percent) {
            $alert = new Alert();
            $alert->threshold_id = $threshold->id;
            $alert->details = 'Water is on alert: ' . $threshold->water_level;
            $alert->status = 'pending';
            $alert->expired_at = now()->addMinutes(2);
            $alert->save();
        } elseif ($threshold->water_level > $threshold->sixty_percent) {
            $alert = new Alert();
            $alert->threshold_id = $threshold->id;
            $alert->details = 'Please monitor water level: ' . $threshold->water_level;
            $alert->status = 'pending';
            $alert->expired_at = now()->addMinutes(2);
            $alert->save();
        }


            $this->logService->logAction('Threshold', $threshold->id, 'update', [
                'old' => $oldData,
                'new' => $threshold->toArray(),
            ]);

            return response(['message' => 'Threshold has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
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
