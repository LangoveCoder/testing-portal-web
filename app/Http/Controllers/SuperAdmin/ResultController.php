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
            DB::beginTransaction();

            // Load Excel file
            $file = $request->file('result_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and Excel starts at 1

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $rollNumber = $row[0] ?? null;

                if (!$rollNumber) {
                    $errors[] = "Row {$rowNumber}: Roll number is missing";
                    $errorCount++;
                    continue;
                }

                // Find student by roll number
                $student = Student::where('test_id', $test->id)
                    ->where('roll_number', $rollNumber)
                    ->first();

                if (!$student) {
                    $errors[] = "Row {$rowNumber}: Student with roll number {$rollNumber} not found";
                    $errorCount++;
                    continue;
                }

                // Process based on test mode
                try {
                    $resultData = $this->processResultRow($row, $test->test_mode, $student->id, $rollNumber);
                    
                    // Create or update result
                    Result::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'test_id' => $test->id,
                        ],
                        $resultData
                    );

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    $errorCount++;
                }
            }

            // Log the upload
            AuditLog::logAction(
                'super_admin',
                Auth::guard('super_admin')->id(),
                'uploaded',
                'Result',
                $test->id,
                "Uploaded results for test: {$test->college->name} - {$test->test_date->format('d M Y')}. Success: {$successCount}, Errors: {$errorCount}",
                null,
                ['success_count' => $successCount, 'error_count' => $errorCount]
            );

            DB::commit();

            $message = "Results uploaded successfully! {$successCount} records processed.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} errors found.";
            }

            return redirect()->route('super-admin.results.show', $test)
                ->with('success', $message)
                ->with('errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error uploading results: ' . $e->getMessage());
        }
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

    // Helper method to process result row based on test mode
// Helper method to process result row based on test mode
// Helper method to process result row based on test mode
private function processResultRow($row, $testMode, $studentId, $rollNumber)
{
    $baseData = [
        'student_id' => $studentId,
        'roll_number' => $rollNumber,
    ];
    
    switch ($testMode) {
        case 'mode_1': // MCQ and Subjective
            return array_merge($baseData, [
                'english_obj' => $row[1] ?? 0,
                'urdu_obj' => $row[2] ?? 0,
                'math_obj' => $row[3] ?? 0,
                'science_obj' => $row[4] ?? 0,
                'english_subj' => $row[5] ?? 0,
                'urdu_subj' => $row[6] ?? 0,
                'math_subj' => $row[7] ?? 0,
                'science_subj' => $row[8] ?? 0,
                'total_marks' => ($row[1] ?? 0) + ($row[2] ?? 0) + ($row[3] ?? 0) + ($row[4] ?? 0) + 
                               ($row[5] ?? 0) + ($row[6] ?? 0) + ($row[7] ?? 0) + ($row[8] ?? 0),
            ]);

        case 'mode_2': // MCQ Only
            return array_merge($baseData, [
                'english' => $row[1] ?? 0,
                'urdu' => $row[2] ?? 0,
                'math' => $row[3] ?? 0,
                'science' => $row[4] ?? 0,
                'total_marks' => ($row[1] ?? 0) + ($row[2] ?? 0) + ($row[3] ?? 0) + ($row[4] ?? 0),
            ]);

        case 'mode_3': // General MCQ
            return array_merge($baseData, [
                'marks' => $row[1] ?? 0,
                'total_marks' => $row[1] ?? 0,
            ]);

        default:
            throw new \Exception("Unknown test mode: {$testMode}");
    }
}
    }
