<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\College;
use App\Models\AuditLog;

class CollegeAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.college-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $college = College::where('email', $request->email)
                         ->where('is_active', true)
                         ->first();

        if ($college && Hash::check($request->password, $college->password)) {
            Auth::guard('college')->login($college);
            
            // Log the login
            AuditLog::logAction(
                'college_admin',
                $college->id,
                'login',
                'College',
                $college->id,
                'College Admin logged in: ' . $college->name
            );

            return redirect()->route('college.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or account is inactive.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $college = Auth::guard('college')->user();
        
        if ($college) {
            AuditLog::logAction(
                'college_admin',
                $college->id,
                'logout',
                'College',
                $college->id,
                'College Admin logged out: ' . $college->name
            );
        }

        Auth::guard('college')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('college.login');
    }
}