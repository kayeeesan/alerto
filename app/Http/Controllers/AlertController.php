<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Response;
use Illuminate\Http\Request;
use App\Http\Requests\AlertRequest;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AlertController extends Controller
{
    /**
     * Display a listing of the alerts with pagination.
     */
    public function index(Request $request)
    {
       $alerts = Alert::with('threshold')->paginate(10); // Load threshold details
    return response()->json($alerts);
    }

    /**
     * Store a newly created alert in storage.
     */
    // public function store(AlertRequest $request)
    // {
    //     try {
    //         $alert = new Alert();
    //         $alert->threshold_id = $request->threshold_id;
    //         $alert->details = $request->details;
    //         $alert->status = $request->status;
    //         $alert->expired_at = $request->expired_at;

    //         // Check if response_id is provided
    //         if ($request->response_id) {
    //             $alert->response_id = $request->response_id;
    //             $alert->status = 'responded'; // Set status to 'responded' when a response_id is provided
    //         }

    //         $alert->save();

    //         return response()->json(['message' => 'Alert has been created successfully!', 'data' => $alert], SymfonyResponse::HTTP_CREATED);
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => $e->getMessage()], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

    /**
     * Display the specified alert.
     */
    public function show($id)
    {
        $alert = Alert::findOrFail($id);
        return response()->json($alert);
    }

    /**
     * Update the specified alert in storage.
     */
    public function update($id, AlertRequest $request)
    {
        try {
            $alert = Alert::findOrFail($id);
            $alert->threshold_id = $request->threshold['id'];
            $alert->details = $request->details;
            $alert->status = $request->status;
            $alert->expired_at = $request->expired_at;

            // Check if response_id is provided and update status to "responded"
            if ($request->response_id) {
                $alert->response_id = $request->response['id'];
                $alert->status = 'responded'; // Set status to 'responded' when response_id is provided
            }

            $alert->save();

            return response()->json(['message' => 'Alert has been updated successfully!', 'data' => $alert]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified alert from storage.
     */
    public function destroy($id)
    {
        try {
            $alert = Alert::findOrFail($id);
            $alert->delete();
            return response()->json(['message' => 'Alert has been deleted successfully!'], SymfonyResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
