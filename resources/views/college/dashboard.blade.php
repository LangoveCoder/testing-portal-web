@extends('layouts.app')

@section('title', 'College Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">🏛️ College Admin Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ Auth::guard('college')->user()->name }}</span>
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-2">Welcome to College Portal</h2>
                <p class="text-gray-600">Manage student registrations, view results, and verify fingerprints</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Register Student -->
                <a href="{{ route('college.students.create') }}" class="block">
                    <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-semibold uppercase">Register</p>
                                <p class="text-white text-3xl font-bold mt-2">Student</p>
                            </div>
                            <div class="text-white text-5xl">➕</div>
                        </div>
                        <div class="mt-4 text-green-100 text-sm">
                            Add individual student registration
                        </div>
                    </div>
                </a>

                <!-- View Students -->
                <a href="{{ route('college.students.index') }}" class="block">
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-semibold uppercase">View</p>
                                <p class="text-white text-3xl font-bold mt-2">Students</p>
                            </div>
                            <div class="text-white text-5xl">👥</div>
                        </div>
                        <div class="mt-4 text-blue-100 text-sm">
                            View all registered students
                        </div>
                    </div>
                </a>

                <!-- Fingerprint Verification -->
                <a href="{{ route('college.fingerprint-verification.index') }}" class="block">
                    <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-semibold uppercase">Verify</p>
                                <p class="text-white text-3xl font-bold mt-2">Fingerprint</p>
                            </div>
                            <div class="text-white text-5xl">👆</div>
                        </div>
                        <div class="mt-4 text-purple-100 text-sm">
                            Verify student fingerprints on test day
                        </div>
                    </div>
                </a>

                <!-- Bulk Upload Template -->
                <a href="javascript:void(0)" onclick="document.getElementById('bulkTemplateForm').style.display='block'" class="block">
                    <div class="bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-semibold uppercase">Download</p>
                                <p class="text-white text-3xl font-bold mt-2">Template</p>
                            </div>
                            <div class="text-white text-5xl">📥</div>
                        </div>
                        <div class="mt-4 text-indigo-100 text-sm">
                            Excel template for bulk upload
                        </div>
                    </div>
                </a>

                <!-- View Results -->
                <a href="{{ route('college.results.index') }}" class="block">
                    <div class="bg-gradient-to-br from-teal-400 to-teal-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-teal-100 text-sm font-semibold uppercase">View</p>
                                <p class="text-white text-3xl font-bold mt-2">Results</p>
                            </div>
                            <div class="text-white text-5xl">📊</div>
                        </div>
                        <div class="mt-4 text-teal-100 text-sm">
                            Check published test results
                        </div>
                    </div>
                </a>

                <!-- Generate Reports -->
                <a href="{{ route('college.reports.index') }}" class="block">
                    <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-semibold uppercase">Generate</p>
                                <p class="text-white text-3xl font-bold mt-2">Reports</p>
                            </div>
                            <div class="text-white text-5xl">📄</div>
                        </div>
                        <div class="mt-4 text-orange-100 text-sm">
                            Download student reports & statistics
                        </div>
                    </div>
                </a>
            </div>

            <!-- Bulk Template Download Modal (Hidden by default) -->
            <div id="bulkTemplateForm" style="display:none;" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">📥 Download Bulk Template</h3>
                        <button onclick="document.getElementById('bulkTemplateForm').style.display='none'" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    @if($availableTests->count() > 0)
                        <form method="POST" action="{{ route('college.download-bulk-template') }}">
                            @csrf
                            <input type="hidden" name="college_id" value="{{ Auth::guard('college')->user()->id }}">
                            
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Test</label>
                                <select name="test_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Select Test --</option>
                                    @foreach($availableTests as $test)
                                        <option value="{{ $test->id }}">
                                            {{ $test->test_date->format('d M Y') }} - Mode {{ str_replace('mode_', '', $test->test_mode) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-4">
                                Download Excel template with dropdowns, fill student data, add photos, create ZIP, and send to Super Admin.
                            </p>
                            
                            <div class="flex space-x-3">
                                <button type="button" onclick="document.getElementById('bulkTemplateForm').style.display='none'" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                                    Cancel
                                </button>
                                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                    Download
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="text-gray-500 text-sm italic mb-4">No tests available. Contact Super Admin.</p>
                        <button onclick="document.getElementById('bulkTemplateForm').style.display='none'" class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            Close
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection