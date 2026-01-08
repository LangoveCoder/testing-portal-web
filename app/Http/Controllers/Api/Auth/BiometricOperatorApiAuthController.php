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

        $operator = BiometricOperator::with(['assignedCollege', 'tests'])->where('email', $request->email)->first();

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
            'message' => 'Login successful',
            'data' => [
                'operator' => [
                    'id' => $operator->id,
                    'name' => $operator->name,
                    'email' => $operator->email,
                    'phone' => $operator->phone,
                    'status' => $operator->status,
                    'assigned_college_id' => $operator->assigned_college_id,
                    'assigned_college' => $operator->assignedCollege ? [
                        'id' => $operator->assignedCollege->id,
                        'name' => $operator->assignedCollege->name,
                        'district' => $operator->assignedCollege->district,
                        'province' => $operator->assignedCollege->province,
                    ] : null,
                    'tests' => $operator->tests->map(function($test) {
                        return [
                            'id' => $test->id,
                            'test_name' => $test->test_name ?? 'Test ' . $test->id,
                            'test_date' => $test->test_date,
                            'test_time' => $test->test_time,
                            'total_marks' => $test->total_marks,
                        ];
                    }),
                    'permissions' => [
                        'can_register_fingerprints' => true,
                        'can_verify_fingerprints' => true,
                        'can_view_students' => true,
                    ]
                ],
                'token' => $token,
                'expires_at' => now()->addDays(30)->toISOString(),
            ]
        ]);
    }
}