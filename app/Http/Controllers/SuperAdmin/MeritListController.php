<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

class MeritListController extends Controller
{
    /**
     * Official Balochistan Divisions and Districts Structure (2024)
     */
    private $balochistanStructure = [
        'Quetta Division' => ['Quetta', 'Pishin', 'Killa Abdullah', 'Chaman'],
        'Kalat Division' => ['Kalat', 'Mastung', 'Khuzdar', 'Awaran', 'Lasbela', 'Hub', 'Surab'],
        'Sibi Division' => ['Sibi', 'Kohlu', 'Dera Bugti', 'Ziarat', 'Harnai', 'Lehri'],
        'Nasirabad Division' => ['Nasirabad', 'Jaffarabad', 'Kachhi', 'Jhal Magsi', 'Sohbatpur'],
        'Makran Division' => ['Kech', 'Gwadar', 'Panjgur'],
        'Zhob Division' => ['Zhob', 'Sherani'],
        'Loralai Division' => ['Loralai', 'Barkhan', 'Musakhel', 'Duki'],
        'Rakhshan Division' => ['Chagai', 'Kharan', 'Washuk', 'Nushki'],
    ];

    /**
     * Show merit list selection page
     */
    public function index()
    {
        // Get all tests that have published results
        $tests = Test::with('college')
            ->whereHas('results', function($query) {
                $query->where('is_published', true);
            })
            ->orderBy('test_date', 'desc')
            ->get();

        return view('super_admin.merit_lists.index', compact('tests'));
    }

    /**
     * Generate and display merit lists for a test
     */
    public function show(Test $test)
    {
        // Check if test has published results
        $hasPublishedResults = Result::where('test_id', $test->id)
            ->where('is_published', true)
            ->exists();

        if (!$hasPublishedResults) {
            return redirect()->route('super-admin.merit-lists.index')
                ->with('error', 'This test does not have published results yet.');
        }

        // Generate merit lists
        $meritLists = $this->generateMeritLists($test);
        
        // Get statistics
        $statistics = $this->generateStatistics($meritLists);

        return view('super_admin.merit_lists.show', compact('test', 'meritLists', 'statistics'));
    }

