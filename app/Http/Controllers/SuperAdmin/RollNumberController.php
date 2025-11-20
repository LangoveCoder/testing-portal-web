<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Student;
use App\Models\TestVenue;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RollNumberController extends Controller
{
    // Show roll number generation page
    public function index()
    {
        // Get tests that have students but roll numbers not generated
        $tests = Test::with('college')
            ->where('roll_numbers_generated', false)
            ->whereHas('students')
            ->orderBy('test_date')
            ->get();
        
        return view('super_admin.roll_numbers.index', compact('tests'));
    }

    // Show preview before generating
    public function preview(Test $test)
    {
        if ($test->roll_numbers_generated) {
            return redirect()->route('super-admin.roll-numbers.index')
                ->with('error', 'Roll numbers already generated for this test.');
        }

        $test->load(['college', 'testVenues.testDistrict', 'students']);
        
        // Get student count per venue
        $venueStats = [];
        foreach ($test->testVenues as $venue) {
            $studentCount = Student::where('test_id', $test->id)
                ->where('test_district_id', $venue->test_district_id)
                ->count();
            
            $venueStats[] = [
                'venue' => $venue,
                'student_count' => $studentCount,
                'capacity' => $venue->total_capacity,
            ];
        }
        
        return view('super_admin.roll_numbers.preview', compact('test', 'venueStats'));
    }

    // Generate roll numbers
    public function generate(Test $test)
    {
        if ($test->roll_numbers_generated) {
            return redirect()->route('super-admin.roll-numbers.index')
                ->with('error', 'Roll numbers already generated for this test.');
        }

        DB::beginTransaction();
        
        try {
            $currentRollNumber = $test->starting_roll_number;
            $bookColors = ['Yellow', 'Green', 'Blue', 'Pink'];
            $colorIndex = 0;
            
            // Process each venue sequentially
            $venues = $test->testVenues()->with('testDistrict')->get();
            
            foreach ($venues as $venue) {
                // Get students for this venue
                $students = Student::where('test_id', $test->id)
                    ->where('test_district_id', $venue->test_district_id)
                    ->whereNull('roll_number')
                    ->orderBy('id')
                    ->get();
                
                if ($students->isEmpty()) {
                    continue;
                }
                
                // Calculate seating positions
                $hallNumber = 1;
                $zoneNumber = 1;
                $rowNumber = 1;
                $seatNumber = 1;
                
                foreach ($students as $student) {
                    // Assign roll number (5 digits)
                    $rollNumber = str_pad($currentRollNumber, 5, '0', STR_PAD_LEFT);
                    
                    // Assign book color (cycles through Yellow, Green, Blue, Pink)
                    $bookColor = $bookColors[$colorIndex % 4];
                    
                    // Update student
                    $student->update([
                        'roll_number' => $rollNumber,
                        'book_color' => $bookColor,
                        'hall_number' => $hallNumber,
                        'zone_number' => $zoneNumber,
                        'row_number' => $rowNumber,
                        'seat_number' => $seatNumber,
                    ]);
                    
                    // Increment for next student
                    $currentRollNumber++;
                    $colorIndex++;
                    
                    // Move to next seat
                    $seatNumber++;
                    
                    // Check if we need to move to next row
                    if ($seatNumber > $venue->seats_per_row) {
                        $seatNumber = 1;
                        $rowNumber++;
                        
                        // Check if we need to move to next zone
                        if ($rowNumber > $venue->rows_per_zone) {
                            $rowNumber = 1;
                            $zoneNumber++;
                            
                            // Check if we need to move to next hall
                            if ($zoneNumber > $venue->zones_per_hall) {
                                $zoneNumber = 1;
                                $hallNumber++;
                                
                                // Check if we exceeded hall capacity
                                if ($hallNumber > $venue->number_of_halls) {
                                    // This shouldn't happen if capacity was calculated correctly
                                    throw new \Exception('Exceeded venue capacity');
                                }
                            }
                        }
                    }
                }
            }
            
            // Mark test as roll numbers generated
            $test->update([
                'roll_numbers_generated' => true,
                'current_roll_number' => $currentRollNumber,
            ]);
            
            // Log action
            AuditLog::logAction(
                'super_admin',
                Auth::guard('super_admin')->id(),
                'generated_roll_numbers',
                'Test',
                $test->id,
                'Generated roll numbers for test: ' . $test->college->name . ' - ' . $test->test_date->format('d M Y'),
                null,
                ['total_students' => $currentRollNumber - $test->starting_roll_number]
            );
            
            DB::commit();
            
            return redirect()->route('super-admin.roll-numbers.index')
                ->with('success', 'Roll numbers generated successfully! Total students: ' . ($currentRollNumber - $test->starting_roll_number));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('super-admin.roll-numbers.index')
                ->with('error', 'Error generating roll numbers: ' . $e->getMessage());
        }
    }

    // Regenerate roll numbers (in case of error)
    public function regenerate(Test $test)
    {
        DB::beginTransaction();
        
        try {
            // Reset all students for this test
            Student::where('test_id', $test->id)->update([
                'roll_number' => null,
                'book_color' => null,
                'hall_number' => null,
                'zone_number' => null,
                'row_number' => null,
                'seat_number' => null,
            ]);
            
            // Reset test
            $test->update([
                'roll_numbers_generated' => false,
                'current_roll_number' => $test->starting_roll_number,
            ]);
            
            // Log action
            AuditLog::logAction(
                'super_admin',
                Auth::guard('super_admin')->id(),
                'reset_roll_numbers',
                'Test',
                $test->id,
                'Reset roll numbers for test: ' . $test->college->name,
                null,
                null
            );
            
            DB::commit();
            
            return redirect()->route('super-admin.roll-numbers.index')
                ->with('success', 'Roll numbers reset successfully. You can now regenerate them.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('super-admin.roll-numbers.index')
                ->with('error', 'Error resetting roll numbers: ' . $e->getMessage());
        }
    }
}