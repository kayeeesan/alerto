<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RiverRequest;
use App\Http\Resources\River as ResourcesRiver;
use App\Models\River;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class RiverController extends Controller
{

    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
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
            $river->municipality_id = $request->input('municipality.id');
            $river->name = ucwords($request->name);
            $river->river_code = $request->river_code;
            $river->save();

            $this->logService->logAction('River', $river->id, 'create', $river->toArray());

            return response()->json(['message' => 'River has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, RiverRequest $request)
    {
        try {
            $river = River::findOrFail($id);
            $oldData = $river->toArray();
            $river->municipality_id = $request->input('municipality.id');
            $river->name = ucwords($request->name);
            $river->river_code = $request->river_code;
            $river->update();

            $this->logService->logAction('River', $river->id, 'update', [
                'old' => $oldData,
                'new' => $river->toArray(),
            ]);

            return response(['message' => 'River has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    // public function destroy($id)
    // {
    //     River::findOrFail($id)->forceDelete();
    //     $this->logService->logAction('River', $id, 'delete');
    //     return response(['message' => 'River has been successfully deleted!']);
    // }

    public function destroy($id)
    {
        try {
            $river = River::find($id);

            if (!$river) {
                return response()->json(['message' => 'River not found.'], Response::HTTP_NOT_FOUND);
            }

            $river->forceDelete(); 

            $this->logService->logAction('River', $id, 'delete');

            return response()->json(['message' => 'River has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
