<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLog;
use App\Http\Resources\UserLog as UserLogResource;

class UserLogController extends Controller
{
    public function index(Request $request)
{
    $query = UserLog::query();

    // Ensure logs are filtered by user_id
    if ($request->has('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    // Search filter (if needed)
    if ($request->has('search')) {
        $query->where('action', 'like', '%' . $request->search . '%');
    }

    // Paginate results
    $logs = $query->latest()->paginate(10); 

    // Return paginated resource collection
    return UserLogResource::collection($logs);
}

}
