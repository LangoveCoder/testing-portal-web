@extends('layouts.app')

@section('title', 'College Admin Dashboard')

@section('content')
<style>
.card-hover:hover .icon-box { transform: scale(1.1); }
.bg-icon { 
    position: absolute; 
    top: -20px; 
    right: -20px; 
    font-size: 120px !important; 
    opacity: 0.12; 
    transition: opacity 0.3s ease;
    z-index: 1;
}
.dark .bg-icon {
    opacity: 0.08;
}
.card-hover:hover .bg-icon { 
    opacity: 0.25; 
}
.dark .card-hover:hover .bg-icon {
    opacity: 0.15;
}
.card-content {
    position: relative;
    z-index: 10;
}
</style>

<div class="min-h-screen bg-gray-50 dark:bg-dark-900">
    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-dark-800/80 backdrop-blur-md border-b border-gray-200 dark:border-dark-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-secondary-600 dark:bg-secondary-500 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-secondary-500/30">
                        C
                    </div>
                    <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">
                        College<span class="text-secondary-600 dark:text-secondary-400"> Portal</span>
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end mr-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ Auth::guard('college')->user()->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">College Admin</span>
                    </div>
                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-700 transition-colors relative">
                            <span class="material-icons-outlined">notifications</span>
                            @if($unreadNotifications > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                                    {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                                </span>
                            @endif
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-dark-800 rounded-lg shadow-xl border border-gray-200 dark:border-dark-700 z-50">
                            <div class="p-4 border-b border-gray-200 dark:border-dark-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                                    @if($unreadNotifications > 0)
                                        <a href="{{ route('college.notifications.mark-all-read') }}" class="text-sm text-secondary-600 dark:text-secondary-400 hover:text-secondary-700 dark:hover:text-secondary-300">
                                            Mark all read
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse($notifications as $notification)
                                    <div class="p-4 border-b border-gray-100 dark:border-dark-700 hover:bg-gray-50 dark:hover:bg-dark-700 {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full bg-{{ $notification->color }}-100 dark:bg-{{ $notification->color }}-900/30 flex items-center justify-center flex-shrink-0">
                                                <span class="material-icons-outlined text-sm text-{{ $notification->color }}-600 dark:text-{{ $notification->color }}-400">{{ $notification->icon }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->title }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $notification->message }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">{{ $notification->time_ago }}</p>
                                            </div>
                                            @if(!$notification->is_read)
                                                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center">
                                        <span class="material-icons-outlined text-4xl text-gray-400 dark:text-gray-600">notifications_none</span>
                                        <p class="text-gray-500 dark:text-gray-400 mt-2">No notifications yet</p>
                                    </div>
                                @endforelse
                            </div>
                            @if($notifications->count() > 0)
                                <div class="p-4 border-t border-gray-200 dark:border-dark-700">
                                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                                        All notifications displayed
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('college.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/50">
                            <span class="material-icons-outlined text-lg">logout</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="mb-10">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">College Dashboard</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Manage student registrations, view results, and verify fingerprints.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Register Student -->
            <a href="{{ route('college.students.create') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-secondary-300 dark:hover:border-secondary-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-secondary-500 dark:text-secondary-400">person_add</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">person_add</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-secondary-600 dark:text-secondary-400 uppercase">Register</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-secondary-600 dark:group-hover:text-secondary-400 transition-colors">Add Student</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Add individual student registration.</p>
                </div>
            </a>

            <!-- View Students -->
            <a href="{{ route('college.students.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-primary-500 dark:text-primary-400">groups</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">groups</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-primary-600 dark:text-primary-400 uppercase">Students</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">View Students</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">View all registered students.</p>
                </div>
            </a>

            <!-- Fingerprint Verification -->
            <a href="{{ route('college.fingerprint-verification.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-purple-500 dark:text-purple-400">fingerprint</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">fingerprint</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-purple-600 dark:text-purple-400 uppercase">Verify</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Fingerprint</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Verify student fingerprints on test day.</p>
                </div>
            </a>

            <!-- Biometric Status -->
            <a href="{{ route('college.biometric-status.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-pink-300 dark:hover:border-pink-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-pink-500 dark:text-pink-400">analytics</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">analytics</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-pink-600 dark:text-pink-400 uppercase">Status</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">Biometric Monitor</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Track fingerprint & photo capture status.</p>
                </div>
            </a>

            <!-- Bulk Upload Template -->
            <a href="javascript:void(0)" onclick="document.getElementById('bulkTemplateForm').style.display='block'" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-indigo-500 dark:text-indigo-400">file_download</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">file_download</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-indigo-600 dark:text-indigo-400 uppercase">Bulk Upload</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Excel Template</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Download Excel template for bulk upload.</p>
                </div>
            </a>

            <!-- View Results -->
            <a href="{{ route('college.results.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-teal-300 dark:hover:border-teal-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-teal-500 dark:text-teal-400">emoji_events</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">emoji_events</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-teal-600 dark:text-teal-400 uppercase">Results</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">View Results</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Check published test results.</p>
                </div>
            </a>

            <!-- Generate Reports -->
            <a href="{{ route('college.reports.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-orange-500 dark:text-orange-400">assessment</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">assessment</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-orange-600 dark:text-orange-400 uppercase">Reports</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Generate Reports</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Download student reports & statistics.</p>
                </div>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-dark-700 py-6 mt-10 bg-white dark:bg-dark-800 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">© 2024 University Admission Portal. All rights reserved.</p>
    </footer>

    <!-- Bulk Template Download Modal (Hidden by default) -->
    <div id="bulkTemplateForm" style="display:none;" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-dark-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4 border border-gray-200 dark:border-dark-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">📥 Download Bulk Template</h3>
                <button onclick="document.getElementById('bulkTemplateForm').style.display='none'" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            @if($availableTests->count() > 0)
                <form method="POST" action="{{ route('college.download-bulk-template') }}">
                    @csrf
                    <input type="hidden" name="college_id" value="{{ Auth::guard('college')->user()->id }}">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select Test</label>
                        <select name="test_id" required class="w-full border border-gray-300 dark:border-dark-600 bg-white dark:bg-dark-700 text-gray-900 dark:text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-secondary-500">
                            <option value="">-- Select Test --</option>
                            @foreach($availableTests as $test)
                                <option value="{{ $test->id }}">
                                    {{ $test->test_date->format('d M Y') }} - Mode {{ str_replace('mode_', '', $test->test_mode) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Download Excel template with dropdowns, fill student data, add photos, create ZIP, and send to Super Admin.
                    </p>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="document.getElementById('bulkTemplateForm').style.display='none'" class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-secondary-600 text-white px-4 py-2 rounded-lg hover:bg-secondary-700">
                            Download
                        </button>
                    </div>
                </form>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm italic mb-4">No tests available. Contact Super Admin.</p>
                <button onclick="document.getElementById('bulkTemplateForm').style.display='none'" class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Close
                </button>
            @endif
        </div>
    </div>
</div>
@endsection