@extends('layouts.app')

@section('title', 'Test Results')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('college.results.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Results
                    </a>
                    <h1 class="text-xl font-bold">Test Results - {{ $test->test_date->format('d M Y') }}</h1>
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
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Total Students</div>
                <div class="text-3xl font-bold text-gray-800">{{ $totalStudents }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Average Marks</div>
                <div class="text-3xl font-bold text-blue-600">{{ number_format($averageMarks, 2) }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Highest Marks</div>
                <div class="text-3xl font-bold text-green-600">{{ $highestMarks }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Lowest Marks</div>
                <div class="text-3xl font-bold text-red-600">{{ $lowestMarks }}</div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @if($results->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roll No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CNIC</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book Color</th>
                                
                                @if($test->test_mode == 'mode_1')
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Eng(O)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Urdu(O)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Math(O)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sci(O)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Eng(S)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Urdu(S)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Math(S)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sci(S)</th>
                                @elseif($test->test_mode == 'mode_2')
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">English</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Urdu</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Math</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Science</th>
                                @endif
                                
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($results as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-mono font-bold">{{ $result->roll_number }}</td>
                                <td class="px-4 py-3 text-sm">{{ $result->student->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm font-mono">{{ $result->student->cnic ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs rounded" 
                                          style="background-color: {{ $result->book_color == 'Yellow' ? '#FEF08A' : ($result->book_color == 'Green' ? '#BBF7D0' : ($result->book_color == 'Blue' ? '#BFDBFE' : '#FBCFE8')) }}">
                                        {{ $result->book_color }}
                                    </span>
                                </td>
                                
                                @if($test->test_mode == 'mode_1')
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->english_obj }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->urdu_obj }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->math_obj }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->science_obj }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->english_subj }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->urdu_subj }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->math_subj }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->science_subj }}</td>
                                @elseif($test->test_mode == 'mode_2')
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->english }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->urdu }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->math }}</td>
                                    <td class="px-4 py-3 text-sm text-center">{{ $result->science }}</td>
                                @endif
                                
                                <td class="px-4 py-3 text-sm text-center font-bold text-green-700">{{ $result->marks }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $results->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No results found for this test.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection