<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorsHistory;
use App\Http\Resources\SensorHistory as SensorHistoryResource;

class SensorHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SensorsHistory::whereHas('sensor', function($q) {
            $q->whereNull('deleted_at'); // only active sensors
        })->with(['sensor' => function($q) {
            $q->whereNull('deleted_at'); // only active sensors
        }]);

        if ($request->has('sensor_uuid')) {
            $query->where('sensor_uuid', $request->sensor_uuid);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('recorded_at', [
                $request->from_date,
                $request->to_date
            ]);
        }

        $query->orderBy('recorded_at', 'asc');

        $data = $query->get();

        return SensorHistoryResource::collection($data);
    }
}
