<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Student;
use App\Models\Result;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResultController extends Controller
{
    // Show all tests for result management
    public function index()
    {
        $tests = Test::with(['college', 'students'])
            ->withCount('students')
            ->orderBy('test_date', 'desc')
            ->get();
        
        return view('super_admin.results.index', compact('tests'));
    }

    // Show upload form for specific test
    public function create(Test $test)
    {
        $test->load(['college', 'students']);
        
        // Check if roll numbers are generated
        if (!$test->roll_numbers_generated) {
            return redirect()->route('super-admin.results.index')
                ->with('error', 'Roll numbers must be generated before uploading results.');
        }
        
        return view('super_admin.results.create', compact('test'));
    }

    // Upload and process Excel file
    public function store(Request $request, Test $test)
    {
        $request->validate([
            'result_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('result_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header
            $headers = array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $warnings = [];

            // Determine test mode and expected columns
            $expectedColumns = $this->getExpectedColumns($test->test_mode);
            
            // Validate header
            if (!$this->validateHeaders($headers, $expectedColumns)) {
                return back()->with('error', 'Invalid Excel format. Expected columns: ' . implode(', ', $expectedColumns));
            }

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Extract data based on test mode
                $rollNumber = isset($row[0]) ? trim($row[0]) : '';
                $uploadedBookColor = isset($row[1]) ? trim($row[1]) : '';

                if (empty($rollNumber)) {
                    $errors[] = "Row {$rowNumber}: Roll number is required";
                    $errorCount++;
                    continue;
                }

                if (empty($uploadedBookColor)) {
                    $errors[] = "Row {$rowNumber}: Book color is required";
                    $errorCount++;
                    continue;
                }

                // Find student by roll number
                $student = Student::where('roll_number', $rollNumber)
                    ->where('test_id', $test->id)
                    ->first();

                if (!$student) {
                    $errors[] = "Row {$rowNumber}: Roll number {$rollNumber} not found in system";
                    $errorCount++;
                    continue;
                }

                // ===== CRITICAL: VALIDATE BOOK COLOR MATCH =====
                if (strcasecmp($uploadedBookColor, $student->book_color) !== 0) {
                    $errors[] = "Row {$rowNumber}: Book color mismatch! Roll {$rollNumber} was assigned '{$student->book_color}' but uploaded as '{$uploadedBookColor}'";
                    $errorCount++;
                    continue;
                }

                // Check if result already exists
                if (Result::where('student_id', $student->id)->exists()) {
                    $warnings[] = "Row {$rowNumber}: Result for roll {$rollNumber} already exists (will be updated)";
                }

                // Parse marks based on test mode
                $resultData = [
                    'test_id' => $test->id,
                    'student_id' => $student->id,
                    'roll_number' => $rollNumber,
                    'book_color' => $student->book_color, // Use student's assigned color
                    'total_marks' => $test->total_marks,
                    'is_published' => false,
                ];

                if ($test->test_mode === 'mode_1') {
                    // MCQ + Subjective (8 subject columns - NO TOTAL COLUMN)
                    $resultData['english_obj'] = isset($row[2]) ? (int)$row[2] : 0;
                    $resultData['urdu_obj'] = isset($row[3]) ? (int)$row[3] : 0;
                    $resultData['math_obj'] = isset($row[4]) ? (int)$row[4] : 0;
                    $resultData['science_obj'] = isset($row[5]) ? (int)$row[5] : 0;
                    $resultData['english_subj'] = isset($row[6]) ? (int)$row[6] : 0;
                    $resultData['urdu_subj'] = isset($row[7]) ? (int)$row[7] : 0;
                    $resultData['math_subj'] = isset($row[8]) ? (int)$row[8] : 0;
                    $resultData['science_subj'] = isset($row[9]) ? (int)$row[9] : 0;
                    
                    // AUTO-CALCULATE TOTAL
                    $resultData['marks'] = $resultData['english_obj'] + $resultData['urdu_obj'] + 
                                          $resultData['math_obj'] + $resultData['science_obj'] +
                                          $resultData['english_subj'] + $resultData['urdu_subj'] + 
                                          $resultData['math_subj'] + $resultData['science_subj'];
                    
                    // Calculate totals per subject
                    $resultData['english'] = $resultData['english_obj'] + $resultData['english_subj'];
                    $resultData['urdu'] = $resultData['urdu_obj'] + $resultData['urdu_subj'];
                    $resultData['math'] = $resultData['math_obj'] + $resultData['math_subj'];
                    $resultData['science'] = $resultData['science_obj'] + $resultData['science_subj'];
                    
                } elseif ($test->test_mode === 'mode_2') {
                    // MCQ Only (4 subject columns - NO TOTAL COLUMN)
                    $resultData['english'] = isset($row[2]) ? (int)$row[2] : 0;
                    $resultData['urdu'] = isset($row[3]) ? (int)$row[3] : 0;
                    $resultData['math'] = isset($row[4]) ? (int)$row[4] : 0;
                    $resultData['science'] = isset($row[5]) ? (int)$row[5] : 0;
                    
                    // AUTO-CALCULATE TOTAL
                    $resultData['marks'] = $resultData['english'] + $resultData['urdu'] + 
                                          $resultData['math'] + $resultData['science'];
                    
                } else {
                    // Mode 3 - General (only total - user provides this)
                    $resultData['marks'] = isset($row[2]) ? (int)$row[2] : 0;
                }

                // Validate marks don't exceed total
                if ($resultData['marks'] > $test->total_marks) {
                    $errors[] = "Row {$rowNumber}: Total marks ({$resultData['marks']}) exceed maximum ({$test->total_marks})";
                    $errorCount++;
                    continue;
                }

                // Update or create result
                Result::updateOrCreate(
                    ['student_id' => $student->id],
                    $resultData
                );

                $successCount++;
            }

            DB::commit();

            // Log the action
            AuditLog::logAction(
                'super_admin',
                Auth::guard('super_admin')->id(),
                'uploaded',
                'Result',
                $test->id,
                "Uploaded results for test: {$test->test_date->format('d M Y')} - Success: {$successCount}, Errors: {$errorCount}",
                null,
                ['test_id' => $test->id, 'success' => $successCount, 'errors' => $errorCount]
            );

            // Prepare response
            $message = "Upload completed! Successfully uploaded {$successCount} results.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} results had errors.";
            }

            return redirect()->route('super-admin.results.show', $test)
                ->with([
                    'success' => $message,
                    'upload_report' => [
                        'success_count' => $successCount,
                        'error_count' => $errorCount,
                        'errors' => $errors,
                        'warnings' => $warnings,
                    ]
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error uploading results: ' . $e->getMessage());
        }
    }

    /**
     * Get expected columns based on test mode
     * UPDATED: Removed "Total" column - system calculates automatically
     */
    private function getExpectedColumns($testMode)
    {
        if ($testMode === 'mode_1') {
            return [
                'Roll Number', 
                'Book Color', 
                'English Obj', 
                'Urdu Obj', 
                'Math Obj ', 
                'Science Obj ',
                'English Subj', 
                'Urdu Subj', 
                'Math Subj', 
                'Science Subj'
                // REMOVED: 'Total' - system calculates this automatically
            ];
        } elseif ($testMode === 'mode_2') {
            return [
                'Roll Number', 
                'Book Color', 
                'English', 
                'Urdu', 
                'Math', 
                'Science'
                // REMOVED: 'Total' - system calculates this automatically
            ];
        } else {
            // Mode 3 - Keep total as user provides it
            return [
                'Roll Number', 
                'Book Color', 
                'Total'
            ];
        }
    }

    /**
     * Validate Excel headers
     */
    private function validateHeaders($headers, $expectedColumns)
    {
        // Normalize headers (trim and case-insensitive)
        $normalizedHeaders = array_map(function($h) {
            return strtolower(trim($h ?? ''));
        }, $headers);

        $normalizedExpected = array_map(function($h) {
            return strtolower(trim($h));
        }, $expectedColumns);

        // Check if all expected columns are present
        foreach ($normalizedExpected as $index => $expected) {
            if (!isset($normalizedHeaders[$index]) || $normalizedHeaders[$index] !== $expected) {
                return false;
            }
        }

        return true;
    }

    // Show results for a test
    public function show(Test $test)
    {
        $test->load(['college', 'students.result']);
        
        $students = $test->students()
            ->with('result')
            ->orderBy('roll_number')
            ->get();

        $stats = [
            'total_students' => $students->count(),
            'results_uploaded' => $students->whereNotNull('result')->count(),
            'results_pending' => $students->whereNull('result')->count(),
            'published_count' => Result::where('test_id', $test->id)->where('is_published', true)->count(),
        ];

        return view('super_admin.results.show', compact('test', 'students', 'stats'));
    }

    // Publish results
    public function publish(Test $test)
    {
        $count = Result::where('test_id', $test->id)
            ->update(['is_published' => true]);

        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'published',
            'Result',
            $test->id,
            "Published results for test: {$test->college->name} - {$test->test_date->format('d M Y')}",
            null,
            ['published_count' => $count]
        );

        return redirect()->back()
            ->with('success', "Results published successfully! {$count} results are now visible to students.");
    }

    // Unpublish results
    public function unpublish(Test $test)
    {
        $count = Result::where('test_id', $test->id)
            ->update(['is_published' => false]);

        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'unpublished',
            'Result',
            $test->id,
            "Unpublished results for test: {$test->college->name} - {$test->test_date->format('d M Y')}",
            null,
            ['unpublished_count' => $count]
        );

        return redirect()->back()
            ->with('success', "Results unpublished successfully! {$count} results are now hidden from students.");
    }

    // Delete all results for a test
    public function destroy(Test $test)
    {
        $count = Result::where('test_id', $test->id)->count();
        Result::where('test_id', $test->id)->delete();

        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'deleted',
            'Result',
            $test->id,
            "Deleted all results for test: {$test->college->name} - {$test->test_date->format('d M Y')}",
            null,
            ['deleted_count' => $count]
        );

        return redirect()->route('super-admin.results.index')
            ->with('success', "All results deleted successfully! {$count} results removed.");
    }
}