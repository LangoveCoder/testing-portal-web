@extends('layouts.app')

@section('title', 'Manage Colleges')

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
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Manage Colleges</h1>
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
        
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 animate-fade-in">
                <div class="flex items-center">
                    <span class="material-icons-outlined text-green-600 dark:text-green-400 mr-3">check_circle</span>
                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">All Colleges</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage registered colleges and their information</p>
            </div>
            <a href="{{ route('super-admin.colleges.create') }}" 
               class="inline-flex items-center px-4 py-2.5 bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95">
                <span class="material-icons-outlined text-lg mr-2">add</span>
                Register New College
            </a>
        </div>

        <!-- Colleges Grid/Table -->
        <div class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 overflow-hidden">
            <!-- Mobile Cards (Hidden on larger screens) -->
            <div class="block lg:hidden">
                @forelse($colleges as $college)
                    <div class="p-6 border-b border-gray-200 dark:border-dark-700 last:border-b-0">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $college->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $college->contact_person }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $college->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                {{ $college->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Email:</span>
                                <p class="text-gray-900 dark:text-gray-100">{{ $college->email }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Phone:</span>
                                <p class="text-gray-900 dark:text-gray-100">{{ $college->phone }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">District:</span>
                                <p class="text-gray-900 dark:text-gray-100">{{ $college->district }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Gender Policy:</span>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $college->gender_policy == 'Both' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' }}">
                                    {{ $college->gender_policy }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('super-admin.colleges.show', $college) }}" 
                               class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors"
                               title="View Details">
                                <span class="material-icons-outlined text-lg">visibility</span>
                            </a>
                            <a href="{{ route('super-admin.colleges.edit', $college) }}" 
                               class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors"
                               title="Edit College">
                                <span class="material-icons-outlined text-lg">edit</span>
                            </a>
                            <form action="{{ route('super-admin.colleges.destroy', $college) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this college?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                        title="Delete College">
                                    <span class="material-icons-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-dark-700 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                            <span class="material-icons-outlined text-2xl text-gray-400">account_balance</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No colleges registered</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by registering your first college</p>
                        <a href="{{ route('super-admin.colleges.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800 text-white font-medium rounded-lg transition-colors">
                            <span class="material-icons-outlined text-lg mr-2">add</span>
                            Register New College
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table (Hidden on smaller screens) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-700">
                    <thead class="bg-gray-50 dark:bg-dark-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                College
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Contact Info
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Location
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Policy
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-dark-800 divide-y divide-gray-200 dark:divide-dark-700">
                        @forelse($colleges as $college)
                            <tr class="hover:bg-gray-50 dark:hover:bg-dark-700 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $college->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $college->contact_person }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $college->email }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $college->phone }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $college->district }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $college->gender_policy == 'Both' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' }}">
                                        {{ $college->gender_policy }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                        {{ $college->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                        {{ $college->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('super-admin.colleges.show', $college) }}" 
                                           class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors"
                                           title="View Details">
                                            <span class="material-icons-outlined text-lg">visibility</span>
                                        </a>
                                        <a href="{{ route('super-admin.colleges.edit', $college) }}" 
                                           class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors"
                                           title="Edit College">
                                            <span class="material-icons-outlined text-lg">edit</span>
                                        </a>
                                        <form action="{{ route('super-admin.colleges.destroy', $college) }}" 
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this college?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                    title="Delete College">
                                                <span class="material-icons-outlined text-lg">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-dark-700 rounded-2xl flex items-center justify-center mb-4">
                                            <span class="material-icons-outlined text-2xl text-gray-400">account_balance</span>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No colleges registered</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by registering your first college</p>
                                        <a href="{{ route('super-admin.colleges.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800 text-white font-medium rounded-lg transition-colors">
                                            <span class="material-icons-outlined text-lg mr-2">add</span>
                                            Register New College
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection