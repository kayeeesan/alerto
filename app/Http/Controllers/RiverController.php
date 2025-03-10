<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RiverRequest;
use App\Http\Resources\River as ResourcesRiver;
use App\Models\River;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class RiverController extends Controller
{
    public function index()
    {
        $rivers = [];
        if (isset($request->search)) {
            $rivers = River::where('name', 'like', '%' . $request->search . '%');
        }

        $rivers = isset($request->search) && $request->search ? $rivers->paginate(10) : River::paginate(10);
        return ResourcesRiver::collection($rivers);
    }

    public function store(RiverRequest $request)
    {
        try {
            $river = new River();
            $river->name = ucwords($request->name);
            $river->river_code = $request->river_code;
            $river->save();

            return response()->json(['message' => 'River has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, RiverRequest $request)
    {
        try {
            $river = River::findOrFail($id);
            $river->name = ucwords($request->name);
            $river->river_code = $request->river_code;
            $river->update();
            return response(['message' => 'River has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        River::findOrFail($id)->forceDelete();
        return response(['message' => 'River has been successfully deleted!']);
    }
}
