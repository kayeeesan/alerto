<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProvinceRequest;
use App\Http\Resources\Province as ResourcesProvince;
use App\Models\Province;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = [];
        if (isset($request->search)) {
            $provinces = Province::where('name', 'like', '%' . $request->search . '%');
        }

        $provinces = isset($request->search) && $request->search ? $provinces->paginate(10) : Province::paginate(10);
        return ResourcesProvince::collection($provinces);
    }

    public function store(ProvinceRequest $request)
    {
        try {
            $province = new Province();
            $province->name = ucwords($request->name);
            $province->save();

            return response()->json(['message' => 'Province has been successfully saved.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, ProvinceRequest $request)
    {
        try {
            $province = Province::findOrFail($id);
            $province->name = ucwords($request->name);
            $province->update();
            return response(['message' => 'Province has been successfully updated.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        Province::findOrFail($id)->forceDelete();
        return response(['message' => 'Province has been successfully deleted!']);
    }
}
