@extends('layouts.app')

@section('title', 'Biometric Operators')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-dark-900">
    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-dark-800/80 backdrop-blur-md border-b border-gray-200 dark:border-dark-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <a href="{{ route('super-admin.dashboard') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        <span class="material-icons-outlined text-xl mr-2">arrow_back</span>
                        Dashboard
                    </a>
                    <div class="h-6 w-px bg-gray-300 dark:bg-dark-700"></div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Biometric Operators</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end mr-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ Auth::guard('super_admin')->user()->name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Super Admin</span>
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
        
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 animate-fade-in">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-green-600 dark:text-green-400 mr-3">check_circle</span>
                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 animate-fade-in">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-red-600 dark:text-red-400 mr-3">error</span>
                    <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-pink-500 to-pink-600 flex justify-between items-center">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-3xl text-white mr-4">security</span>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Biometric Operators</h2>
                        <p class="text-pink-100 mt-1">Manage operators who can register fingerprints</p>
                    </div>
                </div>
                <a href="{{ route('super-admin.biometric-operators.create') }}" 
                   class="inline-flex items-center px-4 py-2.5 bg-white hover:bg-pink-50 text-pink-600 font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95">
                    <span class="material-icons-outlined text-lg mr-2">add</span>
                    Create Operator
                </a>
            </div>

            <div class="p-6">
                @if($operators->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-dark-700 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                            <span class="material-icons-outlined text-2xl text-gray-400">security</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Biometric Operators</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating a new operator</p>
                        <a href="{{ route('super-admin.biometric-operators.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-pink-600 hover:bg-pink-700 dark:bg-pink-700 dark:hover:bg-pink-800 text-white font-medium rounded-lg transition-colors">
                            <span class="material-icons-outlined text-lg mr-2">add</span>
                            Create Operator
                        </a>
                    </div>
                @else
                    <!-- Mobile Cards (Hidden on larger screens) -->
                    <div class="block lg:hidden">
                        @foreach($operators as $operator)
                            <div class="p-6 border-b border-gray-200 dark:border-dark-700 last:border-b-0">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12 bg-pink-100 dark:bg-pink-900/30 rounded-full flex items-center justify-center">
                                            <span class="text-pink-600 dark:text-pink-400 font-semibold text-lg">{{ substr($operator->name, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $operator->name }}</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $operator->email }}</p>
                                        </div>
                                    </div>
                                    @if($operator->status === 'active')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Inactive</span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">College:</span>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $operator->assignedCollege ? $operator->assignedCollege->name : 'No College' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Tests:</span>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $operator->tests->count() }} Tests</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Created:</span>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $operator->created_at->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Created by:</span>
                                        <p class="text-gray-900 dark:text-gray-100">{{ $operator->creator->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('super-admin.biometric-operators.edit', $operator) }}" 
                                       class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors"
                                       title="Edit Operator">
                                        <span class="material-icons-outlined text-lg">edit</span>
                                    </a>
                                    <form action="{{ route('super-admin.biometric-operators.destroy', $operator) }}" 
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this operator?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                title="Delete Operator">
                                            <span class="material-icons-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop Table (Hidden on smaller screens) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
                            <thead class="bg-gray-50 dark:bg-dark-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Operator</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Assigned Colleges</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Assigned Tests</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                                @foreach($operators as $operator)
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-pink-100 dark:bg-pink-900/30 rounded-full flex items-center justify-center">
                                                <span class="text-pink-600 dark:text-pink-400 font-semibold">{{ substr($operator->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $operator->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Created by: {{ $operator->creator->name ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $operator->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ $operator->assignedCollege ? $operator->assignedCollege->name : 'No College' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            {{ $operator->tests->count() }} Tests
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($operator->status === 'active')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Active</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $operator->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('super-admin.biometric-operators.edit', $operator) }}" 
                                               class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors"
                                               title="Edit Operator">
                                                <span class="material-icons-outlined text-lg">edit</span>
                                            </a>
                                            <form action="{{ route('super-admin.biometric-operators.destroy', $operator) }}" 
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this operator?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                        title="Delete Operator">
                                                    <span class="material-icons-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection