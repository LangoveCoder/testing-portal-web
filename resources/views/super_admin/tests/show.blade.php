@extends('layouts.app')

@section('title', 'View Test Details')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.tests.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Tests
                    </a>
                    <h1 class="text-xl font-bold">Test Details</h1>
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
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                <h2 class="text-3xl font-bold">{{ $test->college->name }}</h2>
                <p class="text-lg mt-2">{{ $test->test_date->format('l, d F Y') }} at {{ date('h:i A', strtotime($test->test_time)) }}</p>
                <div class="mt-4 flex space-x-3">
                    <span class="px-3 py-1 bg-white text-blue-700 rounded-full text-sm font-semibold">
                        {{ ucfirst(str_replace('_', ' ', $test->test_mode)) }}
                    </span>
                    <span class="px-3 py-1 bg-white text-blue-700 rounded-full text-sm font-semibold">
                        Total Marks: {{ $test->total_marks }}
                    </span>
                    @if($test->roll_numbers_generated)
                        <span class="px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                            ✓ Roll Numbers Generated
                        </span>
                    @else
                        <span class="px-3 py-1 bg-yellow-500 text-white rounded-full text-sm font-semibold">
                            ⏳ Roll Numbers Pending
                        </span>
                    @endif
                </div>
            </div>

            <!-- Test Information -->
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Test Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Registration Deadline</label>
                        <p class="text-gray-900">{{ $test->registration_deadline->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Starting Roll Number</label>
                        <p class="text-gray-900 font-mono text-lg">{{ str_pad($test->starting_roll_number, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    @if($test->roll_numbers_generated)
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Current Roll Number</label>
                        <p class="text-gray-900 font-mono text-lg">{{ str_pad($test->current_roll_number, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- College Information -->
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800 mb-4">College Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Contact Person</label>
                        <p class="text-gray-900">{{ $test->college->contact_person }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Email</label>
                        <p class="text-gray-900">{{ $test->college->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Phone</label>
                        <p class="text-gray-900">{{ $test->college->phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Gender Policy</label>
                        <p class="text-gray-900">{{ $test->college->gender_policy }}</p>
                    </div>
                    @if($test->college->min_age || $test->college->max_age)
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Age Policy</label>
                        <p class="text-gray-900">
                            @if($test->college->min_age && $test->college->max_age)
                                {{ $test->college->min_age }} - {{ $test->college->max_age }} years
                            @elseif($test->college->min_age)
                                Minimum {{ $test->college->min_age }} years
                            @else
                                Maximum {{ $test->college->max_age }} years
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Test Venues -->
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Test Venues Configuration</h3>
                
                @php
                    $totalCapacity = 0;
                    $totalStudents = $test->students->count();
                @endphp

                <div class="space-y-4">
                    @foreach($test->testVenues as $index => $venue)
                        @php
                            $totalCapacity += $venue->total_capacity;
                            $venueStudents = $test->students->where('test_district_id', $venue->test_district_id)->count();
                        @endphp
                        
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-bold text-gray-800">Venue #{{ $index + 1 }}: {{ $venue->venue_name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $venue->testDistrict->district }}, {{ $venue->testDistrict->province }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $venue->venue_address }}</p>
                                </div>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $venueStudents }} / {{ $venue->total_capacity }} Students
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                                <div>
                                    <label class="block text-xs text-gray-600">Halls</label>
                                    <p class="font-semibold">{{ $venue->number_of_halls }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Zones/Hall</label>
                                    <p class="font-semibold">{{ $venue->zones_per_hall }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Rows/Zone</label>
                                    <p class="font-semibold">{{ $venue->rows_per_zone }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Seats/Row</label>
                                    <p class="font-semibold">{{ $venue->seats_per_row }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Capacity</label>
                                    <p class="font-semibold">{{ $venue->total_capacity }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <label class="block text-sm font-bold text-blue-800">Total Students</label>
                        <p class="text-2xl font-bold text-blue-600">{{ $totalStudents }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <label class="block text-sm font-bold text-green-800">Total Capacity</label>
                        <p class="text-2xl font-bold text-green-600">{{ $totalCapacity }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <label class="block text-sm font-bold text-purple-800">Utilization</label>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ $totalCapacity > 0 ? round(($totalStudents / $totalCapacity) * 100, 1) : 0 }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            @if($test->students->count() > 0)
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Registered Students ({{ $test->students->count() }})</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CNIC</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roll Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book Color</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seating</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($test->students->sortBy('roll_number') as $student)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->father_name }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 font-mono">{{ $student->cnic }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 text-xs font-semibold rounded-full 
                                        {{ $student->gender == 'Male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                        {{ $student->gender }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($student->roll_number)
                                        <span class="font-mono font-bold text-green-600">{{ $student->roll_number }}</span>
                                    @else
                                        <span class="text-xs text-gray-500">Not generated</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($student->book_color)
                                        <span class="px-2 py-1 text-xs font-semibold rounded" 
                                              style="background-color: {{ 
                                                  $student->book_color == 'Yellow' ? '#FEF3C7' : 
                                                  ($student->book_color == 'Green' ? '#D1FAE5' : 
                                                  ($student->book_color == 'Blue' ? '#DBEAFE' : '#FCE7F3'))
                                              }}; color: {{ 
                                                  $student->book_color == 'Yellow' ? '#92400E' : 
                                                  ($student->book_color == 'Green' ? '#065F46' : 
                                                  ($student->book_color == 'Blue' ? '#1E3A8A' : '#831843'))
                                              }}">
                                            {{ $student->book_color }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    @if($student->hall_number)
                                        H{{ $student->hall_number }}-Z{{ $student->zone_number }}-R{{ $student->row_number }}-S{{ $student->seat_number }}
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="p-6 bg-gray-50 flex justify-between">
                <a href="{{ route('super-admin.tests.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                    ← Back to Tests
                </a>
                
                <div class="space-x-3">
                    @if(!$test->roll_numbers_generated && $test->students->count() > 0)
                        <a href="{{ route('super-admin.roll-numbers.preview', $test) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded inline-block">
                            Generate Roll Numbers
                        </a>
                    @endif
                    
                    @if(!$test->roll_numbers_generated)
                        <form action="{{ route('super-admin.tests.destroy', $test) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this test?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                                Delete Test
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection