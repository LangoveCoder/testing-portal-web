@extends('layouts.app')

@section('title', 'Bulk Student Upload')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.dashboard') }}" class="text-white hover:text-gray-200">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">Bulk Student Upload</h1>
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

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <!-- Instructions -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
            <h3 class="text-lg font-bold text-blue-800 mb-3">📋 Bulk Upload Process</h3>
            <ol class="list-decimal list-inside space-y-2 text-blue-900">
                <li><strong>Step 1:</strong> Select college and download Excel template</li>
                <li><strong>Step 2:</strong> College fills template and prepares photos</li>
                <li><strong>Step 3:</strong> College sends ZIP file to Super Admin</li>
                <li><strong>Step 4:</strong> Upload ZIP file and validate data</li>
                <li><strong>Step 5:</strong> Review and import students</li>
            </ol>
        </div>

        <!-- Download Template Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Step 1: Download Template</h2>
            <p class="text-gray-600 mb-4">Generate Excel template for a specific college and test</p>

            <form method="POST" action="{{ route('super-admin.bulk-upload.download-template') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Select College <span class="text-red-500">*</span>
                    </label>
                    <select name="college_id" 
                            id="college-select"
                            required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select College --</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">{{ $college->name }} ({{ $college->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Select Test <span class="text-red-500">*</span>
                    </label>
                    <select name="test_id" 
                            id="test-select"
                            required
                            disabled
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100">
                        <option value="">-- Select college first --</option>
                    </select>
                </div>

                <button type="submit" 
                        id="download-btn"
                        disabled
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded disabled:bg-gray-400 disabled:cursor-not-allowed">
                    📥 Download Template (ZIP)
                </button>
            </form>
        </div>

        <!-- Upload Section -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Step 2: Upload Filled Template</h2>
            <p class="text-gray-600 mb-4">After college fills the template, upload the ZIP file here</p>

            <form method="POST" 
                  action="{{ route('super-admin.bulk-upload.upload') }}" 
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Select College <span class="text-red-500">*</span>
                    </label>
                    <select name="college_id" 
                            id="upload-college-select"
                            required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select College --</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">{{ $college->name }} ({{ $college->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Select Test <span class="text-red-500">*</span>
                    </label>
                    <select name="test_id" 
                            id="upload-test-select"
                            required
                            disabled
                            class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100">
                        <option value="">-- Select college first --</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Upload ZIP File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="upload_file" 
                           accept=".zip"
                           required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Must be ZIP file containing students.xlsx and pictures folder (Max: 100MB)
                    </p>
                </div>

                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                    📤 Upload & Validate
                </button>
            </form>
        </div>

        <!-- Help Section -->
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mt-6">
            <h3 class="text-lg font-bold text-yellow-800 mb-3">💡 Important Notes</h3>
            <ul class="list-disc list-inside space-y-2 text-yellow-900 text-sm">
                <li>Template includes Excel dropdowns to prevent data entry errors</li>
                <li>All photos must be named exactly as Student CNIC (e.g., 4210112345678.jpg)</li>
                <li>ZIP file must contain: students.xlsx and pictures/ folder</li>
                <li>System will validate all data before importing</li>
                <li>You can download error report if any students fail validation</li>
                <li>Only valid students will be imported - failed ones can be fixed and re-uploaded</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Load tests when college selected (for template download)
document.getElementById('college-select').addEventListener('change', function() {
    const collegeId = this.value;
    const testSelect = document.getElementById('test-select');
    const downloadBtn = document.getElementById('download-btn');
    
    if (collegeId) {
        fetch(`/super-admin/bulk-upload/tests/${collegeId}`)
            .then(response => response.json())
            .then(tests => {
                testSelect.innerHTML = '<option value="">-- Select Test --</option>';
                tests.forEach(test => {
                    const date = new Date(test.test_date).toLocaleDateString('en-GB');
                    testSelect.innerHTML += `<option value="${test.id}">${date} - Mode ${test.test_mode.replace('mode_', '')}</option>`;
                });
                testSelect.disabled = false;
            });
    } else {
        testSelect.innerHTML = '<option value="">-- Select college first --</option>';
        testSelect.disabled = true;
        downloadBtn.disabled = true;
    }
});

document.getElementById('test-select').addEventListener('change', function() {
    const downloadBtn = document.getElementById('download-btn');
    downloadBtn.disabled = !this.value;
});

// Load tests when college selected (for upload)
document.getElementById('upload-college-select').addEventListener('change', function() {
    const collegeId = this.value;
    const testSelect = document.getElementById('upload-test-select');
    
    if (collegeId) {
        fetch(`/super-admin/bulk-upload/tests/${collegeId}`)
            .then(response => response.json())
            .then(tests => {
                testSelect.innerHTML = '<option value="">-- Select Test --</option>';
                tests.forEach(test => {
                    const date = new Date(test.test_date).toLocaleDateString('en-GB');
                    testSelect.innerHTML += `<option value="${test.id}">${date} - Mode ${test.test_mode.replace('mode_', '')}</option>`;
                });
                testSelect.disabled = false;
            });
    } else {
        testSelect.innerHTML = '<option value="">-- Select college first --</option>';
        testSelect.disabled = true;
    }
});
</script>
@endsection