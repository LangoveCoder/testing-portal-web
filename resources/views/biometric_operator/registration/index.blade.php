@extends('layouts.app')

@section('title', 'Fingerprint Registration')

@push('scripts')
<!-- SecuGen WebAPI Scripts -->
<script src="{{ asset('js/sgiBioSrv.js') }}"></script>
<script src="{{ asset('js/biometric-scanner.js') }}"></script>
@endpush

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('biometric-operator.dashboard') }}" class="text-white hover:text-gray-200">
                        ← Back to Dashboard
                    </a>
                    <h1 class="text-xl font-bold">Fingerprint Registration</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::guard('biometric_operator')->user()->name }}</span>
                    <form method="POST" action="{{ route('biometric-operator.logout') }}">
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
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-purple-500 to-purple-600">
                <h2 class="text-2xl font-bold text-white">👆 Register Student Fingerprint</h2>
                <p class="text-purple-100 mt-1">Search student and capture fingerprint</p>
            </div>

            <div class="p-6">
                
                <!-- Scanner Status Card -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div id="scanner_indicator" class="w-4 h-4 rounded-full bg-gray-400"></div>
                            <div>
                                <p id="scanner_status" class="text-sm font-bold text-gray-800">Scanner: Initializing...</p>
                                <p id="scanner_device" class="text-xs text-gray-600">Detecting device...</p>
                            </div>
                        </div>
                        <button onclick="troubleshootScanner()" id="troubleshoot_btn" disabled
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed">
                            🔧 Troubleshoot Scanner
                        </button>
                    </div>
                </div>

                <!-- Quality Settings -->
                <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-6">
                    <label for="quality_threshold" class="block text-sm font-semibold text-gray-800 mb-2">
                        📊 Minimum Quality Score
                    </label>
                    <select id="quality_threshold" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="40">Low (40) - Accept most fingerprints</option>
                        <option value="50" selected>Medium (50) - Recommended ✓</option>
                        <option value="60">High (60) - Better quality</option>
                        <option value="70">Very High (70) - Best quality only</option>
                        <option value="80">Excellent (80) - Premium quality</option>
                    </select>
                    <p class="text-xs text-yellow-700 mt-2">
                        <strong>Note:</strong> Higher values require better fingerprint quality. May need multiple capture attempts for clean prints.
                    </p>
                </div>

                <!-- Search Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Search Student</h3>
                    <div class="flex space-x-3">
                        <input type="text" id="search_term" placeholder="Enter Roll Number or CNIC"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <button onclick="searchStudent()" 
                                class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">
                            Search
                        </button>
                    </div>
                    <div id="search_message" class="mt-2 text-sm"></div>
                </div>

                <!-- Student Information (Hidden by default) -->
                <div id="student_info" class="hidden">
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Student Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Photo -->
                            <div class="col-span-1">
                                <div class="border border-gray-300 rounded-lg p-4 text-center bg-gray-50">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Student Photo</p>
                                    <img id="student_photo" src="" alt="Student Photo" 
                                         class="w-32 h-40 mx-auto object-cover rounded border-2 border-gray-300">
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="col-span-2">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Name</p>
                                        <p id="student_name" class="font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Father Name</p>
                                        <p id="student_father" class="font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Roll Number</p>
                                        <p id="student_roll" class="font-semibold text-purple-600 text-lg"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">CNIC</p>
                                        <p id="student_cnic" class="font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Gender</p>
                                        <p id="student_gender" class="font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Test</p>
                                        <p id="student_test" class="font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Venue</p>
                                        <p id="student_venue" class="font-semibold text-gray-900"></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Seating</p>
                                        <p id="student_seating" class="font-semibold text-gray-900"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fingerprint Status -->
                        <div id="fingerprint_status" class="mb-6"></div>

                        <!-- Fingerprint Capture Section -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">👆 Fingerprint Capture</h3>
                            
                            <!-- Instructions -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <p class="text-sm text-blue-800">
                                    <strong>📋 Instructions:</strong><br>
                                    1. Scanner auto-connects when page loads (one-time)<br>
                                    2. Search student by roll number or CNIC<br>
                                    3. Place student's finger firmly on the scanner<br>
                                    4. Click "Capture Fingerprint" and hold steady<br>
                                    5. Review quality score and save<br>
                                    6. Use "Troubleshoot" button if scanner has issues
                                </p>
                            </div>

                            <!-- Capture Controls -->
                            <div class="space-y-4">
                                <button onclick="captureFingerprint()" id="capture_btn" disabled
                                        class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold text-lg disabled:bg-gray-300 disabled:cursor-not-allowed transition">
                                    👆 Capture Fingerprint
                                </button>

                                <!-- Fingerprint Preview -->
                                <div id="fingerprint_preview" class="hidden">
                                    <div class="border-2 border-green-300 rounded-lg p-4 bg-green-50">
                                        <p class="text-sm font-bold text-green-800 mb-3">✓ Fingerprint Captured Successfully</p>
                                        <div class="bg-white rounded p-4 text-center border border-gray-200">
                                            <img id="fingerprint_image" src="" alt="Fingerprint" class="mx-auto max-w-xs border-2 border-gray-300 rounded">
                                        </div>
                                        <div class="mt-4 flex items-center justify-between">
                                            <div>
                                                <p class="text-sm text-gray-700">
                                                    <span class="font-semibold">Quality Score:</span> 
                                                    <span id="quality_score" class="text-lg font-bold text-green-600">--</span>
                                                </p>
                                            </div>
                                            <div class="space-x-2">
                                                <button onclick="captureFingerprint()" 
                                                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm font-semibold">
                                                    🔄 Re-capture
                                                </button>
                                                <button onclick="saveFingerprint()" 
                                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-semibold">
                                                    ✓ Save & Register
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Note -->
        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <strong>⚠️ Important:</strong> Scanner connects automatically once. Works for entire session. 
                Use Troubleshoot button only if you encounter connection errors.
                Supported: SecuGen Hamster Pro 20-A and all SecuGen models.
            </p>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    let currentStudent = null;
    let fingerprintData = null;
    let scanner = null;
    let scannerInitialized = false;

    // Initialize scanner automatically when page loads
    document.addEventListener('DOMContentLoaded', async function() {
        console.log('🚀 Page loaded - initializing scanner...');
        scanner = new BiometricScanner();
        await initializeScanner();
    });

    // Initialize/Connect Scanner
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
                document.getElementById('capture_btn').disabled = false;
                document.getElementById('troubleshoot_btn').disabled = false;
                
                console.log('✓ Scanner initialized successfully:', result.device);
            } else {
                scannerInitialized = false;
                updateScannerStatus('Connection Failed', 'error', result.message || 'Unknown error');
                document.getElementById('capture_btn').disabled = true;
                document.getElementById('troubleshoot_btn').disabled = false;
                
                alert('⚠️ Scanner Connection Failed!\n\n' + result.message + '\n\nClick "Troubleshoot Scanner" to retry.');
            }
        } catch (error) {
            scannerInitialized = false;
            updateScannerStatus('Error', 'error', error.message);
            document.getElementById('troubleshoot_btn').disabled = false;
            
            console.error('❌ Scanner initialization error:', error);
            alert('⚠️ Scanner Error!\n\n' + error.message + '\n\nClick "Troubleshoot Scanner" to retry.');
        }
    }

    // Troubleshoot Scanner (Reconnect)
    async function troubleshootScanner() {
        console.log('🔧 Troubleshooting scanner - attempting reconnection...');
        
        const confirmed = confirm('🔧 Troubleshoot Scanner\n\nThis will:\n1. Disconnect current scanner\n2. Reset connection\n3. Reconnect automatically\n\nContinue?');
        
        if (!confirmed) return;

        // Reset scanner
        if (scanner) {
            scanner.disconnect();
        }
        scanner = null;
        scannerInitialized = false;
        
        // Small delay before reconnection
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Reinitialize
        await initializeScanner();
    }

    // Update Scanner Status Display
    function updateScannerStatus(status, type, device) {
        const statusElement = document.getElementById('scanner_status');
        const deviceElement = document.getElementById('scanner_device');
        const indicatorElement = document.getElementById('scanner_indicator');

        if (statusElement) {
            statusElement.textContent = 'Scanner: ' + status;
        }

        if (deviceElement) {
            deviceElement.textContent = device;
        }

        if (indicatorElement) {
            indicatorElement.className = 'w-4 h-4 rounded-full';
            switch (type) {
                case 'success':
                    indicatorElement.classList.add('bg-green-500');
                    break;
                case 'error':
                    indicatorElement.classList.add('bg-red-500');
                    break;
                case 'loading':
                    indicatorElement.classList.add('bg-yellow-500', 'animate-pulse');
                    break;
                default:
                    indicatorElement.classList.add('bg-gray-400');
            }
        }
    }

    // Search student
    async function searchStudent() {
        const searchTerm = document.getElementById('search_term').value.trim();
        const messageDiv = document.getElementById('search_message');
        
        if (!searchTerm) {
            messageDiv.innerHTML = '<span class="text-red-600 font-semibold">⚠️ Please enter roll number or CNIC</span>';
            return;
        }

        messageDiv.innerHTML = '<span class="text-blue-600 font-semibold">🔍 Searching...</span>';

        try {
            const response = await fetch('{{ route("biometric-operator.registration.search-student") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ search_term: searchTerm })
            });

            const result = await response.json();

            if (result.success) {
                currentStudent = result.data;
                displayStudentInfo(result.data);
                messageDiv.innerHTML = '<span class="text-green-600 font-semibold">✓ Student found successfully</span>';
            } else {
                messageDiv.innerHTML = '<span class="text-red-600 font-semibold">✗ ' + result.message + '</span>';
                document.getElementById('student_info').classList.add('hidden');
            }
        } catch (error) {
            messageDiv.innerHTML = '<span class="text-red-600 font-semibold">✗ Error: ' + error.message + '</span>';
        }
    }

    // Display student information
    function displayStudentInfo(student) {
        document.getElementById('student_photo').src = student.picture || '/images/no-photo.png';
        document.getElementById('student_name').textContent = student.name;
        document.getElementById('student_father').textContent = student.father_name;
        document.getElementById('student_roll').textContent = student.roll_number;
        document.getElementById('student_cnic').textContent = student.cnic;
        document.getElementById('student_gender').textContent = student.gender;
        document.getElementById('student_test').textContent = student.test_name;
        document.getElementById('student_venue').textContent = student.venue;
        document.getElementById('student_seating').textContent = `Hall ${student.hall}, Zone ${student.zone}, Row ${student.row}, Seat ${student.seat}`;

        // Show fingerprint status
        const statusDiv = document.getElementById('fingerprint_status');
        if (student.fingerprint_registered) {
            statusDiv.innerHTML = `
                <div class="bg-green-100 border-2 border-green-400 rounded-lg p-4">
                    <p class="text-green-800 font-bold text-lg">✓ Fingerprint Already Registered</p>
                    <p class="text-green-700 text-sm mt-1">Last registered: ${student.last_registration || 'N/A'}</p>
                    <p class="text-green-700 text-sm">You can re-register to update the fingerprint template.</p>
                </div>
            `;
        } else {
            statusDiv.innerHTML = `
                <div class="bg-yellow-100 border-2 border-yellow-400 rounded-lg p-4">
                    <p class="text-yellow-800 font-bold text-lg">⚠ Fingerprint Not Registered</p>
                    <p class="text-yellow-700 text-sm mt-1">Please capture and register student's fingerprint below.</p>
                </div>
            `;
        }

        document.getElementById('student_info').classList.remove('hidden');
        document.getElementById('fingerprint_preview').classList.add('hidden');
    }

    // Capture fingerprint
    async function captureFingerprint() {
        if (!currentStudent) {
            alert('⚠️ No Student Selected\n\nPlease search and select a student first.');
            return;
        }

        if (!scannerInitialized || !scanner || !scanner.isConnected) {
            alert('⚠️ Scanner Not Connected\n\nClick "Troubleshoot Scanner" to reconnect.');
            return;
        }

        const minQuality = parseInt(document.getElementById('quality_threshold').value);

        document.getElementById('capture_btn').disabled = true;
        document.getElementById('capture_btn').textContent = '📸 Capturing... Place finger firmly!';

        try {
            const result = await scanner.capture();
            
            if (result.success) {
                
                // Check quality threshold
                if (result.quality < minQuality) {
                    document.getElementById('capture_btn').disabled = false;
                    document.getElementById('capture_btn').textContent = '👆 Capture Fingerprint';
                    
                    alert(`⚠️ Quality Too Low!\n\nCaptured Quality: ${result.quality}%\nRequired Minimum: ${minQuality}%\n\nPlease:\n1. Clean the scanner surface\n2. Ensure finger is dry and clean\n3. Press firmly and hold steady\n4. Try again`);
                    return;
                }

                fingerprintData = result.data;
                
                document.getElementById('fingerprint_image').src = result.data.image;
                document.getElementById('quality_score').textContent = result.quality + '%';
                document.getElementById('fingerprint_preview').classList.remove('hidden');
                document.getElementById('capture_btn').disabled = false;
                document.getElementById('capture_btn').textContent = '👆 Capture Fingerprint';
                
                // Success sound/notification
                console.log('✓ Fingerprint captured - Quality:', result.quality + '%');
            } else {
                document.getElementById('capture_btn').disabled = false;
                document.getElementById('capture_btn').textContent = '👆 Capture Fingerprint';
                
                alert(`✗ Capture Failed!\n\n${result.message}\n\nPlease:\n1. Ensure finger is on scanner\n2. Try again\n3. Use Troubleshoot if issue persists`);
            }
        } catch (error) {
            document.getElementById('capture_btn').disabled = false;
            document.getElementById('capture_btn').textContent = '👆 Capture Fingerprint';
            
            alert('✗ Capture Error!\n\n' + error.message);
        }
    }

    // Save fingerprint
    async function saveFingerprint() {
        if (!currentStudent || !fingerprintData) {
            alert('⚠️ No Fingerprint Data\n\nPlease capture fingerprint first.');
            return;
        }

        if (!confirm('💾 Save & Register Fingerprint\n\nStudent: ' + currentStudent.name + '\nRoll Number: ' + currentStudent.roll_number + '\n\nConfirm registration?')) {
            return;
        }

        try {
            const response = await fetch('{{ route("biometric-operator.registration.save-fingerprint") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_id: currentStudent.id,
                    fingerprint_template: fingerprintData.template,
                    fingerprint_image: fingerprintData.image
                })
            });

            const result = await response.json();

            if (result.success) {
                alert('✓ SUCCESS!\n\nFingerprint registered successfully!\n\nStudent: ' + result.data.name + '\nRoll: ' + result.data.roll_number + '\n\nYou can now register the next student.');
                
                // Reset form for next student
                document.getElementById('search_term').value = '';
                document.getElementById('student_info').classList.add('hidden');
                document.getElementById('fingerprint_preview').classList.add('hidden');
                currentStudent = null;
                fingerprintData = null;
                
                // Scanner stays connected - ready for next student
                console.log('✓ Registration complete - Scanner ready for next student');
            } else {
                alert('✗ Save Failed!\n\nError: ' + result.message);
            }
        } catch (error) {
            alert('✗ Save Error!\n\n' + error.message);
        }
    }

    // Allow Enter key to search
    document.getElementById('search_term').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchStudent();
        }
    });
</script>
@endsection