@extends('layouts.app')

@section('title', 'Audit Log Details')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Top Navigation Bar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('super-admin.audit-logs.index') }}" class="text-white hover:text-gray-200">
                        ← Back to Audit Logs
                    </a>
                    <h1 class="text-xl font-bold">Audit Log Details</h1>
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
        
        <!-- Log Header -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $auditLog->description }}</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $auditLog->created_at->format('l, d F Y \a\t h:i A') }}
                    </p>
                </div>
                <div>
                    @php
                        $actionColors = [
                            'created' => 'bg-green-100 text-green-800',
                            'updated' => 'bg-yellow-100 text-yellow-800',
                            'deleted' => 'bg-red-100 text-red-800',
                            'uploaded' => 'bg-blue-100 text-blue-800',
                            'published' => 'bg-purple-100 text-purple-800',
                            'unpublished' => 'bg-gray-100 text-gray-800',
                            'generated' => 'bg-indigo-100 text-indigo-800',
                        ];
                        $color = $actionColors[$auditLog->action] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $color }}">
                        {{ ucfirst($auditLog->action) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-600">User Type</label>
                    <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $auditLog->user_type)) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">User ID</label>
                    <p class="text-gray-900">{{ $auditLog->user_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">Action</label>
                    <p class="text-gray-900">{{ ucfirst($auditLog->action) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">Model Type</label>
                    <p class="text-gray-900">{{ $auditLog->model }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">Model ID</label>
                    <p class="text-gray-900">{{ $auditLog->model_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">Timestamp</label>
                    <p class="text-gray-900">{{ $auditLog->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Old Values -->
        @if($auditLog->old_values)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Old Values (Before Change)</h3>
            <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                <pre class="text-sm">{{ json_encode(json_decode($auditLog->old_values), JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif

        <!-- New Values -->
        @if($auditLog->new_values)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">New Values (After Change)</h3>
            <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                <pre class="text-sm">{{ json_encode(json_decode($auditLog->new_values), JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif

        <!-- Changes Comparison -->
        @if($auditLog->old_values && $auditLog->new_values)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Changes Summary</h3>
            @php
                $oldData = json_decode($auditLog->old_values, true);
                $newData = json_decode($auditLog->new_values, true);
                $changes = [];
                
                if ($oldData && $newData) {
                    foreach ($newData as $key => $newValue) {
                        if (isset($oldData[$key]) && $oldData[$key] != $newValue) {
                            $changes[$key] = [
                                'old' => $oldData[$key],
                                'new' => $newValue
                            ];
                        }
                    }
                }
            @endphp
            
            @if(count($changes) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Old Value</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($changes as $field => $change)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded">
                                        {{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                        {{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No field changes detected</p>
            @endif
        </div>
        @endif

        <!-- IP Address and User Agent -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Technical Details</h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-600">IP Address</label>
                    <p class="text-gray-900 font-mono">{{ $auditLog->ip_address ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-600">User Agent</label>
                    <p class="text-gray-900 text-sm">{{ $auditLog->user_agent ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <a href="{{ route('super-admin.audit-logs.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                ← Back to Logs
            </a>
        </div>
    </div>
</div>
@endsection