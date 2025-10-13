<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Response;
use Illuminate\Http\Request;
use App\Http\Requests\AlertRequest;
use App\http\Requests\AlertResponseRequest;
use App\Http\Resources\Alert as ResourcesAlert;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Support\Facades\Auth;
use App\Events\AlertCreated;
use App\Events\AlertUpdated;


class AlertController extends Controller
{
    /**
     * Display a listing of the alerts with pagination.
     */
    public function index(Request $request)
    {
        $query = Alert::with('threshold', 'threshold.sensorable.municipality', 'threshold.sensorable.river', 'user');
                
            if ($request->has('search')) {
            $query->where('sensor_id', 'like', '%' . $request->search . '%');
        }

        $alerts = $query->paginate(10);
        return ResourcesAlert::collection($alerts);
    }

    // Filter: Pending
    public function pending()
    {
        return $this->getAlertsByStatus('pending');
    }

    // Filter: Responded
    public function responded()
    {
        return $this->getAlertsByStatus('responded');
    }

    // Filter: Expired
    public function expired()
    {
        return $this->getAlertsByStatus('expired');
    }

    // Reusable filter method
    private function getAlertsByStatus($status)
    {
        $alerts = Alert::with('threshold', 'threshold.sensorable.municipality', 'threshold.sensorable.river', 'user')
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->paginate(10);

        return ResourcesAlert::collection($alerts);
    }


    /**
     * Remove the specified alert from storage.
     */
    public function destroy($id)
    {
        try {
            $alert = Alert::findOrFail($id);
            $alert->delete();
            return response()->json(['message' => 'Alert has been deleted successfully!'], SymfonyResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($id, AlertResponseRequest $request){
        try {
            $alert = Alert::findOrFail($id);
            $alert->response_id = $request->response['id'] ?? $request->response_id;
              // Fetch the user ID from the request or authenticated user
            $userId = Auth::id();
            $alert->user_id = $userId;
            $alert->status = 'responded';
            $alert->update();

            event(new AlertUpdated($alert));

            return response(['message' => 'Response successfully added']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
