<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Resources\Notification as ResourcesNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('river', 'alert')
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

   public function markAsSeen($id)
    {
        $user = auth()->user();

        $notification = Notification::where('id', $id)
            ->when(!$user->roles->contains('slug', 'administrator'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();

        if (!$notification->seen_at) {
            $notification->update(['seen_at' => now()]);
            $notification->refresh();
            return response()->json([
                'success' => true,
                'data' => new ResourcesNotification($notification)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification already seen'
        ]);
    }
}
