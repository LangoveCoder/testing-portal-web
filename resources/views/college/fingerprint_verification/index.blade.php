@extends('layouts.app')

@section('title', 'Fingerprint Verification')

@push('scripts')
<!-- SecuGen WebAPI Scripts -->
<script src="{{ asset('js/sgiBioSrv.js') }}"></script>
<script src="{{ asset('js/biometric-scanner.js') }}"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.dashboard') }}" class="text-white hover:text-gray-200">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">Fingerprint Verification (Test Day)</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('college')->user()->name }}</span>
                    <form method="POST" action="{{ route('college.logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-green-500 to-green-600">
                <h2 class="text-2xl font-bold text-white">👆 Fingerprint Verification</h2>
                <p class="text-green-100 mt-1">Verify student identity on test day</p>
            </div>

            <div class="p-6">
                
                <!-- Scanner Status Card -->
                <div class="bg-gradient-to-r from-green-50 to-teal-50 border-2 border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div id="scanner_indicator" class="w-4 h-4 rounded-full bg-gray-400"></div>
                            <div>
                                <p id="scanner_status" class="text-sm font-bold text-gray-800">Scanner: Initializing...</p>
                                <p id="scanner_device" class="text-xs text-gray-600">Detecting device...</p>
                            </div>
                        </div>
                        <button onclick="troubleshootScanner()" id="troubleshoot_btn" disabled
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed">
                            🔧 Troubleshoot Scanner
                        </button>
                    </div>
                </div>

                <!-- Search Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Load Student</h3>
                    <div class="flex space-x-3">
                        <input type="text" id="search_term" placeholder="Enter Roll Number"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button onclick="loadStudent()" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            Load Student
                        </button>
                    </div>
                    <div id="search_message" class="mt-2 text-sm"></div>
                </div>

                <!-- Student Information (Hidden by default) -->
                <div id="student_info" class="hidden">
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Student Verification Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                            <!-- Registration Photo -->
                            <div class="col-span-1">
                                <div class="border border-gray-300 rounded-lg p-3 text-center bg-gray-50">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Registration Photo</p>
                                    <img id="registration_photo" src="" alt="Registration Photo" 
                                         onclick="zoomImage(this)" 
                                         class="w-full h-48 object-cover rounded border-2 border-gray-300 cursor-pointer hover:border-green-500 transition">
                                    <p class="text-xs text-gray-500 mt-1">Click to zoom</p>
                                </div>
                            </div>

                            <!-- Test Photo (from Android) -->
                            <div class="col-span-1">
                                <div class="border border-gray-300 rounded-lg p-3 text-center bg-gray-50">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Test Photo</p>
                                    <img id="test_photo" src="" alt="Test Photo" 
                                         onclick="zoomImage(this)"
                                         class="w-full h-48 object-cover rounded border-2 border-gray-300 cursor-pointer hover:border-green-500 transition">
                                    <p class="text-xs text-gray-500 mt-1">Click to zoom</p>
                                    <p id="test_photo_status" class="text-xs mt-1"></p>
                                </div>
                            </div>

                            <!-- Saved Fingerprint -->
                            <div class="col-span-1">
                                <div class="border border-gray-300 rounded-lg p-3 text-center bg-gray-50">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Saved Fingerprint</p>
                                    <img id="saved_fingerprint" src="" alt="Saved Fingerprint" 
                                         onclick="zoomImage(this)"
                                         class="w-full h-48 object-contain rounded border-2 border-gray-300 cursor-pointer hover:border-green-500 transition bg-white">
                                    <p class="text-xs text-gray-500 mt-1">Click to zoom</p>
                                    <p id="fingerprint_status" class="text-xs mt-1"></p>
                                </div>
                            </div>

                            <!-- Student Details -->
                            <div class="col-span-1">
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <p class="text-xs text-gray-600">Name</p>
                                        <p id="student_name" class="font-semibold text-gray-900 text-sm"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Father Name</p>
                                        <p id="student_father" class="font-semibold text-gray-900 text-sm"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Roll Number</p>
                                        <p id="student_roll" class="font-semibold text-green-600 text-lg"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">CNIC</p>
                                        <p id="student_cnic" class="font-semibold text-gray-900 text-sm"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Hall / Zone / Row / Seat</p>
                                        <p id="student_seating" class="font-semibold text-gray-900 text-sm"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Section -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Live Verification</h3>
                            
                            <!-- Instructions -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-blue-800">
                                    <strong>📋 Verification Steps:</strong><br>
                                    1. Ask student to place finger on scanner<br>
                                    2. Click "Verify Fingerprint" button<br>
                                    3. Wait for match result<br>
                                    4. Review confidence score<br>
                                    5. Allow or Deny entry based on result
                                </p>
                            </div>

                            <!-- Verify Button -->
                            <button onclick="verifyFingerprint()" id="verify_btn" disabled
                                    class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-lg disabled:bg-gray-300 disabled:cursor-not-allowed mb-4">
                                🔍 Verify Fingerprint
                            </button>

                            <!-- Verification Result -->
                            <div id="verification_result" class="hidden">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Note -->
        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <strong>⚠️ Test Day Protocol:</strong> Verify each student's fingerprint before allowing entry to examination hall. 
                Log all verification attempts for audit purposes.
            </p>
        </div>
    </div>
</div>

<!-- Image Zoom Modal -->
<div id="imageZoomModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50" onclick="closeZoom()">
    <div class="relative max-w-4xl max-h-screen p-4">
        <button onclick="closeZoom()" class="absolute top-2 right-2 bg-white text-gray-800 rounded-full p-2 hover:bg-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="zoomedImage" src="" alt="Zoomed" class="max-w-full max-h-screen rounded shadow-2xl">
    </div>
</div>

<!-- JavaScript -->
<script>
    let currentStudent = null;
    let scanner = null;
    let scannerInitialized = false;

    // Initialize scanner automatically when page loads
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('🚀 Verification page loaded - initializing scanner...');
        scanner = new BiometricScanner();
        await initializeScanner();
    });

    // Initialize Scanner
    async function initializeScanner() {
        if (!scanner) {
            scanner = new BiometricScanner();
        }

        updateScannerStatus('Connecting...', 'loading', 'Please wait...');
        document.getElementById('troubleshoot_btn').disabled = true;

        try {
            const result = await scanner.initialize();
            
            if (result.success) {
                scannerInitialized = true;
                updateScannerStatus('Connected & Ready', 'success', result.device || 'SecuGen Scanner');
                document.getElementById('troubleshoot_btn').disabled = false;
                
                console.log('✓ Scanner initialized for verification');
            } else {
                scannerInitialized = false;
                updateScannerStatus('Connection Failed', 'error', result.message || 'Unknown error');
                document.getElementById('troubleshoot_btn').disabled = false;
                
                alert('⚠️ Scanner Connection Failed!\n\n' + result.message);
            }
        } catch (error) {
            scannerInitialized = false;
            updateScannerStatus('Error', 'error', error.message);
            document.getElementById('troubleshoot_btn').disabled = false;
            
            console.error('❌ Scanner error:', error);
        }
    }

    // Troubleshoot Scanner
    async function troubleshootScanner() {
        if (confirm('🔧 Reconnect scanner?\n\nThis will reset the connection.')) {
            if (scanner) scanner.disconnect();
            scanner = null;
            scannerInitialized = false;
            await new Promise(resolve => setTimeout(resolve, 500));
            await initializeScanner();
        }
    }

    // Update Scanner Status
    function updateScannerStatus(status, type, device) {
        const statusElement = document.getElementById('scanner_status');
        const deviceElement = document.getElementById('scanner_device');
        const indicatorElement = document.getElementById('scanner_indicator');

        if (statusElement) statusElement.textContent = 'Scanner: ' + status;
        if (deviceElement) deviceElement.textContent = device;

        if (indicatorElement) {
            indicatorElement.className = 'w-4 h-4 rounded-full';
            switch (type) {
                case 'success': indicatorElement.classList.add('bg-green-500'); break;
                case 'error': indicatorElement.classList.add('bg-red-500'); break;
                case 'loading': indicatorElement.classList.add('bg-yellow-500', 'animate-pulse'); break;
                default: indicatorElement.classList.add('bg-gray-400');
            }
        }
    }

    // Load Student
    async function loadStudent() {
        const searchTerm = document.getElementById('search_term').value.trim();
        const messageDiv = document.getElementById('search_message');
        
        if (!searchTerm) {
            messageDiv.innerHTML = '<span class="text-red-600 font-semibold">⚠️ Please enter roll number</span>';
            return;
        }

        messageDiv.innerHTML = '<span class="text-blue-600 font-semibold">🔍 Loading student data...</span>';

        try {
            // TODO: Replace with actual API endpoint
            const response = await fetch('{{ route("college.fingerprint-verification.load-student") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ roll_number: searchTerm })
            });

            const result = await response.json();

            if (result.success) {
                currentStudent = result.data;
                displayStudentInfo(result.data);
                messageDiv.innerHTML = '<span class="text-green-600 font-semibold">✓ Student loaded successfully</span>';
            } else {
                messageDiv.innerHTML = '<span class="text-red-600 font-semibold">✗ ' + result.message + '</span>';
                document.getElementById('student_info').classList.add('hidden');
            }
        } catch (error) {
            messageDiv.innerHTML = '<span class="text-red-600 font-semibold">✗ Error: ' + error.message + '</span>';
        }
    }

    // Display Student Info
    function displayStudentInfo(student) {
        // Photos
        document.getElementById('registration_photo').src = student.picture || '/images/no-photo.png';
        document.getElementById('test_photo').src = student.test_photo || '/images/no-photo.png';
        
        // Test photo status
        const testPhotoStatus = document.getElementById('test_photo_status');
        if (student.test_photo) {
            testPhotoStatus.innerHTML = '<span class="text-green-600 font-semibold">✓ Captured</span>';
        } else {
            testPhotoStatus.innerHTML = '<span class="text-red-600 font-semibold">✗ Not captured</span>';
        }

        // Fingerprint
        document.getElementById('saved_fingerprint').src = student.fingerprint_image || '/images/no-fingerprint.png';
        
        const fingerprintStatus = document.getElementById('fingerprint_status');
        if (student.fingerprint_template) {
            fingerprintStatus.innerHTML = '<span class="text-green-600 font-semibold">✓ Registered</span>';
            document.getElementById('verify_btn').disabled = !scannerInitialized;
        } else {
            fingerprintStatus.innerHTML = '<span class="text-red-600 font-semibold">✗ Not registered</span>';
            document.getElementById('verify_btn').disabled = true;
        }

        // Details
        document.getElementById('student_name').textContent = student.name;
        document.getElementById('student_father').textContent = student.father_name;
        document.getElementById('student_roll').textContent = student.roll_number;
        document.getElementById('student_cnic').textContent = student.cnic;
        document.getElementById('student_seating').textContent = `Hall ${student.hall}, Zone ${student.zone}, Row ${student.row}, Seat ${student.seat}`;

        document.getElementById('student_info').classList.remove('hidden');
        document.getElementById('verification_result').classList.add('hidden');
    }

    // Verify Fingerprint
    async function verifyFingerprint() {
        if (!currentStudent) {
            alert('⚠️ No student loaded');
            return;
        }

        if (!scannerInitialized || !scanner) {
            alert('⚠️ Scanner not ready. Click Troubleshoot.');
            return;
        }

        if (!currentStudent.fingerprint_template) {
            alert('⚠️ Student has no registered fingerprint template');
            return;
        }

        document.getElementById('verify_btn').disabled = true;
        document.getElementById('verify_btn').textContent = '📸 Capturing live fingerprint...';

        try {
            // Capture live fingerprint
            const captureResult = await scanner.capture();
            
            if (!captureResult.success) {
                document.getElementById('verify_btn').disabled = false;
                document.getElementById('verify_btn').textContent = '🔍 Verify Fingerprint';
                alert('✗ Capture failed!\n\n' + captureResult.message);
                return;
            }

            document.getElementById('verify_btn').textContent = '🔍 Matching...';

            // Verify against stored template
            const verifyResult = await scanner.verify(currentStudent.fingerprint_template, captureResult.data.template);
            
            document.getElementById('verify_btn').disabled = false;
            document.getElementById('verify_btn').textContent = '🔍 Verify Fingerprint';

            // Log verification attempt
            await logVerification(verifyResult);

            // Display result
            displayVerificationResult(verifyResult);

        } catch (error) {
            document.getElementById('verify_btn').disabled = false;
            document.getElementById('verify_btn').textContent = '🔍 Verify Fingerprint';
            alert('✗ Verification error!\n\n' + error.message);
        }
    }

    // Display Verification Result
    function displayVerificationResult(result) {
        const resultDiv = document.getElementById('verification_result');
        
        if (result.match) {
            resultDiv.innerHTML = `
                <div class="bg-green-100 border-2 border-green-500 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-500 rounded-full p-3 mr-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-green-800">✓ FINGERPRINT MATCHED</h4>
                            <p class="text-green-700">Confidence Score: <span class="text-2xl font-bold">${result.score}%</span></p>
                        </div>
                    </div>
                    <p class="text-green-800 mb-4">${result.message}</p>
                    <div class="flex space-x-3">
                        <button onclick="allowEntry()" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-semibold text-lg">
                            ✓ ALLOW ENTRY
                        </button>
                        <button onclick="verifyFingerprint()" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            🔄 Verify Again
                        </button>
                    </div>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-100 border-2 border-red-500 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-red-500 rounded-full p-3 mr-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-red-800">✗ FINGERPRINT MISMATCH</h4>
                            <p class="text-red-700">Confidence Score: <span class="text-2xl font-bold">${result.score}%</span></p>
                        </div>
                    </div>
                    <p class="text-red-800 mb-4">${result.message}</p>
                    <div class="flex space-x-3">
                        <button onclick="denyEntry()" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 font-semibold text-lg">
                            ✗ DENY ENTRY
                        </button>
                        <button onclick="verifyFingerprint()" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            🔄 Try Again
                        </button>
                    </div>
                </div>
            `;
        }
        
        resultDiv.classList.remove('hidden');
    }

    // Log Verification
    async function logVerification(result) {
        try {
            await fetch('{{ route("college.fingerprint-verification.log") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_id: currentStudent.id,
                    roll_number: currentStudent.roll_number,
                    match_result: result.match,
                    confidence_score: result.score
                })
            });
        } catch (error) {
            console.error('Failed to log verification:', error);
        }
    }

    // Allow Entry
    function allowEntry() {
        if (confirm('✓ ALLOW ENTRY\n\nStudent: ' + currentStudent.name + '\nRoll: ' + currentStudent.roll_number + '\n\nConfirm to allow entry to examination hall?')) {
            alert('✓ Entry Allowed!\n\nStudent verified successfully.');
            resetForm();
        }
    }

    // Deny Entry
    function denyEntry() {
        if (confirm('✗ DENY ENTRY\n\nStudent: ' + currentStudent.name + '\nRoll: ' + currentStudent.roll_number + '\n\nConfirm to deny entry?')) {
            alert('✗ Entry Denied!\n\nFingerprint mismatch. Student not allowed.');
            resetForm();
        }
    }

    // Reset Form
    function resetForm() {
        document.getElementById('search_term').value = '';
        document.getElementById('student_info').classList.add('hidden');
        document.getElementById('verification_result').classList.add('hidden');
        currentStudent = null;
    }

    // Image Zoom Functions
    function zoomImage(img) {
        document.getElementById('zoomedImage').src = img.src;
        document.getElementById('imageZoomModal').classList.remove('hidden');
        document.getElementById('imageZoomModal').classList.add('flex');
    }

    function closeZoom() {
        document.getElementById('imageZoomModal').classList.add('hidden');
        document.getElementById('imageZoomModal').classList.remove('flex');
    }

    // Allow Enter key to load
    document.getElementById('search_term').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') loadStudent();
    });
</script>
@endsection