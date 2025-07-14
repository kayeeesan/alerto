<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function receive(Request $request, $model)
    {
        $modelClass = config('sync.models')[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid model'], 400);
        }

        foreach ($request->all() as $data) {
            $modelClass::updateOrCreate(
                ['uuid' => $data['uuid']],
                $data
            );
        }
        return response()->json(['status' => 'ok']);
    }

    public function fetch(Request $request, $model)
    {
        $modelClass = config('sync.models')[$model] ?? null;

        if (!$modelClass || !class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid model'], 400);
        }

        return $modelClass::all();
    }
}
