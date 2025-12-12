<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Roll Number Slip - {{ $student->roll_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .container {
            width: 210mm;
            height: 297mm;
            background: white;
            position: relative;
        }
        
        /* Header Section - Compact */
        .header {
            background: linear-gradient(135deg, #00B4D8 0%, #0096C7 100%);
            padding: 15px 20px;
            color: white;
            border-top: 4px solid #DC2626;
        }
        
        .logo-section {
            display: table;
            width: 100%;
        }
        
        .logo-cell {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            border: 3px solid white;
            border-radius: 50%;
            background: white;
            padding: 3px;
        }
        
        .org-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }
        
        .org-cell h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .org-cell h2 {
            font-size: 11px;
            font-weight: normal;
            opacity: 0.95;
        }
        
        .test-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.3);
            font-size: 13px;
        }
        
        .test-info strong {
            font-size: 14px;
        }
        
        /* Roll Number Section - Prominent */
        .roll-number-section {
            background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
            padding: 15px;
            text-align: center;
            border-bottom: 4px solid #FCD34D;
        }
        
        .roll-label {
            color: white;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .roll-number {
            font-size: 36px;
            font-weight: bold;
            color: #FCD34D;
            letter-spacing: 6px;
            font-family: 'Courier New', monospace;
        }
        
        /* Main Content */
        .main-content {
            padding: 20px;
            display: table;
            width: 100%;
        }
        
        .left-column {
            display: table-cell;
            width: 140px;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .student-photo {
            width: 120px;
            height: 150px;
            border: 3px solid #00B4D8;
            border-radius: 8px;
            object-fit: cover;
            margin-bottom: 15px;
        }
        
        .right-column {
            display: table-cell;
            vertical-align: top;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table tr {
            border-bottom: 1px solid #E5E7EB;
        }
        
        .info-table td {
            padding: 8px 10px;
            font-size: 11px;
        }
        
        .info-table td:first-child {
            color: #6B7280;
            font-weight: 600;
            width: 35%;
        }
        
        .info-table td:last-child {
            color: #1F2937;
            font-weight: bold;
        }
        
        .book-color-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Seating Section - Compact */
        .seating-section {
            background: #F3F4F6;
            padding: 15px 20px;
            margin: 0 20px 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #00B4D8;
        }
        
        .seating-title {
            font-size: 12px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .seating-grid {
            display: table;
            width: 100%;
        }
        
        .seating-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            width: 25%;
        }
        
        .seating-label {
            font-size: 9px;
            color: #6B7280;
            font-weight: 600;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .seating-value {
            font-size: 20px;
            font-weight: bold;
            color: #DC2626;
        }
        
        /* Venue Info */
        .venue-info {
            background: #DBEAFE;
            padding: 10px 20px;
            margin: 0 20px 15px 20px;
            border-radius: 5px;
            border-left: 4px solid #0096C7;
            font-size: 10px;
        }
        
        .venue-info strong {
            color: #1E40AF;
            font-size: 11px;
        }
        
        /* Instructions - Compact */
        .instructions {
            background: #FEF3C7;
            border-left: 4px solid #FCD34D;
            padding: 12px 20px;
            margin: 0 20px 15px 20px;
            border-radius: 5px;
        }
        
        .instructions h4 {
            color: #92400E;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        
        .instructions ul {
            list-style-position: inside;
            color: #78350F;
            font-size: 9px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        /* Footer */
        .footer {
            background: #1F2937;
            color: white;
            padding: 12px 20px;
            text-align: center;
            font-size: 9px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        .footer strong {
            font-size: 10px;
        }
        
        .footer-links {
            margin-top: 5px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo-cell">
                    <img src="{{ public_path('images/bact-logo.png') }}" alt="BACT Logo" class="logo">
                </div>
                <div class="org-cell">
                    <h1>BALOCHISTAN ACADEMY FOR COLLEGE TEACHERS</h1>
                    <h2>Colleges, Higher & Technical Education Department, Quetta</h2>
                    <div class="test-info">
                        <strong>{{ $student->test->test_name }}</strong><br>
                        📅 {{ $student->test->test_date->format('l, F d, Y') }}
                        @if($student->test->test_time)
                            | ⏰ {{ $student->test->test_time }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Roll Number -->
        <div class="roll-number-section">
            <div class="roll-label">ROLL NUMBER</div>
            <div class="roll-number">{{ $student->roll_number }}</div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="left-column">
                <!-- Photo -->
                @if($student->picture)
                    <img src="{{ public_path('storage/' . $student->picture) }}" alt="Student Photo" class="student-photo">
                @else
                    <div class="student-photo" style="background: #E5E7EB; display: flex; align-items: center; justify-content: center; color: #9CA3AF; font-size: 10px;">
                        No Photo
                    </div>
                @endif
                
                <!-- QR Code - FORCED DISPLAY -->
                <div style="text-align: center; margin-top: 10px;">
                    <div style="font-size: 9px; color: #6B7280; margin-bottom: 5px; font-weight: 600;">📍 Venue Location</div>
                    @if(isset($qrCodeBase64) && $qrCodeBase64)
                        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" style="width: 100px; height: 100px; border: 2px solid #00B4D8; border-radius: 5px; padding: 5px;">
                    @else
                        <div style="width: 100px; height: 100px; border: 2px solid #DC2626; display: flex; align-items: center; justify-content: center; font-size: 8px; text-align: center; padding: 5px; background: #FEE2E2; color: #DC2626;">
                            QR: {{ isset($venue) ? 'Venue OK' : 'No Venue' }}<br>
                            Link: {{ isset($venue) && $venue && $venue->google_maps_link ? 'YES' : 'NO' }}<br>
                            Base64: {{ isset($qrCodeBase64) ? 'YES' : 'NO' }}
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="right-column">
                <!-- Student Info Table -->
                <table class="info-table">
                    <tr>
                        <td>Student Name</td>
                        <td>{{ strtoupper($student->name) }}</td>
                    </tr>
                    <tr>
                        <td>Father's Name</td>
                        <td>{{ strtoupper($student->father_name) }}</td>
                    </tr>
                    <tr>
                        <td>CNIC Number</td>
                        <td>{{ $student->cnic }}</td>
                    </tr>
                    <tr>
                        <td>Date of Birth</td>
                        <td>{{ $student->date_of_birth->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <td>{{ $student->gender }}</td>
                    </tr>
                    <tr>
                        <td>District</td>
                        <td>{{ $student->district }}, {{ $student->province }}</td>
                    </tr>
                    <tr>
                        <td>Book Color</td>
                        <td>
                            <span class="book-color-badge" style="background-color: {{ $student->book_color === 'Yellow' ? '#FEF3C7' : ($student->book_color === 'Green' ? '#D1FAE5' : ($student->book_color === 'Blue' ? '#DBEAFE' : '#FCE7F3')) }}; color: {{ $student->book_color === 'Yellow' ? '#92400E' : ($student->book_color === 'Green' ? '#065F46' : ($student->book_color === 'Blue' ? '#1E40AF' : '#9F1239')) }};">
                                {{ $student->book_color }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Seating Details -->
        <div class="seating-section">
            <div class="seating-title">🪑 YOUR SEATING ASSIGNMENT</div>
            <div class="seating-grid">
                <div class="seating-item">
                    <div class="seating-label">Hall</div>
                    <div class="seating-value">{{ $student->hall_number }}</div>
                </div>
                <div class="seating-item">
                    <div class="seating-label">Zone</div>
                    <div class="seating-value">{{ $student->zone_number }}</div>
                </div>
                <div class="seating-item">
                    <div class="seating-label">Row</div>
                    <div class="seating-value">{{ $student->row_number }}</div>
                </div>
                <div class="seating-item">
                    <div class="seating-label">Seat</div>
                    <div class="seating-value">{{ $student->seat_number }}</div>
                </div>
            </div>
        </div>
        
        <!-- Venue Info -->
        @if(isset($venue) && $venue)
        <div class="venue-info">
            <strong>Test Venue:</strong> {{ $venue->venue_name }}<br>
            <strong>Address:</strong> {{ $venue->venue_address }}
        </div>
        @endif
        
        <!-- Instructions -->
        <div class="instructions">
            <h4>⚠️ IMPORTANT INSTRUCTIONS</h4>
            <ul>
                <li>Bring this roll number slip and original CNIC on test day</li>
                <li>Report to your assigned hall 30 minutes before test time</li>
                <li>You will receive question paper with <strong>{{ $student->book_color }}</strong> color cover</li>
                <li>Mobile phones and electronic devices are strictly prohibited</li>
                <li>Late entry will NOT be allowed after test starts</li>
            </ul>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <strong>BALOCHISTAN ACADEMY FOR COLLEGE TEACHERS (BACT)</strong><br>
            Quetta, Balochistan | www.bact.gov.pk | info@bact.gov.pk
        </div>
    </div>
</body>
</html>