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
                $threshold = Threshold::create([
                    'sensorable_type' => $request->sensorable_type,
                    'sensorable_id' => $request->sensorable_id,
                    'baseline' => $request->baseline,
                    'sixty_percent' => $request->sixty_percent,
                    'eighty_percent' => $request->eighty_percent,
                    'one_hundred_percent' => $request->one_hundred_percent,
                    'xs_date' => $request->xs_date,
                    'water_level' => $request->water_level
                ]);
        
                $this->createAlertIfNeeded($threshold);
        
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
                    'xs_date' => $request->xs_date,
                    'water_level' => $request->water_level
                ]);
        
                $this->createAlertIfNeeded($threshold);
        
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
        
        // New protected method to handle alert creation
        protected function createAlertIfNeeded(Threshold $threshold)
        {
            $details = '';
            $status = 'pending';
        
            if ($threshold->water_level >= $threshold->one_hundred_percent) {
                $details = 'Water is critical. Water level: ' . $threshold->water_level;
            } elseif ($threshold->water_level >= $threshold->eighty_percent) {
                $details = 'Water is on alert. Water level: ' . $threshold->water_level;
            } elseif ($threshold->water_level >= $threshold->sixty_percent) {
                $details = 'Please monitor water level. Water level: ' . $threshold->water_level;
            }
        
            if (empty($details)) {
                return; // nothing to create
            }
        
            Alert::create([
                'threshold_id' => $threshold->id,
                'details' => $details,
                'status' => $status,
                'expired_at' => now()->addMinutes(30),
                'user_id' => auth()->id(), // optional
            ]);
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
