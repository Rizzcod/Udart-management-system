<?php

namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', auth()->id());
            })
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        // Same scope as index(): broadcasts (no user) or the user's own notifications.
        abort_unless(is_null($notification->user_id) || $notification->user_id == auth()->id(), 403);

        $notification->update(['is_read' => true]);
        return back();
    }

    public function markAllRead()
    {
        Notification::where(function ($q) {
            $q->whereNull('user_id')->orWhere('user_id', auth()->id());
        })->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
