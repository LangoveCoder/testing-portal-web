@extends('layouts.app')

@section('title', 'Bulk Upload Preview')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.bulk-upload.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Upload
                    </a>
                    <h1 class="text-xl font-bold">Bulk Upload Preview</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('super_admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('super-admin.logout') }}">
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
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Valid Students Card -->
            <div class="bg-green-50 border-l-4 border-green-500 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-green-600">Valid Students</p>
                        <p class="text-3xl font-bold text-green-700">{{ count($validStudents) }}</p>
                        <p class="text-xs text-green-600 mt-1">Ready to import</p>
                    </div>
                </div>
            </div>

            <!-- Invalid Students Card -->
            <div class="bg-red-50 border-l-4 border-red-500 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-red-600">Invalid Students</p>
                        <p class="text-3xl font-bold text-red-700">{{ array_sum(array_map('count', $errors)) }}</p>
                        <p class="text-xs text-red-600 mt-1">Need correction</p>
                    </div>
                </div>
            </div>

            <!-- College Info Card -->
            <div class="bg-blue-50 border-l-4 border-blue-500 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-blue-600">College</p>
                        <p class="text-xl font-bold text-blue-700">{{ $college->name }}</p>
                        <p class="text-xs text-blue-600 mt-1">{{ $test->test_date->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valid Students Table -->
        @if(count($validStudents) > 0)
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-green-700">✅ Valid Students ({{ count($validStudents) }})</h3>
                <p class="text-sm text-gray-600">These students will be imported</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-green-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CNIC</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">DOB</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">District</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Test District</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($validStudents as $index => $student)
                        <tr class="hover:bg-green-50">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $student['name'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 font-mono">{{ $student['cnic'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $student['gender'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $student['date_of_birth'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $student['district'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $college->testDistricts->where('id', $student['test_district_id'])->first()->district ?? 'N/A' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Error Table -->
        @if(count($errors) > 0)
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
               <h3 class="text-lg font-bold text-red-700">❌ Invalid Students ({{ array_sum(array_map('count', $errors)) }})</h3>
                <p class="text-sm text-gray-600">These students have errors and will not be imported</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Row</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Error</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
    @foreach($errors as $errorGroup)
        @foreach($errorGroup as $error)
        <tr class="hover:bg-red-50">
            <td class="px-4 py-3 text-sm font-bold text-red-700">{{ $error[0] }}</td>
            <td class="px-4 py-3 text-sm text-gray-900">{{ $error[1] }}</td>
            <td class="px-4 py-3 text-sm text-red-600">{{ $error[2] }}</td>
        </tr>
        @endforeach
    @endforeach
</tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center">
                <div>
                    <a href="{{ route('super-admin.bulk-upload.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded inline-block">
                        ← Cancel & Go Back
                    </a>
                </div>
                
                <div class="space-x-4">
                    @if(!empty($errors) && array_sum(array_map('count', $errors)) > 0)
                    <a href="{{ route('super-admin.bulk-upload.download-errors') }}" 
                       class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded inline-block">
                        📥 Download Error Report
                    </a>
                    @endif
                    
                    @if(count($validStudents) > 0)
<form method="POST" action="{{ route('super-admin.bulk-upload.import') }}">
    @csrf
    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded">
        ✅ Import {{ count($validStudents) }} Valid Students
    </button>
</form>
                    @else
                    <button disabled class="bg-gray-400 cursor-not-allowed text-white font-bold py-3 px-6 rounded">
                        No Valid Students to Import
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Help Text -->
        @if(count($errors) > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mt-6">
            <h3 class="text-lg font-bold text-yellow-800 mb-2">📝 Next Steps for Errors:</h3>
            <ol class="list-decimal list-inside space-y-2 text-yellow-900">
                <li>Download the error report using the button above</li>
                <li>Fix the errors in your Excel file</li>
                <li>Create a new ZIP file with only the corrected students</li>
                <li>Upload the corrected ZIP file</li>
                <li>Import the corrected students</li>
            </ol>
        </div>
        @endif
    </div>
</div>
@endsection
