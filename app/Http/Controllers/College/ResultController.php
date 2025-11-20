<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    // Show list of tests with results
    public function index()
    {
        $college = Auth::guard('college')->user();
        
        // Get tests for this college that have published results
        $tests = Test::where('college_id', $college->id)
            ->whereHas('results', function($query) {
                $query->where('is_published', true);
            })
            ->with(['results' => function($query) {
                $query->where('is_published', true);
            }])
            ->orderBy('test_date', 'desc')
            ->get();
        
        return view('college.results.index', compact('tests'));
    }
    
    // Show results for a specific test
    public function show(Test $test)
    {
        $college = Auth::guard('college')->user();
        
        // Check if this test belongs to the college
        if ($test->college_id != $college->id) {
            return redirect()->route('college.results.index')
                ->with('error', 'You do not have access to this test.');
        }
        
        // Get published results for this test
        $results = Result::where('test_id', $test->id)
            ->where('is_published', true)
            ->with('student')
            ->orderBy('marks', 'desc')
            ->paginate(50);
        
        // Calculate statistics
        $totalStudents = $results->total();
        $averageMarks = $results->avg('marks');
        $highestMarks = $results->max('marks');
        $lowestMarks = $results->min('marks');
        
        return view('college.results.show', compact('test', 'results', 'totalStudents', 'averageMarks', 'highestMarks', 'lowestMarks'));
    }
}