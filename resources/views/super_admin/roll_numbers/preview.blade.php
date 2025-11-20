@extends('layouts.app')

@section('title', 'Preview Roll Number Generation')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.roll-numbers.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Roll Numbers
                    </a>
                    <h1 class="text-xl font-bold">Preview Roll Number Generation</h1>
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
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <!-- Test Information -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Test Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600">College</label>
                        <p class="text-gray-800">{{ $test->college->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Test Date</label>
                        <p class="text-gray-800">{{ $test->test_date->format('d M Y') }} at {{ date('h:i A', strtotime($test->test_time)) }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Test Mode</label>
                        <p class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $test->test_mode)) }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Total Marks</label>
                        <p class="text-gray-800">{{ $test->total_marks }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Starting Roll Number</label>
                        <p class="text-gray-800 font-mono text-lg">{{ str_pad($test->starting_roll_number, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Total Students Registered</label>
                        <p class="text-gray-800 text-lg font-bold text-green-600">{{ $test->students->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Venue-wise Breakdown -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Venue-wise Student Distribution</h2>
                
                @php
                    $totalStudents = 0;
                    $totalCapacity = 0;
                @endphp
                
                <div class="space-y-4">
                    @foreach($venueStats as $index => $stat)
                        @php
                            $totalStudents += $stat['student_count'];
                            $totalCapacity += $stat['capacity'];
                            $utilizationPercent = $stat['capacity'] > 0 ? round(($stat['student_count'] / $stat['capacity']) * 100, 1) : 0;
                        @endphp
                        
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-gray-800">Venue #{{ $index + 1 }}: {{ $stat['venue']->venue_name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $stat['venue']->testDistrict->district }}, {{ $stat['venue']->testDistrict->province }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $stat['venue']->venue_address }}</p>
                                </div>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $stat['student_count'] > $stat['capacity'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $stat['student_count'] }} / {{ $stat['capacity'] }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                                <div>
                                    <label class="block text-xs text-gray-600">Halls</label>
                                    <p class="font-semibold">{{ $stat['venue']->number_of_halls }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Zones/Hall</label>
                                    <p class="font-semibold">{{ $stat['venue']->zones_per_hall }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Rows/Zone</label>
                                    <p class="font-semibold">{{ $stat['venue']->rows_per_zone }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Seats/Row</label>
                                    <p class="font-semibold">{{ $stat['venue']->seats_per_row }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Utilization</label>
                                    <p class="font-semibold">{{ $utilizationPercent }}%</p>
                                </div>
                            </div>
                            
                            @if($stat['student_count'] > $stat['capacity'])
                                <div class="mt-3 bg-red-50 border border-red-200 rounded p-2">
                                    <p class="text-xs text-red-700">
                                        ⚠️ Warning: Student count exceeds venue capacity by {{ $stat['student_count'] - $stat['capacity'] }} students!
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <!-- Summary -->
                <div class="mt-6 pt-6 border-t-2 border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <label class="block text-sm font-bold text-blue-800">Total Students</label>
                            <p class="text-2xl font-bold text-blue-600">{{ $totalStudents }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <label class="block text-sm font-bold text-green-800">Total Capacity</label>
                            <p class="text-2xl font-bold text-green-600">{{ $totalCapacity }}</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <label class="block text-sm font-bold text-purple-800">Roll Numbers Range</label>
                            <p class="text-lg font-mono font-bold text-purple-600">
                                {{ str_pad($test->starting_roll_number, 5, '0', STR_PAD_LEFT) }} - 
                                {{ str_pad($test->starting_roll_number + $totalStudents - 1, 5, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Book Color Distribution Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-bold text-blue-800 mb-2">Book Color Assignment</h3>
                <p class="text-sm text-blue-700">
                    Book colors will be assigned in sequence: <span class="font-mono font-bold">Yellow → Green → Blue → Pink</span> (repeating). 
                    This ensures even distribution and prevents students with same color from sitting together.
                </p>
            </div>

            <!-- Confirmation -->
            @if($totalStudents > $totalCapacity)
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">⚠️ Capacity Exceeded!</p>
                    <p>The total number of students ({{ $totalStudents }}) exceeds the total venue capacity ({{ $totalCapacity }}). 
                       Please adjust venue configuration or reduce student registrations before generating roll numbers.</p>
                </div>
                
                <div class="flex justify-between items-center">
                    <a href="{{ route('super-admin.roll-numbers.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded">
                        ← Back
                    </a>
                    <button disabled 
                            class="bg-gray-400 text-white font-bold py-3 px-6 rounded cursor-not-allowed">
                        Cannot Generate (Capacity Exceeded)
                    </button>
                </div>
            @else
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">✓ Ready to Generate</p>
                    <p>All venues have sufficient capacity. Roll numbers will be generated with sequential seating assignments.</p>
                </div>
                
                <form action="{{ route('super-admin.roll-numbers.generate', $test) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to generate roll numbers? This action cannot be undone easily.');">
                    @csrf
                    <div class="flex justify-between items-center">
                        <a href="{{ route('super-admin.roll-numbers.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded">
                            ← Back
                        </a>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded text-lg">
                            Generate Roll Numbers →
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection