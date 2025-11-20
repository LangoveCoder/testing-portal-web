<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Student;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    // Show reports page
    public function index()
    {
        $college = Auth::guard('college')->user();
        
        $tests = Test::where('college_id', $college->id)
            ->orderBy('test_date', 'desc')
            ->get();
        
        return view('college.reports.index', compact('tests'));
    }
    
    // Download student list as Excel
    public function downloadStudentList(Request $request)
    {
        $college = Auth::guard('college')->user();
        
        $query = Student::whereHas('test', function($q) use ($college) {
            $q->where('college_id', $college->id);
        })->with(['test', 'testDistrict']);
        
        // Filter by test if provided
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->test_id);
        }
        
        $students = $query->orderBy('created_at', 'desc')->get();
        
        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $headers = [
            'Registration ID', 'Name', 'Father Name', 'CNIC', 'Gender', 
            'Religion', 'Date of Birth', 'District', 'Test District', 
            'Roll Number', 'Book Color', 'Hall', 'Zone', 'Row', 'Seat'
        ];
        $sheet->fromArray($headers, null, 'A1');
        
        // Style header
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        ]);
        
        // Data
        $row = 2;
        foreach ($students as $student) {
            $sheet->fromArray([
                $student->registration_id,
                $student->name,
                $student->father_name,
                $student->cnic,
                $student->gender,
                $student->religion,
                $student->date_of_birth,
                $student->district,
                $student->testDistrict->district ?? 'N/A',
                $student->roll_number ?? 'Not Generated',
                $student->book_color ?? 'N/A',
                $student->hall_number ?? 'N/A',
                $student->zone_number ?? 'N/A',
                $student->row_number ?? 'N/A',
                $student->seat_number ?? 'N/A',
            ], null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Save and download
        $filename = $college->code . '_students_' . date('Y-m-d') . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);
        
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
    
    // Download results report as Excel
    public function downloadResultReport(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id'
        ]);
        
        $college = Auth::guard('college')->user();
        $test = Test::findOrFail($request->test_id);
        
        // Check access
        if ($test->college_id != $college->id) {
            return back()->with('error', 'Unauthorized access.');
        }
        
        // Get published results
        $results = Result::where('test_id', $test->id)
            ->where('is_published', true)
            ->with('student')
            ->orderBy('marks', 'desc')
            ->get();
        
        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers based on test mode
        $headers = ['Roll Number', 'Name', 'CNIC', 'Book Color'];
        
        if ($test->test_mode == 'mode_1') {
            $headers = array_merge($headers, [
                'English (Obj)', 'Urdu (Obj)', 'Math (Obj)', 'Science (Obj)',
                'English (Subj)', 'Urdu (Subj)', 'Math (Subj)', 'Science (Subj)',
                'Total Marks'
            ]);
        } elseif ($test->test_mode == 'mode_2') {
            $headers = array_merge($headers, [
                'English', 'Urdu', 'Math', 'Science', 'Total Marks'
            ]);
        } else {
            $headers = array_merge($headers, ['Total Marks']);
        }
        
        $sheet->fromArray($headers, null, 'A1');
        
        // Style header
        $lastCol = chr(65 + count($headers) - 1);
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        ]);
        
        // Data
        $row = 2;
        foreach ($results as $result) {
            $data = [
                $result->roll_number,
                $result->student->name ?? 'N/A',
                $result->student->cnic ?? 'N/A',
                $result->book_color
            ];
            
            if ($test->test_mode == 'mode_1') {
                $data = array_merge($data, [
                    $result->english_obj, $result->urdu_obj, $result->math_obj, $result->science_obj,
                    $result->english_subj, $result->urdu_subj, $result->math_subj, $result->science_subj,
                    $result->marks
                ]);
            } elseif ($test->test_mode == 'mode_2') {
                $data = array_merge($data, [
                    $result->english, $result->urdu, $result->math, $result->science, $result->marks
                ]);
            } else {
                $data = array_merge($data, [$result->marks]);
            }
            
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Save and download
        $filename = $college->code . '_results_' . $test->test_date->format('Y-m-d') . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);
        
        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}