@extends('layouts.app')

@section('title', 'Registered Students')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-dark-900">
    <!-- Professional Header -->
    <div class="bg-green-600 dark:bg-green-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.dashboard') }}" 
                       class="inline-flex items-center text-white hover:text-gray-200 dark:hover:text-gray-300 transition-colors">
                        <span class="material-icons-outlined text-xl mr-2">arrow_back</span>
                        Dashboard
                    </a>
                    <div class="h-6 w-px bg-green-500 dark:bg-green-600"></div>
                    <h1 class="text-xl font-semibold text-white">Registered Students</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-white">
                        {{ Auth::guard('college')->user()->name }}
                    </div>
                    <form method="POST" action="{{ route('college.logout') }}">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white rounded-lg transition-colors">
                            <span class="material-icons-outlined text-lg mr-1">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 animate-fade-in">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-green-600 dark:text-green-400 mr-3">check_circle</span>
                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 animate-fade-in">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-red-600 dark:text-red-400 mr-3">error</span>
                    <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Student Management</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage students registered for your college tests</p>
            </div>
            <a href="{{ route('college.students.create') }}" 
               class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95">
                <span class="material-icons-outlined text-lg mr-2">person_add</span>
                Register New Student
            </a>
        </div>

        <!-- Students Grid/Table -->
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
                    <thead class="bg-gray-50 dark:bg-dark-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Personal Details
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Test Information
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Roll Number
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($student->picture)
                                            <img src="{{ asset('storage/' . $student->picture) }}" 
                                                 alt="{{ $student->name }}" 
                                                 class="h-12 w-12 rounded-xl object-cover border-2 border-gray-200 dark:border-dark-700">
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center border-2 border-gray-200 dark:border-dark-700">
                                                <span class="text-green-600 dark:text-green-400 font-semibold text-lg">
                                                    {{ substr($student->name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->father_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $student->cnic }}</div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium mt-1
                                        {{ $student->gender == 'Male' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : 'bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300' }}">
                                        <span class="material-icons-outlined text-sm mr-1">
                                            {{ $student->gender == 'Male' ? 'male' : 'female' }}
                                        </span>
                                        {{ $student->gender }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <span class="material-icons-outlined text-lg mr-2">calendar_today</span>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $student->test->test_date->format('d M Y') }}</div>
                                            <div class="text-xs">Entrance Test</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($student->roll_number)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 font-mono">
                                            <span class="material-icons-outlined text-sm mr-1">confirmation_number</span>
                                            {{ $student->roll_number }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-dark-800 text-gray-600 dark:text-gray-400">
                                            <span class="material-icons-outlined text-sm mr-1">pending</span>
                                            Not Generated
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('college.students.show', $student) }}" 
                                           class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                                           title="View Details">
                                            <span class="material-icons-outlined text-lg">visibility</span>
                                        </a>
                                        @if(!$student->roll_number)
                                            <form action="{{ route('college.students.destroy', $student) }}" 
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                        title="Delete Student">
                                                    <span class="material-icons-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-dark-800 text-gray-600 dark:text-gray-400">
                                                <span class="material-icons-outlined text-sm mr-1">lock</span>
                                                Roll # Generated
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-dark-800 rounded-2xl flex items-center justify-center mb-4">
                                            <span class="material-icons-outlined text-2xl text-gray-400">groups</span>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No students registered</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by registering your first student</p>
                                        <a href="{{ route('college.students.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors">
                                            <span class="material-icons-outlined text-lg mr-2">person_add</span>
                                            Register New Student
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection