<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OfflineSync;
use App\Models\StudentAttendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OfflineSyncController extends Controller
{
    /**
     * Get sync status for a device
     */
    public function getSyncStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $stats = OfflineSync::getSyncStats($request->device_id);

        return response()->json([
            'success' => true,
            'data' => [
                'device_id' => $request->device_id,
                'sync_stats' => $stats,
                'needs_sync' => $stats['pending'] > 0,
                'last_sync_time' => $stats['last_sync'] ? 
                    $stats['last_sync']->format('d M Y, h:i A') : 'Never',
            ]
        ]);
    }

    /**
     * Queue data for offline sync
     */
    public function queueForSync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:100',
            'sync_type' => 'required|in:upload,download,update',
            'data_type' => 'required|in:attendance,fingerprint,photo,student_data',
            'record_id' => 'nullable|string|max:50',
            'sync_data' => 'required|array',
            'created_offline_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $syncRecord = OfflineSync::createSyncRecord(
            $request->device_id,
            $request->sync_type,
            $request->data_type,
            $request->record_id,
            $request->sync_data,
            $request->created_offline_at
        );

        return response()->json([
            'success' => true,
            'message' => 'Data queued for sync successfully',
            'data' => [
                'sync_id' => $syncRecord->id,
                'device_id' => $syncRecord->device_id,
                'data_type' => $syncRecord->data_type,
                'sync_status' => $syncRecord->sync_status,
            ]
        ]);
    }

    /**
     * Process pending sync records
     */
    public function processPendingSync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:100',
            'batch_size' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $batchSize = $request->batch_size ?? 20;
        
        $pendingRecords = OfflineSync::pending()
            ->byDevice($request->device_id)
            ->orderBy('created_offline_at')
            ->limit($batchSize)
            ->get();

        if ($pendingRecords->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No pending sync records',
                'data' => [
                    'processed' => 0,
                    'remaining' => 0,
                ]
            ]);
        }

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        DB::beginTransaction();
        
        try {
            foreach ($pendingRecords as $syncRecord) {
                $result = $this->processSyncRecord($syncRecord);
                $results[] = $result;
                
                if ($result['success']) {
                    $successCount++;
                    $syncRecord->markCompleted();
                } else {
                    $errorCount++;
                    $syncRecord->markFailed($result['message']);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Sync processing failed: ' . $e->getMessage()
            ], 500);
        }

        $remainingCount = OfflineSync::pending()->byDevice($request->device_id)->count();

        return response()->json([
            'success' => $errorCount === 0,
            'message' => "Processed {$successCount} successful, {$errorCount} failed",
            'data' => [
                'processed' => $successCount + $errorCount,
                'successful' => $successCount,
                'failed' => $errorCount,
                'remaining' => $remainingCount,
                'results' => $results,
            ]
        ]);
    }

    /**
     * Process individual sync record
     */
    private function processSyncRecord(OfflineSync $syncRecord)
    {
        try {
            switch ($syncRecord->data_type) {
                case 'attendance':
                    return $this->processAttendanceSync($syncRecord);
                    
                case 'fingerprint':
                    return $this->processFingerprintSync($syncRecord);
                    
                case 'photo':
                    return $this->processPhotoSync($syncRecord);
                    
                default:
                    return [
                        'success' => false,
                        'message' => 'Unknown data type: ' . $syncRecord->data_type
                    ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Processing error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process attendance sync
     */
    private function processAttendanceSync(OfflineSync $syncRecord)
    {
        $data = $syncRecord->sync_data;
        
        // Validate required fields
        if (!isset($data['roll_number']) || !isset($data['test_id']) || !isset($data['attendance_status'])) {
            return [
                'success' => false,
                'message' => 'Missing required attendance data'
            ];
        }

        // Check if student exists
        $student = Student::where('roll_number', $data['roll_number'])
            ->where('test_id', $data['test_id'])
            ->first();

        if (!$student) {
            return [
                'success' => false,
                'message' => 'Student not found'
            ];
        }

        // Check if attendance already exists
        $existing = StudentAttendance::where('roll_number', $data['roll_number'])
            ->where('test_id', $data['test_id'])
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'message' => 'Attendance already marked'
            ];
        }

        // Create attendance record
        StudentAttendance::create([
            'roll_number' => $data['roll_number'],
            'student_id' => $student->id,
            'test_id' => $data['test_id'],
            'attendance_status' => $data['attendance_status'],
            'marked_at' => $syncRecord->created_offline_at ?? now(),
            'marked_by' => $data['marked_by'] ?? 'Mobile App (Offline)',
            'device_info' => $data['device_info'] ?? null,
            'ip_address' => '0.0.0.0', // Offline record
            'notes' => ($data['notes'] ?? '') . ' [Synced from offline]',
        ]);

        return [
            'success' => true,
            'message' => 'Attendance synced successfully'
        ];
    }

    /**
     * Process fingerprint sync
     */
    private function processFingerprintSync(OfflineSync $syncRecord)
    {
        $data = $syncRecord->sync_data;
        
        if (!isset($data['roll_number']) || !isset($data['fingerprint_template'])) {
            return [
                'success' => false,
                'message' => 'Missing required fingerprint data'
            ];
        }

        $student = Student::where('roll_number', $data['roll_number'])->first();

        if (!$student) {
            return [
                'success' => false,
                'message' => 'Student not found'
            ];
        }

        $student->update([
            'fingerprint_template' => $data['fingerprint_template'],
            'fingerprint_quality' => $data['fingerprint_quality'] ?? null,
            'fingerprint_registered_at' => $syncRecord->created_offline_at ?? now(),
        ]);

        return [
            'success' => true,
            'message' => 'Fingerprint synced successfully'
        ];
    }

    /**
     * Process photo sync
     */
    private function processPhotoSync(OfflineSync $syncRecord)
    {
        $data = $syncRecord->sync_data;
        
        if (!isset($data['roll_number']) || !isset($data['photo_base64'])) {
            return [
                'success' => false,
                'message' => 'Missing required photo data'
            ];
        }

        $student = Student::where('roll_number', $data['roll_number'])->first();

        if (!$student) {
            return [
                'success' => false,
                'message' => 'Student not found'
            ];
        }

        try {
            // Decode and save photo
            $imageData = base64_decode($data['photo_base64']);
            $filename = 'test_photos/' . $data['roll_number'] . '_offline_' . time() . '.jpg';
            
            \Storage::disk('public')->put($filename, $imageData);
            
            $student->update([
                'test_photo' => $filename
            ]);

            return [
                'success' => true,
                'message' => 'Photo synced successfully'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Photo processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Clear completed sync records
     */
    public function clearCompletedSync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:100',
            'older_than_days' => 'nullable|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $olderThanDays = $request->older_than_days ?? 7;
        $cutoffDate = now()->subDays($olderThanDays);

        $deletedCount = OfflineSync::completed()
            ->byDevice($request->device_id)
            ->where('synced_at', '<', $cutoffDate)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$deletedCount} completed sync records",
            'data' => [
                'deleted_count' => $deletedCount,
                'cutoff_date' => $cutoffDate->format('d M Y, h:i A'),
            ]
        ]);
    }
}