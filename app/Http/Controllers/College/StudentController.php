<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Test;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    // Show list of students
    public function index()
    {
        $college = Auth::guard('college')->user();
        $students = Student::whereHas('test', function($query) use ($college) {
            $query->where('college_id', $college->id);
        })->with(['test', 'testDistrict'])->orderBy('created_at', 'desc')->get();
        
        return view('college.students.index', compact('students'));
    }

    // Show registration form
    public function create()
    {
        $college = Auth::guard('college')->user();
        
        // Get active tests for this college where registration is still open
        $tests = Test::where('college_id', $college->id)
            ->where('registration_deadline', '>=', now())
            ->where('roll_numbers_generated', false)
            ->orderBy('test_date')
            ->get();
        
        return view('college.students.create', compact('tests', 'college'));
    }

    // Store new student
    public function store(Request $request)
    {
        $college = Auth::guard('college')->user();
        
        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'test_center_id' => 'required|exists:test_districts,id',
            'picture' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'name' => 'required|string|max:255',
            'cnic' => 'required|string|size:13|unique:students,cnic',
            'father_name' => 'required|string|max:255',
            'father_cnic' => 'required|string|size:13',
            'gender' => 'required|in:Male,Female',
            'religion' => 'required|string|max:100',
            'disability' => 'required|in:Yes,No',
            'date_of_birth' => 'required|date|before:today',
            'province' => 'required|string',
            'division' => 'nullable|string',
            'district' => 'required|string',
            'address' => 'required|string',
        ]);

        // Verify test belongs to this college
        $test = Test::where('id', $validated['test_id'])
            ->where('college_id', $college->id)
            ->firstOrFail();

        // Check gender policy
        if ($college->gender_policy != 'Both' && $college->gender_policy != $validated['gender'] . ' Only') {
            return back()->withErrors(['gender' => 'This college only accepts ' . $college->gender_policy . ' students.'])->withInput();
        }

        // Calculate age on registration start date
        if ($college->registration_start_date) {
            $age = \Carbon\Carbon::parse($validated['date_of_birth'])
                ->diff(\Carbon\Carbon::parse($college->registration_start_date))
                ->y;

            if ($college->min_age && $age < $college->min_age) {
                return back()->withErrors(['date_of_birth' => 'Student must be at least ' . $college->min_age . ' years old.'])->withInput();
            }

            if ($college->max_age && $age > $college->max_age) {
                return back()->withErrors(['date_of_birth' => 'Student must not be older than ' . $college->max_age . ' years.'])->withInput();
            }
        }

        // Handle picture upload
if ($request->hasFile('picture')) {
    $picturePath = $request->file('picture')->store('student-pictures', 'public');
    $validated['picture'] = $picturePath;
}
        // Generate unique registration ID
        $validated['registration_id'] = 'REG-' . strtoupper(Str::random(10));

        // Create student
        $student = Student::create([
    'test_id' => $validated['test_id'],
    'test_district_id' => $validated['test_center_id'],
    'picture' => $validated['picture'],
    'name' => $validated['name'],
    'cnic' => $validated['cnic'],
    'father_name' => $validated['father_name'],
    'father_cnic' => $validated['father_cnic'],
    'gender' => $validated['gender'],
    'religion' => $validated['religion'],
    'disability' => $validated['disability'],
    'date_of_birth' => $validated['date_of_birth'],
        'province' => $validated['province'],
            'division' => $validated['division'],
            'district' => $validated['district'],
            'address' => $validated['address'],
            'registration_id' => $validated['registration_id'],
        ]);

        // Log action
        AuditLog::logAction(
            'college_admin',
            $college->id,
            'created',
            'Student',
            $student->id,
            'Registered student: ' . $student->name . ' (CNIC: ' . $student->cnic . ')',
            null,
            $validated
        );

        return redirect()->route('college.students.index')
            ->with('success', 'Student registered successfully! Registration ID: ' . $student->registration_id);
    }

    // Show single student
    public function show(Student $student)
    {
        // Verify student belongs to this college
        $college = Auth::guard('college')->user();
        if ($student->test->college_id != $college->id) {
            abort(403, 'Unauthorized access');
        }

        return view('college.students.show', compact('student'));
    }

    // Show edit form
    public function edit(Student $student)
    {
        // Can only edit if roll numbers not generated
        if ($student->roll_number) {
            return redirect()->route('college.students.index')
                ->with('error', 'Cannot edit student after roll numbers are generated.');
        }

        $college = Auth::guard('college')->user();
        
        // Verify student belongs to this college
        if ($student->test->college_id != $college->id) {
            abort(403, 'Unauthorized access');
        }

        $tests = Test::where('college_id', $college->id)
            ->where('registration_deadline', '>=', now())
            ->orderBy('test_date')
            ->get();

        return view('college.students.edit', compact('student', 'tests', 'college'));
    }

    // Update student
    public function update(Request $request, Student $student)
    {
        // Implementation later
    }

    // Delete student
    public function destroy(Student $student)
    {
        // Can only delete if roll numbers not generated
        if ($student->roll_number) {
            return redirect()->route('college.students.index')
                ->with('error', 'Cannot delete student after roll numbers are generated.');
        }

        $college = Auth::guard('college')->user();
        
        // Verify student belongs to this college
        if ($student->test->college_id != $college->id) {
            abort(403, 'Unauthorized access');
        }

        $studentName = $student->name;
        
        AuditLog::logAction(
            'college_admin',
            $college->id,
            'deleted',
            'Student',
            $student->id,
            'Deleted student: ' . $studentName,
            $student->toArray(),
            null
        );

        $student->delete();

        return redirect()->route('college.students.index')
            ->with('success', 'Student deleted successfully!');
    }
    
    // API: Get test districts for a test
    public function getTestDistricts(Test $test)
    {
        $college = Auth::guard('college')->user();
        
        // Verify test belongs to this college
        if ($test->college_id != $college->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get test districts assigned to this college
        $testDistricts = $college->testDistricts()
            ->select('id', 'province', 'division', 'district')
            ->get();
        
        return response()->json($testDistricts);
    }
}