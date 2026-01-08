<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\College;

class CollegeApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $college = College::where('email', $request->email)
                         ->where('is_active', true)
                         ->first();

        if (!$college) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials or account is inactive'
            ], 401);
        }

        if (!Hash::check($request->password, $college->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        $token = $college->createToken('biometric-desktop-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $college->id,
                'name' => $college->name,
                'email' => $college->email,
                'role' => 'college'
            ]
        ]);
    }
}