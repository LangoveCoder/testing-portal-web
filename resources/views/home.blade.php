
@extends('layouts.public')
@section('title', 'Home - BACT | Balochistan Academy for College Teachers')

@section('extra-css')
<style>
    /* Carousel Fade Animation */
    .carousel-item {
        animation: fadeIn 1s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Card Hover Effects */
    .hover-card {
        transition: all 0.3s ease;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Announcement Badge Animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .pulse-animation {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endsection

@section('content')

<!-- Hero Carousel Section -->
<section class="relative bg-gradient-to-r from-cyan-600 to-blue-700 overflow-hidden">
    <div class="container mx-auto px-4 py-20">
        <div class="carousel relative">
            <!-- Carousel Item 1 -->
            <div class="carousel-item active">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div class="text-white space-y-6">
                        <h2 class="text-5xl font-bold leading-tight">Welcome to BACT</h2>
                        <p class="text-xl text-cyan-100">Balochistan Academy for College Teachers - Empowering Education, Shaping Futures</p>
                        <p class="text-lg text-cyan-50">Leading the way in professional development and academic excellence across Balochistan</p>
                        <div class="flex space-x-4">
                            <a href="{{ route('about-us') }}" class="bg-white text-cyan-700 px-6 py-3 rounded-lg font-semibold hover:bg-cyan-50 transition">
                                Learn More
                            </a>
                            <a href="{{ route('student.portal') }}" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                                Check Roll Number
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <img src="https://via.placeholder.com/600x400/0EA5E9/FFFFFF?text=BACT+Building" alt="BACT Building" class="rounded-lg shadow-2xl w-full">
                    </div>
                </div>
            </div>
            
            <!-- Carousel Item 2 -->
            <div class="carousel-item">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div class="text-white space-y-6">
                        <h2 class="text-5xl font-bold leading-tight">Professional Training</h2>
                        <p class="text-xl text-cyan-100">Comprehensive Teacher Development Programs</p>
                        <p class="text-lg text-cyan-50">Join our world-class training sessions designed to enhance teaching methodologies and educational leadership</p>
                        <div class="flex space-x-4">
                            <a href="{{ route('training.events') }}" class="bg-white text-cyan-700 px-6 py-3 rounded-lg font-semibold hover:bg-cyan-50 transition">
                                View Training Events
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <img src="https://via.placeholder.com/600x400/10B981/FFFFFF?text=Training+Session" alt="Training Session" class="rounded-lg shadow-2xl w-full">
                    </div>
                </div>
            </div>
            
            <!-- Carousel Item 3 -->
            <div class="carousel-item">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div class="text-white space-y-6">
                        <h2 class="text-5xl font-bold leading-tight">Admission Tests</h2>
                        <p class="text-xl text-cyan-100">Fair & Transparent Assessment System</p>
                        <p class="text-lg text-cyan-50">Conducting standardized admission tests for colleges across Balochistan with complete transparency</p>
                        <div class="flex space-x-4">
                            <a href="{{ route('tests.upcoming') }}" class="bg-white text-cyan-700 px-6 py-3 rounded-lg font-semibold hover:bg-cyan-50 transition">
                                Upcoming Tests
                            </a>
                            <a href="{{ route('tests.results') }}" class="bg-yellow-400 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-yellow-300 transition">
                                Check Results
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <img src="https://via.placeholder.com/600x400/8B5CF6/FFFFFF?text=Exam+Hall" alt="Exam Hall" class="rounded-lg shadow-2xl w-full">
                    </div>
                </div>
            </div>
            
            <!-- Carousel Controls -->
            <button onclick="prevSlide()" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white rounded-full w-12 h-12 flex items-center justify-center backdrop-blur-sm transition">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="nextSlide()" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white rounded-full w-12 h-12 flex items-center justify-center backdrop-blur-sm transition">
                <i class="fas fa-chevron-right"></i>
            </button>
            
            <!-- Carousel Indicators -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <button onclick="goToSlide(0)" class="indicator w-3 h-3 bg-white rounded-full active"></button>
                <button onclick="goToSlide(1)" class="indicator w-3 h-3 bg-white/50 rounded-full"></button>
                <button onclick="goToSlide(2)" class="indicator w-3 h-3 bg-white/50 rounded-full"></button>
            </div>
        </div>
    </div>
</section>

<!-- Quick Access Cards -->
<section class="py-12 -mt-10 relative z-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <a href="{{ route('student.portal') }}" class="hover-card bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-cyan-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-id-card text-3xl text-cyan-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Check Roll Number</h3>
                <p class="text-sm text-gray-600">Download your roll number slip</p>
            </a>
            
            <a href="{{ route('tests.results') }}" class="hover-card bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-bar text-3xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">View Results</h3>
                <p class="text-sm text-gray-600">Check your test results online</p>
            </a>
            
            <a href="{{ route('tests.model-papers') }}" class="hover-card bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-pdf text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Model Papers</h3>
                <p class="text-sm text-gray-600">Download sample test papers</p>
            </a>
            
            <a href="{{ route('training.events') }}" class="hover-card bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chalkboard-teacher text-3xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Training Events</h3>
                <p class="text-sm text-gray-600">Professional development programs</p>
            </a>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Latest Announcements -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-4 flex items-center justify-between">
                        <h2 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-bullhorn mr-3"></i>
                            Latest Announcements
                        </h2>
                        <span class="pulse-animation bg-yellow-400 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">NEW</span>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Announcement Item -->
                        <div class="border-l-4 border-cyan-600 pl-4 py-2 hover:bg-cyan-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">Admission Test 2025 - Registration Open</h3>
                                    <p class="text-sm text-gray-600 mb-2">Registration for Intermediate Admission Test 2025 is now open. Students can register through their respective colleges till 31st December 2025.</p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <span>Posted: November 25, 2025</span>
                                    </div>
                                </div>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold ml-4">URGENT</span>
                            </div>
                        </div>
                        
                        <!-- Announcement Item -->
                        <div class="border-l-4 border-green-600 pl-4 py-2 hover:bg-green-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">Roll Number Slips Available</h3>
                                    <p class="text-sm text-gray-600 mb-2">Roll number slips for March 2025 Admission Test are now available. Students can download from the Student Portal.</p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <span>Posted: November 20, 2025</span>
                                    </div>
                                </div>
                                <a href="{{ route('student.portal') }}" class="bg-cyan-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-cyan-700 transition ml-4">
                                    Check Now
                                </a>
                            </div>
                        </div>
                        
                        <!-- Announcement Item -->
                        <div class="border-l-4 border-purple-600 pl-4 py-2 hover:bg-purple-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">Results Published - December 2024 Test</h3>
                                    <p class="text-sm text-gray-600 mb-2">Results for December 2024 Admission Test have been announced. Check your result and download merit list.</p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="fas fa-calendar mr-2"></i>
                                        <span>Posted: November 15, 2025</span>
                                    </div>
                                </div>
                                <a href="{{ route('tests.results') }}" class="bg-green-600 text-white px-3 py-1 rounded text-xs font-semibold hover:bg-green-700 transition ml-4">
                                    View Results
                                </a>
                            </div>
                        </div>
                        
                        <!-- View All -->
                        <div class="text-center pt-4">
                            <a href="#" class="text-cyan-600 hover:text-cyan-700 font-semibold inline-flex items-center">
                                View All Announcements
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- BACT Highlights -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-600 to-blue-700 text-white px-6 py-4">
                        <h2 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-star mr-3"></i>
                            BACT Highlights
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Highlight Card -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-cyan-600 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-cyan-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-users text-2xl text-cyan-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-800">150+</h3>
                                        <p class="text-sm text-gray-600">Colleges Registered</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Highlight Card -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-cyan-600 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user-graduate text-2xl text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-800">50,000+</h3>
                                        <p class="text-sm text-gray-600">Students Tested Annually</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Highlight Card -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-cyan-600 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-chalkboard-teacher text-2xl text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-800">200+</h3>
                                        <p class="text-sm text-gray-600">Training Sessions Conducted</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Highlight Card -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-cyan-600 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-map-marked-alt text-2xl text-red-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-800">34</h3>
                                        <p class="text-sm text-gray-600">Districts Covered</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- About BACT -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="font-semibold text-gray-800 mb-3">About BACT</h3>
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">
                                The Balochistan Academy for College Teachers (BACT) is a premier institution dedicated to enhancing the quality of education in Balochistan. We conduct fair and transparent admission tests for colleges and provide comprehensive professional development programs for educators across the province.
                            </p>
                            <a href="{{ route('about-us') }}" class="text-cyan-600 hover:text-cyan-700 font-semibold text-sm inline-flex items-center">
                                Read More About Us
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Test Updates -->
                <div class="bg-gradient-to-br from-cyan-600 to-blue-700 rounded-lg shadow-md overflow-hidden text-white">
                    <div class="px-6 py-4 bg-white/10 backdrop-blur-sm">
                        <h3 class="text-xl font-bold flex items-center">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Test Updates
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Update Item -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-yellow-400 text-gray-800 text-xs font-bold px-2 py-1 rounded">UPCOMING</span>
                                <span class="text-xs">March 15, 2025</span>
                            </div>
                            <h4 class="font-semibold mb-1">Intermediate Admission Test</h4>
                            <p class="text-sm text-cyan-100">Registration deadline: Dec 31, 2025</p>
                        </div>
                        
                        <!-- Update Item -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-green-400 text-gray-800 text-xs font-bold px-2 py-1 rounded">AVAILABLE</span>
                                <span class="text-xs">Now</span>
                            </div>
                            <h4 class="font-semibold mb-1">Roll Number Slips</h4>
                            <p class="text-sm text-cyan-100 mb-3">Download your roll number slip</p>
                            <a href="{{ route('student.portal') }}" class="block text-center bg-white text-cyan-700 py-2 rounded font-semibold hover:bg-cyan-50 transition text-sm">
                                Download Now
                            </a>
                        </div>
                        
                        <!-- Update Item -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-purple-400 text-white text-xs font-bold px-2 py-1 rounded">RESULTS</span>
                                <span class="text-xs">Dec Test</span>
                            </div>
                            <h4 class="font-semibold mb-1">December 2024 Results</h4>
                            <p class="text-sm text-cyan-100 mb-3">Check your test results online</p>
                            <a href="{{ route('tests.results') }}" class="block text-center bg-white text-cyan-700 py-2 rounded font-semibold hover:bg-cyan-50 transition text-sm">
                                View Results
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-800 text-white px-6 py-4">
                        <h3 class="text-xl font-bold flex items-center">
                            <i class="fas fa-link mr-2"></i>
                            Quick Links
                        </h3>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('student.portal') }}" class="flex items-center justify-between p-3 rounded hover:bg-cyan-50 transition group">
                                    <span class="text-gray-700 group-hover:text-cyan-700 font-medium">
                                        <i class="fas fa-id-card mr-2 text-cyan-600"></i>
                                        Check Roll Number
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-cyan-600"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tests.results') }}" class="flex items-center justify-between p-3 rounded hover:bg-green-50 transition group">
                                    <span class="text-gray-700 group-hover:text-green-700 font-medium">
                                        <i class="fas fa-chart-bar mr-2 text-green-600"></i>
                                        View Results
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tests.answer-keys') }}" class="flex items-center justify-between p-3 rounded hover:bg-purple-50 transition group">
                                    <span class="text-gray-700 group-hover:text-purple-700 font-medium">
                                        <i class="fas fa-key mr-2 text-purple-600"></i>
                                        Answer Keys
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-purple-600"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tests.model-papers') }}" class="flex items-center justify-between p-3 rounded hover:bg-red-50 transition group">
                                    <span class="text-gray-700 group-hover:text-red-700 font-medium">
                                        <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                                        Model Papers
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-red-600"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tests.upcoming') }}" class="flex items-center justify-between p-3 rounded hover:bg-yellow-50 transition group">
                                    <span class="text-gray-700 group-hover:text-yellow-700 font-medium">
                                        <i class="fas fa-calendar-alt mr-2 text-yellow-600"></i>
                                        Upcoming Tests
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-yellow-600"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Important Notice -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-yellow-800 mb-1">Important Notice</h4>
                            <p class="text-sm text-yellow-700">
                                Students are advised to check their roll number slips carefully. For any discrepancies, contact your respective college immediately.
                            </p>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</section>

@endsection

@section('extra-js')
<script>
    // Carousel Functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.indicator');
    const totalSlides = slides.length;
    
    function showSlide(n) {
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => {
            indicator.classList.remove('active');
            indicator.classList.add('bg-white/50');
            indicator.classList.remove('bg-white');
        });
        
        currentSlide = (n + totalSlides) % totalSlides;
        slides[currentSlide].classList.add('active');
        indicators[currentSlide].classList.add('active', 'bg-white');
        indicators[currentSlide].classList.remove('bg-white/50');
    }
    
    function nextSlide() {
        showSlide(currentSlide + 1);
    }
    
    function prevSlide() {
        showSlide(currentSlide - 1);
    }
    
    function goToSlide(n) {
        showSlide(n);
    }
    
    // Auto-play carousel
    setInterval(() => {
        nextSlide();
    }, 5000);
</script>
@endsection