<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Test;
use App\Models\Student;
use App\Models\TestDistrict;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class BulkUploadController extends Controller
{
    /**
     * Show bulk upload page
     */
    public function index()
    {
        $colleges = College::orderBy('name')->get();
        return view('super_admin.bulk_upload.index', compact('colleges'));
    }

    /**
     * Get tests for selected college
     */
    public function getTests($collegeId)
    {
        $tests = Test::where('college_id', $collegeId)
            ->where('registration_deadline', '>=', now())
            ->orderBy('test_date')
            ->get();
        
        return response()->json($tests);
    }

    /**
     * Download template for college
     */
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'test_id' => 'required|exists:tests,id',
        ]);

        $college = College::findOrFail($request->college_id);
        $test = Test::findOrFail($request->test_id);

        // Get test districts from test_districts table (old system)
        $testDistricts = TestDistrict::where('college_id', $college->id)->get();

        // Create Excel template
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        // Set headers with bold and colored background
        $headers = [
            'Name', 'Father Name', 'Student CNIC', 'Father CNIC', 
            'Gender', 'Religion', 'Date of Birth (DD/MM/YYYY)', 
            'Province', 'Division', 'District', 'Complete Address', 
            'Test District', 'Picture Filename'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

        // Add sample data row
        $sampleData = [
            'Ali Ahmed', 'Ahmed Khan', '4210112345678', '4210198765432',
            'Male', 'Islam', '15/01/2005', 'Balochistan', 'Quetta Division', 'Quetta',
            'House 123, Street 5, Area ABC', $testDistricts->first()->district ?? 'Quetta',
            '4210112345678.jpg'
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // ========== FORMAT DATE COLUMN AS TEXT ==========
        $sheet->getStyle('G:G')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        $sheet->setCellValueExplicit('G2', '15/01/2005', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        // ========== CREATE DATA VALIDATION (DROPDOWNS) ==========
        
        // Gender Dropdown
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $sheet->getCell('E' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Invalid Gender');
            $validation->setError('Please select from the dropdown');
            $validation->setPromptTitle('Gender');
            $validation->setPrompt('Select: Male or Female');
            $validation->setFormula1('"Male,Female"');
        }

        // Religion Dropdown
        $religions = ['Islam', 'Christianity', 'Hinduism', 'Sikhism', 'Buddhism', 'Other'];
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $sheet->getCell('F' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Invalid Religion');
            $validation->setError('Please select from the dropdown');
            $validation->setPromptTitle('Religion');
            $validation->setPrompt('Select religion from list');
            $validation->setFormula1('"' . implode(',', $religions) . '"');
        }

        // Province Dropdown
        $provinces = ['Balochistan', 'Punjab', 'Sindh', 'Khyber Pakhtunkhwa', 'Gilgit-Baltistan', 'Azad Kashmir'];
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $sheet->getCell('H' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Invalid Province');
            $validation->setError('Please select from the dropdown');
            $validation->setPromptTitle('Province');
            $validation->setPrompt('Select province from list');
            $validation->setFormula1('"' . implode(',', $provinces) . '"');
        }

        // Division Dropdown
        $divisions = [
            'Quetta Division', 'Kalat Division', 'Makran Division', 
            'Nasirabad Division', 'Sibi Division', 'Zhob Division', 'Rakhshan Division', 'Loralai Division'
        ];
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $sheet->getCell('I' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Invalid Division');
            $validation->setError('Please select from the dropdown or leave blank');
            $validation->setPromptTitle('Division (Optional)');
            $validation->setPrompt('Select division from list');
            $validation->setFormula1('"' . implode(',', $divisions) . '"');
        }

        // District Dropdown
        $balochistanDistricts = [
            'Awaran', 'Barkhan', 'Chagai', 'Chaman', 'Dera Bugti', 'Duki', 'Gwadar', 
            'Harnai', 'Jafarabad', 'Jhal Magsi', 'Kachhi', 'Kalat', 'Kech', 'Kharan', 
            'Khuzdar', 'Killa Abdullah', 'Killa Saifullah', 'Kohlu', 'Lasbela', 
            'Lehri', 'Loralai', 'Mastung', 'Musakhel', 'Nasirabad', 'Nushki', 
            'Panjgur', 'Pishin', 'Quetta', 'Sherani', 'Sibi', 'Sohbatpur', 
            'Washuk', 'Zhob', 'Ziarat', 'Punjab', 'Sindh', 'Khyber Pakhtunkhwa','Islamabad','Gilgit-Baltistan','Azad Kashmir'
        ];
        
        sort($balochistanDistricts);
        
        $districtSheet = $spreadsheet->createSheet();
        $districtSheet->setTitle('District_List');
        foreach ($balochistanDistricts as $index => $district) {
            $districtSheet->setCellValue('A' . ($index + 1), $district);
        }
        
        for ($row = 2; $row <= 1000; $row++) {
            $validation = $sheet->getCell('J' . $row)->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Invalid District');
            $validation->setError('Please select from the dropdown');
            $validation->setPromptTitle('District');
            $validation->setPrompt('Select district from Balochistan districts');
            $validation->setFormula1('District_List!$A$1:$A$' . count($balochistanDistricts));
        }

        // Test District Dropdown
        $testDistrictNames = $testDistricts->pluck('district')->toArray();
        if (!empty($testDistrictNames)) {
            for ($row = 2; $row <= 1000; $row++) {
                $validation = $sheet->getCell('L' . $row)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Invalid Test District');
                $validation->setError('Please select from the dropdown');
                $validation->setPromptTitle('Test District');
                $validation->setPrompt('Select where student will take the test');
                $validation->setFormula1('"' . implode(',', $testDistrictNames) . '"');
            }
        }

        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add cell comments
        $sheet->getComment('E2')->getText()->createTextRun('Click to see dropdown: Male or Female');
        $sheet->getComment('F2')->getText()->createTextRun('Click to see dropdown: Select religion');
        $sheet->getComment('G2')->getText()->createTextRun('Format: DD/MM/YYYY (e.g., 15/01/2005)');
        $sheet->getComment('H2')->getText()->createTextRun('Click to see dropdown: Select province');
        $sheet->getComment('I2')->getText()->createTextRun('Click to see dropdown: Select division (optional)');
        $sheet->getComment('J2')->getText()->createTextRun('Click to see dropdown: Select district');
        $sheet->getComment('L2')->getText()->createTextRun('Click to see dropdown: Where student takes test');
        $sheet->getComment('M2')->getText()->createTextRun('Must match photo file (e.g., 4210112345678.jpg)');

        // Add instructions sheet
        $instructionsSheet = $spreadsheet->createSheet();
        $instructionsSheet->setTitle('Instructions');
        
        $instructions = [
            ['STUDENT BULK UPLOAD INSTRUCTIONS'],
            [''],
            ['College: ' . $college->name],
            ['Test Date: ' . $test->test_date->format('d M Y')],
            ['Test Mode: ' . ucfirst(str_replace('_', ' ', $test->test_mode))],
            [''],
            ['IMPORTANT: ALL DROPDOWNS MUST BE USED - DO NOT TYPE MANUALLY!'],
            [''],
            ['COLUMN GUIDE:'],
            ['A - Name: Student full name (type freely)'],
            ['B - Father Name: Father full name (type freely)'],
            ['C - Student CNIC: Exactly 13 digits (e.g., 4210112345678)'],
            ['D - Father CNIC: Exactly 13 digits (e.g., 4210198765432)'],
            ['E - Gender: DROPDOWN ONLY - Male or Female'],
            ['F - Religion: DROPDOWN ONLY - Select from list'],
            ['G - Date of Birth: Format DD/MM/YYYY (e.g., 15/01/2005)'],
            ['H - Province: DROPDOWN ONLY - Select province'],
            ['I - Division: DROPDOWN ONLY - Select division (optional)'],
            ['J - District: DROPDOWN ONLY - Select from Balochistan districts'],
            ['K - Complete Address: Type full address'],
            ['L - Test District: DROPDOWN ONLY - Where student takes test'],
            ['M - Picture Filename: Photo filename (e.g., 4210112345678.jpg)'],
            [''],
            ['HOW TO USE DROPDOWNS:'],
            ['1. Click on the cell (e.g., Gender column)'],
            ['2. A small arrow will appear on the right side of the cell'],
            ['3. Click the arrow to see dropdown options'],
            ['4. Select the correct option from the list'],
            ['5. DO NOT type manually - use dropdown only!'],
            [''],
            ['STEP 1: Fill Student Data in Template Sheet'],
            ['- Use Row 2 as example (you can delete or replace it)'],
            ['- Start your data from Row 3 onwards'],
            ['- Use dropdowns for all dropdown columns'],
            ['- Type other fields normally'],
            [''],
            ['STEP 2: Prepare Student Photos'],
            ['- Create a folder named "pictures"'],
            ['- Rename each photo to match Student CNIC exactly'],
            ['  Example: 4210112345678.jpg'],
            ['- Supported: JPG, JPEG, PNG'],
            ['- Max size: 2MB per photo'],
            [''],
            ['STEP 3: Create ZIP File'],
            ['- Save this Excel file as: students.xlsx'],
            ['- Put students.xlsx in a folder'],
            ['- Put pictures folder in same location'],
            ['- Select both and create ZIP file'],
            ['- Name ZIP: ' . $college->code . '_students.zip'],
            [''],
            ['STEP 4: Send to Super Admin'],
            ['- Email, WhatsApp, or USB drive'],
            [''],
            ['VALIDATION RULES:'],
            ['- Student CNIC must be unique'],
            ['- CNICs must be exactly 13 digits'],
        ];

        if ($college->min_age || $college->max_age) {
            if ($college->min_age && $college->max_age) {
                $instructions[] = ['- Age must be between ' . $college->min_age . ' and ' . $college->max_age . ' years'];
            } elseif ($college->min_age) {
                $instructions[] = ['- Minimum age: ' . $college->min_age . ' years'];
            } else {
                $instructions[] = ['- Maximum age: ' . $college->max_age . ' years'];
            }
        }

        if ($college->gender_policy != 'Both') {
            $instructions[] = ['- Gender: Only ' . $college->gender_policy . ' allowed'];
        }

        $instructions[] = [''];
        $instructions[] = ['AVAILABLE TEST DISTRICTS FOR THIS COLLEGE:'];
        foreach ($testDistricts as $district) {
            $instructions[] = ['  • ' . $district->district . ', ' . $district->province];
        }

        $instructionsSheet->fromArray($instructions, null, 'A1');
        $instructionsSheet->getColumnDimension('A')->setWidth(100);

        $spreadsheet->setActiveSheetIndex(0);

        // Create temp file
        $filename = $college->code . '_upload_template.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        // Create ZIP
        $zipFilename = $college->code . '_upload_template.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($tempPath, 'students.xlsx');
            $zip->addEmptyDir('pictures');
            
            $instructionsText = "STUDENT BULK UPLOAD INSTRUCTIONS\n\n";
            $instructionsText .= "College: {$college->name}\n";
            $instructionsText .= "Test: {$test->test_date->format('d M Y')}\n\n";
            $instructionsText .= "CRITICAL: USE EXCEL DROPDOWNS - DO NOT TYPE!\n\n";
            $instructionsText .= "Columns with dropdowns:\n";
            $instructionsText .= "- Gender (Male/Female)\n";
            $instructionsText .= "- Religion\n";
            $instructionsText .= "- Province\n";
            $instructionsText .= "- Division\n";
            $instructionsText .= "- District (Balochistan)\n";
            $instructionsText .= "- Test District\n\n";
            $instructionsText .= "1. Click on cell to see dropdown arrow\n";
            $instructionsText .= "2. Click arrow and select from list\n";
            $instructionsText .= "3. Fill students.xlsx\n";
            $instructionsText .= "4. Add photos to pictures/ folder (named as CNIC.jpg)\n";
            $instructionsText .= "5. Create ZIP with both\n";
            $instructionsText .= "6. Send to Super Admin\n";
            
            $zip->addFromString('INSTRUCTIONS.txt', $instructionsText);
            $zip->close();
        }

        unlink($tempPath);

        return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
    }

    /**
     * Upload and validate ZIP file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'test_id' => 'required|exists:tests,id',
            'upload_file' => 'required|file|mimes:zip|max:102400',
        ]);

        try {
            $college = College::findOrFail($request->college_id);
            $test = Test::findOrFail($request->test_id);

            $zipFile = $request->file('upload_file');
            $extractPath = storage_path('app/temp/extract_' . time());
            
            $zip = new \ZipArchive();
            if ($zip->open($zipFile->getPathname()) === true) {
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                return back()->with('error', 'Failed to extract ZIP file');
            }

            $excelFile = null;
            $files = scandir($extractPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'xlsx' || pathinfo($file, PATHINFO_EXTENSION) == 'xls') {
                    $excelFile = $extractPath . '/' . $file;
                    break;
                }
            }

            if (!$excelFile || !file_exists($excelFile)) {
                $this->cleanupTemp($extractPath);
                return back()->with('error', 'No Excel file found in ZIP');
            }

            $picturesPath = $extractPath . '/pictures';
            if (!file_exists($picturesPath)) {
                $this->cleanupTemp($extractPath);
                return back()->with('error', 'Pictures folder not found in ZIP');
            }

            $spreadsheet = IOFactory::load($excelFile);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            array_shift($rows);

            $validStudents = [];
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                if (empty(array_filter($row))) {
                    continue;
                }

                $validation = $this->validateStudentRow($row, $college, $test, $picturesPath, $rowNumber);
                
                if ($validation['valid']) {
                    $validStudents[] = $validation['data'];
                } else {
                    $errors[] = $validation['errors'];
                }
            }

            session([
                'bulk_upload_valid' => $validStudents,
                'bulk_upload_errors' => $errors,
                'bulk_upload_college' => $college,
                'bulk_upload_test' => $test,
                'bulk_upload_extract_path' => $extractPath,
            ]);

            return redirect()->route('super-admin.bulk-upload.preview');

        } catch (\Exception $e) {
            return back()->with('error', 'Error processing upload: ' . $e->getMessage());
        }
    }

    /**
     * Show preview page
     */
    public function preview()
    {
        $validStudents = session('bulk_upload_valid', []);
        $errors = session('bulk_upload_errors', []);
        $college = session('bulk_upload_college');
        $test = session('bulk_upload_test');

        if (!$college || !$test) {
            return redirect()->route('super-admin.bulk-upload.index')
                ->with('error', 'No upload data found');
        }

        return view('super_admin.bulk_upload.preview', compact('validStudents', 'errors', 'college', 'test'));
    }

   /**
     * Import valid students
     */
    public function import(Request $request)
    {
        $validStudents = session('bulk_upload_valid', []);
        $extractPath = session('bulk_upload_extract_path');
        $college = session('bulk_upload_college');
        $test = session('bulk_upload_test');

        if (empty($validStudents)) {
            return redirect()->route('super-admin.bulk-upload.index')
                ->with('error', 'No valid students to import');
        }

        try {
            DB::beginTransaction();

            $successCount = 0;
            $failedCount = 0;
            $errors = [];
            
            foreach ($validStudents as $index => $studentData) {
                try {
                    $picturePath = null;
                    if (isset($studentData['temp_picture_path']) && file_exists($studentData['temp_picture_path'])) {
                        $filename = basename($studentData['temp_picture_path']);
                        $picturePath = 'student-pictures/' . $filename;
                        Storage::disk('public')->put($picturePath, file_get_contents($studentData['temp_picture_path']));
                    }

                    $registrationId = 'REG' . time() . substr(microtime(), 2, 6) . rand(100, 999);
                    
                    while (Student::where('registration_id', $registrationId)->exists()) {
                        $registrationId = 'REG' . time() . substr(microtime(), 2, 6) . rand(100, 999);
                    }

                    $student = Student::create([
                        'test_id' => $test->id,
                        'test_district_id' => $studentData['test_district_id'],
                        'name' => $studentData['name'],
                        'cnic' => $studentData['cnic'],
                        'father_name' => $studentData['father_name'],
                        'father_cnic' => $studentData['father_cnic'],
                        'gender' => $studentData['gender'],
                        'religion' => $studentData['religion'],
                        'date_of_birth' => $studentData['date_of_birth'],
                        'province' => $studentData['province'],
                        'division' => $studentData['division'] ?? null,
                        'district' => $studentData['district'],
                        'address' => $studentData['address'],
                        'picture' => $picturePath,
                        'registration_id' => $registrationId,
                        'roll_number' => null,
                        'book_color' => null,
                        'hall_number' => null,
                        'zone_number' => null,
                        'row_number' => null,
                        'seat_number' => null,
                    ]);

                    if ($student && $student->id) {
                        $successCount++;
                        \Log::info("Student created successfully: {$student->name} (ID: {$student->id})");
                    } else {
                        $failedCount++;
                        $errors[] = "Failed to create student: {$studentData['name']} - Unknown error";
                        \Log::error("Student creation returned null: " . json_encode($studentData));
                    }
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Error creating student {$studentData['name']}: " . $e->getMessage();
                    \Log::error("Student creation failed: " . $e->getMessage());
                    \Log::error("Student data: " . json_encode($studentData));
                    \Log::error("Stack trace: " . $e->getTraceAsString());
                }
            }

            if ($successCount > 0) {
                AuditLog::logAction(
                    'super_admin',
                    Auth::guard('super_admin')->id(),
                    'uploaded',
                    'Student',
                    null,
                    "Bulk uploaded {$successCount} students for {$college->name}" . ($failedCount > 0 ? " ({$failedCount} failed)" : ""),
                    null,
                    ['count' => $successCount, 'failed' => $failedCount, 'college_id' => $college->id, 'test_id' => $test->id]
                );

                DB::commit();
                
                $this->cleanupTemp($extractPath);
                session()->forget(['bulk_upload_valid', 'bulk_upload_errors', 'bulk_upload_college', 'bulk_upload_test', 'bulk_upload_extract_path']);

                $message = "Successfully imported {$successCount} students for {$college->name}!";
                if ($failedCount > 0) {
                    $message .= " ({$failedCount} students failed to import - check logs)";
                }
                
                return redirect()->route('super-admin.bulk-upload.index')
                    ->with('success', $message);
            } else {
                DB::rollBack();
                
                \Log::error("All students failed to import. Errors: " . json_encode($errors));
                
                return redirect()->route('super-admin.bulk-upload.index')
                    ->with('error', 'Failed to import any students. Errors: ' . implode(', ', array_slice($errors, 0, 3)) . '... Check logs for details.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Bulk import transaction error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Error importing students: ' . $e->getMessage() . ' (Check logs: storage/logs/laravel.log)');
        }
    }

    /**
     * Download error report
     */
    public function downloadErrors()
    {
        $errors = session('bulk_upload_errors', []);
        
        if (empty($errors)) {
            return back()->with('error', 'No errors to download');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(['Row Number', 'Field', 'Error'], null, 'A1');
        
        $row = 2;
        foreach ($errors as $errorGroup) {
            foreach ($errorGroup as $error) {
                $sheet->fromArray($error, null, 'A' . $row);
                $row++;
            }
        }

        $filename = 'upload_errors_' . date('Y-m-d_His') . '.xlsx';
        $tempPath = storage_path('app/temp/' . $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Validate student row - FIXED VERSION
     */
    private function validateStudentRow($row, $college, $test, $picturesPath, $rowNumber)
    {
        $errors = [];
        $data = [];

        $name = isset($row[0]) ? trim($row[0]) : '';
        $fatherName = isset($row[1]) ? trim($row[1]) : '';
        $cnic = isset($row[2]) ? trim($row[2]) : '';
        $fatherCnic = isset($row[3]) ? trim($row[3]) : '';
        $gender = isset($row[4]) ? trim($row[4]) : '';
        $religion = isset($row[5]) ? trim($row[5]) : '';
        $dob = isset($row[6]) ? $row[6] : '';
        $province = isset($row[7]) ? trim($row[7]) : '';
        $division = isset($row[8]) ? trim($row[8]) : '';
        $district = isset($row[9]) ? trim($row[9]) : '';
        $address = isset($row[10]) ? trim($row[10]) : '';
        $testDistrict = isset($row[11]) ? trim($row[11]) : '';
        $pictureFilename = isset($row[12]) ? trim($row[12]) : '';

        // Validate required fields
        if (empty($name)) $errors[] = [$rowNumber, 'Name', 'Required'];
        if (empty($fatherName)) $errors[] = [$rowNumber, 'Father Name', 'Required'];
        if (empty($cnic)) $errors[] = [$rowNumber, 'Student CNIC', 'Required'];
        if (empty($fatherCnic)) $errors[] = [$rowNumber, 'Father CNIC', 'Required'];
        if (empty($gender)) $errors[] = [$rowNumber, 'Gender', 'Required'];
        if (empty($religion)) $errors[] = [$rowNumber, 'Religion', 'Required'];
        if (empty($dob)) $errors[] = [$rowNumber, 'Date of Birth', 'Required'];
        if (empty($province)) $errors[] = [$rowNumber, 'Province', 'Required'];
        
        if (empty($district)) {
            if (strtolower($province) === 'balochistan') {
                $errors[] = [$rowNumber, 'District', 'Required for Balochistan province'];
            } else {
                $district = null;
            }
        }
        
        if (empty($address)) $errors[] = [$rowNumber, 'Complete Address', 'Required'];
        if (empty($testDistrict)) $errors[] = [$rowNumber, 'Test District', 'Required'];
        if (empty($pictureFilename)) $errors[] = [$rowNumber, 'Picture Filename', 'Required'];

        // Validate CNIC format
        if (!empty($cnic)) {
            $cnicClean = preg_replace('/[^0-9]/', '', $cnic);
            if (strlen($cnicClean) != 13) {
                $errors[] = [$rowNumber, 'Student CNIC', 'Must be exactly 13 digits (got: ' . $cnic . ')'];
            } else {
                $cnic = $cnicClean;
            }
        }

        if (!empty($fatherCnic)) {
            $fatherCnicClean = preg_replace('/[^0-9]/', '', $fatherCnic);
            if (strlen($fatherCnicClean) != 13) {
                $errors[] = [$rowNumber, 'Father CNIC', 'Must be exactly 13 digits (got: ' . $fatherCnic . ')'];
            } else {
                $fatherCnic = $fatherCnicClean;
            }
        }

        // Check CNIC uniqueness
        if (!empty($cnic) && strlen($cnic) == 13) {
            if (Student::where('cnic', $cnic)->exists()) {
                $errors[] = [$rowNumber, 'Student CNIC', 'Already registered in system'];
            }
        }

        // Validate gender
        if (!empty($gender)) {
            if (!in_array($gender, ['Male', 'Female'])) {
                $errors[] = [$rowNumber, 'Gender', 'Must be "Male" or "Female" (got: ' . $gender . ')'];
            }
        }

        // Validate gender policy
        if (!empty($gender) && in_array($gender, ['Male', 'Female'])) {
            $collegePolicy = $college->gender_policy;
            
            if (stripos($collegePolicy, 'Male') !== false && stripos($collegePolicy, 'Female') === false) {
                if ($gender != 'Male') {
                    $errors[] = [$rowNumber, 'Gender', 'College only accepts Male students'];
                }
            } 
            else if (stripos($collegePolicy, 'Female') !== false && stripos($collegePolicy, 'Male') === false) {
                if ($gender != 'Female') {
                    $errors[] = [$rowNumber, 'Gender', 'College only accepts Female students'];
                }
            }
        }

        // Validate date of birth
        if (!empty($dob)) {
            try {
                $dateOfBirth = null;
                $originalDob = $dob;
                
                $dobString = '';
                
                if (is_numeric($dob) && $dob > 0 && $dob < 100000) {
                    $excelEpoch = Carbon::create(1899, 12, 30);
                    $calculatedDate = $excelEpoch->copy()->addDays(intval($dob));
                    $dobString = $calculatedDate->format('d/m/Y');
                    $dateOfBirth = $calculatedDate;
                }
                else if (is_string($dob)) {
                    $dobString = trim($dob);
                    
                    if (strpos($dobString, '/') !== false) {
                        $parts = explode('/', $dobString);
                        
                        if (count($parts) == 3) {
                            $day = (int) $parts[0];
                            $month = (int) $parts[1];
                            $year = (int) $parts[2];
                            
                            if ($year < 100) {
                                $year = $year < 50 ? 2000 + $year : 1900 + $year;
                            }
                            
                            if (checkdate($month, $day, $year)) {
                                $dateOfBirth = Carbon::create($year, $month, $day);
                            } else {
                                $errors[] = [$rowNumber, 'Date of Birth', "Invalid date: $day/$month/$year"];
                            }
                        }
                    }
                    else if (strpos($dobString, '-') !== false) {
                        $parts = explode('-', $dobString);
                        
                        if (count($parts) == 3) {
                            $year = (int) $parts[0];
                            $month = (int) $parts[1];
                            $day = (int) $parts[2];
                            
                            if (checkdate($month, $day, $year)) {
                                $dateOfBirth = Carbon::create($year, $month, $day);
                            }
                        }
                    }
                }
                else if ($dob instanceof \DateTime) {
                    $dateOfBirth = Carbon::instance($dob);
                }
                
                if ($dateOfBirth && $dateOfBirth instanceof Carbon) {
                    $currentYear = date('Y');
                    
                    if ($dateOfBirth->year < 1950 || $dateOfBirth->year > $currentYear) {
                        $errors[] = [$rowNumber, 'Date of Birth', 'Year must be between 1950 and ' . $currentYear . '. Got: ' . $dateOfBirth->format('d/m/Y')];
                    }
                    else if ($dateOfBirth->isFuture()) {
                        $errors[] = [$rowNumber, 'Date of Birth', 'Cannot be in future: ' . $dateOfBirth->format('d/m/Y')];
                    }
                    else if ($dateOfBirth->diffInYears(Carbon::now()) < 5) {
                        $errors[] = [$rowNumber, 'Date of Birth', 'Student too young (less than 5 years): ' . $dateOfBirth->format('d/m/Y')];
                    }
                    else {
                        $registrationDate = $college->registration_start_date 
                            ? Carbon::parse($college->registration_start_date) 
                            : Carbon::now();
                        
                        if ($dateOfBirth->greaterThan($registrationDate)) {
                            $errors[] = [$rowNumber, 'Date of Birth', 
                                'Birth date (' . $dateOfBirth->format('d/m/Y') . ') cannot be after registration date (' . $registrationDate->format('d/m/Y') . ')'
                            ];
                        } else {
                            $age = $dateOfBirth->diffInYears($registrationDate);
                            
                            if ($college->min_age && $age < $college->min_age) {
                                $errors[] = [$rowNumber, 'Age', 
                                    "Too young. Minimum: {$college->min_age} years, student is {$age} years old. " .
                                    "DOB: " . $dateOfBirth->format('d/m/Y') . " (calculated on " . $registrationDate->format('d/m/Y') . ")"
                                ];
                            }
                            
                            if ($college->max_age && $age > $college->max_age) {
                                $errors[] = [$rowNumber, 'Age', 
                                    "Too old. Maximum: {$college->max_age} years, student is {$age} years old. " .
                                    "DOB: " . $dateOfBirth->format('d/m/Y') . " (calculated on " . $registrationDate->format('d/m/Y') . ")"
                                ];
                            }
                            
                            $data['date_of_birth'] = $dateOfBirth->format('Y-m-d');
                        }
                    }
                } else {
                    $errors[] = [$rowNumber, 'Date of Birth', 'Could not parse date: ' . $originalDob . '. Use format DD/MM/YYYY (e.g., 15/01/2005)'];
                }
                
            } catch (\Exception $e) {
                $errors[] = [$rowNumber, 'Date of Birth', 'Error: ' . $e->getMessage()];
            }
        }

        // ========== VALIDATE TEST DISTRICT (FIXED) ==========
        if (!empty($testDistrict)) {
            // Query from test_districts table (old system)
            $testDistrictRecord = TestDistrict::where('college_id', $college->id)
                ->where('district', $testDistrict)
                ->first();
                
            if (!$testDistrictRecord) {
                $availableDistricts = TestDistrict::where('college_id', $college->id)
                    ->pluck('district')->toArray();
                $errors[] = [$rowNumber, 'Test District', 
                    "'{$testDistrict}' not available for this college. Available: " . implode(', ', $availableDistricts)
                ];
            } else {
                $data['test_district_id'] = $testDistrictRecord->id;
            }
        }

        // Validate picture file
        if (!empty($pictureFilename)) {
            $picturePath = $picturesPath . '/' . $pictureFilename;
            
            if (!file_exists($picturePath)) {
                $errors[] = [$rowNumber, 'Picture', "File not found in pictures folder: {$pictureFilename}"];
            } else {
                $imageInfo = @getimagesize($picturePath);
                if ($imageInfo === false) {
                    $errors[] = [$rowNumber, 'Picture', "{$pictureFilename} is not a valid image file"];
                } else {
                    $fileSize = filesize($picturePath);
                    if ($fileSize > 2 * 1024 * 1024) {
                        $errors[] = [$rowNumber, 'Picture', "{$pictureFilename} is too large (max 2MB)"];
                    } else {
                        $data['temp_picture_path'] = $picturePath;
                    }
                }
            }
        }

        // If no errors, compile data
        if (empty($errors)) {
            $data = array_merge($data, [
                'name' => $name,
                'cnic' => $cnic,
                'father_name' => $fatherName,
                'father_cnic' => $fatherCnic,
                'gender' => $gender,
                'religion' => $religion,
                'province' => $province,
                'division' => $division ?: null,
                'district' => $district,
                'address' => $address,
            ]);

            return ['valid' => true, 'data' => $data];
        }

        return ['valid' => false, 'errors' => $errors];
    }

    /**
     * Cleanup temp directory
     */
    private function cleanupTemp($path)
    {
        if (file_exists($path)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($path);
        }
    }
}