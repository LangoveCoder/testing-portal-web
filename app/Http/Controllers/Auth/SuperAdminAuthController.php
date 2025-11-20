<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin;
use App\Models\AuditLog;

class SuperAdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.super-admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = SuperAdmin::where('username', $request->username)
                          ->where('is_active', true)
                          ->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::guard('super_admin')->login($admin);
            
            // Log the login
            AuditLog::logAction(
                'super_admin',
                $admin->id,
                'login',
                'SuperAdmin',
                $admin->id,
                'Super Admin logged in'
            );

            return redirect()->route('super-admin.dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials or account is inactive.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('super_admin')->user();
        
        if ($admin) {
            AuditLog::logAction(
                'super_admin',
                $admin->id,
                'logout',
                'SuperAdmin',
                $admin->id,
                'Super Admin logged out'
            );
        }

        Auth::guard('super_admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login');
    }
}