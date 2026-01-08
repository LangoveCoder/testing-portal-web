<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\BiometricOperator;

class BiometricOperatorApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $operator = BiometricOperator::where('email', $request->email)->first();

        if (!$operator) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        if (isset($operator->status) && $operator->status === 'inactive') {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated'
            ], 403);
        }

        if (!Hash::check($request->password, $operator->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        $token = $operator->createToken('biometric-desktop-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $operator->id,
                'name' => $operator->name,
                'email' => $operator->email,
                'role' => 'operator'
            ]
        ]);
    }
}