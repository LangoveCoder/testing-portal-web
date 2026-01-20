@extends('layouts.app')

@section('title', 'Biometric Registration Status')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Top Navigation -->
    <nav class="bg-blue-600 dark:bg-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.dashboard') }}" class="text-white hover:text-gray-200 dark:hover:text-gray-300 transition-colors">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">📊 Biometric Registration Status</h1>
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

    <div class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-7 gap-3 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition-shadow border-l-4 border-l-blue-500">
                <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Total Students</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition-shadow border-l-4 border-l-green-500">
                <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Complete</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ number_format($stats['complete']) }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['complete']/$stats['total'])*100, 1) : 0 }}%</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition-shadow border-l-4 border-l-red-500">
                <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Incomplete</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ number_format($stats['incomplete']) }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['incomplete']/$stats['total'])*100, 1) : 0 }}%</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition-shadow border-l-4 border-l-purple-500">
                <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">With Fingerprint</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($stats['with_fingerprint']) }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['with_fingerprint']/$stats['total'])*100, 1) : 0 }}%</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition-shadow border-l-4 border-l-teal-500">
                <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">With Test Photo</p>
                <p class="text-2xl font-bold text-teal-600 dark:text-teal-400 mt-1">{{ number_format($stats['with_test_photo']) }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['with_test_photo']/$stats['total'])*100, 1) : 0 }}%</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition-shadow border-l-4 border-l-emerald-500">
                <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Present Students</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['present_students']) }}</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium">📱 From Mobile App</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition-shadow border-l-4 border-l-orange-500">
                <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wide">Present Need 👆</p>
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1">{{ number_format($stats['present_without_fingerprint']) }}</p>
                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1 font-medium">Priority for biometric</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <form method="GET" action="{{ route('super-admin.biometric-status.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name, Roll #, CNIC"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
                </div>

                <!-- College Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">College</label>
                    <select name="college_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Colleges</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}" {{ request('college_id') == $college->id ? 'selected' : '' }}>
                                {{ $college->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Test Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Test</label>
                    <select name="test_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Tests</option>
                        @foreach($tests as $test)
                            <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                                {{ $test->college->name ?? 'Test ' . $test->id }} - {{ $test->test_date->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Attendance Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">📱 Attendance</label>
                    <select name="attendance_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Students</option>
                        <option value="present" {{ request('attendance_status') == 'present' ? 'selected' : '' }}>✅ Present Only</option>
                        <option value="absent" {{ request('attendance_status') == 'absent' ? 'selected' : '' }}>❌ Absent Only</option>
                        <option value="not_marked" {{ request('attendance_status') == 'not_marked' ? 'selected' : '' }}>❓ Not Marked</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Biometric Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Status</option>
                        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>✓ Complete</option>
                        <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>⚠ Incomplete</option>
                        <option value="present_incomplete" {{ request('status') == 'present_incomplete' ? 'selected' : '' }}>🎯 Present + Incomplete</option>
                        <option value="present_no_fingerprint" {{ request('status') == 'present_no_fingerprint' ? 'selected' : '' }}>👆 Present Need Fingerprint</option>
                        <option value="no_fingerprint" {{ request('status') == 'no_fingerprint' ? 'selected' : '' }}>No Fingerprint</option>
                        <option value="no_test_photo" {{ request('status') == 'no_test_photo' ? 'selected' : '' }}>No Test Photo</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Roll #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Test</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">📱 Attendance</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reg Photo</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Test Photo</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">👆 Fingerprint</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($students as $student)
                        @php
                            $hasPhoto = !empty($student->picture);
                            $hasTestPhoto = !empty($student->test_photo);
                            $hasFingerprint = !empty($student->fingerprint_template);
                            
                            $attendance = $student->attendance->first();
                            $attendanceStatus = $attendance ? $attendance->attendance_status : 'not_marked';
                            
                            $completedCount = ($hasTestPhoto ? 1 : 0) + ($hasFingerprint ? 1 : 0);
                            
                            if ($attendanceStatus === 'present' && $completedCount < 2) {
                                $rowColor = 'bg-orange-50 dark:bg-orange-900/10 border-l-4 border-orange-400';
                            } elseif ($completedCount == 2) {
                                $rowColor = 'bg-green-50 dark:bg-green-900/10';
                            } elseif ($attendanceStatus === 'present') {
                                $rowColor = 'bg-blue-50 dark:bg-blue-900/10';
                            } elseif ($completedCount == 1) {
                                $rowColor = 'bg-yellow-50 dark:bg-yellow-900/10';
                            } else {
                                $rowColor = 'bg-red-50 dark:bg-red-900/10';
                            }
                            
                            $isComplete = $completedCount == 2;
                        @endphp
                        <tr class="{{ $rowColor }}">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $student->roll_number }}</span>
                                @if($attendanceStatus === 'present' && !$isComplete)
                                    <div class="text-xs text-orange-600 dark:text-orange-400 font-semibold mt-1">🎯 PRIORITY</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $student->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->cnic }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                    {{ $student->test->college->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $student->test->test_date ? $student->test->test_date->format('d M Y') : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($attendanceStatus === 'present')
                                    <div class="flex flex-col items-center">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            ✅ Present
                                        </span>
                                        @if($attendance)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $attendance->marked_at->format('H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                @elseif($attendanceStatus === 'absent')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                        ❌ Absent
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        ❓ Not Marked
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($hasPhoto)
                                    <img src="{{ asset('storage/' . $student->picture) }}" 
                                         alt="Photo" 
                                         class="w-16 h-16 rounded border-2 border-green-500 dark:border-green-600 mx-auto cursor-pointer hover:scale-110 transition-transform object-cover"
                                         onclick="showImageModal(this.src)">
                                @else
                                    <div class="w-16 h-16 rounded border-2 border-red-300 dark:border-red-700 mx-auto flex items-center justify-center bg-red-50 dark:bg-red-900/20">
                                        <span class="text-red-500 dark:text-red-400 text-2xl font-bold">✗</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($hasTestPhoto)
                                    <img src="{{ asset('storage/' . $student->test_photo) }}" 
                                         alt="Test Photo" 
                                         class="w-16 h-16 rounded border-2 border-green-500 dark:border-green-600 mx-auto cursor-pointer hover:scale-110 transition-transform object-cover"
                                         onclick="showImageModal(this.src)">
                                @else
                                    <div class="w-16 h-16 rounded border-2 border-red-300 dark:border-red-700 mx-auto flex items-center justify-center bg-red-50 dark:bg-red-900/20">
                                        <span class="text-red-500 dark:text-red-400 text-2xl font-bold">✗</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($hasFingerprint)
                                    <div class="flex flex-col items-center">
                                        @if($student->fingerprint_image)
                                            <img src="{{ asset('storage/' . $student->fingerprint_image) }}" 
                                                 alt="Fingerprint" 
                                                 class="w-16 h-16 rounded border-2 border-green-500 dark:border-green-600 mx-auto cursor-pointer hover:scale-110 transition-transform bg-white p-1 object-contain"
                                                 onclick="showImageModal(this.src)">
                                        @else
                                            <div class="w-16 h-16 rounded border-2 border-green-500 dark:border-green-600 mx-auto flex items-center justify-center bg-green-50 dark:bg-green-900/20">
                                                <span class="text-green-500 dark:text-green-400 text-xl font-bold">✓</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Template</p>
                                        @endif
                                        @if($student->fingerprint_quality)
                                            <div class="text-xs mt-1 px-2 py-1 rounded {{ $student->fingerprint_quality >= 80 ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : ($student->fingerprint_quality >= 70 ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400') }}">
                                                {{ $student->fingerprint_quality }}%
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded border-2 border-red-300 dark:border-red-700 mx-auto flex items-center justify-center bg-red-50 dark:bg-red-900/20">
                                            <span class="text-red-500 dark:text-red-400 text-2xl font-bold">✗</span>
                                        </div>
                                        @if($attendanceStatus === 'present')
                                            <div class="text-xs text-orange-600 dark:text-orange-400 font-semibold mt-1">
                                                👆 NEEDED
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($isComplete)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                        ✓ Complete (2/2)
                                    </span>
                                @else
                                    @if($completedCount == 1)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                            ⚠ Partial (1/2)
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                            ✗ None (0/2)
                                        </span>
                                    @endif
                                    <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                                        @if(!$hasTestPhoto) Missing: Test Photo<br>@endif
                                        @if(!$hasFingerprint) Missing: Fingerprint @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('super-admin.biometric-status.show', $student->id) }}" 
                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No students found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                {{ $students->links() }}
            </div>
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