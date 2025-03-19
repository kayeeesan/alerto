<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Response;
use Illuminate\Http\Request;
use App\Http\Requests\AlertRequest;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Support\Facades\Auth;


class AlertController extends Controller
{
    /**
     * Display a listing of the alerts with pagination.
     */
    public function index(Request $request)
    {
       $alerts = Alert::with('threshold', 'threshold.sensor.municipality', 'threshold.sensor.river', 'user')->paginate(10); // Load threshold details
    return response()->json($alerts);
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
