@extends('layouts.app')

@section('title', 'Registration History')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('biometric-operator.dashboard') }}" class="text-white hover:text-gray-200">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">Registration History</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('biometric_operator')->user()->name }}</span>
                    <form method="POST" action="{{ route('biometric-operator.logout') }}">
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
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-indigo-500 to-indigo-600">
                <h2 class="text-2xl font-bold text-white">📋 Your Registration History</h2>
                <p class="text-indigo-100 mt-1">View all fingerprints you have registered</p>
            </div>

            <div class="p-6">
                @if($logs->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Registration History</h3>
                        <p class="mt-1 text-sm text-gray-500">You haven't registered any fingerprints yet.</p>
                        <div class="mt-6">
                            <a href="{{ route('biometric-operator.registration.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                Register Fingerprints
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Summary Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-green-600 text-sm font-semibold">Total Registrations</p>
                            <p class="text-2xl font-bold text-green-700">{{ $logs->total() }}</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-600 text-sm font-semibold">Today</p>
                            <p class="text-2xl font-bold text-blue-700">{{ $logs->where('created_at', '>=', today())->count() }}</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <p class="text-purple-600 text-sm font-semibold">This Week</p>
                            <p class="text-2xl font-bold text-purple-700">{{ $logs->where('created_at', '>=', now()->startOfWeek())->count() }}</p>
                        </div>
                    </div>

                    <!-- History Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNIC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($logs as $index => $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $logs->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-purple-600">{{ $log->roll_number }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->student->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->student->father_name ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->student->cnic ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('d M Y') }}<br>
                                        <span class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->ip_address }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection