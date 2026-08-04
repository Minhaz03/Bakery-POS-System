<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = Notification::orderBy('created_at', 'desc')->paginate(20);
        return view('dashboard.notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $notification->update(['is_read' => true]);

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        Notification::unread()->update(['is_read' => true]);
        
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification): RedirectResponse
    {
        $notification->delete();
        
        return back()->with('success', 'Notification deleted successfully.');
    }
}
