@extends('layouts.app')

@section('title', 'Create New Test')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.tests.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Tests
                    </a>
                    <h1 class="text-xl font-bold">Create New Test</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('super_admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('super-admin.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('super-admin.tests.store') }}" method="POST" id="testForm">
                @csrf

                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Test Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- College Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Select College <span class="text-red-500">*</span>
                        </label>
                        <select name="college_id" id="college_id" required onchange="loadTestDistricts()"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                            <option value="">-- Select College --</option>
                            @foreach($colleges as $college)
                                <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>
                                    {{ $college->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Test Date -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Test Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="test_date" value="{{ old('test_date') }}" required
                               min="{{ date('Y-m-d') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Test Time -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Test Time <span class="text-red-500">*</span>
                        </label>
                        <input type="time" name="test_time" value="{{ old('test_time', '09:00') }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Registration Deadline -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Registration Deadline <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="registration_deadline" value="{{ old('registration_deadline') }}" required
                               min="{{ date('Y-m-d') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Test Mode -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Test Mode <span class="text-red-500">*</span>
                        </label>
                        <select name="test_mode" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                            <option value="">Select Mode</option>
                            <option value="mode_1" {{ old('test_mode') == 'mode_1' ? 'selected' : '' }}>Mode 1 (MCQ + Subjective)</option>
                            <option value="mode_2" {{ old('test_mode') == 'mode_2' ? 'selected' : '' }}>Mode 2 (MCQ Only)</option>
                            <option value="mode_3" {{ old('test_mode') == 'mode_3' ? 'selected' : '' }}>Mode 3 (General MCQ)</option>
                        </select>
                    </div>

                    <!-- Total Marks -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Total Marks <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="total_marks" value="{{ old('total_marks', '100') }}" required min="1"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Starting Roll Number -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Starting Roll Number <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="starting_roll_number" value="{{ old('starting_roll_number', '1') }}" required min="1" max="99999"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Roll numbers will be formatted as 5 digits (e.g., 00001). This starting number cannot be reused.</p>
                    </div>

                    <!-- Test Venues Section -->
                    <div class="md:col-span-2 mt-8">
                        <div class="border-t-2 border-gray-200 pt-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Configure Test Venues</h2>
                            <p class="text-sm text-gray-600 mb-4">Select a college first to see available test districts and configure venues.</p>
                            
                            <div id="testVenuesContainer">
                                <p class="text-gray-500 italic">Please select a college above to configure test venues...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('super-admin.tests.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Create Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let testDistricts = [];

async function loadTestDistricts() {
    const collegeId = document.getElementById('college_id').value;
    const container = document.getElementById('testVenuesContainer');
    
    if (!collegeId) {
        container.innerHTML = '<p class="text-gray-500 italic">Please select a college above to configure test venues...</p>';
        return;
    }
    
    container.innerHTML = '<p class="text-gray-500">Loading test districts...</p>';
    
    try {
        const response = await fetch(`/super-admin/colleges/${collegeId}/test-districts`);
        const data = await response.json();
        testDistricts = data;
        
        if (testDistricts.length === 0) {
            container.innerHTML = '<p class="text-red-500">This college has no test districts assigned. Please edit the college and add test districts first.</p>';
            return;
        }
        
        renderTestVenues();
    } catch (error) {
        container.innerHTML = '<p class="text-red-500">Error loading test districts. Please try again.</p>';
        console.error('Error:', error);
    }
}

function renderTestVenues() {
    const container = document.getElementById('testVenuesContainer');
    let html = '';
    
    testDistricts.forEach((district, index) => {
        html += `
            <div class="border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
                <h3 class="font-bold text-gray-800 mb-3">Test Venue for: ${district.district}, ${district.province}</h3>
                <input type="hidden" name="test_venues[${index}][test_district_id]" value="${district.id}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Venue Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="test_venues[${index}][venue_name]" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                               placeholder="e.g., City School Main Campus">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Venue Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="test_venues[${index}][venue_address]" rows="2" required
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                                  placeholder="Complete address with landmarks"></textarea>
                    </div>
                    <div class="md:col-span-2">
    <label class="block text-gray-700 text-sm font-bold mb-2">
        Google Maps Link <span class="text-red-500">*</span>
    </label>
    <input type="url" name="test_venues[${index}][google_maps_link]" required
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
           placeholder="https://maps.google.com/...">
    <p class="text-xs text-gray-500 mt-1">Right-click on Google Maps location → Copy link address</p>
</div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Number of Halls <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="test_venues[${index}][number_of_halls]" required min="1" value="1"
                               onchange="calculateCapacity(${index})"
                               class="venue-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Zones per Hall <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="test_venues[${index}][zones_per_hall]" required min="1" value="1"
                               onchange="calculateCapacity(${index})"
                               class="venue-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Rows per Zone <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="test_venues[${index}][rows_per_zone]" required min="1" value="5"
                               onchange="calculateCapacity(${index})"
                               class="venue-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Seats per Row <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="test_venues[${index}][seats_per_row]" required min="1" value="10"
                               onchange="calculateCapacity(${index})"
                               class="venue-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded p-3">
                            <p class="text-sm font-bold text-blue-800">Total Capacity: <span id="capacity-${index}">0</span> students</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Calculate initial capacities
    testDistricts.forEach((district, index) => {
        calculateCapacity(index);
    });
}

function calculateCapacity(index) {
    const form = document.getElementById('testForm');
    const halls = parseInt(form.elements[`test_venues[${index}][number_of_halls]`].value) || 0;
    const zones = parseInt(form.elements[`test_venues[${index}][zones_per_hall]`].value) || 0;
    const rows = parseInt(form.elements[`test_venues[${index}][rows_per_zone]`].value) || 0;
    const seats = parseInt(form.elements[`test_venues[${index}][seats_per_row]`].value) || 0;
    
    const capacity = halls * zones * rows * seats;
    document.getElementById(`capacity-${index}`).textContent = capacity;
}

// Load test districts on page load if college is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const collegeId = document.getElementById('college_id').value;
    if (collegeId) {
        loadTestDistricts();
    }
});
</script>
@endsection