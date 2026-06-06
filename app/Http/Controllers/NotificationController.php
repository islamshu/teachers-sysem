<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(10);

        if (request()->wantsJson()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'nextPage' => $notifications->currentPage() < $notifications->lastPage() ? $notifications->currentPage() + 1 : null,
            ]);
        }

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications->items(),
            'nextPage' => $notifications->currentPage() < $notifications->lastPage() ? $notifications->currentPage() + 1 : null,
        ]);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
