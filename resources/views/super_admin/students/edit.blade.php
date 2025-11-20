@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.students.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Students
                    </a>
                    <h1 class="text-xl font-bold">Edit Student</h1>
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

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        
        @if($student->roll_number)
            <!-- Roll Number Generated - Limited Edit -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                <p class="font-bold">Roll Number Already Generated</p>
                <p class="text-sm">You can only change the test district for this student. All other information is locked.</p>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                {{ $student->roll_number ? 'Change Test District' : 'Edit Student Information' }}
            </h2>

            <form method="POST" 
                  action="{{ route('super-admin.students.update', $student) }}" 
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if($student->roll_number)
                    <!-- Only Test District Change -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Test District <span class="text-red-500">*</span>
                        </label>
                        <select name="test_district_id" 
                                required
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('test_district_id') border-red-500 @enderror">
                            <option value="">Select Test District</option>
                            @foreach($testDistricts as $district)
                                <option value="{{ $district->id }}" 
                                        {{ old('test_district_id', $student->test_district_id) == $district->id ? 'selected' : '' }}>
                                    {{ $district->district }}, {{ $district->province }}
                                </option>
                            @endforeach
                        </select>
                        @error('test_district_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Show Read-Only Information -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="font-bold text-gray-700 mb-3">Current Student Information (Read-Only)</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Name:</span>
                                <span class="font-semibold ml-2">{{ $student->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">CNIC:</span>
                                <span class="font-semibold ml-2">{{ $student->cnic }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Roll Number:</span>
                                <span class="font-semibold ml-2 text-green-600">{{ $student->roll_number }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Gender:</span>
                                <span class="font-semibold ml-2">{{ $student->gender }}</span>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- Full Edit Form -->
                    
                    <!-- Test District -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Test District <span class="text-red-500">*</span>
                        </label>
                        <select name="test_district_id" 
                                required
                                class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('test_district_id') border-red-500 @enderror">
                            <option value="">Select Test District</option>
                            @foreach($testDistricts as $district)
                                <option value="{{ $district->id }}" 
                                        {{ old('test_district_id', $student->test_district_id) == $district->id ? 'selected' : '' }}>
                                    {{ $district->district }}, {{ $district->province }}
                                </option>
                            @endforeach
                        </select>
                        @error('test_district_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Student Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Student Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $student->name) }}"
                               required
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Student CNIC -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Student CNIC (13 digits) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="cnic" 
                               value="{{ old('cnic', $student->cnic) }}"
                               maxlength="13"
                               pattern="\d{13}"
                               required
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cnic') border-red-500 @enderror">
                        @error('cnic')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Father Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Father Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="father_name" 
                               value="{{ old('father_name', $student->father_name) }}"
                               required
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('father_name') border-red-500 @enderror">
                        @error('father_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Father CNIC -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Father CNIC (13 digits) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="father_cnic" 
                               value="{{ old('father_cnic', $student->father_cnic) }}"
                               maxlength="13"
                               pattern="\d{13}"
                               required
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('father_cnic') border-red-500 @enderror">
                        @error('father_cnic')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender and Religion -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Gender <span class="text-red-500">*</span>
                            </label>
                            <select name="gender" 
                                    required
                                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Religion <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="religion" 
                                   value="{{ old('religion', $student->religion) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('religion') border-red-500 @enderror">
                            @error('religion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="date_of_birth" 
                               value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}"
                               required
                               max="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Province, Division, District -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Province <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="province" 
                                   value="{{ old('province', $student->province) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('province') border-red-500 @enderror">
                            @error('province')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Division
                            </label>
                            <input type="text" 
                                   name="division" 
                                   value="{{ old('division', $student->division) }}"
                                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('division') border-red-500 @enderror">
                            @error('division')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                District <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="district" 
                                   value="{{ old('district', $student->district) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('district') border-red-500 @enderror">
                            @error('district')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Complete Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" 
                                  rows="3" 
                                  required
                                  class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror">{{ old('address', $student->address) }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Picture Upload -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Student Picture
                        </label>
                        
                        @if($student->picture)
                            <div class="mb-3">
                                <p class="text-sm text-gray-600 mb-2">Current Picture:</p>
                                <img src="{{ asset('storage/' . $student->picture) }}" 
                                     alt="{{ $student->name }}"
                                     class="h-32 w-32 object-cover rounded-lg border-2 border-gray-300">
                            </div>
                        @endif
                        
                        <input type="file" 
                               name="picture" 
                               accept="image/jpeg,image/jpg,image/png"
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('picture') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current picture. Max 2MB (JPG, PNG)</p>
                        @error('picture')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                @endif

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-4 border-t">
                    <a href="{{ route('super-admin.students.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection