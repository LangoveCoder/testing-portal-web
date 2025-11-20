@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">Super Admin Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ Auth::guard('super_admin')->user()->name }}</span>
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
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Welcome to Admission Portal</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Manage Colleges -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-blue-800 mb-2">Manage Colleges</h3>
                        <p class="text-gray-600 mb-4">Register, view, and manage colleges</p>
                        <a href="{{ route('super-admin.colleges.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block">
                            Go to Colleges
                        </a>
                    </div>

                    <!-- Manage Tests -->
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-green-800 mb-2">Manage Tests</h3>
                        <p class="text-gray-600 mb-4">Create and schedule tests</p>
                        <a href="{{ route('super-admin.tests.index') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 inline-block">
    Go to Tests
</a>
                    </div>

                    <!-- Manage Students -->
                    <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-purple-800 mb-2">Manage Students</h3>
                        <p class="text-gray-600 mb-4">View and manage registrations</p>
                        <a href="{{ route('super-admin.students.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 inline-block">
    Go to Students
</a>
                    </div>

                    <!-- Generate Roll Numbers -->
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-yellow-800 mb-2">Roll Number Slips</h3>
                        <p class="text-gray-600 mb-4">Generate roll number slips</p>
                        <a href="{{ route('super-admin.roll-numbers.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 inline-block">
    Generate Roll Numbers
</a>
                    </div>

                    <!-- Manage Results -->
                    <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-red-800 mb-2">Manage Results</h3>
                        <p class="text-gray-600 mb-4">Upload and publish results</p>
                        <a href="{{ route('super-admin.results.index') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 inline-block">
    Go to Results
</a>
                    </div>
      <!-- Bulk Student Upload Card -->
<div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition duration-300">
    <h3 class="text-xl font-bold text-indigo-700 mb-2">Bulk Student Upload</h3>
    <p class="text-gray-600 mb-4">Upload students via Excel</p>
    <p class="text-gray-700 mb-4">
        Download Excel template with dropdowns, colleges fill student data with photos, and upload in bulk for faster registration.
    </p>
    <a href="{{ route('super-admin.bulk-upload.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 inline-block">
        Bulk Upload
    </a>
</div>

                    <!-- Audit Logs -->
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Audit Logs</h3>
                        <p class="text-gray-600 mb-4">View system activity logs</p>
                        <a href="{{ route('super-admin.audit-logs.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 inline-block">
    View Logs
</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection