<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentAttendanceController extends Controller
{
    /**
     * Get student info by roll number for attendance marking
     */
    public function getStudentInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string|max:10',
            'test_id' => 'required|exists:tests,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $student = Student::where('roll_number', $request->roll_number)
            ->where('test_id', $request->test_id)
            ->with(['test.college'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found with this roll number for the specified test'
            ], 404);
        }

        // Check if attendance already marked
        $attendance = StudentAttendance::where('roll_number', $request->roll_number)
            ->where('test_id', $request->test_id)
            ->first();

        // Get biometric status
        $biometricStatus = [
            'has_fingerprint' => !is_null($student->fingerprint_template),
            'has_photo' => !is_null($student->test_photo),
            'fingerprint_quality' => $student->fingerprint_quality,
            'registered_at' => $student->fingerprint_registered_at ? 
                $student->fingerprint_registered_at->format('d M Y, h:i A') : null,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'roll_number' => $student->roll_number,
                    'father_name' => $student->father_name,
                    'cnic' => $student->cnic,
                    'gender' => $student->gender ?? 'Not specified',
                    'picture' => $student->picture ? asset('storage/' . $student->picture) : null,
                    'test_photo' => $student->test_photo ? asset('storage/' . $student->test_photo) : null,
                    'hall_number' => $student->hall_number,
                    'seat_number' => $student->seat_number,
                    'college_name' => $student->test->college->name ?? 'N/A',
                    'test_date' => $student->test->test_date->format('d M Y'),
                ],
                'biometric_status' => $biometricStatus,
                'attendance' => $attendance ? [
                    'status' => $attendance->attendance_status,
                    'marked_at' => $attendance->marked_at->format('d M Y, h:i A'),
                    'marked_by' => $attendance->marked_by,
                    'notes' => $attendance->notes,
                ] : null,
                'already_marked' => $attendance !== null,
                'can_mark_attendance' => $attendance === null, // Only allow if not already marked
            ]
        ]);
    }

    /**
     * Mark student attendance
     */
    public function markAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string|max:10',
            'test_id' => 'required|exists:tests,id',
            'attendance_status' => 'required|in:present,absent',
            'marked_by' => 'nullable|string|max:255',
            'device_info' => 'nullable|string',
            'notes' => 'nullable|string',
            'location' => 'nullable|array',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // Verify student exists
        $student = Student::where('roll_number', $request->roll_number)
            ->where('test_id', $request->test_id)
            ->with(['test.college'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found with this roll number for the specified test'
            ], 404);
        }

        // Check if attendance already marked
        $existingAttendance = StudentAttendance::where('roll_number', $request->roll_number)
            ->where('test_id', $request->test_id)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already marked for this student',
                'data' => [
                    'existing_status' => $existingAttendance->attendance_status,
                    'marked_at' => $existingAttendance->marked_at->format('d M Y, h:i A'),
                    'marked_by' => $existingAttendance->marked_by,
                ]
            ], 409);
        }

        // Prepare notes with location if provided
        $notes = $request->notes ?? '';
        if ($request->location) {
            $locationNote = sprintf(
                'Location: %.6f, %.6f', 
                $request->location['latitude'], 
                $request->location['longitude']
            );
            $notes = $notes ? $notes . ' | ' . $locationNote : $locationNote;
        }

        // Mark attendance
        $attendance = StudentAttendance::markAttendance(
            $request->roll_number,
            $request->test_id,
            $request->attendance_status,
            $request->marked_by ?? 'Mobile App',
            $request->device_info,
            $request->ip(),
            $notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully',
            'data' => [
                'attendance' => [
                    'roll_number' => $attendance->roll_number,
                    'student_name' => $student->name,
                    'father_name' => $student->father_name,
                    'college_name' => $student->test->college->name ?? 'N/A',
                    'attendance_status' => $attendance->attendance_status,
                    'marked_at' => $attendance->marked_at->format('d M Y, h:i A'),
                    'marked_by' => $attendance->marked_by,
                    'notes' => $attendance->notes,
                ],
                'student_info' => [
                    'hall_number' => $student->hall_number,
                    'seat_number' => $student->seat_number,
                    'has_photo' => !is_null($student->test_photo),
                    'has_fingerprint' => !is_null($student->fingerprint_template),
                ]
            ]
        ]);
    }

    /**
     * Get attendance statistics for a test
     */
    public function getAttendanceStats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tests,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $stats = StudentAttendance::getAttendanceStats($request->test_id);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get attendance list for a test
     */
    public function getAttendanceList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|exists:tests,id',
            'status' => 'nullable|in:present,absent',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $query = StudentAttendance::with('student')
            ->where('test_id', $request->test_id);

        if ($request->status) {
            $query->where('attendance_status', $request->status);
        }

        $perPage = $request->per_page ?? 50;
        $attendance = $query->orderBy('roll_number')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    /**
     * Bulk mark attendance (for offline sync)
     */
    public function bulkMarkAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attendance_records' => 'required|array|min:1|max:100',
            'attendance_records.*.roll_number' => 'required|string|max:10',
            'attendance_records.*.test_id' => 'required|exists:tests,id',
            'attendance_records.*.attendance_status' => 'required|in:present,absent',
            'attendance_records.*.marked_by' => 'nullable|string|max:255',
            'attendance_records.*.device_info' => 'nullable|string',
            'attendance_records.*.notes' => 'nullable|string',
            'attendance_records.*.offline_marked_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($request->attendance_records as $record) {
            try {
                // Check if student exists
                $student = Student::where('roll_number', $record['roll_number'])
                    ->where('test_id', $record['test_id'])
                    ->first();

                if (!$student) {
                    $results[] = [
                        'roll_number' => $record['roll_number'],
                        'success' => false,
                        'message' => 'Student not found'
                    ];
                    $errorCount++;
                    continue;
                }

                // Check if already marked
                $existing = StudentAttendance::where('roll_number', $record['roll_number'])
                    ->where('test_id', $record['test_id'])
                    ->first();

                if ($existing) {
                    $results[] = [
                        'roll_number' => $record['roll_number'],
                        'success' => false,
                        'message' => 'Attendance already marked',
                        'existing_status' => $existing->attendance_status
                    ];
                    $errorCount++;
                    continue;
                }

                // Mark attendance
                $attendance = StudentAttendance::create([
                    'roll_number' => $record['roll_number'],
                    'student_id' => $student->id,
                    'test_id' => $record['test_id'],
                    'attendance_status' => $record['attendance_status'],
                    'marked_at' => $record['offline_marked_at'] ?? now(),
                    'marked_by' => $record['marked_by'] ?? 'Mobile App (Offline)',
                    'device_info' => $record['device_info'],
                    'ip_address' => $request->ip(),
                    'notes' => ($record['notes'] ?? '') . ' [Synced from offline]',
                ]);

                $results[] = [
                    'roll_number' => $record['roll_number'],
                    'success' => true,
                    'message' => 'Attendance marked successfully',
                    'status' => $attendance->attendance_status
                ];
                $successCount++;

            } catch (\Exception $e) {
                $results[] = [
                    'roll_number' => $record['roll_number'],
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
                $errorCount++;
            }
        }

        return response()->json([
            'success' => $errorCount === 0,
            'message' => "Processed {$successCount} successful, {$errorCount} failed",
            'summary' => [
                'total_processed' => count($request->attendance_records),
                'successful' => $successCount,
                'failed' => $errorCount,
            ],
            'results' => $results
        ]);
    }

    /**
     * Update attendance status (for corrections)
     */
    public function updateAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string|max:10',
            'test_id' => 'required|exists:tests,id',
            'attendance_status' => 'required|in:present,absent',
            'updated_by' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $attendance = StudentAttendance::where('roll_number', $request->roll_number)
            ->where('test_id', $request->test_id)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found'
            ], 404);
        }

        $oldStatus = $attendance->attendance_status;
        
        $attendance->update([
            'attendance_status' => $request->attendance_status,
            'notes' => ($attendance->notes ?? '') . ' | Updated from ' . $oldStatus . ' to ' . $request->attendance_status . 
                       ($request->reason ? ' (Reason: ' . $request->reason . ')' : '') . 
                       ' by ' . ($request->updated_by ?? 'Mobile App') . ' at ' . now()->format('d M Y, h:i A'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'data' => [
                'roll_number' => $attendance->roll_number,
                'old_status' => $oldStatus,
                'new_status' => $attendance->attendance_status,
                'updated_at' => now()->format('d M Y, h:i A'),
            ]
        ]);
    }
}