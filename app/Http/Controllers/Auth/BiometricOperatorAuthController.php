<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BiometricOperator;

class BiometricOperatorAuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        // If already logged in, redirect to dashboard
        if (Auth::guard('biometric_operator')->check()) {
            return redirect()->route('biometric-operator.dashboard');
        }
        
        return view('biometric_operator.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $operator = BiometricOperator::where('email', $request->email)->first();

        if ($operator && $operator->status === 'inactive') {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact administrator.',
            ])->withInput();
        }

        if (Auth::guard('biometric_operator')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->route('biometric-operator.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('biometric_operator')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('biometric-operator.login');
    }
}