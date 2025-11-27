@extends('layouts.app')

@section('title', 'Register New Student')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.students.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Students
                    </a>
                    <h1 class="text-xl font-bold">Register New Student</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('college')->user()->name }}</span>
                    <form method="POST" action="{{ route('college.logout') }}">
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

            <form action="{{ route('college.students.store') }}" method="POST" enctype="multipart/form-data" id="studentForm">
                @csrf

                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Student Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Test Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Select Test <span class="text-red-500">*</span>
                        </label>
                        <select name="test_id" id="test_id" required onchange="loadTestDistricts()"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                            <option value="">-- Select Test --</option>
                            @foreach($tests as $test)
                                <option value="{{ $test->id }}" {{ old('test_id') == $test->id ? 'selected' : '' }}>
                                    {{ $test->college->name }} - {{ $test->test_date->format('d M Y') }} ({{ ucfirst(str_replace('_', ' ', $test->test_mode)) }})
                                </option>
                            @endforeach
                        </select>
                        @if($tests->count() == 0)
                            <p class="text-red-500 text-xs mt-1">No active tests available for registration.</p>
                        @endif
                    </div>

                    <!-- Test District Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Preferred Test District <span class="text-red-500">*</span>
                        </label>
                        <select name="test_center_id" id="test_center_id" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                            <option value="">-- Select test first --</option>
                        </select>
                    </div>

                    <!-- Picture Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Student Picture <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="picture" id="picture" required accept="image/jpeg,image/jpg,image/png"
                               onchange="previewImage(event)"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                        <p class="text-xs text-gray-500 mt-1">JPG, JPEG or PNG. Max size: 2MB</p>
                        <div id="imagePreview" class="mt-3 hidden">
                            <img id="preview" src="" alt="Preview" class="h-32 w-32 object-cover rounded border">
                        </div>
                    </div>

                    <!-- Student Name -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Student Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                    </div>

                    <!-- Student CNIC -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Student CNIC (13 digits) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="cnic" value="{{ old('cnic') }}" required maxlength="13" pattern="[0-9]{13}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500"
                               placeholder="e.g., 4210112345678">
                    </div>

                    <!-- Father Name -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Father Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="father_name" value="{{ old('father_name') }}" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                    </div>

                    <!-- Father CNIC -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Father CNIC (13 digits) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="father_cnic" value="{{ old('father_cnic') }}" required maxlength="13" pattern="[0-9]{13}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500"
                               placeholder="e.g., 4210112345678">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                            <option value="">Select Gender</option>
                            @if($college->gender_policy == 'Both' || $college->gender_policy == 'Male Only')
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            @endif
                            @if($college->gender_policy == 'Both' || $college->gender_policy == 'Female Only')
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            @endif
                        </select>
                    </div>

                    <!-- Religion -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Religion <span class="text-red-500">*</span>
                        </label>
                        <select name="religion" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                            <option value="">Select Religion</option>
                            <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Christianity" {{ old('religion') == 'Christianity' ? 'selected' : '' }}>Christianity</option>
                            <option value="Hinduism" {{ old('religion') == 'Hinduism' ? 'selected' : '' }}>Hinduism</option>
                            <option value="Other" {{ old('religion') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Disability (NEW) -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Disability <span class="text-red-500">*</span>
                        </label>
                        <select name="disability" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                            <option value="">Select Option</option>
                            <option value="No" {{ old('disability') == 'No' ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ old('disability') == 'Yes' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                               max="{{ date('Y-m-d', strtotime('-5 years')) }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                        @if($college->min_age || $college->max_age)
                            <p class="text-xs text-gray-500 mt-1">
                                Age requirement: 
                                @if($college->min_age && $college->max_age)
                                    {{ $college->min_age }} - {{ $college->max_age }} years
                                @elseif($college->min_age)
                                    Minimum {{ $college->min_age }} years
                                @elseif($college->max_age)
                                    Maximum {{ $college->max_age }} years
                                @endif
                            </p>
                        @endif
                    </div>

                    <!-- Province -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Province <span class="text-red-500">*</span>
                        </label>
                        <select name="province" id="province" required onchange="handleProvinceChange()"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
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
                            Division <span class="text-red-500" id="divisionRequired">*</span>
                        </label>
                        <select name="division" id="division" onchange="loadDistricts()"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                            <option value="">Select Division</option>
                            <option value="Quetta Division" {{ old('division') == 'Quetta Division' ? 'selected' : '' }}>Quetta Division</option>
                            <option value="Kalat Division" {{ old('division') == 'Kalat Division' ? 'selected' : '' }}>Kalat Division</option>
                            <option value="Makran Division" {{ old('division') == 'Makran Division' ? 'selected' : '' }}>Makran Division</option>
                            <option value="Nasirabad Division" {{ old('division') == 'Nasirabad Division' ? 'selected' : '' }}>Nasirabad Division</option>
                            <option value="Rakhshan Division" {{ old('division') == 'Rakhshan Division' ? 'selected' : '' }}>Rakhshan Division</option>
                            <option value="Sibi Division" {{ old('division') == 'Sibi Division' ? 'selected' : '' }}>Sibi Division</option>
                            <option value="Zhob Division" {{ old('division') == 'Zhob Division' ? 'selected' : '' }}>Zhob Division</option>
                            <option value="Loralai Division" {{ old('division') == 'Loralai Division' ? 'selected' : '' }}>Loralai Division</option>
                        </select>
                    </div>

                    <!-- District -->
                    <div id="districtContainer">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            District <span class="text-red-500">*</span>
                        </label>
                        <select name="district" id="district" style="display: none;"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">
                            <option value="">Select Division First</option>
                        </select>
                        <input type="text" name="district_text" id="district_text" value="{{ old('district') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500"
                               placeholder="Enter District">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" rows="3" required
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-green-500">{{ old('address') }}</textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('college.students.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                        Register Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Balochistan divisions and their districts
const balochistanDistricts = {
    'Quetta Division': ['Quetta', 'Pishin', 'Killa Abdullah', 'Chaman'],
    'Kalat Division': ['Kalat', 'Mastung', 'Khuzdar', 'Awaran', 'Hub', 'Lasbela', 'Surab'],
    'Makran Division': ['Kech', 'Gwadar', 'Panjgur'],
    'Nasirabad Division': ['Nasirabad', 'Jaffarabad', 'Jhal Magsi', 'Kachhi', 'Sohbatpur', 'Ustah Muhammad'],
    'Sibi Division': ['Sibi', 'Kohlu', 'Dera Bugti', 'Ziarat', 'Harnai', 'Lehri'],
    'Zhob Division': ['Zhob', 'Sherani', 'Killa Saifullah'],
    'Loralai Division': ['Loralai', 'Musakhel', 'Barkhan', 'Duki'],
    'Rakhshan Division': ['Nushki','Kharan', 'Washuk', 'Chagai']
};

function handleProvinceChange() {
    const province = document.getElementById('province').value;
    const divisionContainer = document.getElementById('divisionContainer');
    const divisionSelect = document.getElementById('division');
    const districtDropdown = document.getElementById('district');
    const districtText = document.getElementById('district_text');
    
    if (province === 'Balochistan') {
        // Show division dropdown for Balochistan
        divisionContainer.style.display = 'block';
        divisionSelect.required = true;
        
        // Hide text input, prepare for dropdown
        districtText.style.display = 'none';
        districtText.required = false;
        districtText.value = '';
        
        // Show district dropdown (will populate when division is selected)
        districtDropdown.style.display = 'block';
        districtDropdown.required = true;
        districtDropdown.innerHTML = '<option value="">Select Division First</option>';
        
    } else {
        // Hide division for other provinces
        divisionContainer.style.display = 'none';
        divisionSelect.required = false;
        divisionSelect.value = '';
        
        // Show text input for district
        districtText.style.display = 'block';
        districtText.required = true;
        
        // Hide dropdown
        districtDropdown.style.display = 'none';
        districtDropdown.required = false;
        districtDropdown.value = '';
    }
}

function loadDistricts() {
    const division = document.getElementById('division').value;
    const districtDropdown = document.getElementById('district');
    
    if (!division) {
        districtDropdown.innerHTML = '<option value="">Select Division First</option>';
        return;
    }
    
    const districts = balochistanDistricts[division] || [];
    
    let html = '<option value="">Select District</option>';
    districts.forEach(district => {
        html += `<option value="${district}">${district}</option>`;
    });
    
    districtDropdown.innerHTML = html;
}

async function loadTestDistricts() {
    const testId = document.getElementById('test_id').value;
    const districtSelect = document.getElementById('test_center_id');
    
    if (!testId) {
        districtSelect.innerHTML = '<option value="">-- Select test first --</option>';
        return;
    }
    
    districtSelect.innerHTML = '<option value="">Loading...</option>';
    
    try {
        const response = await fetch(`/college/tests/${testId}/districts`);
        const districts = await response.json();
        
        if (districts.length === 0) {
            districtSelect.innerHTML = '<option value="">No test districts available</option>';
            return;
        }
        
        let html = '<option value="">-- Select Test District --</option>';
        districts.forEach(district => {
            html += `<option value="${district.id}">${district.district}, ${district.province}</option>`;
        });
        districtSelect.innerHTML = html;
    } catch (error) {
        districtSelect.innerHTML = '<option value="">Error loading districts</option>';
        console.error('Error:', error);
    }
}

function previewImage(event) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

// Before form submit, handle district field properly
document.getElementById('studentForm').addEventListener('submit', function(e) {
    const province = document.getElementById('province').value;
    const districtDropdown = document.getElementById('district');
    const districtText = document.getElementById('district_text');
    
    if (province === 'Balochistan') {
        // Use dropdown value, set text input with same value (for backend compatibility)
        districtText.value = districtDropdown.value;
        districtText.name = 'district'; // Ensure correct field name
        districtDropdown.removeAttribute('name'); // Remove name from dropdown to avoid duplicate
    } else {
        // Use text input value
        districtText.name = 'district';
        districtDropdown.removeAttribute('name');
    }
});

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    handleProvinceChange();
    
    const testId = document.getElementById('test_id').value;
    if (testId) {
        loadTestDistricts();
    }
    
    // If division is already selected (from old() values), load districts
    const division = document.getElementById('division').value;
    if (division) {
        loadDistricts();
        
        // If district was also selected, restore it
        const oldDistrict = "{{ old('district') }}";
        if (oldDistrict) {
            setTimeout(() => {
                document.getElementById('district').value = oldDistrict;
            }, 100);
        }
    }
});
</script>
@endsection