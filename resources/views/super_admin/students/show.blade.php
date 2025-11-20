@extends('layouts.app')

@section('title', 'View Student Details')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.students.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Students
                    </a>
                    <h1 class="text-xl font-bold">Student Details</h1>
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
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            
            <!-- Student Picture and Basic Info -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        @if($student->picture)
                            <img src="{{ asset('storage/' . $student->picture) }}" 
                                 alt="{{ $student->name }}" 
                                 class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="h-32 w-32 rounded-full bg-white flex items-center justify-center text-blue-600 text-4xl font-bold border-4 border-white shadow-lg">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 text-white">
                        <h2 class="text-3xl font-bold">{{ $student->name }}</h2>
                        <p class="text-lg mt-1">S/O {{ $student->father_name }}</p>
                        <div class="mt-3 flex items-center space-x-4">
                            <span class="px-3 py-1 bg-white text-blue-700 rounded-full text-sm font-semibold">
                                {{ $student->gender }}
                            </span>
                            <span class="px-3 py-1 bg-white text-blue-700 rounded-full text-sm font-semibold">
                                {{ $student->religion }}
                            </span>
                            <span class="px-3 py-1 bg-white text-blue-700 rounded-full text-sm font-semibold">
                                Registration ID: {{ $student->registration_id }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Student CNIC</label>
                        <p class="text-gray-900 font-mono">{{ $student->cnic }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Father CNIC</label>
                        <p class="text-gray-900 font-mono">{{ $student->father_cnic }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Date of Birth</label>
                        <p class="text-gray-900">{{ $student->date_of_birth->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Age</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($student->date_of_birth)->age }} years</p>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Province</label>
                        <p class="text-gray-900">{{ $student->province }}</p>
                    </div>
                    @if($student->division)
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Division</label>
                        <p class="text-gray-900">{{ $student->division }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-bold text-gray-600">District</label>
                        <p class="text-gray-900">{{ $student->district }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600">Complete Address</label>
                        <p class="text-gray-900">{{ $student->address }}</p>
                    </div>
                </div>
            </div>

            <!-- College & Test Information -->
            <div class="p-6 border-b">
                <h3 class="text-xl font-bold text-gray-800 mb-4">College & Test Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600">College</label>
                        <p class="text-gray-900">{{ $student->test->college->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Test Date</label>
                        <p class="text-gray-900">{{ $student->test->test_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Test Time</label>
                        <p class="text-gray-900">{{ date('h:i A', strtotime($student->test->test_time)) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Test Mode</label>
                        <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $student->test->test_mode)) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Total Marks</label>
                        <p class="text-gray-900">{{ $student->test->total_marks }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Test District</label>
                        <p class="text-gray-900">{{ $student->testDistrict->district }}, {{ $student->testDistrict->province }}</p>
                    </div>
                </div>
            </div>

            <!-- Roll Number & Seating Information -->
            @if($student->roll_number)
            <div class="p-6 bg-yellow-50">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Roll Number & Seating Assignment</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Roll Number</label>
                        <p class="text-3xl font-mono font-bold text-green-600">{{ $student->roll_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Question Book Color</label>
                        <p class="text-2xl font-bold" style="color: {{ 
                            $student->book_color == 'Yellow' ? '#EAB308' : 
                            ($student->book_color == 'Green' ? '#16A34A' : 
                            ($student->book_color == 'Blue' ? '#2563EB' : '#EC4899'))
                        }}">
                            {{ $student->book_color }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Hall Number</label>
                        <p class="text-gray-900 text-lg font-semibold">Hall {{ $student->hall_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Zone Number</label>
                        <p class="text-gray-900 text-lg font-semibold">Zone {{ $student->zone_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Row Number</label>
                        <p class="text-gray-900 text-lg font-semibold">Row {{ $student->row_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600">Seat Number</label>
                        <p class="text-gray-900 text-lg font-semibold">Seat {{ $student->seat_number }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="p-6 bg-gray-50">
                <div class="text-center text-gray-500">
                    <p class="text-lg">Roll number not yet generated</p>
                    <p class="text-sm mt-2">Roll numbers will be assigned by the administrator before the test date</p>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="p-6 bg-gray-50 flex justify-between items-center">
                <a href="{{ route('super-admin.students.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                    ← Back to List
                </a>
                
                <div class="space-x-3">
                    @if(!$student->roll_number)
                        <a href="{{ route('super-admin.students.edit', $student) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded inline-block">
                            Edit Student
                        </a>
                        <form action="{{ route('super-admin.students.destroy', $student) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this student?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                                Delete Student
                            </button>
                        </form>
                    @else
                        <a href="{{ route('super-admin.students.edit', $student) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded inline-block">
                            Change Test District
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection