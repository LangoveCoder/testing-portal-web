<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Sheet - {{ $test->test_name }}</title>
    <style>
    @page {
        margin: 10mm 8mm;
        size: A4 landscape;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: Arial, sans-serif;
        font-size: 9px;
        line-height: 1.2;
        padding: 0 5mm;
    }
    
    .header {
        text-align: center;
        margin-bottom: 8px;
        padding-bottom: 6px;
        border-bottom: 2px solid #10b981;
    }
    
    .header h1 {
        font-size: 16px;
        color: #059669;
        margin-bottom: 3px;
    }
    
    .header p {
        font-size: 9px;
        color: #4b5563;
    }
    
    .page-section {
        page-break-after: always;
        page-break-inside: avoid;
    }
    
    .venue-header {
        background: #d1fae5;
        padding: 6px 10px;
        margin-bottom: 6px;
        border: 2px solid #10b981;
        border-radius: 3px;
    }
    
    .venue-header h2 {
        font-size: 11px;
        color: #059669;
        margin-bottom: 2px;
        font-weight: bold;
    }
    
    .venue-header p {
        font-size: 8px;
        color: #374151;
    }
    
    .hall-title {
        background: #10b981;
        color: white;
        padding: 5px 8px;
        font-size: 10px;
        font-weight: bold;
        margin-bottom: 5px;
        border-radius: 2px;
    }
    
    .zone-row-title {
        background: #6ee7b7;
        color: #064e3b;
        padding: 4px 8px;
        font-size: 9px;
        font-weight: bold;
        margin-bottom: 5px;
        border-radius: 2px;
    }
    
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    
    .attendance-table th {
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        padding: 6px 4px;
        text-align: left;
        font-size: 8px;
        font-weight: bold;
        color: #374151;
    }
    
    .attendance-table td {
        border: 1px solid #e5e7eb;
        padding: 6px 4px;
        font-size: 9px;
        color: #1f2937;
        vertical-align: middle;
    }
    
    .attendance-table tr:nth-child(even) {
        background: #f9fafb;
    }
    
    .student-photo {
        width: 30px;
        height: 35px;
        object-fit: cover;
        border: 1px solid #d1d5db;
        border-radius: 2px;
    }
    
    .no-photo {
        width: 30px;
        height: 35px;
        background: #e5e7eb;
        border: 1px solid #d1d5db;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 6px;
        color: #9ca3af;
    }
    
    .roll-col {
        font-weight: bold;
        color: #059669;
        font-size: 9px;
    }
    
    .signature-col {
        text-align: center;
        color: #d1d5db;
        font-size: 8px;
    }
    
    .book-badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 2px;
        font-size: 8px;
        font-weight: bold;
    }
    
    .book-yellow {
        background: #fef3c7;
        color: #92400e;
    }
    
    .book-green {
        background: #d1fae5;
        color: #065f46;
    }
    
    .book-blue {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .book-pink {
        background: #fce7f3;
        color: #9f1239;
    }
    
    .footer {
        margin-top: 8px;
        padding-top: 6px;
        border-top: 1px solid #e5e7eb;
        font-size: 8px;
        color: #6b7280;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .invigilator-signature {
        font-size: 9px;
        color: #374151;
    }
</style>
</head>
<body>
    @php
        $isFirstPage = true;
    @endphp

    @foreach($venueData as $venueIndex => $data)
        @foreach($data['halls'] as $hallNum => $students)
            @php
                // Group students by ZONE + ROW (not just row)
                $zoneRowGroups = [];
                foreach($students as $student) {
                    $zoneNum = $student->zone_number;
                    $rowNum = $student->row_number;
                    $key = $zoneNum . '-' . $rowNum;
                    
                    if (!isset($zoneRowGroups[$key])) {
                        $zoneRowGroups[$key] = [
                            'zone' => $zoneNum,
                            'row' => $rowNum,
                            'students' => []
                        ];
                    }
                    $zoneRowGroups[$key]['students'][] = $student;
                }
                ksort($zoneRowGroups);
            @endphp

            @foreach($zoneRowGroups as $key => $group)
                @php
                    $chunks = array_chunk($group['students'], 10);
                @endphp

                @foreach($chunks as $chunkIndex => $chunk)
                <div class="page-section">
                    <!-- Header on first page only -->
                    @if($isFirstPage)
                        <div class="header">
                            <h1>ATTENDANCE SHEET</h1>
                            <p><strong>{{ $test->test_name }}</strong></p>
                            <p>{{ $test->college->name }} | {{ $test->test_date->format('l, F d, Y') }}</p>
                        </div>
                        @php $isFirstPage = false; @endphp
                    @endif

                    <!-- Venue Info -->
                    <div class="venue-header">
                        <h2>VENUE {{ $venueIndex + 1 }}: {{ $data['venue']->venue_name }}</h2>
                        <p><strong>Address:</strong> {{ $data['venue']->venue_address }} | <strong>District:</strong> {{ $data['venue']->testDistrict->district }}, {{ $data['venue']->testDistrict->province }}</p>
                    </div>

                    <div class="hall-title">HALL {{ $hallNum }}</div>
                    <div class="zone-row-title">ZONE {{ $group['zone'] }} - ROW {{ $group['row'] }} - Page {{ $chunkIndex + 1 }} of {{ count($chunks) }} (Students {{ ($chunkIndex * 10) + 1 }} to {{ min(($chunkIndex + 1) * 10, count($group['students'])) }} of {{ count($group['students']) }})</div>

                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th style="width: 6%;">Photo</th>
                                <th style="width: 10%;">Roll Number</th>
                                <th style="width: 24%;">Student Name</th>
                                <th style="width: 14%;">CNIC</th>
                                <th style="width: 5%;">Row</th>
                                <th style="width: 5%;">Seat</th>
                                <th style="width: 8%;">Book</th>
                                <th style="width: 25%;">Signature</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chunk as $index => $student)
                            <tr>
                                <td>{{ ($chunkIndex * 10) + $index + 1 }}</td>
                                <td style="text-align: center;">
                                    @if($student->picture)
                                        <img src="{{ public_path('storage/' . $student->picture) }}" alt="Photo" class="student-photo">
                                    @else
                                        <div class="no-photo">No Photo</div>
                                    @endif
                                </td>
                                <td class="roll-col">{{ $student->roll_number }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->cnic }}</td>
                                <td style="text-align: center; font-weight: bold; font-size: 10px;">{{ $student->row_number }}</td>
                                <td style="text-align: center; font-weight: bold; font-size: 10px;">{{ $student->seat_number }}</td>
                                <td style="text-align: center;">
                                    <span class="book-badge 
                                        {{ $student->book_color === 'Yellow' ? 'book-yellow' : '' }}
                                        {{ $student->book_color === 'Green' ? 'book-green' : '' }}
                                        {{ $student->book_color === 'Blue' ? 'book-blue' : '' }}
                                        {{ $student->book_color === 'Pink' ? 'book-pink' : '' }}">
                                        {{ $student->book_color }}
                                    </span>
                                </td>
                                <td class="signature-col">_____________________</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Footer with Invigilator Signature -->
                    <div class="footer">
                        <div>Generated: {{ now()->format('d M Y, h:i A') }} | V{{ $venueIndex + 1 }}-H{{ $hallNum }}-Z{{ $group['zone'] }}-R{{ $group['row'] }}</div>
                        <div class="invigilator-signature">
                            <strong>Invigilator:</strong> ____________________
                        </div>
                    </div>
                </div>
                @endforeach
            @endforeach
        @endforeach
    @endforeach
</body>
</html>