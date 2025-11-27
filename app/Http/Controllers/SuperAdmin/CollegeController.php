<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\TestDistrict;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CollegeController extends Controller
{
    // Show list of all colleges
    public function index()
    {
        $colleges = College::orderBy('created_at', 'desc')->get();
        return view('super_admin.colleges.index', compact('colleges'));
    }

    // Show form to create new college
    public function create()
    {
        return view('super_admin.colleges.create');
    }

    // Store new college
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:colleges,email',
            'phone' => 'required|string|max:20',
            'province' => 'required|string',
            'division' => 'nullable|string',
            'district' => 'required|string',
            'address' => 'required|string',
            'min_age' => 'nullable|integer|min:1|max:100',
            'max_age' => 'nullable|integer|min:1|max:100|gte:min_age',
            'gender_policy' => 'required|in:Male Only,Female Only,Both',
            'registration_start_date' => 'nullable|date',
            'password' => 'required|string|min:6|confirmed',
            'test_districts' => 'required|array|min:1',
            'test_districts.*.province' => 'required|string',
            'test_districts.*.division' => 'nullable|string',
            'test_districts.*.district' => 'required|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        // Create college
        $college = College::create([
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'province' => $validated['province'],
            'division' => $validated['division'],
            'district' => $validated['district'],
            'address' => $validated['address'],
            'min_age' => $validated['min_age'],
            'max_age' => $validated['max_age'],
            'gender_policy' => $validated['gender_policy'],
            'registration_start_date' => $validated['registration_start_date'],
            'password' => $validated['password'],
            'is_active' => $validated['is_active'],
        ]);

        // Create test districts
        foreach ($validated['test_districts'] as $district) {
            $college->testDistricts()->create([
                'province' => $district['province'],
                'division' => $district['division'] ?? null,
                'district' => $district['district'],
            ]);
        }

        // Log action
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'created',
            'College',
            $college->id,
            'Created college: ' . $college->name . ' with ' . count($validated['test_districts']) . ' test districts',
            null,
            $validated
        );

        return redirect()->route('super-admin.colleges.index')
            ->with('success', 'College registered successfully with test districts!');
    }

    // Show single college
    public function show(College $college)
    {
        return view('super_admin.colleges.show', compact('college'));
    }

    // Show edit form
    public function edit(College $college)
    {
        return view('super_admin.colleges.edit', compact('college'));
    }

    // Update college
    public function update(Request $request, College $college)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:colleges,email,' . $college->id,
            'phone' => 'required|string|max:20',
            'province' => 'required|string',
            'division' => 'nullable|string',
            'district' => 'required|string',
            'address' => 'required|string',
            'min_age' => 'nullable|integer|min:1|max:100',
            'max_age' => 'nullable|integer|min:1|max:100|gte:min_age',
            'gender_policy' => 'required|in:Male Only,Female Only,Both',
            'registration_start_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $oldValues = $college->toArray();
        $college->update($validated);

        // Log action
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'updated',
            'College',
            $college->id,
            'Updated college: ' . $college->name,
            $oldValues,
            $validated
        );

        return redirect()->route('super-admin.colleges.index')
            ->with('success', 'College updated successfully!');
    }

    // Delete college
    public function destroy(College $college)
    {
        $collegeName = $college->name;
        
        // Log action before deleting
        AuditLog::logAction(
            'super_admin',
            Auth::guard('super_admin')->id(),
            'deleted',
            'College',
            $college->id,
            'Deleted college: ' . $collegeName,
            $college->toArray(),
            null
        );

        $college->delete();

        return redirect()->route('super-admin.colleges.index')
            ->with('success', 'College deleted successfully!');
    }

    // API: Get test districts for a college
    public function getTestDistricts(College $college)
    {
        $testDistricts = $college->testDistricts()
            ->select('id', 'province', 'division', 'district')
            ->get();
        
        return response()->json($testDistricts);
    }

    // Show form to add test districts to existing college
    public function addTestDistricts(College $college)
    {
        return view('super_admin.colleges.add-test-districts', compact('college'));
    }

    // Store new test districts for existing college
    public function storeTestDistricts(Request $request, College $college)
    {
        $validated = $request->validate([
            'test_districts' => 'required|array|min:1',
            'test_districts.*.province' => 'required|string',
            'test_districts.*.division' => 'nullable|string',
            'test_districts.*.district' => 'required|string',
        ]);

        $addedDistricts = [];
        $duplicates = [];

        foreach ($validated['test_districts'] as $districtData) {
            // Check if this district already exists for this college
            $exists = $college->testDistricts()
                ->where('province', $districtData['province'])
                ->where('district', $districtData['district'])
                ->exists();

            if ($exists) {
                $duplicates[] = $districtData['district'] . ', ' . $districtData['province'];
            } else {
                $college->testDistricts()->create([
                    'province' => $districtData['province'],
                    'division' => $districtData['division'] ?? null,
                    'district' => $districtData['district'],
                ]);
                $addedDistricts[] = $districtData['district'] . ', ' . $districtData['province'];
            }
        }

        // Log action
        if (count($addedDistricts) > 0) {
            AuditLog::logAction(
                'super_admin',
                Auth::guard('super_admin')->id(),
                'updated',
                'College',
                $college->id,
                'Added ' . count($addedDistricts) . ' test district(s) to college: ' . $college->name,
                null,
                ['added_districts' => $addedDistricts]
            );
        }

        $message = '';
        if (count($addedDistricts) > 0) {
            $message .= count($addedDistricts) . ' test district(s) added successfully! ';
        }
        if (count($duplicates) > 0) {
            $message .= count($duplicates) . ' district(s) already existed and were skipped.';
        }

        return redirect()->route('super-admin.colleges.show', $college)
            ->with('success', $message);
    }
}