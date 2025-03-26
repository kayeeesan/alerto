<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegionRequest;
use App\Http\Resources\Region as ResourcesRegion;
use App\Models\Region;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class RegionController extends Controller
{
    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $regions = [];
        if (isset($request->search)) {
            $regions = Region::where('name', 'like', '%' . $request->search . '%');
        }

        $regions = isset($request->search) && $request->search ? $regions->paginate(10) : Region::paginate(10);
        return ResourcesRegion::collection($regions);
    }

    public function store(RegionRequest $request)
    {
        try {
            $region = new Region();
            $region->name = ucwords($request->name);
            $region->save();

            $this->logService->logAction('Region', $region->id, 'create', $region->toArray());

            return response()->json(['message' => 'Region has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, RegionRequest $request)
    {
        try {
            $region = Region::findOrFail($id);
            $oldData = $region->toArray();
            $region->name = ucwords($request->name);
            $region->update();

            $this->logService->logAction('Region', $region->id, 'update', [
                'old' => $oldData,
                'new' => $region->toArray(),
            ]);

            return response(['message' => 'Region has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        try {
            $region = Region::find($id);

            if (!$region) {
                return response()->json(['message' => 'Region not found.'], Response::HTTP_NOT_FOUND);
            }

            $region->delete(); 

            $this->logService->logAction('Region', $id, 'delete');

            return response()->json(['message' => 'Region has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
