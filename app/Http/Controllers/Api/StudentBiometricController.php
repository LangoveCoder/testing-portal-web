<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Test;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StudentBiometricController extends Controller
{
    /**
     * Get list of active colleges with their active tests
     */
    public function getActiveColleges()
{
    try {
        $colleges = College::where('is_active', true)
            ->select('id', 'name', 'district', 'province')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $colleges
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
}

    /**
     * Get student info by roll number (for Android app preview before photo capture)
     */
    public function getStudentInfo(Request $request)
{
    $validator = Validator::make($request->all(), [
        'roll_number' => 'required|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    $student = Student::where('roll_number', $request->roll_number)
        ->whereNotNull('roll_number')
        ->with(['test.college', 'testDistrict'])
        ->first();

    if (!$student) {
        return response()->json([
            'success' => false,
            'message' => 'Student not found'
        ], 404);
    }

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
            'test_photo' => $student->test_photo ? asset('storage/' . $student->test_photo) : null,
            'test_photo_captured' => !is_null($student->test_photo),
            'test_name' => $student->test->college->name ?? 'N/A',
            'test_date' => $student->test->test_date->format('d M Y'),
            'college_id' => $student->test->college_id,  // ADD THIS
            'college_name' => $student->test->college->name ?? 'N/A',  // ADD THIS
            'hall_number' => $student->hall_number,
            'zone_number' => $student->zone_number,
            'row_number' => $student->row_number,
            'seat_number' => $student->seat_number,
            'venue' => $student->testDistrict 
                ? $student->testDistrict->district . ', ' . $student->testDistrict->province 
                : 'N/A'
        ]
    ]);
}

    /**
     * Upload test photo (for Android app after camera capture)
     */
    public function uploadTestPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string',
            'test_photo' => 'required|image|mimes:jpeg,jpg,png|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::where('roll_number', $request->roll_number)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Delete old test photo if exists
        if ($student->test_photo) {
            Storage::disk('public')->delete($student->test_photo);
        }

        // Store new test photo
        $path = $request->file('test_photo')->store('test_photos', 'public');
        
        $student->update([
            'test_photo' => $path
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test photo uploaded successfully',
            'data' => [
                'roll_number' => $student->roll_number,
                'name' => $student->name,
                'test_photo_url' => asset('storage/' . $path),
                'uploaded_at' => now()->format('d M Y, h:i A')
            ]
        ]);
    }

    /**
     * Upload test photo as base64 (alternative for Android app)
     */
    public function uploadTestPhotoBase64(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string',
            'test_photo_base64' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::where('roll_number', $request->roll_number)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        try {
            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->test_photo_base64));
            
            // Generate unique filename
            $filename = 'test_photos/' . $student->roll_number . '_' . time() . '.jpg';
            
            // Delete old test photo if exists
            if ($student->test_photo) {
                Storage::disk('public')->delete($student->test_photo);
            }
            
            // Store new photo
            Storage::disk('public')->put($filename, $imageData);
            
            $student->update([
                'test_photo' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test photo uploaded successfully',
                'data' => [
                    'roll_number' => $student->roll_number,
                    'name' => $student->name,
                    'test_photo_url' => asset('storage/' . $filename),
                    'uploaded_at' => now()->format('d M Y, h:i A')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload fingerprint template (for biometric module/Windows app)
     */
    public function uploadFingerprintTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string',
            'fingerprint_template' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::where('roll_number', $request->roll_number)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $student->update([
            'fingerprint_template' => $request->fingerprint_template
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint template saved successfully'
        ]);
    }

    /**
     * Upload fingerprint image (for biometric module/Windows app)
     */
    public function uploadFingerprintImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string',
            'fingerprint_image' => 'required|image|mimes:jpeg,jpg,png,bmp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::where('roll_number', $request->roll_number)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Delete old fingerprint image if exists
        if ($student->fingerprint_image) {
            Storage::disk('public')->delete($student->fingerprint_image);
        }

        // Store new fingerprint image
        $path = $request->file('fingerprint_image')->store('fingerprints', 'public');
        
        $student->update([
            'fingerprint_image' => $path
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint image uploaded successfully',
            'data' => [
                'fingerprint_image_url' => asset('storage/' . $path)
            ]
        ]);
    }

    /**
     * Verify fingerprint template (for biometric module/Windows app)
     */
    public function verifyFingerprint(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string',
            'fingerprint_template' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::where('roll_number', $request->roll_number)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        if (!$student->fingerprint_template) {
            return response()->json([
                'success' => false,
                'message' => 'No fingerprint template registered for this student'
            ], 404);
        }

        // Note: Actual fingerprint matching should be done by the biometric SDK
        // This endpoint just returns the stored template for comparison
        return response()->json([
            'success' => true,
            'data' => [
                'roll_number' => $student->roll_number,
                'name' => $student->name,
                'stored_template' => $student->fingerprint_template,
                'match' => false // SDK should update this after matching
            ]
        ]);
    }

    public function bulkDownload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'test_id' => 'nullable|exists:tests,id',
        'college_id' => 'nullable|exists:colleges,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Build query
    $query = Student::whereNotNull('roll_number');

    // Filter by test if provided
    if ($request->has('test_id')) {
        $query->where('test_id', $request->test_id);
    }

    // Filter by college if provided
    if ($request->has('college_id')) {
        $query->whereHas('test', function($q) use ($request) {
            $q->where('college_id', $request->college_id);
        });
    }

    // Get students with relationships
    $students = $query->with(['test.college', 'testDistrict'])
        ->select([
            'id',
            'test_id',
            'test_district_id',
            'roll_number',
            'name',
            'father_name',
            'cnic',
            'gender',
            'picture',
            'test_photo',
            'hall_number',
            'zone_number',
            'row_number',
            'seat_number'
        ])
        ->get()
        ->map(function($student) {
            return [
                'id' => $student->id,
                'roll_number' => $student->roll_number,
                'name' => $student->name,
                'father_name' => $student->father_name,
                'cnic' => $student->cnic,
                'gender' => $student->gender,
                'picture' => $student->picture ? asset('storage/' . $student->picture) : null,
                'test_photo' => $student->test_photo ? asset('storage/' . $student->test_photo) : null,
                'test_photo_captured' => !is_null($student->test_photo),
                'test_name' => $student->test->college->name ?? 'N/A',
                'test_date' => $student->test->test_date->format('d M Y'),
                'hall_number' => $student->hall_number,
                'zone_number' => $student->zone_number,
                'row_number' => $student->row_number,
                'seat_number' => $student->seat_number,
                'venue' => $student->testDistrict 
                    ? $student->testDistrict->district . ', ' . $student->testDistrict->province 
                    : 'N/A'
            ];
        });

    return response()->json([
        'success' => true,
        'count' => $students->count(),
        'data' => $students
    ]);
}
}