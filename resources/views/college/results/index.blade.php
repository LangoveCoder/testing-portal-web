@extends('layouts.app')

@section('title', 'View Results')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.dashboard') }}" class="text-white hover:text-gray-200">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">View Results</h1>
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
        
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Published Test Results</h2>
            <p class="text-gray-600">View published results for tests conducted by your college.</p>
        </div>

        @if($tests->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tests as $test)
                <div class="bg-white shadow rounded-lg p-6 hover:shadow-xl transition duration-300">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-500 rounded-full p-3 mr-4">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $test->test_date->format('d M Y') }}</h3>
                            <p class="text-sm text-gray-600">Mode {{ str_replace('mode_', '', $test->test_mode) }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Marks:</span>
                            <span class="font-semibold">{{ $test->total_marks }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Results Count:</span>
                            <span class="font-semibold">{{ $test->results->count() }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Published:</span>
                            <span class="text-green-600 font-semibold">✓ Yes</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('college.results.show', $test) }}" 
                       class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        View Results
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Published Results</h3>
                <p class="mt-2 text-sm text-gray-500">
                    No test results have been published yet. Results will appear here once Super Admin publishes them.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection