@extends('layouts.app')

@section('title', 'Upload Results')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.results.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Results
                    </a>
                    <h1 class="text-xl font-bold">Upload Results</h1>
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
        
        <!-- Test Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Test Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-600">College</label>
                    <p class="text-gray-900">{{ $test->college->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">Test Date</label>
                    <p class="text-gray-900">{{ $test->test_date->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">Test Mode</label>
                    <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $test->test_mode)) }}</p>
                    <!-- Debug: {{ $test->test_mode }} -->
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">Total Students</label>
                    <p class="text-gray-900 font-bold text-lg">{{ $test->students->count() }}</p>
                </div>
            </div>
        </div>

<!-- Excel Format Instructions -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
    <h3 class="text-lg font-bold text-blue-800 mb-3">📋 Excel File Format for This Test</h3>
    
    <div class="bg-white p-4 rounded border-2 border-blue-300 mb-4">
        <p class="font-bold text-lg text-gray-800 mb-3">
            Test Mode: <span class="text-blue-600">{{ $test->test_mode == 'mode_1' ? 'MCQ and Subjective' : ($test->test_mode == 'mode_2' ? 'MCQ Only' : 'General MCQ') }}</span>
        </p>
        
        @if($test->test_mode == 'mode_1')
            <div class="space-y-3">
                <p class="text-sm font-semibold text-gray-700">Your Excel file MUST have these exact columns in this order:</p>
                <div class="bg-gray-50 p-3 rounded font-mono text-sm">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-2 px-2">Column A</th>
                                <th class="text-left py-2 px-2">Column B</th>
                                <th class="text-left py-2 px-2">Column C</th>
                                <th class="text-left py-2 px-2">Column D</th>
                                <th class="text-left py-2 px-2">Column E</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="py-2 px-2">Roll Number</td>
                                <td class="py-2 px-2">English Obj</td>
                                <td class="py-2 px-2">Urdu Obj</td>
                                <td class="py-2 px-2">Math Obj</td>
                                <td class="py-2 px-2">Science Obj</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="w-full mt-2">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-2 px-2">Column F</th>
                                <th class="text-left py-2 px-2">Column G</th>
                                <th class="text-left py-2 px-2">Column H</th>
                                <th class="text-left py-2 px-2">Column I</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="py-2 px-2">English Subj</td>
                                <td class="py-2 px-2">Urdu Subj</td>
                                <td class="py-2 px-2">Math Subj</td>
                                <td class="py-2 px-2">Science Subj</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-green-50 border border-green-300 rounded p-3 text-sm">
                    <p class="font-semibold text-green-800 mb-2">Example Excel Data:</p>
                    <table class="w-full font-mono text-xs">
                        <tr class="bg-green-100">
                            <td class="border px-2 py-1">00001</td>
                            <td class="border px-2 py-1">25</td>
                            <td class="border px-2 py-1">30</td>
                            <td class="border px-2 py-1">28</td>
                            <td class="border px-2 py-1">32</td>
                            <td class="border px-2 py-1">35</td>
                            <td class="border px-2 py-1">40</td>
                            <td class="border px-2 py-1">38</td>
                            <td class="border px-2 py-1">42</td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">00002</td>
                            <td class="border px-2 py-1">22</td>
                            <td class="border px-2 py-1">28</td>
                            <td class="border px-2 py-1">25</td>
                            <td class="border px-2 py-1">30</td>
                            <td class="border px-2 py-1">32</td>
                            <td class="border px-2 py-1">38</td>
                            <td class="border px-2 py-1">35</td>
                            <td class="border px-2 py-1">40</td>
                        </tr>
                    </table>
                </div>
            </div>
        @elseif($test->test_mode == 'mode_2')
            <div class="space-y-3">
                <p class="text-sm font-semibold text-gray-700">Your Excel file MUST have these exact columns in this order:</p>
                <div class="bg-gray-50 p-3 rounded font-mono text-sm">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-2 px-3">Column A</th>
                                <th class="text-left py-2 px-3">Column B</th>
                                <th class="text-left py-2 px-3">Column C</th>
                                <th class="text-left py-2 px-3">Column D</th>
                                <th class="text-left py-2 px-3">Column E</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="py-2 px-3">Roll Number</td>
                                <td class="py-2 px-3">English</td>
                                <td class="py-2 px-3">Urdu</td>
                                <td class="py-2 px-3">Math</td>
                                <td class="py-2 px-3">Science</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-green-50 border border-green-300 rounded p-3 text-sm">
                    <p class="font-semibold text-green-800 mb-2">Example Excel Data:</p>
                    <table class="w-full font-mono text-xs">
                        <tr class="bg-green-100">
                            <td class="border px-2 py-1">00001</td>
                            <td class="border px-2 py-1">45</td>
                            <td class="border px-2 py-1">48</td>
                            <td class="border px-2 py-1">42</td>
                            <td class="border px-2 py-1">50</td>
                        </tr>
                        <tr>
                            <td class="border px-2 py-1">00002</td>
                            <td class="border px-2 py-1">40</td>
                            <td class="border px-2 py-1">45</td>
                            <td class="border px-2 py-1">38</td>
                            <td class="border px-2 py-1">47</td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
            <div class="space-y-3">
                <p class="text-sm font-semibold text-gray-700">Your Excel file MUST have these exact columns in this order:</p>
                <div class="bg-gray-50 p-3 rounded font-mono text-sm">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-2 px-3">Column A</th>
                                <th class="text-left py-2 px-3">Column B</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200">
                                <td class="py-2 px-3">Roll Number</td>
                                <td class="py-2 px-3">Marks</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-green-50 border border-green-300 rounded p-3 text-sm">
                    <p class="font-semibold text-green-800 mb-2">Example Excel Data:</p>
                    <table class="w-full font-mono text-xs">
                        <tr class="bg-green-100">
                            <td class="border px-3 py-1">00001</td>
                            <td class="border px-3 py-1">185</td>
                        </tr>
                        <tr>
                            <td class="border px-3 py-1">00002</td>
                            <td class="border px-3 py-1">170</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
        <p class="text-sm font-semibold text-yellow-800 mb-2">⚠️ Critical Instructions:</p>
        <ul class="text-sm text-yellow-700 space-y-1">
            <li>• <strong>Row 1 MUST be headers</strong> (exactly as shown above)</li>
            <li>• Roll numbers must match exactly (e.g., 00001, 00002, not 1, 2)</li>
            <li>• All marks must be numbers only</li>
            <li>• Total marks will be calculated automatically</li>
            <li>• File format: .xlsx or .xls only</li>
            <li>• Maximum file size: 10MB</li>
        </ul>
    </div>
