<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BiometricOperator;
use App\Models\College;
use App\Models\Test;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class BiometricOperatorController extends Controller
{
    /**
     * Display listing of biometric operators
     */
    public function index()
    {
        $operators = BiometricOperator::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('super_admin.biometric_operators.index', compact('operators'));
    }

    /**
     * Show form to create new operator
     */
    public function create()
    {
        $colleges = College::all();
        $tests = Test::where('roll_numbers_generated', true)
            ->where('test_date', '>=', now()->subDays(30))
            ->orderBy('test_date', 'desc')
            ->get();

        return view('super_admin.biometric_operators.create', compact('colleges', 'tests'));
    }

    /**
     * Store new operator
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:biometric_operators,email',
            'password' => 'required|string|min:6|confirmed',
            'assigned_colleges' => 'nullable|array',
            'assigned_colleges.*' => 'exists:colleges,id',
            'assigned_tests' => 'nullable|array',
            'assigned_tests.*' => 'exists:tests,id',
            'status' => 'required|in:active,inactive',
        ]);

        $operator = BiometricOperator::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'assigned_colleges' => $request->assigned_colleges ?? [],
            'assigned_tests' => $request->assigned_tests ?? [],
            'status' => $request->status,
            'created_by' => Auth::guard('super_admin')->id(),
        ]);

        // Log action
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'created_biometric_operator',
            'BiometricOperator',
            $operator->id,
            'Created biometric operator: ' . $operator->name,
            null,
            [
                'assigned_colleges' => count($request->assigned_colleges ?? []),
                'assigned_tests' => count($request->assigned_tests ?? [])
            ]
        );

        return redirect()->route('super-admin.biometric-operators.index')
            ->with('success', 'Biometric operator created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(BiometricOperator $biometricOperator)
    {
        $colleges = College::all();
        $tests = Test::where('roll_numbers_generated', true)
            ->where('test_date', '>=', now()->subDays(30))
            ->orderBy('test_date', 'desc')
            ->get();

        return view('super_admin.biometric_operators.edit', compact('biometricOperator', 'colleges', 'tests'));
    }

    /**
     * Update operator
     */
    public function update(Request $request, BiometricOperator $biometricOperator)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:biometric_operators,email,' . $biometricOperator->id,
            'password' => 'nullable|string|min:6|confirmed',
            'assigned_colleges' => 'nullable|array',
            'assigned_colleges.*' => 'exists:colleges,id',
            'assigned_tests' => 'nullable|array',
            'assigned_tests.*' => 'exists:tests,id',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'assigned_colleges' => $request->assigned_colleges ?? [],
            'assigned_tests' => $request->assigned_tests ?? [],
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $biometricOperator->update($data);

        // Log action
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'updated_biometric_operator',
            'BiometricOperator',
            $biometricOperator->id,
            'Updated biometric operator: ' . $biometricOperator->name,
            null,
            null
        );

        return redirect()->route('super-admin.biometric-operators.index')
            ->with('success', 'Biometric operator updated successfully!');
    }

    /**
     * Delete operator
     */
    public function destroy(BiometricOperator $biometricOperator)
    {
        $name = $biometricOperator->name;
        
        $biometricOperator->delete();

        // Log action
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'deleted_biometric_operator',
            'BiometricOperator',
            null,
            'Deleted biometric operator: ' . $name,
            null,
            null
        );

        return redirect()->route('super-admin.biometric-operators.index')
            ->with('success', 'Biometric operator deleted successfully!');
    }
}