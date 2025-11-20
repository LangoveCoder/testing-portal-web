<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\College;
use App\Models\TestVenue;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    // Show list of all tests
    public function index()
    {
        $tests = Test::with('college')->orderBy('test_date', 'desc')->get();
        return view('super_admin.tests.index', compact('tests'));
    }

    // Show form to create new test
    public function create()
    {
        $colleges = College::where('is_active', true)->orderBy('name')->get();
        return view('super_admin.tests.create', compact('colleges'));
    }

    // Store new test
    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'test_date' => 'required|date|after_or_equal:today',
            'test_time' => 'required|date_format:H:i',
            'registration_deadline' => 'required|date|before:test_date',
            'test_mode' => 'required|in:mode_1,mode_2,mode_3',
            'total_marks' => 'required|integer|min:1',
            'starting_roll_number' => 'required|integer|min:1|max:99999|unique:tests,starting_roll_number',
            'test_venues' => 'required|array|min:1',
            'test_venues.*.test_district_id' => 'required|exists:test_districts,id',
            'test_venues.*.venue_name' => 'required|string',
            'test_venues.*.venue_address' => 'required|string',
            'test_venues.*.number_of_halls' => 'required|integer|min:1',
            'test_venues.*.zones_per_hall' => 'required|integer|min:1',
            'test_venues.*.rows_per_zone' => 'required|integer|min:1',
            'test_venues.*.seats_per_row' => 'required|integer|min:1',
        ]);

        // Create test
        $test = Test::create([
            'college_id' => $validated['college_id'],
            'test_date' => $validated['test_date'],
            'test_time' => $validated['test_time'],
            'registration_deadline' => $validated['registration_deadline'],
            'test_mode' => $validated['test_mode'],
            'total_marks' => $validated['total_marks'],
            'starting_roll_number' => $validated['starting_roll_number'],
            'current_roll_number' => $validated['starting_roll_number'],
            'roll_numbers_generated' => false,
            'results_published' => false,
        ]);

        // Create test venues
        foreach ($validated['test_venues'] as $venue) {
            $totalCapacity = $venue['number_of_halls'] * 
                           $venue['zones_per_hall'] * 
                           $venue['rows_per_zone'] * 
                           $venue['seats_per_row'];

            TestVenue::create([
                'test_id' => $test->id,
                'test_district_id' => $venue['test_district_id'],
                'venue_name' => $venue['venue_name'],
                'venue_address' => $venue['venue_address'],
                'number_of_halls' => $venue['number_of_halls'],
                'zones_per_hall' => $venue['zones_per_hall'],
                'rows_per_zone' => $venue['rows_per_zone'],
                'seats_per_row' => $venue['seats_per_row'],
                'total_capacity' => $totalCapacity,
            ]);
        }

        // Log action
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'created',
            'Test',
            $test->id,
            'Created test for ' . $test->college->name . ' on ' . $test->test_date->format('d M Y'),
            null,
            $validated
        );

        return redirect()->route('super-admin.tests.index')
            ->with('success', 'Test created successfully!');
    }

    // Show single test
    public function show(Test $test)
    {
        $test->load(['college', 'testVenues.testDistrict']);
        return view('super_admin.tests.show', compact('test'));
    }

    // Show edit form
    public function edit(Test $test)
    {
        $colleges = College::where('is_active', true)->orderBy('name')->get();
        $test->load(['testVenues.testDistrict']);
        return view('super_admin.tests.edit', compact('test', 'colleges'));
    }

    // Update test
    public function update(Request $request, Test $test)
    {
        // Implementation later
    }

    // Delete test
    public function destroy(Test $test)
    {
        $testInfo = $test->college->name . ' - ' . $test->test_date->format('d M Y');
        
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'deleted',
            'Test',
            $test->id,
            'Deleted test: ' . $testInfo,
            $test->toArray(),
            null
        );

        $test->delete();

        return redirect()->route('super-admin.tests.index')
            ->with('success', 'Test deleted successfully!');
    }
}