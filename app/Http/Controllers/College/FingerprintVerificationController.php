<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\BiometricLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FingerprintVerificationController extends Controller
{
    /**
     * Display fingerprint verification page
     */
    public function index()
    {
        return view('college.fingerprint_verification.index');
    }

    /**
     * Load student by roll number
     */
    public function loadStudent(Request $request)
    {
        $request->validate([
            'roll_number' => 'required|string'
        ]);

        try {
            $college = Auth::guard('college')->user();
            
            // Find student by roll number with test relationship
            // Students are linked to tests, and tests are linked to colleges
            $student = Student::where('roll_number', $request->roll_number)
                ->whereHas('test', function($query) use ($college) {
                    $query->where('college_id', $college->id);
                })
                ->with(['test'])
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found or does not belong to your college'
                ]);
            }

            // Check if student has fingerprint registered
            $fingerprintRegistered = !empty($student->fingerprint_template);

            // Get test photo if exists
            $testPhoto = null;
            if ($student->test_photo) {
                // Assuming test_photo is stored as base64 or path
                $testPhoto = $student->test_photo;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'father_name' => $student->father_name,
                    'roll_number' => $student->roll_number,
                    'cnic' => $student->cnic,
                    'gender' => $student->gender,
                    'picture' => $student->picture ? asset('storage/' . $student->picture) : null,
                    'test_photo' => $testPhoto ? (str_starts_with($testPhoto, 'data:') ? $testPhoto : asset('storage/' . $testPhoto)) : null,
                    'test_name' => $student->test->name ?? 'N/A',
                    'venue' => $student->venue ?? 'N/A',
                    'hall' => $student->hall ?? 'N/A',
                    'zone' => $student->zone ?? 'N/A',
                    'row' => $student->row ?? 'N/A',
                    'seat' => $student->seat ?? 'N/A',
                    'fingerprint_template' => $student->fingerprint_template,
                    'fingerprint_image' => $student->fingerprint_image,
                    'fingerprint_registered' => $fingerprintRegistered
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log verification attempt
     */
    public function logVerification(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'roll_number' => 'required|string',
            'match_result' => 'required|boolean',
            'confidence_score' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $college = Auth::guard('college')->user();

            // Create biometric log entry
            BiometricLog::create([
                'student_id' => $request->student_id,
                'roll_number' => $request->roll_number,
                'action' => 'verification',
                'match_result' => $request->match_result,
                'confidence_score' => $request->confidence_score,
                'performed_by' => $college->name,
                'performed_by_type' => 'college',
                'performed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification logged successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error logging verification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get verification history for a student
     */
    public function getVerificationHistory($studentId)
    {
        try {
            $college = Auth::guard('college')->user();

            // Verify student belongs to this college through test relationship
            $student = Student::where('id', $studentId)
                ->whereHas('test', function($query) use ($college) {
                    $query->where('college_id', $college->id);
                })
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
            }

            $history = BiometricLog::where('student_id', $studentId)
                ->where('action', 'verification')
                ->orderBy('performed_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's verification statistics
     */
    public function getStatistics()
    {
        try {
            $college = Auth::guard('college')->user();

            $today = now()->startOfDay();

            $stats = [
                'total_verifications' => BiometricLog::where('performed_by', $college->name)
                    ->where('action', 'verification')
                    ->whereDate('performed_at', $today)
                    ->count(),
                
                'successful_matches' => BiometricLog::where('performed_by', $college->name)
                    ->where('action', 'verification')
                    ->where('match_result', true)
                    ->whereDate('performed_at', $today)
                    ->count(),
                
                'failed_matches' => BiometricLog::where('performed_by', $college->name)
                    ->where('action', 'verification')
                    ->where('match_result', false)
                    ->whereDate('performed_at', $today)
                    ->count(),
                
                'average_confidence' => BiometricLog::where('performed_by', $college->name)
                    ->where('action', 'verification')
                    ->where('match_result', true)
                    ->whereDate('performed_at', $today)
                    ->avg('confidence_score')
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}