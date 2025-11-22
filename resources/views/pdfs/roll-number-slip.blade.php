<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Roll Number Slip</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            padding: 15px;
        }
        
        .container {
            border: 3px solid #000;
            padding: 0;
        }
        
        /* Header */
        .header {
            background: linear-gradient(to bottom, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 15px;
            text-align: center;
            border-bottom: 3px solid #000;
        }
        
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
        }
        
        .logo-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid white;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 10px;
            color: #1e3a8a;
        }
        
        .header-text {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
            padding-left: 15px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 9px;
            margin: 2px 0;
        }
        
        /* Title Box */
        .title-box {
            background: white;
            border: 2px solid #000;
            margin: 15px;
            padding: 12px;
            text-align: center;
        }
        
        .title-box h2 {
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }
        
        /* Main Content */
        .main-content {
            border: 3px solid #000;
            margin: 15px;
            padding: 0;
        }
        
        /* Roll Number Section */
        .roll-section {
            padding: 15px;
            min-height: 160px;
        }
        
        .roll-left {
            float: left;
            width: 60%;
        }
        
        .roll-right {
            float: right;
            width: 38%;
            text-align: center;
        }
        
        .roll-number {
            margin-bottom: 15px;
        }
        
        .roll-number-label {
            font-weight: normal;
            font-size: 11px;
            color: #000;
        }
        
        .roll-number-value {
            font-size: 36px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
            margin-top: 5px;
        }
        
        .name-section {
            margin-bottom: 8px;
        }
        
        .name-label {
            font-size: 11px;
            font-weight: normal;
            display: block;
            margin-bottom: 2px;
        }
        
        .name-value {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }
        
        .photo-box {
            width: 120px;
            height: 140px;
            border: 2px solid #000;
            background: #f0f0f0;
            margin: 0 auto 8px;
            overflow: hidden;
        }
        
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .cnic-text {
            font-size: 10px;
            font-weight: bold;
            color: #000;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        /* Info Grid */
        .info-grid {
            border-top: 2px solid #000;
        }
        
        .info-row {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-cell {
            display: table-cell;
            padding: 12px;
            border-right: 2px solid #000;
            vertical-align: top;
            width: 50%;
        }
        
        .info-cell:last-child {
            border-right: none;
        }
        
        .info-cell-small {
            width: 33.33%;
        }
        
        .border-bottom {
            border-bottom: 2px solid #000;
        }
        
        .info-icon {
            width: 26px;
            height: 26px;
            background: #1e3a8a;
            color: white;
            text-align: center;
            line-height: 26px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .info-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            display: block;
            margin-bottom: 3px;
        }
        
        .info-value {
            font-size: 13px;
            font-weight: bold;
            color: #000;
        }
        
        /* Notes Section */
        .notes-section {
            padding: 15px;
            border-top: 2px solid #000;
        }
        
        .note-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
        }
        
        .notes-section ul {
            margin-left: 15px;
            font-size: 9px;
            line-height: 1.7;
        }
        
        .notes-section li {
            margin-bottom: 4px;
        }
        
        /* Warning Box */
        .warning-box {
            background: #dc2626;
            color: white;
            padding: 12px;
            margin: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
        
        /* QR Code Section */
        .qr-section {
            text-align: center;
            padding: 20px;
            border-top: 2px solid #000;
        }
        
        .qr-code {
            width: 150px;
            height: 150px;
            border: 2px solid #000;
            display: inline-block;
            background: white;
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="logo">
                    <div class="logo-circle">
                        COLLEGE<br>LOGO
                    </div>
                </div>
                <div class="header-text">
                    <h1>{{ $student->test->college->name }}</h1>
                    <p>{{ $student->test->college->address ?? 'College Address Here' }}</p>
                    <p>Contact: {{ $student->test->college->phone ?? '(+92) 081-1234567' }} | Email: {{ $student->test->college->email ?? 'info@college.edu.pk' }}</p>
                    <p>Admission Test {{ $student->test->test_date->format('Y') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Title Box -->
        <div class="title-box">
            <h2>Admission Test {{ $student->test->test_date->format('Y') }}</h2>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            
            <!-- Roll Number Section -->
            <div class="roll-section clearfix">
                <div class="roll-left">
                    <div class="roll-number">
                        <span class="roll-number-label">ROLL NUMBER:</span>
                        <div class="roll-number-value">{{ $student->roll_number }}</div>
                    </div>
                    
                    <div class="name-section">
                        <span class="name-label">NAME:</span>
                        <div class="name-value">{{ strtoupper($student->name) }}</div>
                    </div>
                </div>
                
                <div class="roll-right">
                    <div class="photo-box">
                        @if($student->picture)
                            <img src="{{ public_path('storage/' . $student->picture) }}" alt="Photo">
                        @endif
                    </div>
                    <div class="cnic-text">CNIC: {{ $student->cnic }}</div>
                </div>
            </div>
            
            <!-- Info Grid Row 1 -->
            <div class="info-grid">
                <div class="info-row border-bottom">
                    <div class="info-cell">
                        <div class="info-icon">📍</div>
                        <span class="info-label">VENUE:</span>
                        <div class="info-value">{{ $student->testDistrict->district }}, {{ $student->testDistrict->province }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-icon">📅</div>
                        <span class="info-label">TEST DATE:</span>
                        <div class="info-value">{{ $student->test->test_date->format('d-m-Y') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Info Grid Row 2 -->
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell info-cell-small">
                        <div class="info-icon">🕐</div>
                        <span class="info-label">REPORTING TIME:</span>
                        <div class="info-value">{{ $student->test->start_time ?? '9:00 AM' }}</div>
                    </div>
                    <div class="info-cell info-cell-small">
                        <div class="info-icon">👤</div>
                        <span class="info-label">ROW #</span>
                        <div class="info-value">{{ $student->row_number }}</div>
                    </div>
                    <div class="info-cell info-cell-small">
                        <div class="info-icon">#</div>
                        <span class="info-label">SEAT #</span>
                        <div class="info-value">{{ $student->seat_number }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Notes Section -->
            <div class="notes-section">
                <div class="note-title">Note:</div>
                <ul>
                    <li>No Candidate will be allowed to Take Paper when Roll Number Slip, Original CNIC and Verifications by the Team.</li>
                    <li>Candidates are provisionally to appear in that the subject areas of the Team.</li>
                    <li>Candidates are provisionally to appear in the test field for verification of credentials and eligibility criteria.</li>
                    <li>Candidates are not being Paper Holder, Ball Pre required to take paper.</li>
                </ul>
            </div>
        </div>
        
        <!-- Warning Box -->
        <div class="warning-box">
            Warning:<br>
            Mobile phones, and other electronic devices are not incontomet inside test enter premises.
        </div>
        
        <!-- QR Code -->
        <div class="qr-section">
            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('Venue: ' . $student->testDistrict->district . ', ' . $student->testDistrict->province . ' - Roll: ' . $student->roll_number) }}" alt="QR Code">
            </div>
        </div>
        
    </div>
</body>
</html>