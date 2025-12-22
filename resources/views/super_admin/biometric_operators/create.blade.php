@extends('layouts.app')

@section('title', 'Create Biometric Operator')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.biometric-operators.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Operators
                    </a>
                    <h1 class="text-xl font-bold">Create Biometric Operator</h1>
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
            <div class="p-6 bg-gradient-to-r from-purple-500 to-purple-600">
                <h2 class="text-2xl font-bold text-white">🔐 Create New Biometric Operator</h2>
                <p class="text-purple-100 mt-1">Create an operator account for fingerprint registration</p>
            </div>

            <form action="{{ route('super-admin.biometric-operators.store') }}" method="POST" class="p-6">
                @csrf

                <!-- Basic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                            <input type="password" name="password" id="password" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('password') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Assign Colleges -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Colleges</h3>
                    <p class="text-sm text-gray-600 mb-3">Select colleges this operator can access</p>
                    
                    <div class="border border-gray-300 rounded-md p-4 max-h-60 overflow-y-auto">
                        @if($colleges->isEmpty())
                            <p class="text-sm text-gray-500">No active colleges available</p>
                        @else
                            @foreach($colleges as $college)
                            <div class="flex items-center mb-2">
                                <input type="checkbox" name="assigned_colleges[]" value="{{ $college->id }}" 
                                       id="college_{{ $college->id }}"
                                       {{ in_array($college->id, old('assigned_colleges', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="college_{{ $college->id }}" class="ml-2 text-sm text-gray-700">
                                    {{ $college->name }} ({{ $college->district }}, {{ $college->province }})
                                </label>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    @error('assigned_colleges')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assign Tests -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Tests</h3>
                    <p class="text-sm text-gray-600 mb-3">Select tests this operator can register fingerprints for</p>
                    
                    <div class="border border-gray-300 rounded-md p-4 max-h-60 overflow-y-auto">
                        @if($tests->isEmpty())
                            <p class="text-sm text-gray-500">No tests available</p>
                        @else
                            @foreach($tests as $test)
                            <div class="flex items-center mb-2">
                                <input type="checkbox" name="assigned_tests[]" value="{{ $test->id }}" 
                                       id="test_{{ $test->id }}"
                                       {{ in_array($test->id, old('assigned_tests', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="test_{{ $test->id }}" class="ml-2 text-sm text-gray-700">
                                    {{ $test->test_name }} - {{ $test->test_date->format('d M Y') }}
                                </label>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    @error('assigned_tests')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="{{ route('super-admin.biometric-operators.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-purple-700">
                        Create Operator
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection