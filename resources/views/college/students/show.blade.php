@extends('layouts.app')

@section('title', 'View Student Details')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-dark-900">
    <!-- Top Navigation Bar -->
    <nav class="bg-green-600 dark:bg-green-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.students.index') }}" class="text-white hover:text-gray-200 dark:hover:text-gray-300">
                        ← Back to Students
                    </a>
                    <h1 class="text-xl font-bold">Student Details</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('college')->user()->name }}</span>
                    <form method="POST" action="{{ route('college.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 px-4 py-2 rounded transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-dark-800 shadow-md rounded-lg overflow-hidden border border-gray-200 dark:border-dark-700">
            
            <!-- Student Picture and Basic Info -->
            <div class="bg-gradient-to-r from-green-500 to-teal-600 dark:from-green-600 dark:to-teal-700 p-6">
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        @if($student->picture)
                            <img src="{{ asset('storage/' . $student->picture) }}" 
                                 alt="{{ $student->name }}" 
                                 class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="h-32 w-32 rounded-full bg-white dark:bg-gray-100 flex items-center justify-center text-green-600 dark:text-green-700 text-4xl font-bold border-4 border-white shadow-lg">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 text-white">
                        <h2 class="text-3xl font-bold">{{ $student->name }}</h2>
                        <p class="text-lg mt-1">S/O {{ $student->father_name }}</p>
                        <div class="mt-3 flex items-center space-x-4">
                            <span class="px-3 py-1 bg-white dark:bg-gray-100 text-green-700 dark:text-green-800 rounded-full text-sm font-semibold">
                                {{ $student->gender }}
                            </span>
                            <span class="px-3 py-1 bg-white dark:bg-gray-100 text-green-700 dark:text-green-800 rounded-full text-sm font-semibold">
                                {{ $student->religion }}
                            </span>
                            <span class="px-3 py-1 bg-white dark:bg-gray-100 text-green-700 dark:text-green-800 rounded-full text-sm font-semibold">
                                Registration ID: {{ $student->registration_id }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="p-6 border-b border-gray-200 dark:border-dark-600">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Student CNIC</label>
                        <p class="text-gray-900 dark:text-gray-100 font-mono">{{ $student->cnic }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Father CNIC</label>
                        <p class="text-gray-900 dark:text-gray-100 font-mono">{{ $student->father_cnic }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Date of Birth</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $student->date_of_birth->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Age</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($student->date_of_birth)->age }} years</p>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="p-6 border-b border-gray-200 dark:border-dark-600">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Province</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $student->province }}</p>
                    </div>
                    @if($student->division)
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Division</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $student->division }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">District</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $student->district }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Complete Address</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $student->address }}</p>
                    </div>
                </div>
            </div>

            <!-- Test Information -->
            <div class="p-6 border-b border-gray-200 dark:border-dark-600">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Test Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Test Date</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $student->test->test_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Test Time</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ date('h:i A', strtotime($student->test->test_time)) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Test Mode</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $student->test->test_mode)) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Total Marks</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $student->test->total_marks }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Test District</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $student->testDistrict->district }}, {{ $student->testDistrict->province }}</p>
                    </div>
                </div>
            </div>

            <!-- Roll Number & Seating Information -->
            @if($student->roll_number)
            <div class="p-6 bg-yellow-50 dark:bg-yellow-900/20">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Roll Number & Seating Assignment</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Roll Number</label>
                        <p class="text-3xl font-mono font-bold text-green-600 dark:text-green-400">{{ $student->roll_number }}</p>
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
                <a href="{{ route('college.students.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                    ← Back to List
                </a>
                
                @if(!$student->roll_number)
                    <form action="{{ route('college.students.destroy', $student) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this student?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                            Delete Student
                        </button>
                    </form>
                @else
                    <div class="text-sm text-gray-600">
                        Roll number generated - cannot be deleted
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection