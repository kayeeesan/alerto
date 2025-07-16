<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SyncController extends Controller
{
    //main receiving data from local server
    public function receive(Request $request, $model)
    {
        $modelClass = config('sync.models')[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid model'], 400);
        }

        foreach ($request->all() as $data) {
              try {
                $modelClass::updateOrCreate(
                    ['uuid' => $data['uuid']],
                    $data
                );
            } catch (\Exception $e) {
                \Log::error("Failed syncing $model: " . $e->getMessage());
                continue;
            }
        }
        return response()->json(['status' => 'ok']);
    }

    //local server fetching data from main server
    public function fetch(Request $request, $model)
    {
        $modelClass = config('sync.models')[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid model'], 400);
        }

        return $modelClass::withTrashed()->get();

    }
}
