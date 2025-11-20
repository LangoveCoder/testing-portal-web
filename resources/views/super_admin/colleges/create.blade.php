@extends('layouts.app')

@section('title', 'Register New College')

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
                    <h1 class="text-xl font-bold">Register New College</h1>
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

            <form action="{{ route('super-admin.colleges.store') }}" method="POST">
                @csrf

                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">College Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- College Name -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            College Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Contact Person -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Contact Person <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
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
                            <option value="Balochistan" {{ old('province') == 'Balochistan' ? 'selected' : '' }}>Balochistan</option>
                            <option value="Sindh" {{ old('province') == 'Sindh' ? 'selected' : '' }}>Sindh</option>
                            <option value="Punjab" {{ old('province') == 'Punjab' ? 'selected' : '' }}>Punjab</option>
                            <option value="Khyber Pakhtunkhwa" {{ old('province') == 'Khyber Pakhtunkhwa' ? 'selected' : '' }}>Khyber Pakhtunkhwa</option>
                            <option value="Gilgit Baltistan" {{ old('province') == 'Gilgit Baltistan' ? 'selected' : '' }}>Gilgit Baltistan</option>
                            <option value="Azad Jammu Kashmir" {{ old('province') == 'Azad Jammu Kashmir' ? 'selected' : '' }}>Azad Jammu Kashmir</option>
                            <option value="Islamabad" {{ old('province') == 'Islamabad' ? 'selected' : '' }}>Islamabad</option>
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
                            <option value="Quetta" {{ old('division') == 'Quetta' ? 'selected' : '' }}>Quetta</option>
                            <option value="Kalat" {{ old('division') == 'Kalat' ? 'selected' : '' }}>Kalat</option>
                            <option value="Makran" {{ old('division') == 'Makran' ? 'selected' : '' }}>Makran</option>
                            <option value="Nasirabad" {{ old('division') == 'Nasirabad' ? 'selected' : '' }}>Nasirabad</option>
                            <option value="Rakhshan" {{ old('division') == 'Rakhshan' ? 'selected' : '' }}>Rakhshan</option>
                            <option value="Sibi" {{ old('division') == 'Sibi' ? 'selected' : '' }}>Sibi</option>
                            <option value="Zhob" {{ old('division') == 'Zhob' ? 'selected' : '' }}>Zhob</option>
                            <option value="Loralai" {{ old('division') == 'Loralai' ? 'selected' : '' }}>Loralai</option>
                        </select>
                    </div>

                    <!-- District -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            District <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="district" value="{{ old('district') }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" rows="3" required
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">{{ old('address') }}</textarea>
                    </div>

                    <!-- Age Policy -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Minimum Age (Optional)
                        </label>
                        <input type="number" name="min_age" value="{{ old('min_age') }}" min="1" max="100"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                               placeholder="e.g., 17">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Maximum Age (Optional)
                        </label>
                        <input type="number" name="max_age" value="{{ old('max_age') }}" min="1" max="100"
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
                            <option value="Male Only" {{ old('gender_policy') == 'Male Only' ? 'selected' : '' }}>Male Only</option>
                            <option value="Female Only" {{ old('gender_policy') == 'Female Only' ? 'selected' : '' }}>Female Only</option>
                            <option value="Both" {{ old('gender_policy') == 'Both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>
                    <!-- Registration Start Date -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Registration Start Date (Optional)
                        </label>
                        <input type="date" name="registration_start_date" value="{{ old('registration_start_date') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Age will be calculated on this date</p>
                    </div>
                    <!-- Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required minlength="6"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required minlength="6"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Test Districts Section -->
                    <div class="md:col-span-2 mt-8">
                        <div class="border-t-2 border-gray-200 pt-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4">Assign Test Districts</h2>
                            <p class="text-sm text-gray-600 mb-4">Add districts where students can take their tests. Test centers (venues) will be assigned later when creating the test.</p>
                            
                            <div id="testDistrictsContainer">
                                <!-- Test District 1 (Default) -->
                                <div class="test-district-block border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-bold text-gray-700">Test District #1</h4>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                                Province <span class="text-red-500">*</span>
                                            </label>
                                            <select name="test_districts[0][province]" required onchange="toggleTestDistrictDivision(0)"
                                                    class="td-province shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                                                <option value="">Select Province</option>
                                                <option value="Balochistan" {{ old('test_districts.0.province') == 'Balochistan' ? 'selected' : '' }}>Balochistan</option>
                                                <option value="Sindh" {{ old('test_districts.0.province') == 'Sindh' ? 'selected' : '' }}>Sindh</option>
                                                <option value="Punjab" {{ old('test_districts.0.province') == 'Punjab' ? 'selected' : '' }}>Punjab</option>
                                                <option value="Khyber Pakhtunkhwa" {{ old('test_districts.0.province') == 'Khyber Pakhtunkhwa' ? 'selected' : '' }}>Khyber Pakhtunkhwa</option>
                                                <option value="Gilgit Baltistan" {{ old('test_districts.0.province') == 'Gilgit Baltistan' ? 'selected' : '' }}>Gilgit Baltistan</option>
                                                <option value="Azad Jammu Kashmir" {{ old('test_districts.0.province') == 'Azad Jammu Kashmir' ? 'selected' : '' }}>Azad Jammu Kashmir</option>
                                                <option value="Islamabad" {{ old('test_districts.0.province') == 'Islamabad' ? 'selected' : '' }}>Islamabad</option>
                                            </select>
                                        </div>

                                        <div class="td-division-container" style="display: none;">
                                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                                Division
                                            </label>
                                            <select name="test_districts[0][division]" class="td-division shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                                                <option value="">Select Division</option>
                                                <option value="Quetta" {{ old('test_districts.0.division') == 'Quetta' ? 'selected' : '' }}>Quetta</option>
                                                <option value="Kalat" {{ old('test_districts.0.division') == 'Kalat' ? 'selected' : '' }}>Kalat</option>
                                                <option value="Makran" {{ old('test_districts.0.division') == 'Makran' ? 'selected' : '' }}>Makran</option>
                                                <option value="Nasirabad" {{ old('test_districts.0.division') == 'Nasirabad' ? 'selected' : '' }}>Nasirabad</option>
                                                <option value="Rakhshan" {{ old('test_districts.0.division') == 'Rakhshan' ? 'selected' : '' }}>Rakhshan</option>
                                                <option value="Sibi" {{ old('test_districts.0.division') == 'Sibi' ? 'selected' : '' }}>Sibi</option>
                                                <option value="Zhob" {{ old('test_districts.0.division') == 'Zhob' ? 'selected' : '' }}>Zhob</option>
                                                <option value="Loralai" {{ old('test_districts.0.division') == 'Loralai' ? 'selected' : '' }}>Loralai</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                                District <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="test_districts[0][district]" value="{{ old('test_districts.0.district') }}" required
                                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                                                   placeholder="e.g., Quetta">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addTestDistrict()" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                + Add Another Test District
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('super-admin.colleges.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Register College
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let testDistrictCount = 1;

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

function addTestDistrict() {
    const container = document.getElementById('testDistrictsContainer');
    const newBlock = `
        <div class="test-district-block border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-bold text-gray-700">Test District #${testDistrictCount + 1}</h4>
                <button type="button" onclick="removeTestDistrict(this)" 
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                    Remove
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Province <span class="text-red-500">*</span>
                    </label>
                    <select name="test_districts[${testDistrictCount}][province]" required onchange="toggleTestDistrictDivision(${testDistrictCount})"
                            class="td-province shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                        <option value="">Select Province</option>
                        <option value="Balochistan">Balochistan</option>
                        <option value="Sindh">Sindh</option>
                        <option value="Punjab">Punjab</option>
                        <option value="Khyber Pakhtunkhwa">Khyber Pakhtunkhwa</option>
                        <option value="Gilgit Baltistan">Gilgit Baltistan</option>
                        <option value="Azad Jammu Kashmir">Azad Jammu Kashmir</option>
                        <option value="Islamabad">Islamabad</option>
                    </select>
                </div>

                <div class="td-division-container" style="display: none;">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Division
                    </label>
                    <select name="test_districts[${testDistrictCount}][division]" class="td-division shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                        <option value="">Select Division</option>
                        <option value="Quetta">Quetta</option>
                        <option value="Kalat">Kalat</option>
                        <option value="Makran">Makran</option>
                        <option value="Nasirabad">Nasirabad</option>
                        <option value="Rakhshan">Rakhshan</option>
                        <option value="Sibi">Sibi</option>
                        <option value="Zhob">Zhob</option>
                        <option value="Loralai">Loralai</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        District <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="test_districts[${testDistrictCount}][district]" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                           placeholder="e.g., Quetta">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newBlock);
    testDistrictCount++;
}

function removeTestDistrict(button) {
    button.closest('.test-district-block').remove();
}

function toggleTestDistrictDivision(index) {
    const blocks = document.querySelectorAll('.test-district-block');
    if (blocks[index]) {
        const province = blocks[index].querySelector('.td-province').value;
        const divisionContainer = blocks[index].querySelector('.td-division-container');
        const divisionSelect = blocks[index].querySelector('.td-division');
        
        if (province === 'Balochistan') {
            divisionContainer.style.display = 'block';
            divisionSelect.required = true;
        } else {
            divisionContainer.style.display = 'none';
            divisionSelect.required = false;
            divisionSelect.value = '';
        }
    }
}

// Run on page load to handle old values
document.addEventListener('DOMContentLoaded', function() {
    toggleDivision();
    const blocks = document.querySelectorAll('.test-district-block');
    blocks.forEach((block, index) => {
        toggleTestDistrictDivision(index);
    });
});
</script>
@endsection