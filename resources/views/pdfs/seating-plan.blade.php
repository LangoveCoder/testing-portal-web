<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seating Plan - {{ $test->test_name }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2563eb;
        }
        
        .header h1 {
            font-size: 20px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            color: #4b5563;
        }
        
        .venue-section {
            page-break-after: always;
            margin-bottom: 20px;
        }
        
        .venue-header {
            background: #dbeafe;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
        }
        
        .venue-header h2 {
            font-size: 14px;
            color: #1e40af;
            margin-bottom: 3px;
        }
        
        .venue-header p {
            font-size: 9px;
            color: #374151;
        }
        
        .hall-section {
            margin-bottom: 20px;
        }
        
        .hall-title {
            background: #2563eb;
            color: white;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .zone-section {
            background: #eff6ff;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        
        .zone-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 8px;
        }
        
        .row-section {
            margin-bottom: 8px;
        }
        
        .row-label {
            font-size: 8px;
            font-weight: bold;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .seats-grid {
            display: table;
            width: 100%;
            border-spacing: 3px;
        }
        
        .seat {
            display: table-cell;
            width: 9%;
            border: 1.5px solid;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            border-radius: 3px;
        }
        
        .seat-filled {
            background: white;
        }
        
        .seat-empty {
            background: #f3f4f6;
            border-color: #d1d5db;
        }
        
        .seat-yellow {
            background: #fef3c7;
            border-color: #fbbf24;
        }
        
        .seat-green {
            background: #d1fae5;
            border-color: #10b981;
        }
        
        .seat-blue {
            background: #dbeafe;
            border-color: #3b82f6;
        }
        
        .seat-pink {
            background: #fce7f3;
            border-color: #ec4899;
        }
        
        .roll {
            font-size: 9px;
            font-weight: bold;
            color: #111827;
        }
        
        .name {
            font-size: 7px;
            color: #4b5563;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .seat-num {
            font-size: 7px;
            color: #9ca3af;
            font-weight: bold;
            margin-top: 2px;
        }
        
        .legend {
            margin-top: 15px;
            padding: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }
        
        .legend-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        
        .legend-grid {
            display: table;
            width: 100%;
        }
        
        .legend-item {
            display: table-cell;
            width: 25%;
            padding: 4px;
        }
        
        .legend-box {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1.5px solid;
            margin-right: 5px;
            vertical-align: middle;
            border-radius: 2px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <!-- Header on first page only -->
    <div class="header">
        <h1>SEATING PLAN</h1>
        <p><strong>{{ $test->test_name }}</strong></p>
        <p>{{ $test->college->name }} | {{ $test->test_date->format('l, F d, Y') }}</p>
    </div>

    @foreach($venueData as $data)
    <div class="venue-section">
        <!-- Venue Info -->
        <div class="venue-header">
            <h2>VENUE: {{ $data['venue']->venue_name }}</h2>
            <p>{{ $data['venue']->venue_address }}</p>
            <p><strong>District:</strong> {{ $data['venue']->testDistrict->district }}, {{ $data['venue']->testDistrict->province }}</p>
        </div>

        @foreach($data['halls'] as $hallNum => $zones)
        <div class="hall-section">
            <div class="hall-title">HALL {{ $hallNum }}</div>

            @foreach($zones as $zoneNum => $rows)
            <div class="zone-section">
                <div class="zone-title">ZONE {{ $zoneNum }}</div>

                @foreach($rows as $rowNum => $seats)
                <div class="row-section">
                    <div class="row-label">Row {{ $rowNum }}</div>
                    
                    @php
                        // Break seats into groups of 10
                        $totalSeats = $data['venue']->seats_per_row;
                        $seatsPerLine = 10;
                        $numLines = ceil($totalSeats / $seatsPerLine);
                    @endphp
                    
                    @for($line = 0; $line < $numLines; $line++)
                        @php
                            $startSeat = ($line * $seatsPerLine) + 1;
                            $endSeat = min(($line + 1) * $seatsPerLine, $totalSeats);
                        @endphp
                        
                        @if($line > 0)
                            <div style="height: 3px;"></div>
                        @endif
                        
                        <div class="seats-grid">
                            @for($seatNum = $startSeat; $seatNum <= $endSeat; $seatNum++)
                                @if(isset($seats[$seatNum]))
                                    @php $student = $seats[$seatNum]; @endphp
                                    <div class="seat seat-filled 
                                        {{ $student->book_color === 'Yellow' ? 'seat-yellow' : '' }}
                                        {{ $student->book_color === 'Green' ? 'seat-green' : '' }}
                                        {{ $student->book_color === 'Blue' ? 'seat-blue' : '' }}
                                        {{ $student->book_color === 'Pink' ? 'seat-pink' : '' }}">
                                        <div class="roll">{{ $student->roll_number }}</div>
                                        <div class="name">{{ Str::limit($student->name, 15) }}</div>
                                        <div class="seat-num">S{{ $seatNum }}</div>
                                    </div>
                                @else
                                    <div class="seat seat-empty">
                                        <div class="seat-num" style="color: #d1d5db;">S{{ $seatNum }}</div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    @endfor
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @endforeach

        <!-- Legend -->
        <div class="legend">
            <div class="legend-title">Book Color Legend</div>
            <div class="legend-grid">
                <div class="legend-item">
                    <span class="legend-box seat-yellow"></span>
                    <span>Yellow</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box seat-green"></span>
                    <span>Green</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box seat-blue"></span>
                    <span>Blue</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box seat-pink"></span>
                    <span>Pink</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Generated on {{ now()->format('d F Y, h:i A') }} | BACT Admission Portal
        </div>
    </div>
    @endforeach
</body>
</html>