@extends('layouts.app')

@section('title', 'Result Management')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-dark-900">
    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-dark-800/80 backdrop-blur-md border-b border-gray-200 dark:border-dark-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <a href="{{ route('super-admin.dashboard') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        <span class="material-icons-outlined text-xl mr-2">arrow_back</span>
                        Dashboard
                    </a>
                    <div class="h-6 w-px bg-gray-300 dark:bg-dark-700"></div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Result Management</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end mr-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ Auth::guard('super_admin')->user()->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Super Admin</span>
                    </div>
                    <form method="POST" action="{{ route('super-admin.logout') }}">
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
    <main class="flex-grow py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        
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
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-dark-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/30 rounded-xl p-3">
                        <span class="material-icons-outlined text-2xl text-blue-600 dark:text-blue-400">description</span>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tests</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $tests->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-dark-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 dark:bg-green-900/30 rounded-xl p-3">
                        <span class="material-icons-outlined text-2xl text-green-600 dark:text-green-400">check_circle</span>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Results Uploaded</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Result::distinct('test_id')->count('test_id') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-dark-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900/30 rounded-xl p-3">
                        <span class="material-icons-outlined text-2xl text-purple-600 dark:text-purple-400">visibility</span>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Published Results</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ \App\Models\Result::where('is_published', true)->distinct('test_id')->count('test_id') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-dark-700">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl p-3">
                        <span class="material-icons-outlined text-2xl text-yellow-600 dark:text-yellow-400">schedule</span>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Results</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $tests->filter(function($test) { 
                                return $test->students_count > 0 && \App\Models\Result::where('test_id', $test->id)->count() == 0;
                            })->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tests List -->
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tests</h2>
            </div>

            @if($tests->count() > 0)
                <!-- Mobile Cards (Hidden on larger screens) -->
                <div class="block lg:hidden">
                    @foreach($tests as $test)
                        @php
                            $resultsCount = \App\Models\Result::where('test_id', $test->id)->count();
                            $publishedCount = \App\Models\Result::where('test_id', $test->id)->where('is_published', true)->count();
                        @endphp
                        <div class="p-6 border-b border-gray-200 dark:border-dark-700 last:border-b-0">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $test->college->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $test->test_mode)) }} - {{ $test->total_marks }} marks</p>
                                </div>
                                @if($resultsCount == 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                        No Results
                                    </span>
                                @elseif($publishedCount > 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        Published ({{ $publishedCount }}/{{ $resultsCount }})
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                        Uploaded ({{ $resultsCount }})
                                    </span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Test Date:</span>
                                    <p class="text-gray-900 dark:text-gray-100">{{ $test->test_date->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ date('h:i A', strtotime($test->test_time)) }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Students:</span>
                                    <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $test->students_count }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-end">
                                @if($test->roll_numbers_generated)
                                    @if($resultsCount == 0)
                                        <a href="{{ route('super-admin.results.create', $test) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg transition-colors">
                                            <span class="material-icons-outlined text-lg mr-2">upload</span>
                                            Upload Results
                                        </a>
                                    @else
                                        <a href="{{ route('super-admin.results.show', $test) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors">
                                            <span class="material-icons-outlined text-lg mr-2">visibility</span>
                                            View Results
                                        </a>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Generate Roll Numbers First</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table (Hidden on smaller screens) -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
                        <thead class="bg-gray-50 dark:bg-dark-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    College & Test
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Test Date
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Students
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Results Status
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                            @foreach($tests as $test)
                                @php
                                    $resultsCount = \App\Models\Result::where('test_id', $test->id)->count();
                                    $publishedCount = \App\Models\Result::where('test_id', $test->id)->where('is_published', true)->count();
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $test->college->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ ucfirst(str_replace('_', ' ', $test->test_mode)) }} - {{ $test->total_marks }} marks
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $test->test_date->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ date('h:i A', strtotime($test->test_time)) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $test->students_count }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Total Students</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($resultsCount == 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                No Results
                                            </span>
                                        @elseif($publishedCount > 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                Published ({{ $publishedCount }}/{{ $resultsCount }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                Uploaded ({{ $resultsCount }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($test->roll_numbers_generated)
                                            @if($resultsCount == 0)
                                                <a href="{{ route('super-admin.results.create', $test) }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg transition-colors">
                                                    <span class="material-icons-outlined text-lg mr-2">upload</span>
                                                    Upload Results
                                                </a>
                                            @else
                                                <a href="{{ route('super-admin.results.show', $test) }}" 
                                                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors">
                                                    <span class="material-icons-outlined text-lg mr-2">visibility</span>
                                                    View Results
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Generate Roll Numbers First</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-dark-700 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                        <span class="material-icons-outlined text-2xl text-gray-400">description</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No tests found</h3>
                    <p class="text-gray-500 dark:text-gray-400">Create a test first to upload results</p>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection