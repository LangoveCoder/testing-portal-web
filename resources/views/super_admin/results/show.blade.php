@extends('layouts.app')

@section('title', 'View Results')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.results.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Results
                    </a>
                    <h1 class="text-xl font-bold">View Results</h1>
                </div>
                <div class="flex items-center space-x-4">
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

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('errors') && is_array(session('errors')))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <p class="font-bold mb-2">Upload Errors:</p>
                <ul class="list-disc list-inside text-sm max-h-48 overflow-y-auto">
                    @foreach(session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <!-- Test Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $test->college->name }}</h2>
                    <p class="text-gray-600">{{ $test->test_date->format('d M Y') }} - {{ ucfirst(str_replace('_', ' ', $test->test_mode)) }}</p>
                </div>
                <div class="text-right">
                    @if($stats['results_uploaded'] > 0)
                        @php
                            $isPublished = \App\Models\Result::where('test_id', $test->id)->where('is_published', true)->exists();
                        @endphp
                        @if($isPublished)
                            <span class="px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                                ✓ Published
                            </span>
                        @else
                            <span class="px-3 py-1 bg-yellow-500 text-white rounded-full text-sm font-semibold">
                                ⏳ Not Published
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <label class="block text-sm font-bold text-blue-800">Total Students</label>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <label class="block text-sm font-bold text-green-800">Results Uploaded</label>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['results_uploaded'] }}</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <label class="block text-sm font-bold text-yellow-800">Results Pending</label>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['results_pending'] }}</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <label class="block text-sm font-bold text-purple-800">Published</label>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['published_count'] }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div class="space-x-3">
                    <a href="{{ route('super-admin.results.create', $test) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded inline-block">
                        Re-upload Results
                    </a>
                </div>
                
                <div class="space-x-3">
                    @if($stats['results_uploaded'] > 0)
                        @if($stats['published_count'] > 0)
                            <form method="POST" 
                                  action="{{ route('super-admin.results.unpublish', $test) }}" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to unpublish these results? Students will no longer be able to see them.');">
                                @csrf
                                <button type="submit" 
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded">
                                    Unpublish Results
                                </button>
                            </form>
                        @else
                            <form method="POST" 
                                  action="{{ route('super-admin.results.publish', $test) }}" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to publish these results? Students will be able to check their results.');">
                                @csrf
                                <button type="submit" 
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                    Publish Results
                                </button>
                            </form>
                        @endif

                        <form method="POST" 
                              action="{{ route('super-admin.results.destroy', $test) }}" 
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete ALL results for this test? This action cannot be undone!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                                Delete All Results
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Student Results</h3>
            </div>

            @if($students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roll Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                
                                @if($test->test_mode == 'mode_1')
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Eng Obj</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urd Obj</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Math Obj</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sci Obj</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Eng Subj</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urd Subj</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Math Subj</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sci Subj</th>
                                @elseif($test->test_mode == 'mode_2')
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">English</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urdu</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Math</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Science</th>
                                @else
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marks</th>
                                @endif
                                
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono font-bold text-sm">{{ $student->roll_number }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-medium text-gray-900">{{ $student->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->cnic }}</div>
                                </td>
                                
                                @if($student->result)
                                    @if($test->test_mode == 'mode_1')
                                        <td class="px-4 py-3 text-sm">{{ $student->result->english_obj ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->urdu_obj ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->math_obj ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->science_obj ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->english_subj ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->urdu_subj ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->math_subj ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->science_subj ?? '-' }}</td>
                                    @elseif($test->test_mode == 'mode_2')
                                        <td class="px-4 py-3 text-sm">{{ $student->result->english ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->urdu ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->math ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $student->result->science ?? '-' }}</td>
                                    @else
                                        <td class="px-4 py-3 text-sm">{{ $student->result->marks ?? '-' }}</td>
                                    @endif
                                    
                                    <td class="px-4 py-3">
                                        <span class="font-bold text-green-600">{{ $student->result->total_marks }}</span>
                                        <span class="text-xs text-gray-500">/ {{ $test->total_marks }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($student->result->is_published)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Uploaded
                                            </span>
                                        @endif
                                    </td>
                                @else
                                    @if($test->test_mode == 'mode_1')
                                        <td class="px-4 py-3 text-sm text-gray-400" colspan="8">-</td>
                                    @elseif($test->test_mode == 'mode_2')
                                        <td class="px-4 py-3 text-sm text-gray-400" colspan="4">-</td>
                                    @else
                                        <td class="px-4 py-3 text-sm text-gray-400">-</td>
                                    @endif
                                    <td class="px-4 py-3 text-sm text-gray-400">-</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Pending
                                        </span>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No students found for this test</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection