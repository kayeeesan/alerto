<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ResponseRequest;
use App\Http\Resources\Response as ResourcesResponse;
use App\Models\Response;
use App\Services\UserLogService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResponseController extends Controller
{
    protected $logService;

    public function __construct(UserLogService $logService)
    {
        $this->logService = $logService;
    }

    public function index()
    {
        $responses = [];
        if (isset($request->search)) {
            $responses = Response::where('name', 'like', '%' . $request->search . '%');
        }

        $responses = isset($request->search) && $request->search ? $responses->paginate(10) : Response::paginate(10);
        return ResourcesResponse::collection($responses);
    }

    public function store(ResponseRequest $request){
        try{
            $response = new Response();
            $response->color = $request->color;
            $response->action = $request->action;
            $response->code = $request->code;
            $response->save();

            $this->logService->logAction('Response', $response->id, 'create', $response->toArray());

            return response()->json(['message' => 'Response has been successfully saved']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function update($id, ResponseRequest $request){
        try{
            $response = Response::findOrFail($id);
            $oldData = $response->toArray();
            $response->color = $request->color;
            $response->action = $request->action;
            $response->code = $request->code;
            $response->update();

            $this->logService->logAction('Response', $response->id, 'update', [
                'old' => $oldData,
                'new' => $response->toArray(),
            ]);

            return response()->json(['message' => 'Response has been successfully updated']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $response = Response::find($id);

            if (!$response) {
                return response()->json(['message' => 'Response not found.'], SymfonyResponse::HTTP_NOT_FOUND);
            }

            $response->delete(); 

            $this->logService->logAction('Response', $id, 'delete');

            return response()->json(['message' => 'Response has been successfully deleted!'], SymfonyResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
