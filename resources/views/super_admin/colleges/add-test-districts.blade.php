@extends('layouts.app')

@section('title', 'Add Test Districts')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.colleges.edit', $college) }}" class="text-white hover:text-gray-200">
                        ← Back to Edit College
                    </a>
                    <h1 class="text-xl font-bold">Add Test Districts</h1>
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
            
            <!-- College Info -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <h2 class="text-lg font-bold text-blue-800">{{ $college->name }}</h2>
                <p class="text-sm text-blue-700">Adding new test districts for this college</p>
            </div>

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

            <!-- Current Test Districts -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-3">Currently Assigned Test Districts:</h3>
                @if($college->testDistricts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($college->testDistricts as $district)
                            <div class="bg-white p-3 rounded border border-gray-300">
                                <p class="text-sm">
                                    <span class="font-semibold">{{ $district->district }}</span>, {{ $district->province }}
                                    @if($district->division)
                                        <span class="text-gray-500">({{ $district->division }})</span>
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No test districts assigned yet.</p>
                @endif
            </div>

            <form action="{{ route('super-admin.colleges.store-test-districts', $college) }}" method="POST" id="addDistrictsForm">
                @csrf

                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Add New Test Districts</h3>

                <!-- Test Districts Container -->
                <div id="testDistrictsContainer">
                    <!-- First test district (always visible) -->
                    <div class="test-district-group border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-gray-700">Test District #1</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Province -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Province <span class="text-red-500">*</span>
                                </label>
                                <select name="test_districts[0][province]" class="province-select shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500" required onchange="toggleDivisionField(this)">
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

                            <!-- Division (Only for Balochistan) -->
                            <div class="division-container" style="display: none;">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Division
                                </label>
                                <select name="test_districts[0][division]" class="division-select shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
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

                            <!-- District -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    District <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="test_districts[0][district]" required
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                                       placeholder="e.g., Quetta">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add More Button -->
                <button type="button" onclick="addTestDistrict()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-4">
                    + Add Another Test District
                </button>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 mt-6 pt-6 border-t-2 border-gray-200">
                    <a href="{{ route('super-admin.colleges.edit', $college) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Add Test Districts
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let districtCount = 1;

function addTestDistrict() {
    const container = document.getElementById('testDistrictsContainer');
    const newDistrict = `
        <div class="test-district-group border border-gray-300 rounded-lg p-4 mb-4 bg-gray-50">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-bold text-gray-700">Test District #${districtCount + 1}</h4>
                <button type="button" onclick="removeTestDistrict(this)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                    Remove
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Province -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Province <span class="text-red-500">*</span>
                    </label>
                    <select name="test_districts[${districtCount}][province]" class="province-select shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500" required onchange="toggleDivisionField(this)">
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

                <!-- Division (Only for Balochistan) -->
                <div class="division-container" style="display: none;">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Division
                    </label>
                    <select name="test_districts[${districtCount}][division]" class="division-select shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
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

                <!-- District -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        District <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="test_districts[${districtCount}][district]" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500"
                           placeholder="e.g., Quetta">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', newDistrict);
    districtCount++;
}

function removeTestDistrict(button) {
    button.closest('.test-district-group').remove();
}

function toggleDivisionField(selectElement) {
    const districtGroup = selectElement.closest('.test-district-group');
    const divisionContainer = districtGroup.querySelector('.division-container');
    const divisionSelect = districtGroup.querySelector('.division-select');
    
    if (selectElement.value === 'Balochistan') {
        divisionContainer.style.display = 'block';
        divisionSelect.required = true;
    } else {
        divisionContainer.style.display = 'none';
        divisionSelect.required = false;
        divisionSelect.value = '';
    }
}
</script>
@endsection