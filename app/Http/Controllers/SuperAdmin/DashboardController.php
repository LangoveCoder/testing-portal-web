<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get notifications for super admin
        $notifications = Notification::forSuperAdmin()
            ->recent()
            ->limit(10)
            ->get();
            
        $unreadNotifications = Notification::forSuperAdmin()
            ->unread()
            ->count();

        return view('super_admin.dashboard', compact('notifications', 'unreadNotifications'));
    }

    public function markAllNotificationsRead()
    {
        Notification::forSuperAdmin()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }
}
