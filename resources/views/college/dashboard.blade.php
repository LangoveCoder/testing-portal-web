@extends('layouts.app')

@section('title', 'College Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">College Admin Dashboard</h1>
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
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Welcome, {{ Auth::guard('college')->user()->name }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Register Student -->
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-green-800 mb-2">Register Student</h3>
                        <p class="text-gray-600 mb-4">Add individual student registration</p>
                        <a href="{{ route('college.students.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-block">
    Register Student
</a>
                    </div>

                    <!-- View Students -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-blue-800 mb-2">View Students</h3>
                        <p class="text-gray-600 mb-4">View all registered students</p>
                        <a href="{{ route('college.students.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block">
    View Students
</a>
                    </div>

<!-- Download Bulk Upload Template Card -->
<div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
    <div class="flex items-center mb-4">
        <div class="bg-purple-500 rounded-full p-3 mr-4">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div>
            <h3 class="text-xl font-bold text-gray-800">Download Bulk Template</h3>
            <p class="text-gray-600 text-sm">Excel template for bulk upload</p>
        </div>
    </div>
    <p class="text-gray-700 mb-4">
        Download Excel template with dropdowns, fill student data, add photos, create ZIP, and send to Super Admin for bulk registration.
    </p>
    
    @if($availableTests->count() > 0)
        <form method="POST" action="{{ route('college.download-bulk-template') }}">
            @csrf
            <input type="hidden" name="college_id" value="{{ Auth::guard('college')->user()->id }}">
            
            <div class="mb-3">
                <select name="test_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Select Test --</option>
                    @foreach($availableTests as $test)
                        <option value="{{ $test->id }}">
                            {{ $test->test_date->format('d M Y') }} - Mode {{ str_replace('mode_', '', $test->test_mode) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                Download Template
            </button>
        </form>
    @else
        <p class="text-gray-500 text-sm italic">No tests available. Contact Super Admin.</p>
    @endif
</div>
                    <!-- View Results Card -->
<div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
    <div class="flex items-center mb-4">
        <div class="bg-green-500 rounded-full p-3 mr-4">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h3 class="text-xl font-bold text-gray-800">View Results</h3>
            <p class="text-gray-600 text-sm">Check published test results</p>
        </div>
    </div>
    <p class="text-gray-700 mb-4">
        View published results for your college's students. See subject-wise marks, total marks, and overall performance.
    </p>
    <a href="{{ route('college.results.index') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-block">
        View Results
    </a>
</div>

<!-- Generate Reports Card -->
<div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
    <div class="flex items-center mb-4">
        <div class="bg-orange-500 rounded-full p-3 mr-4">
            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div>
            <h3 class="text-xl font-bold text-gray-800">Generate Reports</h3>
            <p class="text-gray-600 text-sm">Download student reports</p>
        </div>
    </div>
    <p class="text-gray-700 mb-4">
        Download student lists, result reports, and statistics for your college in Excel or PDF format.
    </p>
    <a href="{{ route('college.reports.index') }}" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 inline-block">
        Generate Reports
    </a>
</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection