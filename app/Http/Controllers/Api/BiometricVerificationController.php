<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\FingerprintVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BiometricVerificationController extends Controller
{
    /**
     * Search student for verification
     */
    public function search(Request $request)
    {
        $request->validate([
            'roll_number' => 'required|string|size:5'
        ]);

        $student = Student::with(['test', 'college'])
            ->where('roll_number', $request->roll_number)
            ->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }

        // Get verification history for this student
        $verificationHistory = FingerprintVerification::where('student_id', $student->id)
            ->orderBy('verified_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($v) {
                return [
                    'verified_at' => $v->verified_at?->format('Y-m-d H:i:s'),
                    'is_matched' => $v->is_matched,
                    'match_score' => $v->match_score,
                    'result' => $v->result,
                ];
            });

        // Base response with ALL student data
        $response = [
            'roll_number' => $student->roll_number,
            'name' => $student->name,
            'father_name' => $student->father_name,
            'cnic' => $student->cnic,
            'gender' => $student->gender,
            'test_name' => $student->test?->name,
            'college_name' => $student->college?->name,
            
            // Read files from public directory and encode as base64
            'picture' => $this->getImageAsBase64($student->picture),
            'test_photo' => $this->getImageAsBase64($student->test_photo),
            
            // Seating information
            'venue' => $student->venue,
            'hall' => $student->hall,
            'zone' => $student->zone,
            'row' => $student->row,
            'seat' => $student->seat,
            
            'fingerprint_registered' => false,
            'verification_history' => $verificationHistory,
        ];

        // Check if fingerprint is registered
        if (!$student->fingerprint_template) {
            // Return 422 with FULL student data
            return response()->json([
                'message' => 'Student fingerprint not registered',
                'student' => $response
            ], 422);
        }

        // Fingerprint IS registered - add fingerprint data
        $response['fingerprint_registered'] = true;
        $response['fingerprint_template'] = $student->fingerprint_template; // Already base64
        $response['fingerprint_image'] = $this->getImageAsBase64($student->fingerprint_image);
        $response['fingerprint_registered_at'] = $student->fingerprint_registered_at?->format('Y-m-d H:i:s');
        $response['fingerprint_quality'] = $student->fingerprint_quality;

        return response()->json($response);
    }

    /**
     * Helper function to read image file and return as base64
     */
    private function getImageAsBase64($filePath)
    {
        if (!$filePath) {
            return null;
        }

        // Try public path first (most common for Laravel)
        $publicPath = public_path($filePath);
        if (file_exists($publicPath)) {
            return base64_encode(file_get_contents($publicPath));
        }

        // Try storage/app/public path
        $storagePath = storage_path('app/public/' . $filePath);
        if (file_exists($storagePath)) {
            return base64_encode(file_get_contents($storagePath));
        }

        // Try Laravel Storage facade
        if (Storage::disk('public')->exists($filePath)) {
            return base64_encode(Storage::disk('public')->get($filePath));
        }

        // Try default storage
        if (Storage::exists($filePath)) {
            return base64_encode(Storage::get($filePath));
        }

        // File not found
        return null;
    }

    /**
     * Save verification result (match or no match)
     */
    public function verify(Request $request)
    {
        $request->validate([
            'roll_number' => 'required|string|size:5',
            'is_matched' => 'required|boolean',
            'match_score' => 'required|integer|min:0|max:200',
            'captured_template' => 'required|string', // base64
            'captured_image' => 'nullable|string', // base64
            'capture_quality' => 'nullable|integer|min:0|max:100',
            'verified_by' => 'required|string',
            'device_info' => 'nullable|string',
            'failure_reason' => 'nullable|string',
        ]);

        // Find student
        $student = Student::where('roll_number', $request->roll_number)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Determine result
        $result = $request->is_matched ? 'matched' : 'rejected';

        // Create verification log
        $verification = FingerprintVerification::create([
            'roll_number' => $request->roll_number,
            'student_id' => $student->id,
            'test_id' => $student->test_id,
            'college_name' => $student->college_name,
            'is_matched' => $request->is_matched,
            'match_score' => $request->match_score,
            'result' => $result,
            'captured_template' => base64_decode($request->captured_template),
            'captured_image' => $request->captured_image ? base64_decode($request->captured_image) : null,
            'capture_quality' => $request->capture_quality,
            'verified_by' => $request->verified_by,
            'verified_at' => now(),
            'device_info' => $request->device_info,
            'failure_reason' => $request->failure_reason,
        ]);

        return response()->json([
            'message' => 'Verification logged successfully',
            'verification_id' => $verification->id,
            'result' => $result,
            'is_matched' => $request->is_matched,
            'match_score' => $request->match_score,
        ], 201);
    }

    /**
     * Get verification history for a student
     */
    public function history(Request $request)
    {
        $request->validate([
            'roll_number' => 'required|string|size:5'
        ]);

        $verifications = FingerprintVerification::where('roll_number', $request->roll_number)
            ->orderBy('verified_at', 'desc')
            ->get()
            ->map(function ($v) {
                return [
                    'id' => $v->id,
                    'is_matched' => $v->is_matched,
                    'match_score' => $v->match_score,
                    'result' => $v->result,
                    'capture_quality' => $v->capture_quality,
                    'verified_by' => $v->verified_by,
                    'verified_at' => $v->verified_at?->format('Y-m-d H:i:s'),
                    'device_info' => $v->device_info,
                    'failure_reason' => $v->failure_reason,
                ];
            });

        return response()->json($verifications);
    }

    /**
     * Get today's verification stats
     */
    public function stats(Request $request)
    {
        $today = today();

        $stats = [
            'total_verifications' => FingerprintVerification::whereDate('verified_at', $today)->count(),
            'total_matched' => FingerprintVerification::whereDate('verified_at', $today)
                ->where('is_matched', true)->count(),
            'total_rejected' => FingerprintVerification::whereDate('verified_at', $today)
                ->where('is_matched', false)->count(),
            'avg_match_score' => round(FingerprintVerification::whereDate('verified_at', $today)
                ->where('is_matched', true)
                ->avg('match_score'), 2),
            'last_verification' => FingerprintVerification::whereDate('verified_at', $today)
                ->latest('verified_at')
                ->first()?->verified_at?->format('H:i:s'),
        ];

        return response()->json($stats);
    }
}