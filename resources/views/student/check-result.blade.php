@extends('layouts.app')

@section('title', 'Check Result')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full">
        <!-- Header Card -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-6">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h2 class="mt-4 text-3xl font-extrabold text-gray-900">
                    Check Your Result
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter your CNIC and Roll Number to view your test result
                </p>
            </div>
        </div>

        <!-- Search Form Card -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <form method="POST" action="{{ route('student.check-result.search') }}">
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
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg"
                           value="{{ old('cnic') }}">
                    @error('cnic')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Roll Number Input -->
                <div class="mb-6">
                    <label for="roll_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Roll Number
                    </label>
                    <input type="text" 
                           name="roll_number" 
                           id="roll_number"
                           placeholder="Enter your Roll Number"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg"
                           value="{{ old('roll_number') }}">
                    @error('roll_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
                    🎓 Check Result
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

            @if(isset($result))
            <!-- Result Card -->
            <div class="mt-6 bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-300 rounded-lg p-6">
                <!-- Header -->
                <div class="text-center border-b-2 border-purple-300 pb-4 mb-4">
                    <h3 class="text-2xl font-bold text-purple-900">Test Result</h3>
                    <p class="text-sm text-purple-600 mt-1">{{ $result->test->test_date->format('d M Y') }}</p>
                </div>

                <!-- Student Info -->
                <div class="bg-white rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Student Name</p>
                            <p class="text-sm font-bold text-gray-900">{{ $result->student->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Father Name</p>
                            <p class="text-sm font-bold text-gray-900">{{ $result->student->father_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Roll Number</p>
                            <p class="text-sm font-bold text-gray-900">{{ $result->roll_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Book Color</p>
                            <span class="text-xs font-bold px-2 py-1 rounded inline-block"
                                  style="background-color: {{ $result->book_color === 'Yellow' ? '#FEF3C7' : ($result->book_color === 'Green' ? '#D1FAE5' : ($result->book_color === 'Blue' ? '#DBEAFE' : '#FCE7F3')) }}; color: {{ $result->book_color === 'Yellow' ? '#92400E' : ($result->book_color === 'Green' ? '#065F46' : ($result->book_color === 'Blue' ? '#1E40AF' : '#9F1239')) }};">
                                {{ $result->book_color }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Marks Table -->
                <div class="bg-white rounded-lg p-4">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">Subject-wise Marks</h4>
                    
                    @if($result->test->test_mode === 'mcq_and_subjective')
                    <!-- Mode 1: MCQ + Subjective -->
                    <table class="w-full text-sm">
                        <thead class="bg-purple-100">
                            <tr>
                                <th class="px-2 py-2 text-left">Subject</th>
                                <th class="px-2 py-2 text-center">Objective</th>
                                <th class="px-2 py-2 text-center">Subjective</th>
                                <th class="px-2 py-2 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr>
                                <td class="px-2 py-2 font-medium">English</td>
                                <td class="px-2 py-2 text-center">{{ $result->english_obj ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $result->english_subj ?? '-' }}</td>
                                <td class="px-2 py-2 text-center font-bold">{{ $result->english ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 font-medium">Urdu</td>
                                <td class="px-2 py-2 text-center">{{ $result->urdu_obj ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $result->urdu_subj ?? '-' }}</td>
                                <td class="px-2 py-2 text-center font-bold">{{ $result->urdu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 font-medium">Mathematics</td>
                                <td class="px-2 py-2 text-center">{{ $result->math_obj ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $result->math_subj ?? '-' }}</td>
                                <td class="px-2 py-2 text-center font-bold">{{ $result->math ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-2 py-2 font-medium">Science</td>
                                <td class="px-2 py-2 text-center">{{ $result->science_obj ?? '-' }}</td>
                                <td class="px-2 py-2 text-center">{{ $result->science_subj ?? '-' }}</td>
                                <td class="px-2 py-2 text-center font-bold">{{ $result->science ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    @elseif($result->test->test_mode === 'mcq_only')
                    <!-- Mode 2: MCQ Only -->
                    <table class="w-full text-sm">
                        <thead class="bg-purple-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Subject</th>
                                <th class="px-3 py-2 text-center">Marks Obtained</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr>
                                <td class="px-3 py-2 font-medium">English</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $result->english ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 font-medium">Urdu</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $result->urdu ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 font-medium">Mathematics</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $result->math ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 font-medium">Science</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $result->science ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    @else
                    <!-- Mode 3: General MCQ -->
                    <div class="text-center py-4">
                        <p class="text-gray-600 text-sm">General Knowledge Test</p>
                    </div>
                    @endif

                    <!-- Total Marks -->
                    <div class="mt-4 pt-4 border-t-2 border-purple-300">
                        <div class="flex justify-between items-center bg-purple-100 rounded-lg p-3">
                            <span class="text-lg font-bold text-purple-900">Total Marks</span>
                            <span class="text-2xl font-extrabold text-purple-900">
                                {{ $result->marks }} / {{ $result->total_marks }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Download Button (Future Feature) -->
                <div class="mt-6">
                    <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                        📄 Download Result Card (Coming Soon)
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Other Links -->
        <div class="mt-6 text-center">
            <a href="{{ route('student.check-roll-number') }}" class="text-white hover:text-gray-200 font-medium">
                ← Check Roll Number Instead
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