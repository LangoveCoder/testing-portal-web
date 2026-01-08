@extends('layouts.app')

@section('title', 'Biometric Registration Status')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.dashboard') }}" class="text-white hover:text-gray-200">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">📊 Biometric Registration Status</h1>
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
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">Total Students</p>
                <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">Complete</p>
                <p class="text-3xl font-bold text-green-600">{{ number_format($stats['complete']) }}</p>
                <p class="text-xs text-gray-500">{{ $stats['total'] > 0 ? round(($stats['complete']/$stats['total'])*100, 1) : 0 }}%</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">Incomplete</p>
                <p class="text-3xl font-bold text-red-600">{{ number_format($stats['incomplete']) }}</p>
                <p class="text-xs text-gray-500">{{ $stats['total'] > 0 ? round(($stats['incomplete']/$stats['total'])*100, 1) : 0 }}%</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">With Fingerprint</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['with_fingerprint']) }}</p>
                <p class="text-xs text-gray-500">{{ $stats['total'] > 0 ? round(($stats['with_fingerprint']/$stats['total'])*100, 1) : 0 }}%</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-gray-600 text-sm">With Test Photo</p>
                <p class="text-3xl font-bold text-teal-600">{{ number_format($stats['with_test_photo']) }}</p>
                <p class="text-xs text-gray-500">{{ $stats['total'] > 0 ? round(($stats['with_test_photo']/$stats['total'])*100, 1) : 0 }}%</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('college.biometric-status.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name, Roll #, CNIC"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Test Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Test</label>
                    <select name="test_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">All Tests</option>
                        @foreach($tests as $test)
                            <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                {{ $test->name }} - {{ $test->test_date->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">All Status</option>
                        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>✓ Complete</option>
                        <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>⚠ Incomplete</option>
                        <option value="no_fingerprint" {{ request('status') == 'no_fingerprint' ? 'selected' : '' }}>No Fingerprint</option>
                        <option value="no_test_photo" {{ request('status') == 'no_test_photo' ? 'selected' : '' }}>No Test Photo</option>
                        <option value="no_registration_photo" {{ request('status') == 'no_registration_photo' ? 'selected' : '' }}>No Reg Photo</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roll #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Test</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Reg Photo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Test Photo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fingerprint</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                        @php
                            $hasPhoto = !empty($student->picture);
                            $hasTestPhoto = !empty($student->test_photo);
                            $hasFingerprint = !empty($student->fingerprint_template);
                            
                            // Only count test_photo and fingerprint (NOT registration picture)
                            $completedCount = ($hasTestPhoto ? 1 : 0) + ($hasFingerprint ? 1 : 0);
                            
                            // Three-tier color coding (out of 2, not 3)
                            if ($completedCount == 2) {
                                $rowColor = 'bg-green-50'; // Both complete
                            } elseif ($completedCount == 1) {
                                $rowColor = 'bg-yellow-50'; // One complete
                            } else {
                                $rowColor = 'bg-red-50'; // None
                            }
                            
                            $isComplete = $completedCount == 2;
                        @endphp
                        <tr class="{{ $rowColor }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-blue-600">{{ $student->roll_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $student->cnic }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $student->test->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($hasPhoto)
                                    <img src="{{ asset('storage/' . $student->picture) }}" 
                                         alt="Photo" 
                                         class="w-12 h-12 rounded border-2 border-green-500 mx-auto cursor-pointer hover:scale-150 transition"
                                         onclick="showImageModal(this.src)">
                                @else
                                    <span class="text-red-500 text-2xl">✗</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($hasTestPhoto)
                                    <img src="{{ asset('storage/' . $student->test_photo) }}" 
                                         alt="Test Photo" 
                                         class="w-12 h-12 rounded border-2 border-green-500 mx-auto cursor-pointer hover:scale-150 transition"
                                         onclick="showImageModal(this.src)">
                                @else
                                    <span class="text-red-500 text-2xl">✗</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($hasFingerprint)
                                    @if($student->fingerprint_image)
                                        <img src="{{ asset('storage/' . $student->fingerprint_image) }}" 
                                             alt="Fingerprint" 
                                             class="w-16 h-16 rounded border-2 border-green-500 mx-auto cursor-pointer hover:scale-150 transition"
                                             style="filter: invert(100%); background: white;"
                                             onclick="showImageModal(this.src)">
                                    @else
                                        <span class="text-green-500 text-2xl">✓</span>
                                        <p class="text-xs text-gray-500">Template Only</p>
                                    @endif
                                @else
                                    <span class="text-red-500 text-2xl">✗</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isComplete)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        ✓ Complete (2/2)
                                    </span>
                                @else
                                    @if($completedCount == 1)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            ⚠ Partial (1/2)
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            ✗ None (0/2)
                                        </span>
                                    @endif
                                    <div class="text-xs text-red-600 mt-1">
                                        @if(!$hasTestPhoto) Missing: Test Photo<br>@endif
                                        @if(!$hasFingerprint) Missing: Fingerprint @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No students found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $students->links() }}
            </div>
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