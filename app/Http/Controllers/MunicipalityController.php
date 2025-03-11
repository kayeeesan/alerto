<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MunicipalityRequest;
use App\Http\Resources\Municipality as ResourcesMunicipality;
use App\Models\Municipality;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class MunicipalityController extends Controller
{
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

            return response()->json(['message' => 'Municipality has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, MunicipalityRequest $request)
    {
        try {
            $municipality = Municipality::findOrFail($id);
            $municipality->province_id = $request->input('province.id');
            $municipality->name = ucwords($request->name);
            $municipality->update();
            return response(['message' => 'Municipality has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id){
        try {
            $municipality = Municipality::findOrFail($id);
            $municipality->delete();
            return response(['message' => 'Municipality has been successfully deleted'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
     }
}
