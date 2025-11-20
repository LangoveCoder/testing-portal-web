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
                        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Go to Colleges
                        </button>
                    </div>

                    <!-- Manage Tests -->
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-green-800 mb-2">Manage Tests</h3>
                        <p class="text-gray-600 mb-4">Create and schedule tests</p>
                        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Go to Tests
                        </button>
                    </div>

                    <!-- Manage Students -->
                    <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-purple-800 mb-2">Manage Students</h3>
                        <p class="text-gray-600 mb-4">View and manage registrations</p>
                        <button class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                            Go to Students
                        </button>
                    </div>

                    <!-- Generate Roll Numbers -->
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-yellow-800 mb-2">Roll Number Slips</h3>
                        <p class="text-gray-600 mb-4">Generate roll number slips</p>
                        <button class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                            Generate Slips
                        </button>
                    </div>

                    <!-- Manage Results -->
                    <div class="bg-red-50 border-2 border-red-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-red-800 mb-2">Manage Results</h3>
                        <p class="text-gray-600 mb-4">Upload and publish results</p>
                        <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Go to Results
                        </button>
                    </div>

                    <!-- Audit Logs -->
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Audit Logs</h3>
                        <p class="text-gray-600 mb-4">View system activity logs</p>
                        <button class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            View Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection