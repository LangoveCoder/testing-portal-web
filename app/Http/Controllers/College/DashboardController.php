<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $collegeId = Auth::guard('college')->id();
        
        // Get notifications for this college
        $notifications = Notification::forCollege($collegeId)
            ->recent()
            ->limit(10)
            ->get();
            
        $unreadNotifications = Notification::forCollege($collegeId)
            ->unread()
            ->count();

        // Get available tests for bulk template download
        $availableTests = Test::where('college_id', $collegeId)
            ->where('registration_deadline', '>=', now())
            ->orderBy('test_date')
            ->get();

        return view('college.dashboard', compact('notifications', 'unreadNotifications', 'availableTests'));
    }

    public function markAllNotificationsRead()
    {
        $collegeId = Auth::guard('college')->id();
        
        Notification::forCollege($collegeId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return redirect()->back()->with('success', 'All notifications marked as read');
    }
}
