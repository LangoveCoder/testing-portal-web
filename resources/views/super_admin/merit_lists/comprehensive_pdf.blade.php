<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprehensive Merit List - {{ $test->college->name }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 5px 0;
            font-size: 22pt;
            color: #1e40af;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16pt;
            color: #334155;
        }
        
        .header p {
            margin: 3px 0;
            font-size: 10pt;
            color: #64748b;
        }
        
        .statistics-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .statistics-box h3 {
            margin: 0 0 10px 0;
            font-size: 12pt;
            color: #1e40af;
        }
        
        .statistics-grid {
            display: table;
            width: 100%;
        }
        
        .stat-item {
            display: table-cell;
            padding: 5px;
            text-align: center;
        }
        
        .stat-label {
            font-size: 9pt;
            color: #64748b;
        }
        
        .stat-value {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
        }
        
        .division-header {
            background-color: #1e40af;
            color: white;
            padding: 12px;
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 14pt;
            font-weight: bold;
            page-break-before: always;
        }
        
        .division-header:first-child {
            page-break-before: auto;
        }
        
        .district-header {
            background-color: #3b82f6;
            color: white;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 12pt;
            font-weight: bold;
        }
        
        .province-header {
            background-color: #7c3aed;
            color: white;
            padding: 12px;
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 14pt;
            font-weight: bold;
            page-break-before: always;
        }
        
        .merit-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .merit-table thead {
            background-color: #e2e8f0;
        }
        
        .merit-table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            border: 1px solid #cbd5e1;
        }
        
        .merit-table td {
            padding: 6px 5px;
            font-size: 9pt;
            border: 1px solid #e2e8f0;
        }
        
        .merit-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .merit-table tbody tr:hover {
            background-color: #f1f5f9;
        }
        
        .position-cell {
            text-align: center;
            font-weight: bold;
            color: #1e40af;
        }
        
        .marks-cell {
            text-align: center;
            font-weight: bold;
        }
        
        .district-stats {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        
        .district-stats strong {
            color: #1e40af;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            padding: 10px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    
    <!-- Main Header -->
    <div class="header">
        <h1>COMPREHENSIVE MERIT LIST</h1>
        <h2>{{ $test->college->name }}</h2>
        <p><strong>Test Date:</strong> {{ $test->test_date->format('l, d F Y') }} | <strong>Time:</strong> {{ $test->test_time }}</p>
        <p><strong>Test Mode:</strong> {{ ucwords(str_replace('_', ' ', $test->test_mode)) }} | <strong>Total Marks:</strong> {{ $test->total_marks }}</p>
        <p><strong>Generated On:</strong> {{ now()->format('d F Y, h:i A') }}</p>
    </div>

    <!-- Overall Statistics -->
    <div class="statistics-box">
        <h3>📊 Overall Statistics</h3>
        <div class="statistics-grid">
            <div class="stat-item">
                <div class="stat-label">Total Merit Lists</div>
                <div class="stat-value">{{ $statistics['total_lists'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Total Students</div>
                <div class="stat-value">{{ $statistics['total_students'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Balochistan Lists</div>
                <div class="stat-value">{{ $statistics['balochistan_lists'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Other Provinces</div>
                <div class="stat-value">{{ $statistics['other_province_lists'] }}</div>
            </div>
        </div>
    </div>

    <!-- BALOCHISTAN DIVISIONS -->
    @foreach($organizedLists['balochistan_divisions'] as $divisionData)
    <div class="division-header">
        🏛️ {{ $divisionData['division_name'] }} ({{ count($divisionData['districts']) }} Districts)
    </div>

    @foreach($divisionData['districts'] as $districtList)
    <div class="district-header">
        📍 {{ $districtList['district'] }} District - Merit List
    </div>

    <div class="district-stats">
        <strong>Total Students:</strong> {{ $districtList['total_students'] }} | 
        <strong>Highest Marks:</strong> {{ $districtList['highest_marks'] }} | 
        <strong>Lowest Marks:</strong> {{ $districtList['lowest_marks'] }} | 
        <strong>Average:</strong> {{ $districtList['average_marks'] }}
    </div>

    <table class="merit-table">
        <thead>
            <tr>
                <th style="width: 8%;">Position</th>
                <th style="width: 12%;">Roll No.</th>
                <th style="width: 20%;">Student Name</th>
                <th style="width: 18%;">Father Name</th>
                <th style="width: 15%;">CNIC</th>
                <th style="width: 10%;">Gender</th>
                <th style="width: 9%;">Marks</th>
                <th style="width: 8%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($districtList['results'] as $result)
            <tr>
                <td class="position-cell">{{ $result->position }}</td>
                <td>{{ $result->roll_number }}</td>
                <td>{{ $result->student->name }}</td>
                <td>{{ $result->student->father_name }}</td>
                <td style="font-family: monospace;">{{ $result->student->cnic }}</td>
                <td>{{ $result->student->gender }}</td>
                <td class="marks-cell">{{ $result->marks }}</td>
                <td class="marks-cell">{{ $result->total_marks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
    @endforeach

    <!-- OTHER PROVINCES -->
    @if(count($organizedLists['other_provinces']) > 0)
    @foreach($organizedLists['other_provinces'] as $provinceList)
    <div class="province-header">
        🌍 {{ $provinceList['province'] }} Province - Merit List
    </div>

    <div class="district-stats">
        <strong>Total Students:</strong> {{ $provinceList['total_students'] }} | 
        <strong>Highest Marks:</strong> {{ $provinceList['highest_marks'] }} | 
        <strong>Lowest Marks:</strong> {{ $provinceList['lowest_marks'] }} | 
        <strong>Average:</strong> {{ $provinceList['average_marks'] }}
    </div>

    <table class="merit-table">
        <thead>
            <tr>
                <th style="width: 8%;">Position</th>
                <th style="width: 12%;">Roll No.</th>
                <th style="width: 20%;">Student Name</th>
                <th style="width: 18%;">Father Name</th>
                <th style="width: 15%;">CNIC</th>
                <th style="width: 10%;">Gender</th>
                <th style="width: 9%;">Marks</th>
                <th style="width: 8%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($provinceList['results'] as $result)
            <tr>
                <td class="position-cell">{{ $result->position }}</td>
                <td>{{ $result->roll_number }}</td>
                <td>{{ $result->student->name }}</td>
                <td>{{ $result->student->father_name }}</td>
                <td style="font-family: monospace;">{{ $result->student->cnic }}</td>
                <td>{{ $result->student->gender }}</td>
                <td class="marks-cell">{{ $result->marks }}</td>
                <td class="marks-cell">{{ $result->total_marks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach
    @endif

    <!-- Footer -->
    <div class="footer">
        {{ $test->college->name }} | Merit List Generated: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>