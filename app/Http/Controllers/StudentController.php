<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Result;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Show check roll number page
     */
    public function checkRollNumber()
    {
        return view('student.check-roll-number');
    }

    /**
     * Search roll number
     */
    public function searchRollNumber(Request $request)
    {
        $request->validate([
            'cnic' => 'required|digits:13',
            'registration_id' => 'required|string',
        ]);

        // Find student by CNIC and Registration ID
        $student = Student::with('test')
            ->where('cnic', $request->cnic)
            ->where('registration_id', $request->registration_id)
            ->first();

        if (!$student) {
            return back()
                ->withInput()
                ->with('error', 'No record found with the provided CNIC and Registration ID. Please check your details and try again.');
        }

        // Check if roll number has been generated
        if (!$student->roll_number) {
            return back()
                ->withInput()
                ->with('error', 'Roll number has not been generated yet for this student. Please check back later.');
        }

        return view('student.check-roll-number', compact('student'));
    }

    /**
     * Show check result page
     */
    public function checkResult()
    {
        return view('student.check-result');
    }

    /**
     * Search result
     */
    public function searchResult(Request $request)
    {
        $request->validate([
            'cnic' => 'required|digits:13',
            'roll_number' => 'required|string',
        ]);

        // Find student by CNIC
        $student = Student::where('cnic', $request->cnic)
            ->where('roll_number', $request->roll_number)
            ->first();

        if (!$student) {
            return back()
                ->withInput()
                ->with('error', 'No record found with the provided CNIC and Roll Number. Please check your details and try again.');
        }

        // Find result for this student
        $result = Result::with(['student', 'test'])
            ->where('student_id', $student->id)
            ->first();

        if (!$result) {
            return back()
                ->withInput()
                ->with('error', 'Result has not been uploaded yet for this student. Please check back later.');
        }

        // Check if result is published
        if (!$result->is_published) {
            return back()
                ->withInput()
                ->with('error', 'Result has not been published yet. Please check back later.');
        }

        return view('student.check-result', compact('result'));
    }
}