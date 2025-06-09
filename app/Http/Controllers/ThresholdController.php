<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ThresholdRequest;
use App\Http\Resources\Threshold as ResourcesThreshold;
use App\Models\Threshold;
use App\Services\UserLogService;
use App\Services\AlertService;
use Symfony\Component\HttpFoundation\Response;

class ThresholdController extends Controller
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
        $query = Threshold::with([
            'sensorable.river',
            'sensorable.municipality'
        ]);

        if ($request->has('search')) {
            $query->where('sensorable_id', 'like', '%' . $request->search . '%');
        }

        $thresholds = $query->paginate(10);
        return ResourcesThreshold::collection($thresholds);
    }

    public function store(ThresholdRequest $request)
    {
        try {
            if (Threshold::where('sensorable_type', $request->sensorable_type)
                ->where('sensorable_id', $request->sensorable_id)
                ->exists()) {
                return response()->json(['message' => 'Threshold already exists for this sensor.'], Response::HTTP_CONFLICT);
            }


            $threshold = Threshold::create([
                'sensorable_type' => $request->sensorable_type,
                'sensorable_id' => $request->sensorable_id,
                'baseline' => $request->baseline,
                'sixty_percent' => $request->sixty_percent,
                'eighty_percent' => $request->eighty_percent,
                'one_hundred_percent' => $request->one_hundred_percent,
                'xs_date' => $request->xs_date
            ]);

            // Use AlertService
            $this->alertService->createAlertIfNeeded($threshold);

            $this->logService->logAction('Threshold', $threshold->id, 'create', $threshold->toArray());

            return response()->json([
                'message' => 'Threshold has been successfully saved.',
                'data' => new ResourcesThreshold($threshold)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update($id, ThresholdRequest $request)
    {
        try {
            $threshold = Threshold::findOrFail($id);
            $oldData = $threshold->toArray();

            $threshold->update([
                'sensorable_type' => $request->sensorable_type,
                'sensorable_id' => $request->sensorable_id,
                'baseline' => $request->baseline,
                'sixty_percent' => $request->sixty_percent,
                'eighty_percent' => $request->eighty_percent,
                'one_hundred_percent' => $request->one_hundred_percent,
                'xs_date' => $request->xs_date
            ]);

            // Use AlertService
            $this->alertService->createAlertIfNeeded($threshold);

            $this->logService->logAction('Threshold', $threshold->id, 'update', [
                'old' => $oldData,
                'new' => $threshold->toArray(),
            ]);

            return response()->json([
                'message' => 'Threshold has been successfully updated.',
                'data' => new ResourcesThreshold($threshold)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
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
