<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    /**
     * Allowed models for synchronization.
     */
    protected array $allowedModels = [
        'App\Models\User',
        'App\Models\Role',
        'App\Models\Region',
        'App\Models\Province',
        'App\Models\Municipality',
        'App\Models\River',
        'App\Models\SensorUnderAlerto',
        'App\Models\SensorUnderPh',
        'App\Models\Response',
        'App\Models\Threshold',
        'App\Models\Alert',
        'App\Models\UserLog',
        'App\Models\Staff',
        'App\Models\ContactMessage',
        'App\Models\Notification'
    ];

    /**
     * Local LGU pushes updated records to main
     */
    public function push(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'data' => 'required|array',
        ]);

        $modelName = $request->input('model');
        $records = $request->input('data');

        if (!in_array($modelName, $this->allowedModels)) {
            return response()->json(['error' => 'Model not allowed.'], 403);
        }

        $synced = 0;
        foreach ($records as $item) {
            if (!isset($item['uuid'])) {
                Log::warning("Missing UUID in sync record: " . json_encode($item));
                continue;
            }

            try {
                $modelName::updateOrCreate(['uuid' => $item['uuid']], $item);
                $synced++;
            } catch (\Exception $e) {
                Log::error("Sync error: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'synced_records' => $synced,
        ]);
    }

    /**
     * Local LGU pulls updated records from main
     */
    public function updates(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'since' => 'nullable|date',
        ]);

        $modelName = $request->input('model');
        $since = $request->input('since');

        if (!in_array($modelName, $this->allowedModels)) {
            return response()->json(['error' => 'Model not allowed.'], 403);
        }

        $query = $modelName::query();

        if ($since) {
            $query->where('updated_at', '>', $since);
        }

        $data = $query->get();

        return response()->json([
            'status' => 'success',
            'records' => $data,
        ]);
    }
}
