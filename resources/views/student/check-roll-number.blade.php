@extends('layouts.app')

@section('title', 'Check Roll Number')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Header Card -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-6">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h2 class="mt-4 text-3xl font-extrabold text-gray-900">
                    Check Your Roll Number
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter your CNIC and Registration ID to view your roll number and seat details
                </p>
            </div>
        </div>

        <!-- Search Form Card -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <form method="POST" action="{{ route('student.check-roll-number.search') }}">
                @csrf
                
                <!-- CNIC Input -->
                <div class="mb-6">
                    <label for="cnic" class="block text-sm font-medium text-gray-700 mb-2">
                        Student CNIC
                    </label>
                    <input type="text" 
                           name="cnic" 
                           id="cnic" 
                           maxlength="13"
                           placeholder="Enter 13-digit CNIC"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                           value="{{ old('cnic') }}">
                    @error('cnic')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Registration ID Input -->
                <div class="mb-6">
                    <label for="registration_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Registration ID
                    </label>
                    <input type="text" 
                           name="registration_id" 
                           id="registration_id"
                           placeholder="Enter your Registration ID"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                           value="{{ old('registration_id') }}">
                    @error('registration_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
                    🔍 Check Roll Number
                </button>
            </form>

            <!-- Error/Success Messages -->
            @if(session('error'))
            <div class="mt-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($student))
            <!-- Roll Number Result Card -->
            <div class="mt-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <svg class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="ml-2 text-lg font-bold text-green-800">Roll Number Found!</h3>
                </div>

                <!-- Student Details -->
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-green-200 pb-2">
                        <span class="text-sm font-medium text-green-700">Student Name:</span>
                        <span class="text-sm font-bold text-green-900">{{ $student->name }}</span>
                    </div>

                    <div class="flex justify-between border-b border-green-200 pb-2">
                        <span class="text-sm font-medium text-green-700">Father Name:</span>
                        <span class="text-sm font-bold text-green-900">{{ $student->father_name }}</span>
                    </div>

                    <div class="flex justify-between border-b border-green-200 pb-2">
                        <span class="text-sm font-medium text-green-700">Roll Number:</span>
                        <span class="text-xl font-bold text-green-900">{{ $student->roll_number }}</span>
                    </div>

                    <div class="flex justify-between border-b border-green-200 pb-2">
                        <span class="text-sm font-medium text-green-700">Book Color:</span>
                        <span class="text-sm font-bold px-3 py-1 rounded"
                              style="background-color: {{ $student->book_color === 'Yellow' ? '#FEF3C7' : ($student->book_color === 'Green' ? '#D1FAE5' : ($student->book_color === 'Blue' ? '#DBEAFE' : '#FCE7F3')) }}; color: {{ $student->book_color === 'Yellow' ? '#92400E' : ($student->book_color === 'Green' ? '#065F46' : ($student->book_color === 'Blue' ? '#1E40AF' : '#9F1239')) }};">
                            {{ $student->book_color }}
                        </span>
                    </div>

                    <div class="mt-4 pt-4 border-t border-green-300">
                        <h4 class="text-sm font-bold text-green-800 mb-2">Test Venue Details:</h4>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-green-600">Hall Number</p>
                                <p class="text-lg font-bold text-green-900">{{ $student->hall_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-green-600">Zone Number</p>
                                <p class="text-lg font-bold text-green-900">{{ $student->zone_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-green-600">Row Number</p>
                                <p class="text-lg font-bold text-green-900">{{ $student->row_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-green-600">Seat Number</p>
                                <p class="text-lg font-bold text-green-900">{{ $student->seat_number }}</p>
                            </div>
                        </div>

                        @if($student->test)
                        <div class="mt-3 pt-3 border-t border-green-200">
                            <p class="text-xs text-green-600">Test Date</p>
                            <p class="text-sm font-bold text-green-900">{{ $student->test->test_date->format('l, d F Y') }}</p>
                            <p class="text-xs text-green-600 mt-1">Test Time</p>
                            <p class="text-sm font-bold text-green-900">{{ $student->test->test_time }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Download Roll Slip Button -->
<div class="text-center mt-8 pt-6 border-t-2 border-gray-200">
    <form action="{{ route('student.roll-slip.download') }}" method="POST">
        @csrf
        <input type="hidden" name="cnic" value="{{ $student->cnic }}">
        <input type="hidden" name="registration_id" value="{{ $student->registration_id }}">
        <button type="submit" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center justify-center mx-auto">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download Roll Number Slip (PDF)
        </button>
    </form>
    <p class="text-xs text-gray-500 mt-3">
        📄 Download and print your roll number slip to bring on test day
    </p>
</div>
 @endif
        <!-- Other Links -->
        <div class="mt-6 text-center">
            <a href="{{ route('student.check-result') }}" class="text-white hover:text-gray-200 font-medium">
                Check Result Instead →
            </a>
        </div>

        <div class="mt-2 text-center">
            <a href="{{ route('home') }}" class="text-white hover:text-gray-200 text-sm">
                ← Back to Home
            </a>
        </div>
    </div>
</div>

<script>
// Auto-format CNIC input (numbers only)
document.getElementById('cnic').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
});
</script>
@endsection