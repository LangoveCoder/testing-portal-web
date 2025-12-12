<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Student;
use App\Models\TestVenue;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SeatingPlanController extends Controller
{
    /**
     * Show seating plans index
     */
    public function index()
    {
        $tests = Test::with('college')
            ->where('roll_numbers_generated', true)
            ->orderBy('test_date', 'desc')
            ->get();
        
        return view('super_admin.seating_plans.index', compact('tests'));
    }

    /**
     * Show seating plan for a test
     */
    public function show(Test $test)
    {
        if (!$test->roll_numbers_generated) {
            return redirect()->route('super-admin.seating-plans.index')
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
                    'halls' => $this->organizeByHalls($students, $venue)
                ];
            }
        }

        return view('super_admin.seating_plans.show', compact('test', 'venueData'));
    }

    /**
     * Download seating plan PDF
     */
    public function download(Test $test)
    {
        if (!$test->roll_numbers_generated) {
            return redirect()->route('super-admin.seating-plans.index')
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
                    'halls' => $this->organizeByHalls($students, $venue)
                ];
            }
        }

        $pdf = PDF::loadView('pdfs.seating-plan', compact('test', 'venueData'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Seating_Plan_' . $test->test_name . '.pdf');
    }

    /**
     * Organize students by halls
     */
    private function organizeByHalls($students, $venue)
    {
        $halls = [];
        
        foreach ($students as $student) {
            $hallNum = $student->hall_number;
            $zoneNum = $student->zone_number;
            $rowNum = $student->row_number;
            $seatNum = $student->seat_number;
            
            if (!isset($halls[$hallNum])) {
                $halls[$hallNum] = [];
            }
            
            if (!isset($halls[$hallNum][$zoneNum])) {
                $halls[$hallNum][$zoneNum] = [];
            }
            
            if (!isset($halls[$hallNum][$zoneNum][$rowNum])) {
                $halls[$hallNum][$zoneNum][$rowNum] = [];
            }
            
            $halls[$hallNum][$zoneNum][$rowNum][$seatNum] = $student;
        }
        
        return $halls;
    }
}