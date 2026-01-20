@extends('layouts.app')

@section('title', 'Generate Reports')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-dark-900">
    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-dark-800/80 backdrop-blur-md border-b border-gray-200 dark:border-dark-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <a href="{{ route('college.dashboard') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        <span class="material-icons-outlined text-xl mr-2">arrow_back</span>
                        Dashboard
                    </a>
                    <div class="h-6 w-px bg-gray-300 dark:bg-dark-700"></div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Generate Reports</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end mr-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ Auth::guard('college')->user()->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">College Admin</span>
                    </div>
                    <form method="POST" action="{{ route('college.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/50">
                            <span class="material-icons-outlined text-lg">logout</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 animate-fade-in">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-green-600 dark:text-green-400 mr-3">check_circle</span>
                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 animate-fade-in">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-red-600 dark:text-red-400 mr-3">error</span>
                    <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 p-6 mb-6">
            <div class="flex items-center mb-4">
                <span class="material-icons-outlined text-3xl text-orange-600 dark:text-orange-400 mr-4">assessment</span>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Download Reports</h2>
                    <p class="text-gray-600 dark:text-gray-400">Generate and download student lists, result reports, and statistics in Excel format</p>
                </div>
            </div>
        </div>

        <!-- Report Cards -->
        <div class="space-y-6">
            
            <!-- Student List Report -->
            <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 dark:bg-blue-900/30 rounded-xl p-3 mr-4">
                        <span class="material-icons-outlined text-2xl text-blue-600 dark:text-blue-400">groups</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Student List Report</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Complete list of registered students</p>
                    </div>
                </div>

                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Download a complete list of all registered students with their details including registration ID, CNIC, contact information, and roll numbers (if generated).
                </p>

                <form method="POST" action="{{ route('college.reports.download-student-list') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Filter by Test (Optional)
                        </label>
                        <select name="test_id" class="w-full border border-gray-300 dark:border-dark-600 bg-white dark:bg-dark-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Tests</option>
                            @foreach($tests as $test)
                                <option value="{{ $test->id }}">
                                    {{ $test->test_date->format('d M Y') }} - Mode {{ str_replace('mode_', '', $test->test_mode) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg transition-colors">
                        <span class="material-icons-outlined text-lg mr-2">download</span>
                        Download Student List (Excel)
                    </button>
                </form>
            </div>

            <!-- Result Report -->
            <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 dark:bg-green-900/30 rounded-xl p-3 mr-4">
                        <span class="material-icons-outlined text-2xl text-green-600 dark:text-green-400">check_circle</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Test Results Report</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Published test results with marks</p>
                    </div>
                </div>

                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Download published test results with subject-wise marks, total marks, and student details in Excel format for analysis and record keeping.
                </p>

                <form method="POST" action="{{ route('college.reports.download-result-report') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Select Test <span class="text-red-500">*</span>
                        </label>
                        <select name="test_id" required class="w-full border border-gray-300 dark:border-dark-600 bg-white dark:bg-dark-700 text-gray-900 dark:text-gray-100 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
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

                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors">
                        <span class="material-icons-outlined text-lg mr-2">download</span>
                        Download Results Report (Excel)
                    </button>
                </form>
            </div>

            <!-- Statistics Card -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border-l-4 border-purple-500 dark:border-purple-400 rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-3">
                    <span class="material-icons-outlined text-purple-600 dark:text-purple-400 mr-2">info</span>
                    <h4 class="text-lg font-bold text-purple-800 dark:text-purple-200">Report Information</h4>
                </div>
                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <li class="flex items-start">
                        <span class="text-purple-600 dark:text-purple-400 mr-2">•</span>
                        <span><strong>Student List:</strong> Includes all personal details, test district, roll numbers, and seating information</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 dark:text-purple-400 mr-2">•</span>
                        <span><strong>Result Report:</strong> Contains roll numbers, marks in all subjects, and total marks for published tests only</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 dark:text-purple-400 mr-2">•</span>
                        <span><strong>Format:</strong> All reports are generated in Microsoft Excel format (.xlsx) for easy analysis</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 dark:text-purple-400 mr-2">•</span>
                        <span><strong>Data Privacy:</strong> Reports contain your college's students only and are for internal use</span>
                    </li>
                </ul>
            </div>

        </div>
    </main>
</div>
@endsection