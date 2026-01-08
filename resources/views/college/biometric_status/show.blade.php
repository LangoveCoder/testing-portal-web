@extends('layouts.app')

@section('title', 'Student Biometric Details')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-dark-900">
    <!-- Top Navigation -->
    <nav class="bg-green-600 dark:bg-green-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.biometric-status.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Biometric Status
                    </a>
                    <h1 class="text-xl font-bold">Student Biometric Details</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('college')->user()->name }}</span>
                    <form method="POST" action="{{ route('college.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Student Info Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">{{ $student->name }}</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Roll Number</p>
                    <p class="font-semibold">{{ $student->roll_number }}</p>
                </div>
                <div>
                    <p class="text-gray-600">CNIC</p>
                    <p class="font-semibold">{{ $student->cnic }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Father Name</p>
                    <p class="font-semibold">{{ $student->father_name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Test</p>
                    <p class="font-semibold">
                        {{ $student->test->college->name ?? 'N/A' }}
                        @if($student->test->test_date)
                            <br><span class="text-sm text-gray-500">{{ $student->test->test_date->format('d M Y') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Hall - Zone - Row - Seat</p>
                    <p class="font-semibold">{{ $student->hall_number }} - {{ $student->zone_number }} - {{ $student->row_number }} - {{ $student->seat_number }}</p>
                </div>
            </div>
        </div>

        <!-- Images Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Registration Photo -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Registration Photo</h3>
                @if($student->picture)
                    <img src="{{ asset('storage/' . $student->picture) }}" 
                         alt="Registration Photo" 
                         class="w-full h-64 object-cover rounded border-2 border-green-500 cursor-pointer"
                         onclick="showImageModal(this.src)">
                    <p class="text-green-600 mt-2 text-sm">✓ Captured</p>
                @else
                    <div class="w-full h-64 bg-gray-200 rounded flex items-center justify-center">
                        <span class="text-red-500 text-4xl">✗</span>
                    </div>
                    <p class="text-red-600 mt-2 text-sm">✗ Not Captured</p>
                @endif
            </div>

            <!-- Test Photo -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Test Day Photo</h3>
                @if($student->test_photo)
                    <img src="{{ asset('storage/' . $student->test_photo) }}" 
                         alt="Test Photo" 
                         class="w-full h-64 object-cover rounded border-2 border-green-500 cursor-pointer"
                         onclick="showImageModal(this.src)">
                    <p class="text-green-600 mt-2 text-sm">✓ Captured</p>
                @else
                    <div class="w-full h-64 bg-gray-200 rounded flex items-center justify-center">
                        <span class="text-red-500 text-4xl">✗</span>
                    </div>
                    <p class="text-red-600 mt-2 text-sm">✗ Not Captured</p>
                @endif
            </div>

            <!-- Fingerprint -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Fingerprint</h3>
                @if($student->fingerprint_template)
                    @if($student->fingerprint_image)
                        <img src="{{ asset('storage/' . $student->fingerprint_image) }}" 
                             alt="Fingerprint" 
                             class="w-full h-64 object-contain rounded border-2 border-green-500 cursor-pointer bg-white"
                             style="filter: invert(100%); background: white;"
                             onclick="showImageModal(this.src)">
                        <p class="text-green-600 mt-2 text-sm">✓ Template & Image Captured</p>
                    @else
                        <div class="w-full h-64 bg-gray-200 rounded flex items-center justify-center">
                            <span class="text-green-500 text-4xl">✓</span>
                        </div>
                        <p class="text-green-600 mt-2 text-sm">✓ Template Only (No Image)</p>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Registered: {{ $student->fingerprint_registered_at ? $student->fingerprint_registered_at->format('d M Y, h:i A') : 'N/A' }}</p>
                @else
                    <div class="w-full h-64 bg-gray-200 rounded flex items-center justify-center">
                        <span class="text-red-500 text-4xl">✗</span>
                    </div>
                    <p class="text-red-600 mt-2 text-sm">✗ Not Captured</p>
                @endif
            </div>
        </div>

        <!-- Status Summary -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Biometric Status Summary</h3>
            @php
                $hasPhoto = !empty($student->picture);
                $hasTestPhoto = !empty($student->test_photo);
                $hasFingerprint = !empty($student->fingerprint_template);
                
                // Only count test_photo and fingerprint (NOT registration picture)
                $completedCount = ($hasTestPhoto ? 1 : 0) + ($hasFingerprint ? 1 : 0);
                $isComplete = $completedCount == 2;
            @endphp

            @if($isComplete)
                <div class="bg-green-100 border-l-4 border-green-500 p-4">
                    <p class="text-green-700 font-semibold">✓ Complete (2/2) - All biometric data captured</p>
                </div>
            @elseif($completedCount == 1)
                <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4">
                    <p class="text-yellow-700 font-semibold">⚠ Partial (1/2) - Missing:</p>
                    <ul class="list-disc list-inside mt-2 text-yellow-600">
                        @if(!$hasTestPhoto) <li>Test Day Photo</li> @endif
                        @if(!$hasFingerprint) <li>Fingerprint</li> @endif
                    </ul>
                </div>
            @else
                <div class="bg-red-100 border-l-4 border-red-500 p-4">
                    <p class="text-red-700 font-semibold">✗ None (0/2) - No biometric data captured</p>
                    <ul class="list-disc list-inside mt-2 text-red-600">
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
        <button onclick="closeImageModal()" class="absolute top-2 right-2 bg-white text-gray-800 rounded-full p-2 hover:bg-gray-200">
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
    
    // Apply invert filter to fingerprint images in modal
    if (src.includes('/fingerprints/')) {
        img.style.filter = 'invert(100%)';
        img.style.background = 'white';
    } else {
        img.style.filter = '';
        img.style.background = '';
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