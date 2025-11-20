@extends('layouts.app')

@section('title', 'Generate Reports')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-orange-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.dashboard') }}" class="text-white hover:text-gray-200">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">Generate Reports</h1>
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
        
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Download Reports</h2>
            <p class="text-gray-600">Generate and download student lists, result reports, and statistics in Excel format.</p>
        </div>

        <!-- Report Cards -->
        <div class="space-y-6">
            
            <!-- Student List Report -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-500 rounded-full p-3 mr-4">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Student List Report</h3>
                        <p class="text-sm text-gray-600">Complete list of registered students</p>
                    </div>
                </div>

                <p class="text-gray-700 mb-4">
                    Download a complete list of all registered students with their details including registration ID, CNIC, contact information, and roll numbers (if generated).
                </p>

                <form method="POST" action="{{ route('college.reports.download-student-list') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Filter by Test (Optional)
                        </label>
                        <select name="test_id" class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Tests</option>
                            @foreach($tests as $test)
                                <option value="{{ $test->id }}">
                                    {{ $test->test_date->format('d M Y') }} - Mode {{ str_replace('mode_', '', $test->test_mode) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        📥 Download Student List (Excel)
                    </button>
                </form>
            </div>

            <!-- Result Report -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-green-500 rounded-full p-3 mr-4">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Test Results Report</h3>
                        <p class="text-sm text-gray-600">Published test results with marks</p>
                    </div>
                </div>

                <p class="text-gray-700 mb-4">
                    Download published test results with subject-wise marks, total marks, and student details in Excel format for analysis and record keeping.
                </p>

                <form method="POST" action="{{ route('college.reports.download-result-report') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Select Test <span class="text-red-500">*</span>
                        </label>
                        <select name="test_id" required class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- Select Test --</option>
                            @foreach($tests as $test)
                                @if($test->results()->where('is_published', true)->count() > 0)
                                    <option value="{{ $test->id }}">
                                        {{ $test->test_date->format('d M Y') }} - Mode {{ str_replace('mode_', '', $test->test_mode) }}
                                        ({{ $test->results()->where('is_published', true)->count() }} results)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                        📥 Download Results Report (Excel)
                    </button>
                </form>
            </div>

            <!-- Statistics Card -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-500 shadow rounded-lg p-6">
                <div class="flex items-center mb-3">
                    <svg class="h-6 w-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h4 class="text-lg font-bold text-purple-800">Report Information</h4>
                </div>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="text-purple-600 mr-2">•</span>
                        <span><strong>Student List:</strong> Includes all personal details, test district, roll numbers, and seating information</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 mr-2">•</span>
                        <span><strong>Result Report:</strong> Contains roll numbers, marks in all subjects, and total marks for published tests only</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 mr-2">•</span>
                        <span><strong>Format:</strong> All reports are generated in Microsoft Excel format (.xlsx) for easy analysis</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 mr-2">•</span>
                        <span><strong>Data Privacy:</strong> Reports contain your college's students only and are for internal use</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection