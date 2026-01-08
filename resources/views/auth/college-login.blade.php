@extends('layouts.app')

@section('title', 'College Admin Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-500 to-teal-600 dark:from-green-800 dark:to-teal-900">
    <div class="bg-white dark:bg-dark-800 p-8 rounded-lg shadow-2xl w-full max-w-md border border-gray-200 dark:border-dark-700">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">College Admin Portal</h1>
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

        <form method="POST" action="{{ route('college.login.post') }}">
            @csrf
            
            <div class="mb-6">
                <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Email Address
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}"
                    class="shadow appearance-none border border-gray-300 dark:border-dark-600 rounded w-full py-3 px-4 text-gray-700 dark:text-gray-100 bg-white dark:bg-dark-700 leading-tight focus:outline-none focus:shadow-outline focus:border-green-500 dark:focus:border-green-400 placeholder-gray-500 dark:placeholder-gray-400"
                    placeholder="Enter your email"
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
                    class="shadow appearance-none border border-gray-300 dark:border-dark-600 rounded w-full py-3 px-4 text-gray-700 dark:text-gray-100 bg-white dark:bg-dark-700 leading-tight focus:outline-none focus:shadow-outline focus:border-green-500 dark:focus:border-green-400 placeholder-gray-500 dark:placeholder-gray-400"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2 text-green-600 dark:text-green-400 bg-white dark:bg-dark-700 border-gray-300 dark:border-dark-600 rounded focus:ring-green-500 dark:focus:ring-green-400">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Remember me</span>
                </label>
            </div>

            <button 
                type="submit" 
                class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200"
            >
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('super-admin.login') }}" class="text-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors">
                ← Super Admin Login
            </a>
        </div>
    </div>
</div>
@endsection