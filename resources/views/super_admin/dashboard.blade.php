@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

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
                    <div class="w-8 h-8 bg-primary-600 dark:bg-primary-500 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary-500/30">
                        S
                    </div>
                    <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">
                        SuperAdmin <span class="text-primary-600 dark:text-primary-400">Portal</span>
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end mr-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ Auth::guard('super_admin')->user()->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Super Admin</span>
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
                                        <a href="{{ route('super-admin.notifications.mark-all-read') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
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
                    <form method="POST" action="{{ route('super-admin.logout') }}">
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
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Welcome back! Here's what's happening in your admission portal today.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Manage Colleges -->
            <a href="{{ route('super-admin.colleges.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-primary-300 dark:hover:border-primary-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-primary-500 dark:text-primary-400">account_balance</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">account_balance</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-primary-600 dark:text-primary-400 uppercase">Colleges</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Manage Colleges</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Register, view, and manage affiliated colleges.</p>
                </div>
            </a>

            <!-- Manage Tests -->
            <a href="{{ route('super-admin.tests.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-secondary-300 dark:hover:border-secondary-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-secondary-500 dark:text-secondary-400">edit_note</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">edit_note</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-secondary-600 dark:text-secondary-400 uppercase">Tests</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-secondary-600 dark:group-hover:text-secondary-400 transition-colors">Manage Tests</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Create and schedule upcoming entrance tests.</p>
                </div>
            </a>

            <!-- Manage Students -->
            <a href="{{ route('super-admin.students.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-purple-500 dark:text-purple-400">groups</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">groups</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-purple-600 dark:text-purple-400 uppercase">Students</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Manage Students</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">View and manage student registrations.</p>
                </div>
            </a>

            <!-- Generate Roll Numbers -->
            <a href="{{ route('super-admin.roll-numbers.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-amber-300 dark:hover:border-amber-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-amber-500 dark:text-amber-400">confirmation_number</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">confirmation_number</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-amber-600 dark:text-amber-400 uppercase">Roll Numbers</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Generate Numbers</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Generate roll numbers with seating allocation.</p>
                </div>
            </a>

            <!-- Seating Plans -->
            <a href="{{ route('super-admin.seating-plans.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-indigo-500 dark:text-indigo-400">event_seat</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">event_seat</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-indigo-600 dark:text-indigo-400 uppercase">Seating Plans</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">View / Print</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Hall-wise seat arrangements and layouts.</p>
                </div>
            </a>

            <!-- Attendance Sheets -->
            <a href="{{ route('super-admin.attendance-sheets.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-teal-300 dark:hover:border-teal-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-teal-500 dark:text-teal-400">assignment_turned_in</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">assignment_turned_in</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-teal-600 dark:text-teal-400 uppercase">Attendance</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">Generate Sheets</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Create hall-wise attendance check-lists.</p>
                </div>
            </a>

            <!-- Biometric Operators -->
            <a href="{{ route('super-admin.biometric-operators.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-pink-300 dark:hover:border-pink-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-pink-500 dark:text-pink-400">fingerprint</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">security</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-pink-600 dark:text-pink-400 uppercase">Operators</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">Manage Biometrics</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Manage fingerprint operators and access.</p>
                </div>
            </a>

            <!-- Biometric Status -->
            <a href="{{ route('super-admin.biometric-status.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-orange-500 dark:text-orange-400">bar_chart</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">analytics</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-orange-600 dark:text-orange-400 uppercase">Status</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Biometric Monitor</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Track fingerprint & photo capture status.</p>
                </div>
            </a>

            <!-- Student Attendance -->
            <a href="{{ route('super-admin.attendance.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-emerald-300 dark:hover:border-emerald-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-emerald-500 dark:text-emerald-400">smartphone</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">qr_code_scanner</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-emerald-600 dark:text-emerald-400 uppercase">Live Tracking</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Mobile Attendance</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Real-time attendance from mobile app.</p>
                </div>
            </a>

            <!-- Manage Results -->
            <a href="{{ route('super-admin.results.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-red-300 dark:hover:border-red-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-red-500 dark:text-red-400">emoji_events</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">emoji_events</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-red-600 dark:text-red-400 uppercase">Results</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">Publish Results</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Upload and publish exam results.</p>
                </div>
            </a>

            <!-- Bulk Student Upload -->
            <a href="{{ route('super-admin.bulk-upload.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-sky-300 dark:hover:border-sky-600 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-sky-500 dark:text-sky-400">cloud_upload</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">file_upload</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-sky-600 dark:text-sky-400 uppercase">Bulk Upload</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">Excel Import</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">Upload students via Excel template.</p>
                </div>
            </a>

            <!-- Audit Logs -->
            <a href="{{ route('super-admin.audit-logs.index') }}" class="group card-hover bg-white dark:bg-dark-800 rounded-3xl p-8 border border-gray-200 dark:border-dark-700 shadow-sm hover:shadow-2xl hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-300 relative overflow-hidden">
                <span class="material-icons-outlined bg-icon text-gray-500 dark:text-gray-400">list_alt</span>
                <div class="flex flex-col h-full card-content">
                    <div class="icon-box w-16 h-16 rounded-2xl bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center mb-6 transition-transform duration-300">
                        <span class="material-icons-outlined text-3xl">history</span>
                    </div>
                    <div class="mb-2 text-xs font-bold tracking-wider text-gray-600 dark:text-gray-400 uppercase">System</div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">View Audit Logs</h3>
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed">View detailed system activity logs.</p>
                </div>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-dark-700 py-6 mt-10 bg-white dark:bg-dark-800 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">© 2024 University Admission Portal. All rights reserved.</p>
    </footer>
</div>
@endsection