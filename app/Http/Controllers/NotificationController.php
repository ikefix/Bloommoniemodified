<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    public function index()
{
    $user = Auth::user();

    // Fetch notifications
    $unreadNotifications = $user->unreadNotifications()
        ->where('type', 'App\Notifications\LowStockAlert')
        ->get();

    $readNotifications = $user->readNotifications()
        ->where('type', 'App\Notifications\LowStockAlert')
        ->get();

    // Dynamically select view based on role
    $view = match ($user->role) {
        'admin' => 'admin.notifications',
        'manager' => 'manager.notification',
        default => abort(403, 'Unauthorized'),
    };

    // Load the proper view
    return view($view, compact('unreadNotifications', 'readNotifications'));
}

 

    // This method is to mark notifications as read when viewed
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->read_at = now();  // Set read_at to the current time
            $notification->save();
        }

        return redirect()->back();  // Redirect back to the notifications page
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return redirect()->back()->with('success', 'Notification deleted.');
        }

        return redirect()->back()->with('error', 'Notification not found.');
    }
}
