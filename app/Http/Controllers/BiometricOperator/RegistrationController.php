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
        $searchTerm = $request->input('search_term');
        
        if (!$searchTerm) {
            return response()->json([
                'success' => false,
                'message' => 'Search term is required'
            ], 400);
        }

        // Search by roll number or CNIC
        $student = Student::where('roll_number', $searchTerm)
            ->orWhere('cnic', $searchTerm)
            ->with(['test.college'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found with this roll number or CNIC'
            ], 404);
        }

        // Check if fingerprint already registered
        $fingerprintRegistered = !empty($student->fingerprint_template);

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
                'test_id' => $student->test_id,
                'test_name' => $student->test->college->name ?? 'N/A',
                'venue' => $student->test->venue ?? 'N/A',
                'hall' => $student->hall_number ?? 'N/A',
                'zone' => $student->zone_number ?? 'N/A',
                'row' => $student->row_number ?? 'N/A',
                'seat' => $student->seat_number ?? 'N/A',
                'fingerprint_template' => $student->fingerprint_template,
                'fingerprint_image' => $student->fingerprint_image ? asset('storage/' . $student->fingerprint_image) : null,
                'fingerprint_registered' => $fingerprintRegistered,
                'last_registration' => $student->fingerprint_registered_at ? $student->fingerprint_registered_at->format('d M Y, h:i A') : null
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
            'fingerprint_image' => 'required|string', // Base64 image
        ]);

        $student = Student::find($request->student_id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        try {
            // Save fingerprint template
            $student->fingerprint_template = $request->fingerprint_template;

            // Save fingerprint image (base64)
            if ($request->filled('fingerprint_image')) {
                // Check if image is already base64 encoded with data:image prefix
                $imageData = $request->fingerprint_image;
                
                if (strpos($imageData, 'data:image') === 0) {
                    // Extract base64 part
                    $imageData = explode(',', $imageData)[1];
                }
                
                // Decode base64
                $decodedImage = base64_decode($imageData);
                
                // Delete old fingerprint image if exists
                if ($student->fingerprint_image && Storage::disk('public')->exists($student->fingerprint_image)) {
                    Storage::disk('public')->delete($student->fingerprint_image);
                }
                
                // Generate unique filename
                $filename = 'fingerprints/' . $student->roll_number . '_' . time() . '.png';
                
                // Process image: invert colors + adjust brightness/contrast
                try {
                    // Try to load image (handle both PNG and BMP formats)
                    $image = @imagecreatefromstring($decodedImage);
                    
                    if ($image !== false) {
                        // Invert colors (white fingerprint on black → black on white)
                        imagefilter($image, IMG_FILTER_NEGATE);
                        
                        // Increase brightness to make it visible
                        imagefilter($image, IMG_FILTER_BRIGHTNESS, 80);
                        
                        // Increase contrast for clarity
                        imagefilter($image, IMG_FILTER_CONTRAST, -30);
                        
                        ob_start();
                        imagepng($image);
                        $processedImage = ob_get_clean();
                        imagedestroy($image);
                        
                        Storage::disk('public')->put($filename, $processedImage);
                    } else {
                        // Fallback: store original if processing fails
                        Storage::disk('public')->put($filename, $decodedImage);
                    }
                } catch (\Exception $e) {
                    // Fallback: store original if processing fails
                    Storage::disk('public')->put($filename, $decodedImage);
                }
                
                $student->fingerprint_image = $filename;
            }

            $student->fingerprint_registered_at = now();
            $student->save();

            // Get authenticated operator if exists
            $operator = Auth::guard('biometric_operator')->user();

            // Log the registration
            BiometricLog::create([
                'student_id' => $student->id,
                'roll_number' => $student->roll_number,
                'action' => 'registration',
                'match_result' => null,
                'confidence_score' => null,
                'performed_by' => $operator ? $operator->name : 'System',
                'performed_by_type' => $operator ? 'biometric_operator' : 'web',
                'performed_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fingerprint saved successfully',
                'data' => [
                    'name' => $student->name,
                    'roll_number' => $student->roll_number
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
        
        $logs = BiometricLog::where('performed_by', $operator->name)
            ->where('performed_by_type', 'biometric_operator')
            ->where('action', 'registration')
            ->with('student')
            ->orderBy('performed_at', 'desc')
            ->paginate(50);

        return view('biometric_operator.registration.history', compact('logs'));
    }
}