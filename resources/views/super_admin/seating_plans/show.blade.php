@extends('layouts.app')

@section('title', 'Seating Plan - ' . $test->test_name)

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.seating-plans.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Seating Plans
                    </a>
                    <h1 class="text-xl font-bold">Seating Plan</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.seating-plans.download', $test) }}" 
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
            <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600">
                <h2 class="text-2xl font-bold text-white">{{ $test->test_name }}</h2>
                <p class="text-blue-100 mt-1">{{ $test->college->name }} | {{ $test->test_date->format('l, F d, Y') }}</p>
            </div>
        </div>

        @foreach($venueData as $data)
        <!-- Venue Section -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-4 bg-gray-100 border-b">
                <h3 class="text-lg font-bold text-gray-900">📍 {{ $data['venue']->venue_name }}</h3>
                <p class="text-sm text-gray-600">{{ $data['venue']->venue_address }}</p>
                <p class="text-sm text-gray-600 mt-1">
                    <strong>District:</strong> {{ $data['venue']->testDistrict->district }}, {{ $data['venue']->testDistrict->province }}
                </p>
            </div>

            @foreach($data['halls'] as $hallNum => $zones)
            <!-- Hall Section -->
            <div class="p-6 border-b">
                <h4 class="text-md font-bold text-blue-600 mb-4">🏛️ HALL {{ $hallNum }}</h4>

                @foreach($zones as $zoneNum => $rows)
                <!-- Zone Section -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h5 class="text-sm font-bold text-blue-700 mb-3">ZONE {{ $zoneNum }}</h5>

                    @foreach($rows as $rowNum => $seats)
                    <!-- Row Section -->
                    <div class="mb-4">
                        <div class="text-xs font-semibold text-gray-600 mb-2">Row {{ $rowNum }}</div>
                        <div class="grid grid-cols-10 gap-2">
                            @for($seatNum = 1; $seatNum <= $data['venue']->seats_per_row; $seatNum++)
                                @if(isset($seats[$seatNum]))
                                    @php $student = $seats[$seatNum]; @endphp
                                    <div class="border-2 rounded p-2 text-center text-xs
                                        {{ $student->book_color === 'Yellow' ? 'bg-yellow-100 border-yellow-400' : '' }}
                                        {{ $student->book_color === 'Green' ? 'bg-green-100 border-green-400' : '' }}
                                        {{ $student->book_color === 'Blue' ? 'bg-blue-100 border-blue-400' : '' }}
                                        {{ $student->book_color === 'Pink' ? 'bg-pink-100 border-pink-400' : '' }}">
                                        <div class="font-bold text-gray-900">{{ $student->roll_number }}</div>
                                        <div class="text-gray-600 truncate" title="{{ $student->name }}">{{ Str::limit($student->name, 12) }}</div>
                                        <div class="text-gray-500 font-semibold">S{{ $seatNum }}</div>
                                    </div>
                                @else
                                    <div class="border border-gray-200 rounded p-2 text-center text-xs bg-gray-50">
                                        <div class="text-gray-400">Empty</div>
                                        <div class="text-gray-300">S{{ $seatNum }}</div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @endforeach

        <!-- Legend -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            <h4 class="text-md font-bold mb-4">📖 Book Color Legend</h4>
            <div class="grid grid-cols-4 gap-4">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-yellow-100 border-2 border-yellow-400 rounded"></div>
                    <span class="text-sm font-medium">Yellow</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-green-100 border-2 border-green-400 rounded"></div>
                    <span class="text-sm font-medium">Green</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-blue-100 border-2 border-blue-400 rounded"></div>
                    <span class="text-sm font-medium">Blue</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-pink-100 border-2 border-pink-400 rounded"></div>
                    <span class="text-sm font-medium">Pink</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection