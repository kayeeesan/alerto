<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Resources\Notification as ResourcesNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('river')
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(10);

        return ResourcesNotification::collection($notifications);
    }

    public function markAsRead($id)
    {
        $notification = Notification::with('river')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return new ResourcesNotification($notification);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    
        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }
}
