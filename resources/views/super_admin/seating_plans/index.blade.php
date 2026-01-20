@extends('layouts.app')

@section('title', 'Seating Plans')

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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Seating Plans</h1>
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

        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-indigo-500 to-indigo-600">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-3xl text-white mr-4">event_seat</span>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Generate Seating Plans</h2>
                        <p class="text-indigo-100 mt-1">View and download hall-wise seating arrangements</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if($tests->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-dark-700 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                            <span class="material-icons-outlined text-2xl text-gray-400">event_seat</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Tests Available</h3>
                        <p class="text-gray-500 dark:text-gray-400">Generate roll numbers first to create seating plans</p>
                    </div>
                @else
                    <!-- Mobile Cards (Hidden on larger screens) -->
                    <div class="block lg:hidden">
                        @foreach($tests as $test)
                            <div class="p-6 border-b border-gray-200 dark:border-dark-700 last:border-b-0">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $test->test_name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $test->college->name }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        {{ $test->students()->whereNotNull('roll_number')->count() }} Students
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 gap-4 text-sm mb-4">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Test Date:</span>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $test->test_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('super-admin.seating-plans.show', $test) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg transition-colors">
                                        <span class="material-icons-outlined text-lg mr-2">visibility</span>
                                        View Plan
                                    </a>
                                    <a href="{{ route('super-admin.seating-plans.download', $test) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors">
                                        <span class="material-icons-outlined text-lg mr-2">download</span>
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop Table (Hidden on smaller screens) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
                            <thead class="bg-gray-50 dark:bg-dark-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Test Name</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">College</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Test Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Students</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                                @foreach($tests as $test)
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $test->test_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $test->college->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $test->test_date->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $test->students()->whereNotNull('roll_number')->count() }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('super-admin.seating-plans.show', $test) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg transition-colors">
                                                <span class="material-icons-outlined text-lg mr-2">visibility</span>
                                                View Plan
                                            </a>
                                            <a href="{{ route('super-admin.seating-plans.download', $test) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors">
                                                <span class="material-icons-outlined text-lg mr-2">download</span>
                                                Download PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection