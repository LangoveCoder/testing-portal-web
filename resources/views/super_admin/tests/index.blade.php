@extends('layouts.app')

@section('title', 'Manage Tests')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-dark-900">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 dark:bg-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.dashboard') }}" class="text-white hover:text-gray-200 dark:hover:text-gray-300">
                        ← Dashboard
                    </a>
                    <h1 class="text-xl font-bold">Manage Tests</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('super_admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('super-admin.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 px-4 py-2 rounded transition-colors">
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
            
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Header with Add Button -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">All Tests</h2>
                <a href="{{ route('super-admin.tests.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-bold py-2 px-6 rounded transition-colors">
                    + Create New Test
                </a>
            </div>

            <!-- Tests Table -->
            <div class="bg-white dark:bg-dark-800 shadow-md rounded-lg overflow-hidden border border-gray-200 dark:border-dark-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
                    <thead class="bg-gray-50 dark:bg-dark-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                College Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Test Date & Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Test Mode
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Total Marks
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Roll Numbers
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Results
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                        @forelse($tests as $test)
                            <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $test->college->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $test->test_date->format('d M Y') }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ date('h:i A', strtotime($test->test_time)) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        {{ ucfirst(str_replace('_', ' ', $test->test_mode)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $test->total_marks }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $test->roll_numbers_generated ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }}">
                                        {{ $test->roll_numbers_generated ? 'Generated' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $test->results_published ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300' }}">
                                        {{ $test->results_published ? 'Published' : 'Not Published' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('super-admin.tests.show', $test) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors">View</a>
                                    <a href="{{ route('super-admin.tests.edit', $test) }}" 
                                       class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 transition-colors">Edit</a>
                                    <form action="{{ route('super-admin.tests.destroy', $test) }}" 
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this test?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No tests created yet. Click "Create New Test" to add one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection