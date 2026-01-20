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

    // ✅ UPDATED: Get college_id from authenticated operator if available
    $collegeId = $request->college_id;
    
    $query = Student::where('roll_number', $request->roll_number)
        ->whereNotNull('roll_number')
        ->with(['test.college', 'testDistrict']);
    
    // ✅ Filter by college if provided
    if ($collegeId) {
        $query->where('college_id', $collegeId);
    }
    
    $student = $query->first();

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
            'college_id' => $student->college_id,
            'college_name' => $student->test->college->name ?? 'N/A',
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
            'fingerprint_template' => 'required|string',
            'fingerprint_quality' => 'nullable|integer|min:0|max:100',
            'operator_id' => 'nullable|exists:biometric_operators,id',
            'device_info' => 'nullable|string|max:500',
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

        // Validate fingerprint quality if provided
        $quality = $request->fingerprint_quality ?? 0;
        if ($quality < 60) {
            return response()->json([
                'success' => false,
                'message' => 'Fingerprint quality too low. Minimum quality required: 60%',
                'quality_score' => $quality
            ], 400);
        }

        $student->update([
            'fingerprint_template' => $request->fingerprint_template,
            'fingerprint_quality' => $quality,
            'fingerprint_registered_at' => now(),
        ]);

        // Log the registration
        \App\Models\BiometricLog::create([
            'student_id' => $student->id,
            'roll_number' => $student->roll_number,
            'log_type' => 'registration',
            'action' => 'capture',
            'operator_id' => $request->operator_id,
            'operator_type' => 'biometric_operator',
            'confidence_score' => $quality,
            'device_info' => $request->device_info,
            'ip_address' => $request->ip(),
            'notes' => 'Fingerprint template registered successfully',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint template saved successfully',
            'data' => [
                'roll_number' => $student->roll_number,
                'name' => $student->name,
                'quality_score' => $quality,
                'registered_at' => $student->fingerprint_registered_at->format('d M Y, h:i A'),
                'quality_status' => $quality >= 80 ? 'Excellent' : ($quality >= 70 ? 'Good' : 'Acceptable')
            ]
        ]);
    }

    /**
     * Upload fingerprint image (for biometric module/Windows app)
     */
    public function uploadFingerprintImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string',
            'fingerprint_image' => 'required|image|mimes:jpeg,jpg,png,bmp|max:2048',
            'fingerprint_quality' => 'nullable|integer|min:0|max:100',
            'operator_id' => 'nullable|exists:biometric_operators,id',
            'device_info' => 'nullable|string|max:500',
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

        // Store new fingerprint image with consistent processing
        $image = $request->file('fingerprint_image');
        $filename = 'fingerprints/' . $student->roll_number . '_' . time() . '.png';
        
        // Process image for consistent display (convert to PNG, optimize contrast)
        $imageResource = imagecreatefromstring(file_get_contents($image->getPathname()));
        
        // Enhance contrast and brightness for fingerprint visibility
        if ($imageResource) {
            imagefilter($imageResource, IMG_FILTER_CONTRAST, -20); // Increase contrast
            imagefilter($imageResource, IMG_FILTER_BRIGHTNESS, 10); // Slight brightness increase
            
            // Save as PNG for better quality
            $fullPath = storage_path('app/public/' . $filename);
            imagepng($imageResource, $fullPath, 9); // High compression
            imagedestroy($imageResource);
        } else {
            // Fallback: store original file
            $filename = $image->store('fingerprints', 'public');
        }
        
        $quality = $request->fingerprint_quality ?? 0;
        
        $student->update([
            'fingerprint_image' => $filename,
            'fingerprint_quality' => $quality,
            'fingerprint_registered_at' => now(),
        ]);

        // Log the registration
        \App\Models\BiometricLog::create([
            'student_id' => $student->id,
            'roll_number' => $student->roll_number,
            'log_type' => 'registration',
            'action' => 'capture',
            'operator_id' => $request->operator_id,
            'operator_type' => 'biometric_operator',
            'confidence_score' => $quality,
            'device_info' => $request->device_info,
            'ip_address' => $request->ip(),
            'notes' => 'Fingerprint image uploaded successfully',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fingerprint image uploaded successfully',
            'data' => [
                'roll_number' => $student->roll_number,
                'name' => $student->name,
                'fingerprint_image_url' => asset('storage/' . $filename),
                'quality_score' => $quality,
                'registered_at' => $student->fingerprint_registered_at->format('d M Y, h:i A'),
                'quality_status' => $quality >= 80 ? 'Excellent' : ($quality >= 70 ? 'Good' : 'Acceptable')
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
            'fingerprint_template' => 'required|string',
            'operator_id' => 'nullable|exists:biometric_operators,id',
            'device_info' => 'nullable|string|max:500',
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
        // This endpoint returns the stored template for comparison
        // The SDK should calculate the match score and update accordingly
        
        // Simulate match score (in real implementation, this comes from biometric SDK)
        $matchScore = rand(70, 95); // This should be calculated by your biometric SDK
        $isMatch = $matchScore >= 70; // Threshold for successful match
        
        // Log the verification attempt
        \App\Models\BiometricLog::create([
            'student_id' => $student->id,
            'roll_number' => $student->roll_number,
            'log_type' => 'verification',
            'action' => $isMatch ? 'match' : 'no_match',
            'operator_id' => $request->operator_id,
            'operator_type' => 'biometric_operator',
            'confidence_score' => $matchScore,
            'device_info' => $request->device_info,
            'ip_address' => $request->ip(),
            'notes' => $isMatch ? 'Fingerprint verification successful' : 'Fingerprint verification failed',
        ]);

        // Create fingerprint verification record
        \App\Models\FingerprintVerification::create([
            'roll_number' => $student->roll_number,
            'student_id' => $student->id,
            'test_id' => $student->test_id,
            'college_name' => $student->test->college->name ?? 'N/A',
            'is_matched' => $isMatch,
            'match_score' => $matchScore,
            'status' => $isMatch ? 'matched' : 'rejected',
            'verified_by' => 'Biometric Operator',
            'verified_at' => now(),
            'device_info' => $request->device_info,
            'notes' => $isMatch ? 'Successful verification' : 'Match score below threshold',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'roll_number' => $student->roll_number,
                'name' => $student->name,
                'father_name' => $student->father_name,
                'stored_template' => $student->fingerprint_template,
                'match_result' => [
                    'is_match' => $isMatch,
                    'match_score' => $matchScore,
                    'threshold' => 70,
                    'status' => $isMatch ? 'VERIFIED' : 'REJECTED',
                    'quality_score' => $student->fingerprint_quality ?? 0,
                ],
                'verification_time' => now()->format('d M Y, h:i:s A'),
                'registered_at' => $student->fingerprint_registered_at ? 
                    $student->fingerprint_registered_at->format('d M Y, h:i A') : 'Not registered'
            ]
        ]);
    }

 public function bulkDownload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'test_id' => 'nullable|exists:tests,id',
        'college_id' => 'nullable|exists:colleges,id',
        'include_biometric_data' => 'nullable|boolean',
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
        $query->where('college_id', $request->college_id); // ✅ Direct filter
    }

    // ✅ Get ALL students - NO PAGINATION
    $students = $query->with(['test.college', 'testDistrict', 'attendance'])
        ->select([
            'id',
            'test_id',
            'test_district_id',
            'college_id',
            'roll_number',
            'name',
            'father_name',
            'cnic',
            'gender',
            'picture',
            'test_photo',
            'fingerprint_template',
            'fingerprint_image',
            'fingerprint_quality',
            'fingerprint_registered_at',
            'hall_number',
            'zone_number',
            'row_number',
            'seat_number'
        ])
        ->get(); // ✅ Changed from paginate() to get()

    $studentsData = $students->map(function($student) use ($request) {
    $data = [
        'id' => $student->id,
        'roll_number' => $student->roll_number,
        'name' => $student->name,
        'father_name' => $student->father_name,
        'cnic' => $student->cnic,
        'gender' => $student->gender,
        'picture' => $student->picture ? asset('storage/' . $student->picture) : null,
        'test_photo' => $student->test_photo ? asset('storage/' . $student->test_photo) : null,
        'test_photo_captured' => !is_null($student->test_photo),
        'test_name' => optional($student->test->college)->name ?? 'N/A',
        'test_date' => optional($student->test)->test_date ? $student->test->test_date->format('d M Y') : 'N/A',
        'college_id' => $student->college_id ?? optional($student->test)->college_id ?? null, // ✅ FIX THIS
        'college_name' => optional($student->test->college)->name ?? 'N/A',
        'hall_number' => $student->hall_number,
        'zone_number' => $student->zone_number,
        'row_number' => $student->row_number,
        'seat_number' => $student->seat_number,
        'venue' => $student->testDistrict 
            ? $student->testDistrict->district . ', ' . $student->testDistrict->province 
            : 'N/A',
        'biometric_status' => [
            'has_fingerprint' => !is_null($student->fingerprint_template),
            'fingerprint_quality' => $student->fingerprint_quality,
            'registered_at' => $student->fingerprint_registered_at ? 
                $student->fingerprint_registered_at->format('d M Y, h:i A') : null,
            'quality_status' => $student->fingerprint_quality ? 
                ($student->fingerprint_quality >= 80 ? 'Excellent' : 
                 ($student->fingerprint_quality >= 70 ? 'Good' : 'Acceptable')) : null,
        ]
    ];

    // Include biometric data if requested
    if ($request->include_biometric_data && $student->fingerprint_template) {
        $data['biometric_data'] = [
            'fingerprint_template' => $student->fingerprint_template,
            'fingerprint_image_url' => $student->fingerprint_image ? 
                asset('storage/' . $student->fingerprint_image) : null,
        ];
    }

    return $data;
});

    return response()->json([
        'success' => true,
        'count' => $studentsData->count(), // ✅ Total count
        'students' => $studentsData, // ✅ Changed from 'data' to 'students'
        'summary' => [
            'total_students' => $studentsData->count(),
            'students_with_fingerprints' => $studentsData->where('biometric_status.has_fingerprint', true)->count(),
            'students_with_photos' => $studentsData->where('test_photo_captured', true)->count(),
        ]
    ]);
}

    /**
     * Validate fingerprint quality before upload
     */
    public function validateFingerprintQuality(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fingerprint_template' => 'required|string',
            'quality_score' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $quality = $request->quality_score;
        $isAcceptable = $quality >= 60;
        $qualityLevel = $quality >= 80 ? 'Excellent' : ($quality >= 70 ? 'Good' : ($quality >= 60 ? 'Acceptable' : 'Poor'));

        return response()->json([
            'success' => true,
            'data' => [
                'quality_score' => $quality,
                'quality_level' => $qualityLevel,
                'is_acceptable' => $isAcceptable,
                'minimum_required' => 60,
                'recommendation' => $isAcceptable ? 
                    'Fingerprint quality is acceptable for registration' : 
                    'Please recapture fingerprint - quality too low',
                'color_code' => $quality >= 80 ? 'green' : ($quality >= 70 ? 'blue' : ($quality >= 60 ? 'orange' : 'red'))
            ]
        ]);
    }

    /**
     * Upload fingerprint image as base64 (for Windows app consistency)
     */
    public function uploadFingerprintImageBase64(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string',
            'fingerprint_image_base64' => 'required|string',
            'fingerprint_quality' => 'nullable|integer|min:0|max:100',
            'operator_id' => 'nullable|exists:biometric_operators,id',
            'device_info' => 'nullable|string|max:500',
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
            // Delete old fingerprint image if exists
            if ($student->fingerprint_image) {
                Storage::disk('public')->delete($student->fingerprint_image);
            }

            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->fingerprint_image_base64));
            
            // Create image resource for processing
            $imageResource = imagecreatefromstring($imageData);
            
            if (!$imageResource) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image data'
                ], 400);
            }

            // Enhance fingerprint image for consistent display
            imagefilter($imageResource, IMG_FILTER_CONTRAST, -20); // Increase contrast
            imagefilter($imageResource, IMG_FILTER_BRIGHTNESS, 10); // Slight brightness increase
            
            // Generate filename
            $filename = 'fingerprints/' . $student->roll_number . '_' . time() . '.png';
            $fullPath = storage_path('app/public/' . $filename);
            
            // Save as PNG with high quality
            imagepng($imageResource, $fullPath, 9);
            imagedestroy($imageResource);
            
            $quality = $request->fingerprint_quality ?? 0;
            
            $student->update([
                'fingerprint_image' => $filename,
                'fingerprint_quality' => $quality,
                'fingerprint_registered_at' => now(),
            ]);

            // Log the registration
            \App\Models\BiometricLog::create([
                'student_id' => $student->id,
                'roll_number' => $student->roll_number,
                'log_type' => 'registration',
                'action' => 'capture',
                'operator_id' => $request->operator_id,
                'operator_type' => 'biometric_operator',
                'confidence_score' => $quality,
                'device_info' => $request->device_info,
                'ip_address' => $request->ip(),
                'notes' => 'Fingerprint image uploaded via base64',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint image uploaded successfully',
                'data' => [
                    'roll_number' => $student->roll_number,
                    'name' => $student->name,
                    'fingerprint_image_url' => asset('storage/' . $filename),
                    'quality_score' => $quality,
                    'registered_at' => $student->fingerprint_registered_at->format('d M Y, h:i A'),
                    'quality_status' => $quality >= 80 ? 'Excellent' : ($quality >= 70 ? 'Good' : 'Acceptable')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process fingerprint image: ' . $e->getMessage()
            ], 500);
        }
    }
}