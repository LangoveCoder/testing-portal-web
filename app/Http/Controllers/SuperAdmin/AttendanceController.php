<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\StudentAttendance;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display attendance status page
     */
    public function index(Request $request)
    {
        $tests = Test::with('college')
            ->where('test_date', '>=', now()->subDays(30))
            ->orderBy('test_date', 'desc')
            ->get();

        $selectedTest = null;
        $attendanceData = null;
        $stats = null;

        if ($request->test_id) {
            $selectedTest = Test::with('college')->find($request->test_id);
            
            if ($selectedTest) {
                // Get attendance statistics
                $stats = StudentAttendance::getAttendanceStats($request->test_id);
                
                // Get attendance data with student info
                $attendanceQuery = StudentAttendance::with('student')
                    ->where('test_id', $request->test_id);
                
                // Filter by status if provided
                if ($request->status && in_array($request->status, ['present', 'absent'])) {
                    $attendanceQuery->where('attendance_status', $request->status);
                }
                
                // Search by roll number or name if provided
                if ($request->search) {
                    $attendanceQuery->where(function($q) use ($request) {
                        $q->where('roll_number', 'like', '%' . $request->search . '%')
                          ->orWhereHas('student', function($sq) use ($request) {
                              $sq->where('name', 'like', '%' . $request->search . '%');
                          });
                    });
                }
                
                $attendanceData = $attendanceQuery->orderBy('roll_number')->paginate(50);
            }
        }

        return view('super_admin.attendance.index', compact(
            'tests', 
            'selectedTest', 
            'attendanceData', 
            'stats'
        ));
    }

    /**
     * Export attendance data
     */
    public function export(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'format' => 'required|in:csv,excel',
        ]);

        $test = Test::with('college')->find($request->test_id);
        $attendance = StudentAttendance::with('student')
            ->where('test_id', $request->test_id)
            ->orderBy('roll_number')
            ->get();

        if ($request->format === 'csv') {
            return $this->exportToCsv($attendance, $test);
        }

        // For future Excel export implementation
        return back()->with('error', 'Excel export not implemented yet');
    }

    private function exportToCsv($attendance, $test)
    {
        $filename = 'attendance_' . $test->college->name . '_' . $test->test_date->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendance) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Roll Number',
                'Student Name',
                'Father Name',
                'CNIC',
                'Attendance Status',
                'Marked At',
                'Marked By',
                'Notes'
            ]);

            // CSV data
            foreach ($attendance as $record) {
                fputcsv($file, [
                    $record->roll_number,
                    $record->student ? $record->student->name : 'N/A',
                    $record->student ? $record->student->father_name : 'N/A',
                    $record->student ? $record->student->cnic : 'N/A',
                    ucfirst($record->attendance_status),
                    $record->marked_at ? $record->marked_at->format('Y-m-d H:i:s') : 'N/A',
                    $record->marked_by ?? 'N/A',
                    $record->notes ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}