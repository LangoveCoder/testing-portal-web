@extends('layouts.app')

@section('title', 'Student Biometric Details')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Top Navigation -->
    <nav class="bg-blue-600 dark:bg-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.biometric-status.index') }}" class="text-white hover:text-gray-200 dark:hover:text-gray-300 transition-colors">
                        ← Back to Biometric Status
                    </a>
                    <h1 class="text-xl font-bold">Student Biometric Details</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Admin</span>
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

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Student Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $student->name }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Roll Number</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $student->roll_number }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">CNIC</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $student->cnic }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Father Name</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $student->father_name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Test</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $student->test->college->name ?? 'N/A' }}
                    </p>
                    @if($student->test->test_date)
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->test->test_date->format('d M Y') }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Hall - Zone - Row - Seat</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $student->hall_number }} - {{ $student->zone_number }} - {{ $student->row_number }} - {{ $student->seat_number }}</p>
                </div>
            </div>
        </div>

        <!-- Images Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Registration Photo -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Registration Photo</h3>
                @if($student->picture)
                    <img src="{{ asset('storage/' . $student->picture) }}" 
                         alt="Registration Photo" 
                         class="w-full h-64 object-cover rounded border-4 border-green-500 dark:border-green-600 cursor-pointer hover:scale-105 transition-transform"
                         onclick="showImageModal(this.src)">
                    <p class="text-green-600 dark:text-green-400 mt-3 text-sm font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        Captured
                    </p>
                @else
                    <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded border-4 border-red-300 dark:border-red-700 flex items-center justify-center">
                        <span class="text-red-500 dark:text-red-400 text-6xl font-bold">✗</span>
                    </div>
                    <p class="text-red-600 dark:text-red-400 mt-3 text-sm font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                        </svg>
                        Not Captured
                    </p>
                @endif
            </div>

            <!-- Test Photo -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Test Day Photo</h3>
                @if($student->test_photo)
                    <img src="{{ asset('storage/' . $student->test_photo) }}" 
                         alt="Test Photo" 
                         class="w-full h-64 object-cover rounded border-4 border-green-500 dark:border-green-600 cursor-pointer hover:scale-105 transition-transform"
                         onclick="showImageModal(this.src)">
                    <p class="text-green-600 dark:text-green-400 mt-3 text-sm font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        Captured
                    </p>
                @else
                    <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded border-4 border-red-300 dark:border-red-700 flex items-center justify-center">
                        <span class="text-red-500 dark:text-red-400 text-6xl font-bold">✗</span>
                    </div>
                    <p class="text-red-600 dark:text-red-400 mt-3 text-sm font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                        </svg>
                        Not Captured
                    </p>
                @endif
            </div>

            <!-- Fingerprint -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Fingerprint</h3>
                @if($student->fingerprint_template)
                    @if($student->fingerprint_image)
                        <img src="{{ asset('storage/' . $student->fingerprint_image) }}" 
                             alt="Fingerprint" 
                             class="w-full h-64 object-contain rounded border-4 border-green-500 dark:border-green-600 cursor-pointer hover:scale-105 transition-transform bg-white p-2"
                             onclick="showImageModal(this.src)">
                        <p class="text-green-600 dark:text-green-400 mt-3 text-sm font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            Template & Image Captured
                        </p>
                    @else
                        <div class="w-full h-64 bg-green-50 dark:bg-green-900/20 rounded border-4 border-green-500 dark:border-green-600 flex items-center justify-center">
                            <span class="text-green-500 dark:text-green-400 text-6xl font-bold">✓</span>
                        </div>
                        <p class="text-green-600 dark:text-green-400 mt-3 text-sm font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                            Template Only (No Image)
                        </p>
                    @endif
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Registered: {{ $student->fingerprint_registered_at ? $student->fingerprint_registered_at->format('d M Y, h:i A') : 'N/A' }}
                    </p>
                @else
                    <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded border-4 border-red-300 dark:border-red-700 flex items-center justify-center">
                        <span class="text-red-500 dark:text-red-400 text-6xl font-bold">✗</span>
                    </div>
                    <p class="text-red-600 dark:text-red-400 mt-3 text-sm font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                        </svg>
                        Not Captured
                    </p>
                @endif
            </div>
        </div>

        <!-- Status Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Biometric Status Summary</h3>
            @php
                $hasPhoto = !empty($student->picture);
                $hasTestPhoto = !empty($student->test_photo);
                $hasFingerprint = !empty($student->fingerprint_template);
                
                $completedCount = ($hasTestPhoto ? 1 : 0) + ($hasFingerprint ? 1 : 0);
                $isComplete = $completedCount == 2;
            @endphp

            @if($isComplete)
                <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 dark:border-green-600 p-4 rounded">
                    <p class="text-green-700 dark:text-green-300 font-semibold text-lg">✓ Complete (2/2) - All biometric data captured</p>
                </div>
            @elseif($completedCount == 1)
                <div class="bg-yellow-100 dark:bg-yellow-900/30 border-l-4 border-yellow-500 dark:border-yellow-600 p-4 rounded">
                    <p class="text-yellow-700 dark:text-yellow-300 font-semibold text-lg">⚠ Partial (1/2) - Missing:</p>
                    <ul class="list-disc list-inside mt-2 text-yellow-600 dark:text-yellow-400">
                        @if(!$hasTestPhoto) <li>Test Day Photo</li> @endif
                        @if(!$hasFingerprint) <li>Fingerprint</li> @endif
                    </ul>
                </div>
            @else
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-600 p-4 rounded">
                    <p class="text-red-700 dark:text-red-300 font-semibold text-lg">✗ None (0/2) - No biometric data captured</p>
                    <ul class="list-disc list-inside mt-2 text-red-600 dark:text-red-400">
                        <li>Test Day Photo</li>
                        <li>Fingerprint</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-screen p-4">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-full p-2 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-screen rounded shadow-2xl">
    </div>
</div>

<script>
function showImageModal(src) {
    const img = document.getElementById('modalImage');
    img.src = src;
    
    if (src.includes('/fingerprints/')) {
        img.style.background = 'white';
        img.style.padding = '20px';
    } else {
        img.style.background = '';
        img.style.padding = '';
    }
    
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
}
</script>
@endsection