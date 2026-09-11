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
