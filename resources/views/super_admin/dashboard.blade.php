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
                    <a href="{{ route('super-admin.colleges.index') }}" class="block">
                        <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm font-semibold uppercase">Colleges</p>
                                    <p class="text-white text-3xl font-bold mt-2">Manage</p>
                                </div>
                                <div class="text-white text-5xl">🏛️</div>
                            </div>
                            <div class="mt-4 text-blue-100 text-sm">
                                Register, view, and manage colleges
                            </div>
                        </div>
                    </a>

                    <!-- Manage Tests -->
                    <a href="{{ route('super-admin.tests.index') }}" class="block">
                        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm font-semibold uppercase">Tests</p>
                                    <p class="text-white text-3xl font-bold mt-2">Manage</p>
                                </div>
                                <div class="text-white text-5xl">📝</div>
                            </div>
                            <div class="mt-4 text-green-100 text-sm">
                                Create and schedule tests
                            </div>
                        </div>
                    </a>

                    <!-- Manage Students -->
                    <a href="{{ route('super-admin.students.index') }}" class="block">
                        <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm font-semibold uppercase">Students</p>
                                    <p class="text-white text-3xl font-bold mt-2">Manage</p>
                                </div>
                                <div class="text-white text-5xl">👥</div>
                            </div>
                            <div class="mt-4 text-purple-100 text-sm">
                                View and manage registrations
                            </div>
                        </div>
                    </a>

                    <!-- Generate Roll Numbers -->
                    <a href="{{ route('super-admin.roll-numbers.index') }}" class="block">
                        <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-yellow-100 text-sm font-semibold uppercase">Roll Numbers</p>
                                    <p class="text-white text-3xl font-bold mt-2">Generate</p>
                                </div>
                                <div class="text-white text-5xl">🎲</div>
                            </div>
                            <div class="mt-4 text-yellow-100 text-sm">
                                Generate roll numbers with seating
                            </div>
                        </div>
                    </a>

                    <!-- Seating Plans -->
                    <a href="{{ route('super-admin.seating-plans.index') }}" class="block">
                        <div class="bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-indigo-100 text-sm font-semibold uppercase">Seating Plans</p>
                                    <p class="text-white text-3xl font-bold mt-2">View/Print</p>
                                </div>
                                <div class="text-white text-5xl">🪑</div>
                            </div>
                            <div class="mt-4 text-indigo-100 text-sm">
                                Hall-wise seat arrangements
                            </div>
                        </div>
                    </a>

                    <!-- Attendance Sheets -->
<a href="{{ route('super-admin.attendance-sheets.index') }}" class="block">
    <div class="bg-gradient-to-br from-teal-400 to-teal-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-teal-100 text-sm font-semibold uppercase">Attendance Sheets</p>
                <p class="text-white text-3xl font-bold mt-2">Generate</p>
            </div>
            <div class="text-white text-5xl">✅</div>
        </div>
        <div class="mt-4 text-teal-100 text-sm">
            Hall-wise attendance lists
        </div>
    </div>
</a>

                    <!-- Manage Results -->
                    <a href="{{ route('super-admin.results.index') }}" class="block">
                        <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-100 text-sm font-semibold uppercase">Results</p>
                                    <p class="text-white text-3xl font-bold mt-2">Manage</p>
                                </div>
                                <div class="text-white text-5xl">📊</div>
                            </div>
                            <div class="mt-4 text-red-100 text-sm">
                                Upload and publish results
                            </div>
                        </div>
                    </a>

                    <!-- Bulk Student Upload -->
                    <a href="{{ route('super-admin.bulk-upload.index') }}" class="block">
                        <div class="bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-cyan-100 text-sm font-semibold uppercase">Bulk Upload</p>
                                    <p class="text-white text-3xl font-bold mt-2">Excel Import</p>
                                </div>
                                <div class="text-white text-5xl">📤</div>
                            </div>
                            <div class="mt-4 text-cyan-100 text-sm">
                                Upload students via Excel template
                            </div>
                        </div>
                    </a>

                    <!-- Audit Logs -->
                    <a href="{{ route('super-admin.audit-logs.index') }}" class="block">
                        <div class="bg-gradient-to-br from-gray-400 to-gray-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-100 text-sm font-semibold uppercase">Audit Logs</p>
                                    <p class="text-white text-3xl font-bold mt-2">View Logs</p>
                                </div>
                                <div class="text-white text-5xl">📋</div>
                            </div>
                            <div class="mt-4 text-gray-100 text-sm">
                                View system activity logs
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection