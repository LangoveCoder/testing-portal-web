@extends('layouts.app')

@section('title', 'Student Attendance Management')

@section('content')
<div class="container mx-auto px-4 py-6 bg-gray-50 dark:bg-dark-900 min-h-screen">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student Attendance Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Monitor and manage student attendance for tests</p>
    </div>

    <!-- Test Selection -->
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-md border border-gray-200 dark:border-dark-700 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Select Test</h2>
        
        <form method="GET" action="{{ route('super-admin.attendance.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-64">
                <label for="test_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Test</label>
                <select name="test_id" id="test_id" class="w-full border-gray-300 dark:border-dark-600 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 dark:focus:ring-purple-400 dark:focus:border-purple-400 bg-white dark:bg-dark-700 text-gray-900 dark:text-gray-100" onchange="this.form.submit()">
                    <option value="">Select a test...</option>
                    @foreach($tests as $test)
                        <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                            {{ $test->college->name ?? 'Test ' . $test->id }} - {{ $test->test_date->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            @if($selectedTest)
            <div class="flex-1 min-w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Filter</label>
                <select name="status" id="status" class="w-full border-gray-300 dark:border-dark-600 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 dark:focus:ring-purple-400 dark:focus:border-purple-400 bg-white dark:bg-dark-700 text-gray-900 dark:text-gray-100">
                    <option value="">All Students</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present Only</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent Only</option>
                </select>
            </div>
            
            <div class="flex-1 min-w-48">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Roll number or name..." 
                       class="w-full border-gray-300 dark:border-dark-600 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 dark:focus:ring-purple-400 dark:focus:border-purple-400 bg-white dark:bg-dark-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 text-white rounded-md focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 transition-colors">
                    Filter
                </button>
                <a href="{{ route('super-admin.attendance.index', ['test_id' => request('test_id')]) }}" 
                   class="px-4 py-2 bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white rounded-md focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 transition-colors">
                    Clear
                </a>
            </div>
            @endif
        </form>
    </div>

    @if($selectedTest && $stats)
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-md border border-gray-200 dark:border-dark-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-md border border-gray-200 dark:border-dark-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Present</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['present'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-md border border-gray-200 dark:border-dark-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Absent</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['absent'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-md border border-gray-200 dark:border-dark-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attendance Rate</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['present_percentage'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Information -->
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-md border border-gray-200 dark:border-dark-700 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Test Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">College</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $selectedTest->college->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Test Date</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $selectedTest->test_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">District</p>
                <p class="text-lg text-gray-900 dark:text-white">{{ $selectedTest->college->district ?? 'N/A' }}</p>
            </div>
        </div>
        
        <div class="mt-4 flex gap-2">
            <a href="{{ route('super-admin.attendance.export', ['test_id' => $selectedTest->id, 'format' => 'csv']) }}" 
               class="px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white rounded-md focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 transition-colors">
                Export CSV
            </a>
        </div>
    </div>

    <!-- Attendance List -->
    @if($attendanceData && $attendanceData->count() > 0)
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-md border border-gray-200 dark:border-dark-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attendance Records</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
                <thead class="bg-gray-50 dark:bg-dark-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Roll Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Marked At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Marked By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                    @foreach($attendanceData as $record)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-100 dark:bg-dark-600 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 dark:text-gray-300 font-semibold">{{ substr($record->student->name ?? 'N/A', 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $record->student->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $record->student->father_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $record->roll_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($record->attendance_status === 'present')
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">Present</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">Absent</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $record->marked_at ? $record->marked_at->format('d M Y, h:i A') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $record->marked_by ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">
                            {{ $record->notes ?? 'No notes' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-700 bg-gray-50 dark:bg-dark-700">
            {{ $attendanceData->appends(request()->query())->links() }}
        </div>
    </div>
    @elseif($selectedTest)
    <div class="bg-white dark:bg-dark-800 rounded-lg shadow-md border border-gray-200 dark:border-dark-700 p-6 text-center">
        <div class="text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No attendance records found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No students have marked attendance for this test yet.</p>
        </div>
    </div>
    @endif
    @endif
</div>

<script>
// Auto-refresh every 30 seconds if viewing attendance data
@if($selectedTest)
setTimeout(function() {
    location.reload();
}, 30000);
@endif
</script>
@endsection