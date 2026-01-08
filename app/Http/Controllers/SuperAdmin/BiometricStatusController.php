<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Test;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiometricStatusController extends Controller
{
    /**
     * Display biometric registration status
     */
    public function index(Request $request)
    {
        $query = Student::with(['test'])
            ->select('students.*');

        // Filter by college
        if ($request->filled('college_id')) {
            $query->whereHas('test', function($q) use ($request) {
                $q->where('college_id', $request->college_id);
            });
        }

        // Filter by test
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->test_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'complete':
                    // Has registration photo, test photo, and fingerprint
                    $query->whereNotNull('picture')
                          ->whereNotNull('test_photo')
                          ->whereNotNull('fingerprint_template');
                    break;
                case 'incomplete':
                    // Missing at least one
                    $query->where(function($q) {
                        $q->whereNull('picture')
                          ->orWhereNull('test_photo')
                          ->orWhereNull('fingerprint_template');
                    });
                    break;
                case 'no_fingerprint':
                    $query->whereNull('fingerprint_template');
                    break;
                case 'no_test_photo':
                    $query->whereNull('test_photo');
                    break;
                case 'no_registration_photo':
                    $query->whereNull('picture');
                    break;
            }
        }

        // Search by name or roll number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('roll_number')->paginate(50);

        // Get statistics
        $stats = [
            'total' => Student::count(),
            'with_fingerprint' => Student::whereNotNull('fingerprint_template')->count(),
            'with_test_photo' => Student::whereNotNull('test_photo')->count(),
            'complete' => Student::whereNotNull('picture')
                ->whereNotNull('test_photo')
                ->whereNotNull('fingerprint_template')
                ->count(),
            'incomplete' => Student::where(function($q) {
                $q->whereNull('picture')
                  ->orWhereNull('test_photo')
                  ->orWhereNull('fingerprint_template');
            })->count()
        ];

        $colleges = College::orderBy('name')->get();
        $tests = Test::orderBy('test_date', 'desc')->get();

        return view('super_admin.biometric_status.index', compact('students', 'stats', 'colleges', 'tests'));
    }

    /**
     * Show individual student details
     */
    public function show($id)
    {
        $student = Student::with(['test'])->findOrFail($id);
        
        return view('super_admin.biometric_status.show', compact('student'));
    }

    /**
     * Export to Excel
     */
    public function export(Request $request)
    {
        // Similar query as index but for export
        // TODO: Implement Excel export
    }
}