@extends('layouts.app')

@section('title', 'Merit Lists - ' . $test->college->name)

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">Merit Lists</h1>
                    <p class="text-blue-100 mt-1">{{ $test->college->name }}</p>
                    <p class="text-sm text-blue-200 mt-1">
                        Test Date: {{ $test->test_date->format('d M Y') }} | {{ $test->test_time }}
                    </p>
                </div>
                <a href="{{ route('super-admin.merit-lists.index') }}" class="bg-white text-blue-600 hover:bg-blue-50 px-4 py-2 rounded font-medium">
                    ← Back to Tests
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600">Total Merit Lists</div>
                <div class="text-3xl font-bold text-blue-600">{{ $statistics['total_lists'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600">Total Students</div>
                <div class="text-3xl font-bold text-green-600">{{ $statistics['total_students'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600">Balochistan Lists</div>
                <div class="text-3xl font-bold text-purple-600">{{ $statistics['balochistan_lists'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600">Other Provinces</div>
                <div class="text-3xl font-bold text-orange-600">{{ $statistics['other_province_lists'] }}</div>
            </div>
        </div>

        <!-- Download Options -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">📥 Download Options</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('super-admin.merit-lists.download-pdf', $test) }}" 
                   class="flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-lg transition">
                    <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Download Complete Merit List (PDF)
                </a>
                <a href="{{ route('super-admin.merit-lists.download-all', $test) }}" 
                   class="flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition">
                    <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download All Lists (Excel ZIP)
                </a>
            </div>
        </div>

        <!-- Merit Lists Display -->
        <div class="space-y-8">
            
            <!-- BALOCHISTAN DIVISIONS -->
            @php
                $balochistanDivisions = collect($meritLists)->filter(fn($list) => $list['type'] === 'balochistan_district')->groupBy('division');
            @endphp

            @if($balochistanDivisions->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4">
                    <h2 class="text-2xl font-bold">🏛️ Balochistan - Division & District Wise Merit Lists</h2>
                    <p class="text-blue-100 text-sm mt-1">{{ $statistics['balochistan_lists'] }} district merit lists across {{ count($balochistanDivisions) }} divisions</p>
                </div>

                @foreach($balochistanDivisions as $divisionName => $divisionLists)
                <div class="border-b border-gray-200 last:border-b-0">
                    <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                        <h3 class="text-lg font-bold text-blue-900">{{ $divisionName }}</h3>
                        <p class="text-sm text-blue-700">{{ $divisionLists->count() }} districts | {{ $divisionLists->sum('total_students') }} students</p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($divisionLists as $districtList)
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">📍 {{ $districtList['district'] }} District</h4>
                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                        <span>👥 {{ $districtList['total_students'] }} Students</span>
                                        <span>🏆 Highest: {{ $districtList['highest_marks'] }}</span>
                                        <span>📊 Average: {{ $districtList['average_marks'] }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('super-admin.merit-lists.download', [$test, 'province' => $districtList['province'], 'division' => $districtList['division'], 'district' => $districtList['district']]) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
                                    📥 Download Excel
                                </a>
                            </div>

                            <!-- Top 5 Students Preview -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-medium text-gray-700">Pos.</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-700">Roll No.</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-700">Name</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-700">Father Name</th>
                                            <th class="px-3 py-2 text-center font-medium text-gray-700">Gender</th>
                                            <th class="px-3 py-2 text-center font-medium text-gray-700">Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($districtList['results']->take(5) as $result)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 font-bold text-blue-600">{{ $result->position }}</td>
                                            <td class="px-3 py-2">{{ $result->roll_number }}</td>
                                            <td class="px-3 py-2">{{ $result->student->name }}</td>
                                            <td class="px-3 py-2">{{ $result->student->father_name }}</td>
                                            <td class="px-3 py-2 text-center">{{ $result->student->gender }}</td>
                                            <td class="px-3 py-2 text-center font-bold">{{ $result->marks }}/{{ $result->total_marks }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($districtList['results']->count() > 5)
                                <div class="text-center py-2 text-sm text-gray-500 bg-gray-50">
                                    ... and {{ $districtList['results']->count() - 5 }} more students
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- OTHER PROVINCES -->
            @php
                $otherProvinces = collect($meritLists)->filter(fn($list) => $list['type'] === 'other_province');
            @endphp

            @if($otherProvinces->count() > 0)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-purple-600 text-white px-6 py-4">
                    <h2 class="text-2xl font-bold">🌍 Other Provinces - Merit Lists</h2>
                    <p class="text-purple-100 text-sm mt-1">{{ $otherProvinces->count() }} province(s) | {{ $otherProvinces->sum('total_students') }} students</p>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($otherProvinces as $provinceList)
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">{{ $provinceList['province'] }} Province</h4>
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                    <span>👥 {{ $provinceList['total_students'] }} Students</span>
                                    <span>🏆 Highest: {{ $provinceList['highest_marks'] }}</span>
                                    <span>📊 Average: {{ $provinceList['average_marks'] }}</span>
                                </div>
                            </div>
                            <a href="{{ route('super-admin.merit-lists.download', [$test, 'province' => $provinceList['province'], 'division' => '', 'district' => '']) }}" 
                               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm font-medium">
                                📥 Download Excel
                            </a>
                        </div>

                        <!-- Top 10 Students Preview -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Pos.</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Roll No.</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Name</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">Father Name</th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-700">District</th>
                                        <th class="px-3 py-2 text-center font-medium text-gray-700">Gender</th>
                                        <th class="px-3 py-2 text-center font-medium text-gray-700">Marks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($provinceList['results']->take(10) as $result)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 font-bold text-purple-600">{{ $result->position }}</td>
                                        <td class="px-3 py-2">{{ $result->roll_number }}</td>
                                        <td class="px-3 py-2">{{ $result->student->name }}</td>
                                        <td class="px-3 py-2">{{ $result->student->father_name }}</td>
                                        <td class="px-3 py-2">{{ $result->student->district }}</td>
                                        <td class="px-3 py-2 text-center">{{ $result->student->gender }}</td>
                                        <td class="px-3 py-2 text-center font-bold">{{ $result->marks }}/{{ $result->total_marks }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($provinceList['results']->count() > 10)
                            <div class="text-center py-2 text-sm text-gray-500 bg-gray-50">
                                ... and {{ $provinceList['results']->count() - 10 }} more students
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection