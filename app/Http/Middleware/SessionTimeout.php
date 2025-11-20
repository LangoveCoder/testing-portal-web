<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::guard('super_admin')->check()) {
            $lastActivity = session('last_activity_time');
            $timeout = 15 * 60; // 15 minutes in seconds

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::guard('super_admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('super-admin.login')
                    ->with('message', 'Session expired due to inactivity.');
            }
        }

        if (Auth::guard('college')->check()) {
            $lastActivity = session('last_activity_time');
            $timeout = 15 * 60; // 15 minutes in seconds

            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::guard('college')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('college.login')
                    ->with('message', 'Session expired due to inactivity.');
            }
        }

        // Update last activity time
        session(['last_activity_time' => time()]);

        return $next($request);
    }
}