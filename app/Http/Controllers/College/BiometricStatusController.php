<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiometricStatusController extends Controller
{
    /**
     * Display biometric registration status for college students
     */
    public function index(Request $request)
    {
        $college = Auth::guard('college')->user();

        $query = Student::whereHas('test', function($q) use ($college) {
                $q->where('college_id', $college->id);
            })
            ->with(['test']);

        // Filter by test
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->test_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'complete':
                    $query->whereNotNull('picture')
                          ->whereNotNull('test_photo')
                          ->whereNotNull('fingerprint_template');
                    break;
                case 'incomplete':
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

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('roll_number')->paginate(50);

        // Statistics for this college
        $stats = [
            'total' => Student::whereHas('test', function($q) use ($college) {
                $q->where('college_id', $college->id);
            })->count(),
            'with_fingerprint' => Student::whereHas('test', function($q) use ($college) {
                $q->where('college_id', $college->id);
            })->whereNotNull('fingerprint_template')->count(),
            'with_test_photo' => Student::whereHas('test', function($q) use ($college) {
                $q->where('college_id', $college->id);
            })->whereNotNull('test_photo')->count(),
            'complete' => Student::whereHas('test', function($q) use ($college) {
                $q->where('college_id', $college->id);
            })->whereNotNull('picture')
                ->whereNotNull('test_photo')
                ->whereNotNull('fingerprint_template')
                ->count(),
            'incomplete' => Student::whereHas('test', function($q) use ($college) {
                $q->where('college_id', $college->id);
            })->where(function($q) {
                $q->whereNull('picture')
                  ->orWhereNull('test_photo')
                  ->orWhereNull('fingerprint_template');
            })->count()
        ];

        $tests = Test::where('college_id', $college->id)
            ->orderBy('test_date', 'desc')
            ->get();

        return view('college.biometric_status.index', compact('students', 'stats', 'tests'));
    }

    /**
     * Show individual student details
     */
    public function show($id)
    {
        $college = Auth::guard('college')->user();
        
        $student = Student::whereHas('test', function($q) use ($college) {
                $q->where('college_id', $college->id);
            })
            ->with(['test'])
            ->findOrFail($id);
        
        return view('college.biometric_status.show', compact('student'));
    }
}