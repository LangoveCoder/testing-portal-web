@extends('layouts.app')

@section('title', 'Attendance Sheet - ' . $test->test_name)

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.attendance-sheets.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Attendance Sheets
                    </a>
                    <h1 class="text-xl font-bold">Attendance Sheet</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.attendance-sheets.download', $test) }}" 
                       class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded">
                        📥 Download PDF
                    </a>
                    <span>{{ Auth::guard('super_admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('super-admin.logout') }}">
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
        
        <!-- Test Info -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6 bg-gradient-to-r from-green-500 to-green-600">
                <h2 class="text-2xl font-bold text-white">{{ $test->test_name }}</h2>
                <p class="text-green-100 mt-1">{{ $test->college->name }} | {{ $test->test_date->format('l, F d, Y') }}</p>
            </div>
        </div>

        @foreach($venueData as $venueIndex => $data)
        <!-- Venue Section -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-4 bg-green-50 border-b-4 border-green-500">
                <h3 class="text-xl font-bold text-green-700">📍 VENUE {{ $venueIndex + 1 }}: {{ $data['venue']->venue_name }}</h3>
                <p class="text-sm text-gray-700 mt-1">
                    <strong>Address:</strong> {{ $data['venue']->venue_address }} | 
                    <strong>District:</strong> {{ $data['venue']->testDistrict->district }}, {{ $data['venue']->testDistrict->province }}
                </p>
            </div>

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

                <!-- Hall Header -->
                <div class="p-4 bg-green-100 border-b-2 border-green-400">
                    <h4 class="text-lg font-bold text-green-800">🏛️ HALL {{ $hallNum }} ({{ count($students) }} Students)</h4>
                </div>

                @foreach($zoneRowGroups as $key => $group)
                    @php
                        $chunks = array_chunk($group['students'], 10);
                    @endphp

                    @foreach($chunks as $chunkIndex => $chunk)
                    <!-- Zone-Row Section -->
                    <div class="p-6 border-b">
                        <div class="bg-teal-50 border-l-4 border-teal-500 px-4 py-3 mb-4">
                            <h5 class="text-sm font-bold text-teal-800">
                                ZONE {{ $group['zone'] }} - ROW {{ $group['row'] }} - Page {{ $chunkIndex + 1 }} of {{ count($chunks) }} 
                                (Students {{ ($chunkIndex * 10) + 1 }} to {{ min(($chunkIndex + 1) * 10, count($group['students'])) }} of {{ count($group['students']) }})
                            </h5>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r">#</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r">Photo</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r">Roll Number</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r">Student Name</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r">CNIC</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase border-r">Row</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase border-r">Seat</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase border-r">Book Color</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Signature</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($chunk as $index => $student)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ ($chunkIndex * 10) + $index + 1 }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap border-r">
                                            @if($student->picture)
                                                <img src="{{ asset('storage/' . $student->picture) }}" alt="Photo" class="w-10 h-12 object-cover border border-gray-300 rounded">
                                            @else
                                                <div class="w-10 h-12 bg-gray-200 border border-gray-300 rounded flex items-center justify-center text-xs text-gray-400">No Photo</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm font-bold text-green-700 border-r">{{ $student->roll_number }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ $student->name }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-600 border-r">{{ $student->cnic }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-bold text-gray-900 border-r">{{ $student->row_number }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-bold text-gray-900 border-r">{{ $student->seat_number }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-center border-r">
                                            <span class="px-2 py-1 text-xs font-semibold rounded
                                                {{ $student->book_color === 'Yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $student->book_color === 'Green' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $student->book_color === 'Blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $student->book_color === 'Pink' ? 'bg-pink-100 text-pink-800' : '' }}">
                                                {{ $student->book_color }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-400">_____________</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Invigilator Signature for this page -->
                        <div class="mt-4 p-3 bg-gray-50 border border-gray-300 rounded flex justify-between items-center">
                            <div class="text-xs text-gray-600">V{{ $venueIndex + 1 }}-H{{ $hallNum }}-Z{{ $group['zone'] }}-R{{ $group['row'] }} - Page {{ $chunkIndex + 1 }}</div>
                            <div class="text-sm font-semibold text-gray-700">
                                <strong>Invigilator Signature:</strong> _______________________________
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            @endforeach
        </div>
        @endforeach
    </div>
</div>
@endsection