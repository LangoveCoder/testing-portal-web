<?php

namespace App\Http\Controllers\BiometricOperator;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\BiometricLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    /**
     * Show registration interface
     */
    public function index()
    {
        $operator = Auth::guard('biometric_operator')->user();
        $assignedTests = $operator->tests();
        
        return view('biometric_operator.registration.index', compact('assignedTests'));
    }

    /**
     * Search student by roll number or CNIC
     */
    public function searchStudent(Request $request)
    {
        $request->validate([
            'search_term' => 'required|string',
        ]);

        $operator = Auth::guard('biometric_operator')->user();
        
        // Search by roll number or CNIC
        $student = Student::where(function($query) use ($request) {
                $query->where('roll_number', $request->search_term)
                      ->orWhere('cnic', $request->search_term);
            })
            ->whereIn('test_id', $operator->assigned_tests ?? [])
            ->with(['test', 'testDistrict'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found or you do not have access to this student.'
            ], 404);
        }

        // Get registration status
        $registrationLog = BiometricLog::where('student_id', $student->id)
            ->where('log_type', 'registration')
            ->where('action', 'capture')
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $student->id,
                'roll_number' => $student->roll_number,
                'name' => $student->name,
                'father_name' => $student->father_name,
                'cnic' => $student->cnic,
                'gender' => $student->gender,
                'picture' => $student->picture ? asset('storage/' . $student->picture) : null,
                'fingerprint_registered' => !is_null($student->fingerprint_template),
                'fingerprint_image' => $student->fingerprint_image ? asset('storage/' . $student->fingerprint_image) : null,
                'test_name' => $student->test->test_name,
                'test_date' => $student->test->test_date->format('d M Y'),
                'venue' => $student->testDistrict->district . ', ' . $student->testDistrict->province,
                'hall' => $student->hall_number,
                'zone' => $student->zone_number,
                'row' => $student->row_number,
                'seat' => $student->seat_number,
                'last_registration' => $registrationLog ? $registrationLog->created_at->format('d M Y, h:i A') : null,
            ]
        ]);
    }

    /**
     * Save fingerprint data
     */
    public function saveFingerprint(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fingerprint_template' => 'required|string',
            'fingerprint_image' => 'nullable|string', // Base64 image
        ]);

        $operator = Auth::guard('biometric_operator')->user();
        $student = Student::findOrFail($request->student_id);

        // Verify operator has access to this student
        if (!in_array($student->test_id, $operator->assigned_tests ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to register this student.'
            ], 403);
        }

        try {
            // Save fingerprint template
            $student->fingerprint_template = $request->fingerprint_template;

            // Save fingerprint image if provided
            if ($request->filled('fingerprint_image')) {
                // Decode base64 image
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->fingerprint_image));
                
                // Delete old fingerprint image if exists
                if ($student->fingerprint_image) {
                    Storage::disk('public')->delete($student->fingerprint_image);
                }
                
                // Generate unique filename
                $filename = 'fingerprints/' . $student->roll_number . '_' . time() . '.png';
                
                // Store new fingerprint image
                Storage::disk('public')->put($filename, $imageData);
                
                $student->fingerprint_image = $filename;
            }

            $student->save();

            // Log the registration
            BiometricLog::create([
                'student_id' => $student->id,
                'roll_number' => $student->roll_number,
                'log_type' => 'registration',
                'action' => 'capture',
                'operator_id' => $operator->id,
                'operator_type' => 'biometric_operator',
                'confidence_score' => null,
                'device_info' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'notes' => 'Fingerprint registered successfully',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint registered successfully!',
                'data' => [
                    'roll_number' => $student->roll_number,
                    'name' => $student->name,
                    'registered_at' => now()->format('d M Y, h:i A'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save fingerprint: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View registration history
     */
    public function history()
    {
        $operator = Auth::guard('biometric_operator')->user();
        
        $logs = BiometricLog::where('operator_id', $operator->id)
            ->where('operator_type', 'biometric_operator')
            ->where('log_type', 'registration')
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('biometric_operator.registration.history', compact('logs'));
    }
}