    /**
     * Generate merit lists based on province and district
     */
    private function generateMeritLists(Test $test)
    {
        $meritLists = [];

        // Get all results with student info for this test
        $results = Result::with(['student'])
            ->where('test_id', $test->id)
            ->where('is_published', true)
            ->get();

        // Group students by province
        $byProvince = $results->groupBy(function($result) {
            return $result->student->province;
        });

        foreach ($byProvince as $province => $provinceResults) {
            
            // BALOCHISTAN: Create division-wise and district-wise merit lists
            if (strtolower($province) === 'balochistan') {
                
                // Group by division using official structure
                foreach ($this->balochistanStructure as $divisionName => $districts) {
                    
                    // Get all students from districts in this division
                    $divisionResults = $provinceResults->filter(function($result) use ($districts) {
                        $studentDistrict = $result->student->district;
                        
                        // Check if student's district matches any district in this division
                        // Use case-insensitive comparison and handle variations
                        foreach ($districts as $officialDistrict) {
                            if (stripos($studentDistrict, $officialDistrict) !== false || 
                                stripos($officialDistrict, $studentDistrict) !== false) {
                                return true;
                            }
                        }
                        return false;
                    });

                    if ($divisionResults->isEmpty()) {
                        continue; // Skip divisions with no students
                    }

                    // Now create district-wise merit lists within this division
                    foreach ($districts as $officialDistrict) {
                        
                        $districtResults = $divisionResults->filter(function($result) use ($officialDistrict) {
                            $studentDistrict = $result->student->district;
                            return stripos($studentDistrict, $officialDistrict) !== false || 
                                   stripos($officialDistrict, $studentDistrict) !== false;
                        });

                        if ($districtResults->isEmpty()) {
                            continue; // Skip districts with no students
                        }

                        // Sort by marks (highest first)
                        $sortedResults = $districtResults->sortByDesc('marks')->values();

                        // Add position/rank
                        $rankedResults = $sortedResults->map(function($result, $index) {
                            $result->position = $index + 1;
                            return $result;
                        });

                        $meritLists[] = [
                            'type' => 'balochistan_district',
                            'province' => $province,
                            'division' => $divisionName,
                            'district' => $officialDistrict,
                            'title' => "{$officialDistrict} District, {$divisionName}",
                            'total_students' => $rankedResults->count(),
                            'highest_marks' => $rankedResults->first()->marks ?? 0,
                            'lowest_marks' => $rankedResults->last()->marks ?? 0,
                            'average_marks' => round($rankedResults->avg('marks'), 2),
                            'results' => $rankedResults,
                        ];
                    }
                }
            }
            // OTHER PROVINCES: Create single province-wide merit list
            else {
                // Sort by marks (highest first)
                $sortedResults = $provinceResults->sortByDesc('marks')->values();

                // Add position/rank
                $rankedResults = $sortedResults->map(function($result, $index) {
                    $result->position = $index + 1;
                    return $result;
                });

                $meritLists[] = [
                    'type' => 'other_province',
                    'province' => $province,
                    'division' => null,
                    'district' => null,
                    'title' => "{$province} Province",
                    'total_students' => $rankedResults->count(),
                    'highest_marks' => $rankedResults->first()->marks ?? 0,
                    'lowest_marks' => $rankedResults->last()->marks ?? 0,
                    'average_marks' => round($rankedResults->avg('marks'), 2),
                    'results' => $rankedResults,
                ];
            }
        }

        // Sort merit lists: Balochistan divisions first (in order), then other provinces
        usort($meritLists, function($a, $b) {
            // Balochistan districts first
            if ($a['type'] === 'balochistan_district' && $b['type'] !== 'balochistan_district') return -1;
            if ($a['type'] !== 'balochistan_district' && $b['type'] === 'balochistan_district') return 1;
            
            // Within Balochistan, sort by division order, then district
            if ($a['type'] === 'balochistan_district' && $b['type'] === 'balochistan_district') {
                $divisionOrder = array_keys($this->balochistanStructure);
                $aDivPos = array_search($a['division'], $divisionOrder);
                $bDivPos = array_search($b['division'], $divisionOrder);
                
                if ($aDivPos !== $bDivPos) {
                    return $aDivPos <=> $bDivPos;
                }
                
                return strcmp($a['district'], $b['district']);
            }
            
            // Other provinces sorted alphabetically
            return strcmp($a['province'], $b['province']);
        });

        return $meritLists;
    }

    /**
     * Generate statistics for merit lists
     */
    private function generateStatistics($meritLists)
    {
        $stats = [
            'total_lists' => count($meritLists),
            'total_students' => 0,
            'balochistan_lists' => 0,
            'other_province_lists' => 0,
            'by_division' => [],
            'by_province' => [],
        ];

        foreach ($meritLists as $list) {
            $stats['total_students'] += $list['total_students'];
            
            if ($list['type'] === 'balochistan_district') {
                $stats['balochistan_lists']++;
                
                if (!isset($stats['by_division'][$list['division']])) {
                    $stats['by_division'][$list['division']] = [
                        'districts' => 0,
                        'students' => 0,
                    ];
                }
                $stats['by_division'][$list['division']]['districts']++;
                $stats['by_division'][$list['division']]['students'] += $list['total_students'];
            } else {
                $stats['other_province_lists']++;
                
                if (!isset($stats['by_province'][$list['province']])) {
                    $stats['by_province'][$list['province']] = 0;
                }
                $stats['by_province'][$list['province']] += $list['total_students'];
            }
        }

        return $stats;
    }

    /**
     * Download specific merit list as Excel
     */
    public function downloadExcel(Test $test, Request $request)
    {
        $province = $request->query('province');
        $division = $request->query('division');
        $district = $request->query('district');

        // Generate merit lists
        $allMeritLists = $this->generateMeritLists($test);

        // Filter to the specific merit list requested
        $meritList = collect($allMeritLists)->first(function($list) use ($province, $division, $district) {
            if ($list['type'] === 'balochistan_district') {
                return $list['province'] === $province 
                    && $list['division'] === $division 
                    && $list['district'] === $district;
            } else {
                return $list['province'] === $province;
            }
        });

        if (!$meritList) {
            return back()->with('error', 'Merit list not found.');
        }

        // Create Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'MERIT LIST');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', $meritList['title']);
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'College: ' . $test->college->name);
        $sheet->mergeCells('A3:I3');
        $sheet->setCellValue('A4', 'Test Date: ' . $test->test_date->format('d F Y'));
        $sheet->mergeCells('A4:I4');

