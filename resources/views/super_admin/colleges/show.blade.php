@extends('layouts.app')

@section('title', 'View College')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 dark:bg-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.colleges.index') }}" class="text-white hover:text-gray-200 dark:hover:text-gray-300">
                        ← Back to Colleges
                    </a>
                    <h1 class="text-xl font-bold">View College Details</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('super_admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('super-admin.logout') }}">
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
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $college->name }}</h2>
                <div class="space-x-2">
                    <a href="{{ route('super-admin.colleges.edit', $college) }}" 
                       class="bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-700 text-white px-4 py-2 rounded transition-colors">
                        Edit College
                    </a>
                    <span class="px-3 py-2 rounded text-sm font-semibold 
                        {{ $college->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                        {{ $college->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <!-- College Information -->
            <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">College Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Contact Person</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->contact_person }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Email</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Phone</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->phone }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Province</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->province }}</p>
                    </div>

                    @if($college->division)
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Division</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->division }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">District</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->district }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Address</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->address }}</p>
                    </div>

                    @if($college->min_age || $college->max_age)
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Age Requirement</label>
                        <p class="text-gray-800 dark:text-gray-200">
                            @if($college->min_age && $college->max_age)
                                {{ $college->min_age }} - {{ $college->max_age }} years
                            @elseif($college->min_age)
                                Minimum {{ $college->min_age }} years
                            @elseif($college->max_age)
                                Maximum {{ $college->max_age }} years
                            @endif
                        </p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Gender Policy</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->gender_policy }}</p>
                    </div>
                    @if($college->registration_start_date)
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Registration Start Date</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->registration_start_date->format('d M Y') }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-bold text-gray-600 dark:text-gray-400">Registered On</label>
                        <p class="text-gray-800 dark:text-gray-200">{{ $college->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Test Districts (FIXED - Uses test_districts table) -->
            <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-6 mt-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Assigned Test Districts</h3>
                
                @php
                    $testDistricts = \App\Models\TestDistrict::where('college_id', $college->id)->get();
                @endphp
                
                @if($testDistricts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($testDistricts as $index => $district)
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                                <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-2">Test District #{{ $index + 1 }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold">Province:</span> {{ $district->province }}
                                </p>
                                @if($district->division)
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold">Division:</span> {{ $district->division }}
                                </p>
                                @endif
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold">District:</span> {{ $district->district }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No test districts assigned yet.</p>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-6 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                <a href="{{ route('super-admin.colleges.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition-colors">
                    Back to List
                </a>
                <a href="{{ route('super-admin.colleges.edit', $college) }}" 
                   class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-6 rounded transition-colors">
                    Edit College
                </a>
            </div>
        </div>
    </div>
</div>
@endsection