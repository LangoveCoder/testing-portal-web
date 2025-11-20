<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestDistrict;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // Show all students
    public function index(Request $request)
    {
        $query = Student::with(['test.college', 'testDistrict']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%");
            });
        }
        
        // Filter by college
        if ($request->filled('college_id')) {
            $query->whereHas('test', function($q) use ($request) {
                $q->where('college_id', $request->college_id);
            });
        }
        
        // Filter by test
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->test_id);
        }
        
        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        $students = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get colleges for filter dropdown
        $colleges = \App\Models\College::orderBy('name')->get();
        
        // Get tests for filter dropdown
        $tests = Test::with('college')->orderBy('test_date', 'desc')->get();
        
        return view('super_admin.students.index', compact('students', 'colleges', 'tests'));
    }

    // Show single student
    public function show(Student $student)
    {
        $student->load(['test.college', 'testDistrict']);
        return view('super_admin.students.show', compact('student'));
    }

    // Show edit form
    public function edit(Student $student)
    {
        $student->load(['test.college', 'testDistrict']);
        
        // Get available test districts for this college
        $testDistricts = $student->test->college->testDistricts;
        
        return view('super_admin.students.edit', compact('student', 'testDistricts'));
    }

    // Update student
    public function update(Request $request, Student $student)
    {
        // If roll numbers generated, only allow test district change
        if ($student->roll_number) {
            $validated = $request->validate([
                'test_district_id' => 'required|exists:test_districts,id',
            ]);
            
            $oldValues = $student->toArray();
            $student->update(['test_district_id' => $validated['test_district_id']]);
            
            AuditLog::logAction(
                'super_admin',
                Auth::guard('super_admin')->id(),
                'updated',
                'Student',
                $student->id,
                'Changed test district for student: ' . $student->name,
                $oldValues,
                $validated
            );
            
            return redirect()->route('super-admin.students.index')
                ->with('success', 'Student test district updated successfully!');
        }
        
        // Full edit if roll numbers not generated
        $validated = $request->validate([
            'test_district_id' => 'required|exists:test_districts,id',
            'name' => 'required|string|max:255',
            'cnic' => 'required|string|size:13|unique:students,cnic,' . $student->id,
            'father_name' => 'required|string|max:255',
            'father_cnic' => 'required|string|size:13',
            'gender' => 'required|in:Male,Female',
            'religion' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'province' => 'required|string',
            'division' => 'nullable|string',
            'district' => 'required|string',
            'address' => 'required|string',
            'picture' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        
        // Handle picture upload
        if ($request->hasFile('picture')) {
            // Delete old picture
            if ($student->picture) {
                Storage::disk('public')->delete($student->picture);
            }
            
            $picturePath = $request->file('picture')->store('student-pictures', 'public');
            $validated['picture'] = $picturePath;
        }
        
        $oldValues = $student->toArray();
        $student->update($validated);
        
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'updated',
            'Student',
            $student->id,
            'Updated student: ' . $student->name,
            $oldValues,
            $validated
        );
        
        return redirect()->route('super-admin.students.index')
            ->with('success', 'Student updated successfully!');
    }

    // Delete student
    public function destroy(Student $student)
    {
        if ($student->roll_number) {
            return redirect()->route('super-admin.students.index')
                ->with('error', 'Cannot delete student after roll numbers are generated.');
        }
        
        $studentName = $student->name;
        
        // Delete picture
        if ($student->picture) {
            Storage::disk('public')->delete($student->picture);
        }
        
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'deleted',
            'Student',
            $student->id,
            'Deleted student: ' . $studentName,
            $student->toArray(),
            null
        );
        
        $student->delete();
        
        return redirect()->route('super-admin.students.index')
            ->with('success', 'Student deleted successfully!');
    }
}