<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MunicipalityRequest;
use App\Http\Resources\Municipality as ResourcesMunicipality;
use App\Models\Municipality;
use App\Services\UserLogService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class MunicipalityController extends Controller
{

    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index(Request $request)
    {
        $municipalities = [];
        if (isset($request->search)) {
            $municipalities = Municipality::where('name', 'like', '%' . $request->search . '%');
        } 

        $municipalities = isset($request->search) && $request->search ? $municipalities->paginate(10) : Municipality::paginate(10);
        return ResourcesMunicipality::collection(Municipality::paginate(10));
    }

    public function store(MunicipalityRequest $request)
    {
        try {
            $municipality = new Municipality();
            $municipality->province_id = $request->input('province.id');
            $municipality->name = ucwords($request->name);
            $municipality->save();

            $this->logService->logAction('Municipality', $municipality->id, 'create', $municipality->toArray());

            return response()->json(['message' => 'Municipality has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, MunicipalityRequest $request)
    {
       //dd($request->province['id']);
        try {
            $municipality = Municipality::findOrFail($id);
            $oldData = $municipality->toArray();
            $municipality->province_id = $request->province['id'];
            $municipality->name = ucwords($request->name);
            $municipality->update();

            $this->logService->logAction('Municipality', $municipality->id, 'update', [
                'old' => $oldData,
                'new' => $municipality->toArray(),
            ]);

            return response(['message' => 'Municipality has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    // public function destroy($id){
    //     try {
    //         $municipality = Municipality::findOrFail($id);
    //         $municipality->delete();

    //          // Log the deletion
    //          $this->logService->logAction('Municipality', $id, 'delete');

             
    //         return response(['message' => 'Municipality has been successfully deleted'], Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    //  }

    public function destroy($id)
    {
        try {
            $municipality = Municipality::find($id);

            if (!$municipality) {
                return response()->json(['message' => 'Municipality not found.'], Response::HTTP_NOT_FOUND);
            }

            $municipality->forceDelete(); 

            $this->logService->logAction('Municipality', $id, 'delete');

            return response()->json(['message' => 'Municipality has been successfully deleted!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