</div>
        <!-- Upload Form -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Upload Excel File</h3>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" 
                  action="{{ route('super-admin.results.store', $test) }}" 
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Select Excel File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="result_file" 
                           accept=".xlsx,.xls"
                           required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('result_file') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">Supported formats: .xlsx, .xls (Max: 10MB)</p>
                    @error('result_file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-50 p-4 rounded mb-6">
                    <h4 class="font-semibold text-gray-800 mb-2">Before uploading, ensure:</h4>
                    <div class="space-y-2 text-sm text-gray-700">
                        <label class="flex items-center">
                            <input type="checkbox" required class="mr-2">
                            <span>Excel file format matches the required columns above</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" required class="mr-2">
                            <span>All roll numbers are correct and match student records</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" required class="mr-2">
                            <span>All marks are entered correctly</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" required class="mr-2">
                            <span>First row contains column headers</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <a href="{{ route('super-admin.results.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Upload Results
                    </button>
                </div>
            </form>
        </div>

        <!-- Sample Students List -->
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Sample Students (First 10)</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Roll Number</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($test->students->take(10) as $student)
                        <tr>
                            <td class="px-4 py-2 font-mono text-sm">{{ $student->roll_number }}</td>
                            <td class="px-4 py-2 text-sm">{{ $student->name }}</td>
                            <td class="px-4 py-2 text-sm">{{ $student->gender }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($test->students->count() > 10)
                <p class="text-xs text-gray-500 mt-2">Showing 10 of {{ $test->students->count() }} students</p>
            @endif
        </div>
    </div>
</div>
@endsection