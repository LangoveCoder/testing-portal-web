@extends('layouts.app')

@section('title', 'Merit Lists')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Merit Lists</h1>
                    <p class="text-sm text-gray-600 mt-1">Generate division/district-wise merit lists based on test results</p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Info Card -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-blue-800">Merit List Generation Rules</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Balochistan Students:</strong> Separate merit lists for each district within 8 divisions</li>
                            <li><strong>Other Provinces:</strong> One merit list per province (Punjab, Sindh, KPK, etc.)</li>
                            <li><strong>Ranking:</strong> Based on marks obtained in descending order</li>
                            <li><strong>Format:</strong> Download as Excel or ZIP (all lists)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tests with Published Results -->
        @if($tests->count() > 0)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Select Test to Generate Merit Lists</h2>
                <p class="text-sm text-gray-600">Only tests with published results are shown</p>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($tests as $test)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $test->college->name }}</h3>
                                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $test->test_date->format('d M Y') }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $test->test_time }}
                                        </span>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                            Results Published
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        Mode: {{ ucwords(str_replace('_', ' ', $test->test_mode)) }} | 
                                        Total Marks: {{ $test->total_marks }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ml-6">
                            <a href="{{ route('super-admin.merit-lists.show', $test) }}" 
                               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition">
                                📊 Generate Merit Lists
                                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <!-- No Tests Found -->
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No Tests with Published Results</h3>
            <p class="mt-2 text-sm text-gray-500">
                Merit lists can only be generated for tests that have published results.
            </p>
            <div class="mt-6">
                <a href="{{ route('super-admin.results.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                    Go to Results Management
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection