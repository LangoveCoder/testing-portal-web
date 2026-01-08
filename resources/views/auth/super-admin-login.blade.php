@extends('layouts.app')

@section('title', 'Super Admin Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-800 dark:to-purple-900">
    <div class="bg-white dark:bg-dark-800 p-8 rounded-lg shadow-2xl w-full max-w-md border border-gray-200 dark:border-dark-700">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Super Admin Portal</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Admission Test Management System</p>
        </div>

        @if(session('message'))
            <div class="bg-yellow-100 dark:bg-yellow-900/30 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-400 p-4 mb-4" role="alert">
                <p>{{ session('message') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 mb-4" role="alert">
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('super-admin.login.post') }}">
            @csrf
            
            <div class="mb-6">
                <label for="username" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Username
                </label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="{{ old('username') }}"
                    class="shadow appearance-none border border-gray-300 dark:border-dark-600 rounded w-full py-3 px-4 text-gray-700 dark:text-gray-100 bg-white dark:bg-dark-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 dark:focus:border-blue-400 placeholder-gray-500 dark:placeholder-gray-400"
                    placeholder="Enter your username"
                    required
                    autofocus
                >
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    class="shadow appearance-none border border-gray-300 dark:border-dark-600 rounded w-full py-3 px-4 text-gray-700 dark:text-gray-100 bg-white dark:bg-dark-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 dark:focus:border-blue-400 placeholder-gray-500 dark:placeholder-gray-400"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2 text-blue-600 dark:text-blue-400 bg-white dark:bg-dark-700 border-gray-300 dark:border-dark-600 rounded focus:ring-blue-500 dark:focus:ring-blue-400">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Remember me</span>
                </label>
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200"
            >
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('college.login') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                College Admin Login →
            </a>
        </div>
    </div>
</div>
@endsection