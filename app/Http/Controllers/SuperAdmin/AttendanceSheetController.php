<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Student;
use App\Models\TestVenue;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceSheetController extends Controller
{
    /**
     * Show attendance sheets index
     */
    public function index()
    {
        $tests = Test::with('college')
            ->where('roll_numbers_generated', true)
            ->orderBy('test_date', 'desc')
            ->get();
        
        return view('super_admin.attendance_sheets.index', compact('tests'));
    }

    /**
     * Show attendance sheet for a test
     */
    public function show(Test $test)
    {
        if (!$test->roll_numbers_generated) {
            return redirect()->route('super-admin.attendance-sheets.index')
                ->with('error', 'Roll numbers must be generated first.');
        }

        $venues = TestVenue::where('test_id', $test->id)
            ->with(['testDistrict'])
            ->get();

        $venueData = [];
        
        foreach ($venues as $venue) {
            $students = Student::where('test_id', $test->id)
                ->where('test_district_id', $venue->test_district_id)
                ->whereNotNull('roll_number')
                ->orderBy('hall_number')
                ->orderBy('zone_number')
                ->orderBy('row_number')
                ->orderBy('seat_number')
                ->get();

            if ($students->isNotEmpty()) {
                $venueData[] = [
                    'venue' => $venue,
                    'students' => $students,
                    'halls' => $this->organizeByHalls($students)
                ];
            }
        }

        return view('super_admin.attendance_sheets.show', compact('test', 'venueData'));
    }

    /**
     * Download attendance sheet PDF
     */
    public function download(Test $test)
    {
        if (!$test->roll_numbers_generated) {
            return redirect()->route('super-admin.attendance-sheets.index')
                ->with('error', 'Roll numbers must be generated first.');
        }

        $venues = TestVenue::where('test_id', $test->id)
            ->with(['testDistrict'])
            ->get();

        $venueData = [];
        
        foreach ($venues as $venue) {
            $students = Student::where('test_id', $test->id)
                ->where('test_district_id', $venue->test_district_id)
                ->whereNotNull('roll_number')
                ->orderBy('hall_number')
                ->orderBy('zone_number')
                ->orderBy('row_number')
                ->orderBy('seat_number')
                ->get();

            if ($students->isNotEmpty()) {
                $venueData[] = [
                    'venue' => $venue,
                    'students' => $students,
                    'halls' => $this->organizeByHalls($students)
                ];
            }
        }

        $pdf = PDF::loadView('pdfs.attendance-sheet', compact('test', 'venueData'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Attendance_Sheet_' . $test->test_name . '.pdf');
    }

    /**
     * Organize students by halls
     */
    private function organizeByHalls($students)
    {
        $halls = [];
        
        foreach ($students as $student) {
            $hallNum = $student->hall_number;
            
            if (!isset($halls[$hallNum])) {
                $halls[$hallNum] = [];
            }
            
            $halls[$hallNum][] = $student;
        }
        
        return $halls;
    }
}