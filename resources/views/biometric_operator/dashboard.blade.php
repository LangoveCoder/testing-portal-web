@extends('layouts.app')

@section('title', 'Biometric Operator Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">🔐 Biometric Operator Portal</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Welcome, {{ Auth::guard('biometric_operator')->user()->name }}</span>
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
        <div class="px-4 py-6 sm:px-0">
            
            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-2">Welcome to Fingerprint Registration Portal</h2>
                <p class="text-gray-600">You can register student fingerprints for the following tests:</p>
            </div>

            <!-- Assigned Tests -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">📋 Your Assigned Tests</h3>
                
                @if($assignedTests->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500">No tests assigned yet. Please contact Super Admin.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($assignedTests as $test)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $test->test_name }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <strong>Date:</strong> {{ $test->test_date->format('d M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>College:</strong> {{ $test->college->name }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $test->test_date > now() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $test->test_date > now() ? 'Upcoming' : 'Past' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Action Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Register Fingerprint -->
                <a href="{{ route('biometric-operator.registration.index') }}" class="block">
                    <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-semibold uppercase">Register</p>
                                <p class="text-white text-3xl font-bold mt-2">Fingerprints</p>
                            </div>
                            <div class="text-white text-5xl">👆</div>
                        </div>
                        <div class="mt-4 text-purple-100 text-sm">
                            Capture and register student fingerprints
                        </div>
                    </div>
                </a>

                <!-- Registration History -->
                <a href="{{ route('biometric-operator.registration.history') }}" class="block">
                    <div class="bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-lg shadow-lg p-6 hover:shadow-xl transform hover:scale-105 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-semibold uppercase">View</p>
                                <p class="text-white text-3xl font-bold mt-2">History</p>
                            </div>
                            <div class="text-white text-5xl">📋</div>
                        </div>
                        <div class="mt-4 text-indigo-100 text-sm">
                            View your registration history
                        </div>
                    </div>
                </a>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">📖 Instructions</h3>
                <ol class="list-decimal list-inside space-y-2 text-blue-800">
                    <li>Click "Register Fingerprints" to start registration process</li>
                    <li>Search student by Roll Number or CNIC</li>
                    <li>Connect your fingerprint scanner device</li>
                    <li>Capture student's fingerprint</li>
                    <li>Save and confirm registration</li>
                    <li>View registration history for your records</li>
                </ol>
            </div>

        </div>
    </div>
</div>
@endsection