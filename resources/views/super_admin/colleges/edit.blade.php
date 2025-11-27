@extends('layouts.app')

@section('title', 'Edit College')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.colleges.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Colleges
                    </a>
                    <h1 class="text-xl font-bold">Edit College</h1>
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
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
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

            <form action="{{ route('super-admin.colleges.update', $college) }}" method="POST">
                @csrf
                @method('PUT')

                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">College Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- College Name -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            College Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $college->name) }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Contact Person -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Contact Person <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $college->contact_person) }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $college->email) }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $college->phone) }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Province -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Province <span class="text-red-500">*</span>
                        </label>
                        <select name="province" id="province" required onchange="toggleDivision()"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                            <option value="">Select Province</option>
                            <option value="Balochistan" {{ old('province', $college->province) == 'Balochistan' ? 'selected' : '' }}>Balochistan</option>
                            <option value="Sindh" {{ old('province', $college->province) == 'Sindh' ? 'selected' : '' }}>Sindh</option>
                            <option value="Punjab" {{ old('province', $college->province) == 'Punjab' ? 'selected' : '' }}>Punjab</option>
                            <option value="Khyber Pakhtunkhwa" {{ old('province', $college->province) == 'Khyber Pakhtunkhwa' ? 'selected' : '' }}>Khyber Pakhtunkhwa</option>
                            <option value="Gilgit Baltistan" {{ old('province', $college->province) == 'Gilgit Baltistan' ? 'selected' : '' }}>Gilgit Baltistan</option>
                            <option value="Azad Jammu Kashmir" {{ old('province', $college->province) == 'Azad Jammu Kashmir' ? 'selected' : '' }}>Azad Jammu Kashmir</option>
                            <option value="Islamabad" {{ old('province', $college->province) == 'Islamabad' ? 'selected' : '' }}>Islamabad</option>
                        </select>
                    </div>

                    <!-- Division (Only for Balochistan) -->
                    <div id="divisionContainer" style="display: none;">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Division
                        </label>
                        <select name="division" id="division"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                            <option value="">Select Division</option>
                            <option value="Quetta" {{ old('division', $college->division) == 'Quetta' ? 'selected' : '' }}>Quetta</option>
                            <option value="Kalat" {{ old('division', $college->division) == 'Kalat' ? 'selected' : '' }}>Kalat</option>
                            <option value="Makran" {{ old('division', $college->division) == 'Makran' ? 'selected' : '' }}>Makran</option>
                            <option value="Nasirabad" {{ old('division', $college->division) == 'Nasirabad' ? 'selected' : '' }}>Nasirabad</option>
                            <option value="Rakhshan" {{ old('division', $college->division) == 'Rakhshan' ? 'selected' : '' }}>Rakhshan</option>
                            <option value="Sibi" {{ old('division', $college->division) == 'Sibi' ? 'selected' : '' }}>Sibi</option>
                            <option value="Zhob" {{ old('division', $college->division) == 'Zhob' ? 'selected' : '' }}>Zhob</option>
                            <option value="Loralai" {{ old('division', $college->division) == 'Loralai' ? 'selected' : '' }}>Loralai</option>
                        </select>
                    </div>

                    <!-- District -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            District <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="district" value="{{ old('district', $college->district) }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" rows="3" required
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">{{ old('address', $college->address) }}</textarea>
                    </div>

                    <!-- Age Policy -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Minimum Age (Optional)
                        </label>
                        <input type="number" name="min_age" value="{{ old('min_age', $college->min_age) }}" min="1" max="100"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                               placeholder="e.g., 17">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Maximum Age (Optional)
                        </label>
                        <input type="number" name="max_age" value="{{ old('max_age', $college->max_age) }}" min="1" max="100"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                               placeholder="e.g., 25">
                    </div>

                    <!-- Gender Policy -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Gender Policy <span class="text-red-500">*</span>
                        </label>
                        <select name="gender_policy" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                            <option value="">Select Gender Policy</option>
                            <option value="Male Only" {{ old('gender_policy', $college->gender_policy) == 'Male Only' ? 'selected' : '' }}>Male Only</option>
                            <option value="Female Only" {{ old('gender_policy', $college->gender_policy) == 'Female Only' ? 'selected' : '' }}>Female Only</option>
                            <option value="Both" {{ old('gender_policy', $college->gender_policy) == 'Both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                    <!-- Registration Start Date -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Registration Start Date (Optional)
                        </label>
                        <input type="date" name="registration_start_date" value="{{ old('registration_start_date', $college->registration_start_date ? $college->registration_start_date->format('Y-m-d') : '') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Age will be calculated on this date</p>
                    </div>
                    <!-- Status -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="is_active" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                            <option value="1" {{ old('is_active', $college->is_active) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $college->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Test Districts Information -->
                <div class="border-t-2 border-gray-200 pt-6 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Assigned Test Districts</h3>
                        <a href="{{ route('super-admin.colleges.add-test-districts', $college) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add More Districts
                        </a>
                    </div>
                    
                    @if($college->testDistricts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($college->testDistricts as $index => $district)
                                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                    <h4 class="font-bold text-gray-700 mb-2">Test District #{{ $index + 1 }}</h4>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold">Province:</span> {{ $district->province }}
                                    </p>
                                    @if($district->division)
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold">Division:</span> {{ $district->division }}
                                    </p>
                                    @endif
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold">District:</span> {{ $district->district }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                            <p class="text-sm">
                                <strong>Note:</strong> You can add new test districts but cannot remove existing ones to protect student data.
                            </p>
                        </div>
                    @else
                        <p class="text-gray-500 mb-3">No test districts assigned yet.</p>
                        <div class="p-3 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700">
                            <p class="text-sm">
                                <strong>Warning:</strong> This college has no test districts. Please add at least one test district.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('super-admin.colleges.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Update College
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDivision() {
    const province = document.getElementById('province').value;
    const divisionContainer = document.getElementById('divisionContainer');
    const divisionSelect = document.getElementById('division');
    
    if (province === 'Balochistan') {
        divisionContainer.style.display = 'block';
        divisionSelect.required = true;
    } else {
        divisionContainer.style.display = 'none';
        divisionSelect.required = false;
        divisionSelect.value = '';
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDivision();
});
</script>
@endsection