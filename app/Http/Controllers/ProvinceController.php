<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProvinceRequest;
use App\Http\Resources\Province as ResourcesProvince;
use App\Models\Province;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ProvinceController extends Controller
{

    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $provinces = [];
        if (isset($request->search)) {
            $provinces = Province::where('name', 'like', '%' . $request->search . '%');
        }

        $provinces = isset($request->search) && $request->search ? $provinces->paginate(999) : Province::paginate(999);
        return ResourcesProvince::collection($provinces);
    }

    public function store(ProvinceRequest $request)
    {
        try {

            $existingProvince = Province::whereRaw('LOWER(name) = ? AND region_id = ?', [strtolower($request->name), $request->input('region.id')])->first();
            if ($existingProvince) {
                return response()->json(['message' => 'Province with this name already exists in the selected region.'], Response::HTTP_CONFLICT);
            }
            $province = new Province();
            $province->region_id = $request->input('region.id');
            $province->name = ucwords($request->name);
            $province->save();

            $this->logService->logAction('Province', $province->id, 'create', $province->toArray());

            return response()->json(['message' => 'Province has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, ProvinceRequest $request)
    {
        try {
            $province = Province::findOrFail($id);
            $oldData = $province->toArray();
            $province->region_id = $request->region['id'];
            $province->name = ucwords($request->name);
            $province->update();

            $this->logService->logAction('Province', $province->id, 'update', [
                'old' => $oldData,
                'new' => $province->toArray(),
            ]);

            return response(['message' => 'Province has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        Province::findOrFail($id)->forceDelete();
        $this->logService->logAction('Province', $id, 'delete');
        return response(['message' => 'Province has been successfully deleted!']);
    }
}
