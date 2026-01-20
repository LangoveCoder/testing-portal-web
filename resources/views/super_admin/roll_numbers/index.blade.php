@extends('layouts.app')

@section('title', 'Generate Roll Numbers')

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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Generate Roll Numbers</h1>
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

        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Tests Ready for Roll Number Generation</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Select a test to preview and generate roll numbers with seating assignments</p>
        </div>

        <!-- Tests Grid/Table -->
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 overflow-hidden">
            <!-- Mobile Cards (Hidden on larger screens) -->
            <div class="block lg:hidden">
                @forelse($tests as $test)
                    <div class="p-6 border-b border-gray-200 dark:border-dark-700 last:border-b-0">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $test->college->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $test->test_date->format('d M Y') }} at {{ date('h:i A', strtotime($test->test_time)) }}</p>
                            </div>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                {{ $test->students->count() }} Students
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Starting Roll:</span>
                                <p class="text-gray-900 dark:text-gray-100 font-mono">{{ str_pad($test->starting_roll_number, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end">
                            <a href="{{ route('super-admin.roll-numbers.preview', $test) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors">
                                <span class="material-icons-outlined text-lg mr-2">preview</span>
                                Preview & Generate
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-dark-700 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                            <span class="material-icons-outlined text-2xl text-gray-400">confirmation_number</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No tests ready</h3>
                        <p class="text-gray-500 dark:text-gray-400">Tests must have registered students and roll numbers not yet generated</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table (Hidden on smaller screens) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
                    <thead class="bg-gray-50 dark:bg-dark-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                College Name
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Test Date
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Students Registered
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Starting Roll Number
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                        @forelse($tests as $test)
                            <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $test->college->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $test->test_date->format('d M Y') }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ date('h:i A', strtotime($test->test_time)) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        {{ $test->students->count() }} Students
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ str_pad($test->starting_roll_number, 5, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('super-admin.roll-numbers.preview', $test) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors">
                                        <span class="material-icons-outlined text-lg mr-2">preview</span>
                                        Preview & Generate
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-dark-700 rounded-2xl flex items-center justify-center mb-4">
                                            <span class="material-icons-outlined text-2xl text-gray-400">confirmation_number</span>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No tests ready</h3>
                                        <p class="text-gray-500 dark:text-gray-400">Tests must have registered students and roll numbers not yet generated</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection