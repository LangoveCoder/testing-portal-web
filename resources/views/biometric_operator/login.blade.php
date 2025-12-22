<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometric Operator Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-100 to-purple-200 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-center">
                <div class="text-6xl mb-3">🔐</div>
                <h2 class="text-2xl font-bold text-white">Biometric Operator</h2>
                <p class="text-purple-100 text-sm mt-1">Fingerprint Registration Portal</p>
            </div>

            <!-- Login Form -->
            <div class="p-8">
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('biometric-operator.login.post') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">
                            Email Address
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Enter your email">
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">
                            Password
                        </label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Enter your password">
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6 flex items-center">
                        <input type="checkbox" name="remember" id="remember"
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-purple-500 to-purple-600 text-white font-bold py-3 px-4 rounded-lg hover:from-purple-600 hover:to-purple-700 transition duration-200">
                        Login
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-purple-600 hover:text-purple-800 text-sm">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-4 bg-white rounded-lg shadow p-4 text-center">
            <p class="text-gray-600 text-sm">
                <strong>Note:</strong> Biometric operator accounts are created by Super Admin.
            </p>
        </div>
    </div>
</body>
</html>