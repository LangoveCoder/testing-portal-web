@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('super-admin.biometric-operators.index') }}" 
               class="inline-flex items-center text-blue-400 hover:text-blue-300 transition mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Operators
            </a>
            <h1 class="text-3xl font-bold text-white">Edit Biometric Operator</h1>
        </div>

        <!-- Error Display -->
        @if ($errors->any())
            <div class="bg-red-900/50 border border-red-500 text-red-200 px-6 py-4 rounded-lg mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-semibold mb-2">Please fix the following errors:</h3>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('super-admin.biometric-operators.update', $biometricOperator->id) }}" 
              method="POST" 
              class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            @csrf
            @method('PUT')

            <!-- Basic Information Section -->
            <div class="p-8 border-b border-gray-700">
                <h2 class="text-xl font-semibold text-white mb-6">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Full Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $biometricOperator->name) }}"
                               required
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Email Address <span class="text-red-400">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $biometricOperator->email) }}"
                               required
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input type="text" 
                               name="phone" 
                               value="{{ old('phone', $biometricOperator->phone) }}"
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Password
                        </label>
                        <input type="password" 
                               name="password" 
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <p class="mt-1 text-sm text-gray-400">Leave blank to keep current password. Minimum 6 characters if changing.</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Confirm Password
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Status <span class="text-red-400">*</span>
                        </label>
                        <select name="status" 
                                required
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="active" {{ old('status', $biometricOperator->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $biometricOperator->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Assign College Section -->
            <div class="p-8 border-b border-gray-700">
                <h2 class="text-xl font-semibold text-white mb-2">Assign College</h2>
                <p class="text-gray-400 text-sm mb-6">Select the college this operator will work with</p>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        College <span class="text-red-400">*</span>
                    </label>
                    <select name="assigned_college_id" 
                            required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">-- Select College --</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}" 
                                    {{ old('assigned_college_id', $biometricOperator->assigned_college_id) == $college->id ? 'selected' : '' }}>
                                {{ $college->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Assign Tests Section -->
            <div class="p-8 border-b border-gray-700">
                <h2 class="text-xl font-semibold text-white mb-2">Assign Tests</h2>
                <p class="text-gray-400 text-sm mb-6">Select tests this operator can register fingerprints for</p>
                
                <div class="space-y-3 max-h-64 overflow-y-auto bg-gray-900/50 rounded-lg p-4">
                    @forelse($tests as $test)
                        <label class="flex items-start p-3 bg-gray-700 hover:bg-gray-600 rounded-lg cursor-pointer transition group">
                            <input type="checkbox" 
                                   name="assigned_tests[]" 
                                   value="{{ $test->id }}"
                                   {{ in_array($test->id, old('assigned_tests', $biometricOperator->tests->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="mt-1 w-5 h-5 text-blue-500 bg-gray-600 border-gray-500 rounded focus:ring-2 focus:ring-blue-500">
                            <div class="ml-3 flex-1">
                                <div class="text-white font-medium group-hover:text-blue-300 transition">
                                    {{ $test->college->name ?? 'Unknown College' }} - Test
                                </div>
                                <div class="text-sm text-gray-400">{{ $test->test_date ? $test->test_date->format('M d, Y') : 'Date not set' }}</div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p>No tests available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-8 bg-gray-750 flex justify-end space-x-4">
                <a href="{{ route('super-admin.biometric-operators.index') }}" 
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg transition shadow-lg hover:shadow-xl">
                    Update Operator
                </button>
            </div>
        </form>
    </div>
</div>
@endsection