<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https: http:;">
    <title>@yield('title', 'BACT - Balochistan Academy for College Teachers')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* BACT Color Scheme */
        :root {
            --bact-blue: #00B4D8;
            --bact-red: #DC2626;
            --bact-gray: #6B7280;
            --bact-yellow: #FCD34D;
        }
        
        /* News Ticker Animation */
        @keyframes scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        
        .ticker-content {
            animation: scroll 30s linear infinite;
            display: inline-block;
            white-space: nowrap;
        }
        
        .ticker-content:hover {
            animation-play-state: paused;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Dropdown Menu */
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        
        /* Hero Carousel */
        .carousel-item {
            display: none;
        }
        
        .carousel-item.active {
            display: block;
        }
    </style>
    
    @yield('extra-css')
</head>
<body class="bg-gray-50">
    
    <!-- News Ticker -->
    <div class="bg-red-600 text-white py-2 overflow-hidden">
        <div class="container mx-auto px-4 flex items-center">
            <span class="bg-yellow-400 text-red-700 px-3 py-1 rounded font-bold mr-4 flex-shrink-0">
                <i class="fas fa-bullhorn"></i> LATEST
            </span>
            <div class="flex-1 overflow-hidden">
                <div class="ticker-content">
                    <span class="mr-12">📢 Admission Test 2025 - Registration open till 31st December 2025</span>
                    <span class="mr-12">🎫 Roll Number Slips now available for March 2025 Test</span>
                    <span class="mr-12">📊 Results for December 2024 Test announced - Check now!</span>
                    <span class="mr-12">📚 New Model Papers uploaded for upcoming tests</span>
                    <span class="mr-12">🎓 Teacher Training Workshop scheduled for January 2025</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <!-- Top Header with Logo -->
            <div class="flex items-center justify-between py-4 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <img src="https://via.placeholder.com/80x80/00B4D8/FFFFFF?text=BACT" alt="BACT Logo" class="h-20 w-20 rounded-full">
                    <div>
                        <h1 class="text-2xl font-bold text-cyan-600">BACT</h1>
                        <p class="text-sm text-gray-600">Balochistan Academy for College Teachers</p>
                        <p class="text-xs text-gray-500">Colleges Higher & Technical Education Department</p>
                    </div>
                </div>
                
                <!-- Quick Contact -->
                <div class="hidden md:flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2 text-gray-600">
                        <i class="fas fa-phone text-cyan-600"></i>
                        <span>+92-81-XXXXXXX</span>
                    </div>
                    <div class="flex items-center space-x-2 text-gray-600">
                        <i class="fas fa-envelope text-cyan-600"></i>
                        <span><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="95fcfbf3fad5f7f4f6e1bbf0f1e0bbe5fe">[email&#160;protected]</a></span>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="py-4">
                <ul class="flex items-center justify-center space-x-8 text-gray-700 font-medium">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-cyan-600 transition {{ request()->routeIs('home') ? 'text-cyan-600 font-semibold' : '' }}">
                            <i class="fas fa-home mr-1"></i> Home
                        </a>
                    </li>
                    
                    <!-- About Dropdown -->
                    <li class="relative dropdown">
                        <a href="#" class="hover:text-cyan-600 transition flex items-center">
                            About <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <ul class="dropdown-menu absolute hidden bg-white shadow-lg rounded-lg mt-2 py-2 w-48">
                            <li><a href="{{ route('about-us') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600">About Us</a></li>
                            <li><a href="{{ route('organogram') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600">Organogram</a></li>
                        </ul>
                    </li>
                    
                    <!-- Training Dropdown -->
                    <li class="relative dropdown">
                        <a href="#" class="hover:text-cyan-600 transition flex items-center">
                            Training <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <ul class="dropdown-menu absolute hidden bg-white shadow-lg rounded-lg mt-2 py-2 w-48">
                            <li><a href="{{ route('training.events') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600">Events</a></li>
                            <li><a href="{{ route('training.materials') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600">Training Material</a></li>
                            <li><a href="{{ route('training.outcomes') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600">Training Outcomes</a></li>
                        </ul>
                    </li>
                    
                    <!-- Tests Dropdown -->
                    <li class="relative dropdown">
                        <a href="#" class="hover:text-cyan-600 transition flex items-center">
                            Tests <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <ul class="dropdown-menu absolute hidden bg-white shadow-lg rounded-lg mt-2 py-2 w-56">
                            <li><a href="{{ route('student.portal') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600"><i class="fas fa-id-card mr-2"></i>Check Roll Number</a></li>
                            <li><a href="{{ route('tests.results') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600"><i class="fas fa-chart-bar mr-2"></i>View Results</a></li>
                            <li><a href="{{ route('tests.answer-keys') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600"><i class="fas fa-key mr-2"></i>Answer Keys</a></li>
                            <li><a href="{{ route('tests.upcoming') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600"><i class="fas fa-calendar-alt mr-2"></i>Upcoming Tests</a></li>
                            <li><a href="{{ route('tests.model-papers') }}" class="block px-4 py-2 hover:bg-cyan-50 hover:text-cyan-600"><i class="fas fa-file-pdf mr-2"></i>Model Papers</a></li>
                        </ul>
                    </li>
                    
                    <li>
                        <a href="{{ route('achievements') }}" class="hover:text-cyan-600 transition {{ request()->routeIs('achievements') ? 'text-cyan-600 font-semibold' : '' }}">
                            <i class="fas fa-trophy mr-1"></i> Achievements
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('contact') }}" class="hover:text-cyan-600 transition {{ request()->routeIs('contact') ? 'text-cyan-600 font-semibold' : '' }}">
                            <i class="fas fa-envelope mr-1"></i> Contact
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- About BACT -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="https://via.placeholder.com/64x64/00B4D8/FFFFFF?text=BACT" alt="BACT Logo" class="h-16 w-16 rounded-full">
                        <div>
                            <h3 class="text-xl font-bold text-cyan-400">BACT</h3>
                            <p class="text-xs text-gray-300">Quetta, Balochistan</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-300 leading-relaxed">
                        Balochistan Academy for College Teachers is committed to excellence in education and professional development.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-cyan-400">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>Home</a></li>
                        <li><a href="{{ route('about-us') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>About Us</a></li>
                        <li><a href="{{ route('training.events') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>Training</a></li>
                        <li><a href="{{ route('achievements') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>Achievements</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>Contact Us</a></li>
                    </ul>
                </div>
                
                <!-- Important Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-cyan-400">For Students</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('student.portal') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>Check Roll Number</a></li>
                        <li><a href="{{ route('tests.results') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>View Results</a></li>
                        <li><a href="{{ route('tests.answer-keys') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>Answer Keys</a></li>
                        <li><a href="{{ route('tests.model-papers') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>Model Papers</a></li>
                        <li><a href="{{ route('tests.upcoming') }}" class="hover:text-cyan-400 transition"><i class="fas fa-angle-right mr-2"></i>Upcoming Tests</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-cyan-400">Contact Us</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-cyan-400 mt-1"></i>
                            <span>Balochistan Academy for College Teachers<br>Quetta, Balochistan, Pakistan</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone text-cyan-400"></i>
                            <span>+92-81-XXXXXXX</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-cyan-400"></i>
                            <span><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="c5acaba3aa85a7a4a6b1eba0a1b0ebb5ae">[email&#160;protected]</a></span>
                        </li>
                    </ul>
                    
                    <!-- Social Media -->
                    <div class="mt-4 flex space-x-3">
                        <a href="#" class="w-8 h-8 bg-cyan-600 rounded-full flex items-center justify-center hover:bg-cyan-500 transition">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-cyan-600 rounded-full flex items-center justify-center hover:bg-cyan-500 transition">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-cyan-600 rounded-full flex items-center justify-center hover:bg-cyan-500 transition">
                            <i class="fab fa-youtube text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Footer -->
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Balochistan Academy for College Teachers (BACT). All rights reserved.</p>
                <p class="mt-1">Managed by Higher & Technical Education Department, Government of Balochistan</p>
            </div>
        </div>
    </footer>
    
    <!-- Scroll to Top Button -->
    <button id="scrollTopBtn" class="fixed bottom-8 right-8 bg-cyan-600 text-white w-12 h-12 rounded-full shadow-lg hover:bg-cyan-500 transition hidden items-center justify-center">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- JavaScript -->
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
        // Scroll to Top Button
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.remove('hidden');
                scrollTopBtn.classList.add('flex');