        // Headers
        $headers = ['Position', 'Roll Number', 'Name', 'Father Name', 'CNIC', 'Gender', 'District', 'Marks Obtained', 'Total Marks'];
        $sheet->fromArray($headers, null, 'A6');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A6:I6')->applyFromArray($headerStyle);

        // Data rows
        $row = 7;
        foreach ($meritList['results'] as $result) {
            $sheet->fromArray([
                $result->position,
                $result->roll_number,
                $result->student->name,
                $result->student->father_name,
                $result->student->cnic,
                $result->student->gender,
                $result->student->district,
                $result->marks,
                $result->total_marks,
            ], null, 'A' . $row);
            
            // Add borders
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            $row++;
        }

        // Add statistics at the bottom
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Statistics:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Students: ' . $meritList['total_students']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Highest Marks: ' . $meritList['highest_marks']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Lowest Marks: ' . $meritList['lowest_marks']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Average Marks: ' . $meritList['average_marks']);

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate filename
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $meritList['title']);
        $filename = 'Merit_List_' . $safeName . '_' . date('Y-m-d') . '.xlsx';

        // Save and download
        $writer = new Xlsx($spreadsheet);
        $tempPath = storage_path('app/temp/' . $filename);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Download all merit lists as ZIP
     */
    public function downloadAllExcel(Test $test)
    {
        // Generate all merit lists
        $allMeritLists = $this->generateMeritLists($test);

        if (empty($allMeritLists)) {
            return back()->with('error', 'No merit lists to download.');
        }

        $zipFilename = 'All_Merit_Lists_' . $test->college->code . '_' . date('Y-m-d_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);
        
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Failed to create ZIP file.');
        }

        $tempFiles = [];

        foreach ($allMeritLists as $meritList) {
            // Create Excel for this merit list
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Title
            $sheet->setCellValue('A1', 'MERIT LIST');
            $sheet->mergeCells('A1:I1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', $meritList['title']);
            $sheet->mergeCells('A2:I2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A3', 'College: ' . $test->college->name);
            $sheet->mergeCells('A3:I3');
            $sheet->setCellValue('A4', 'Test Date: ' . $test->test_date->format('d F Y'));
            $sheet->mergeCells('A4:I4');

            // Headers
            $headers = ['Position', 'Roll Number', 'Name', 'Father Name', 'CNIC', 'Gender', 'District', 'Marks Obtained', 'Total Marks'];
            $sheet->fromArray($headers, null, 'A6');
            
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
            ];
            $sheet->getStyle('A6:I6')->applyFromArray($headerStyle);

            // Data
            $row = 7;
            foreach ($meritList['results'] as $result) {
                $sheet->fromArray([
                    $result->position,
                    $result->roll_number,
                    $result->student->name,
                    $result->student->father_name,
                    $result->student->cnic,
                    $result->student->gender,
                    $result->student->district,
                    $result->marks,
                    $result->total_marks,
                ], null, 'A' . $row);
                $row++;
            }

            // Statistics
            $row += 2;
            $sheet->setCellValue('A' . $row, 'Statistics:');
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            $sheet->setCellValue('A' . $row, 'Total Students: ' . $meritList['total_students']);
            $row++;
            $sheet->setCellValue('A' . $row, 'Highest Marks: ' . $meritList['highest_marks']);
            $row++;
            $sheet->setCellValue('A' . $row, 'Lowest Marks: ' . $meritList['lowest_marks']);
            $row++;
            $sheet->setCellValue('A' . $row, 'Average Marks: ' . $meritList['average_marks']);

            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Save to temp
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $meritList['title']);
            
            // Organize in folders by division for Balochistan
            if ($meritList['type'] === 'balochistan_district') {
                $folderName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $meritList['division']);
                $filename = $folderName . '/' . $safeName . '.xlsx';
            } else {
                $filename = 'Other_Provinces/' . $safeName . '.xlsx';
            }
            
            $tempExcel = storage_path('app/temp/' . uniqid() . '.xlsx');
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempExcel);

            // Add to ZIP with folder structure
            $zip->addFile($tempExcel, $filename);
            $tempFiles[] = $tempExcel;
        }

        $zip->close();

        // Clean up temp Excel files
        foreach ($tempFiles as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }

        return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
    }
}