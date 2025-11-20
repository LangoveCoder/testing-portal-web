<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // Show all audit logs
    // Show all audit logs
public function index(Request $request)
{
    $query = AuditLog::query();
    
    // Filter by user type
    if ($request->filled('user_type')) {
        $query->where('user_type', $request->user_type);
    }
    
    // Filter by action
    if ($request->filled('action')) {
        $query->where('action', $request->action);
    }
    
    // Filter by model
    if ($request->filled('model')) {
        $query->where('model', $request->model);
    }
    
    // Search by description
    if ($request->filled('search')) {
        $query->where('description', 'like', '%' . $request->search . '%');
    }
    
    // Date range filter
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }
    
    // Get logs with pagination
    $logs = $query->orderBy('created_at', 'desc')->paginate(50);
    
    // Get unique values for filters
    $userTypes = AuditLog::distinct()->pluck('user_type');
    $actions = AuditLog::distinct()->pluck('action');
    $modelTypes = AuditLog::distinct()->pluck('model');
    
    return view('super_admin.audit_logs.index', compact('logs', 'userTypes', 'actions', 'modelTypes'));
}

    // Show single audit log details
    public function show(AuditLog $auditLog)
    {
        return view('super_admin.audit_logs.show', compact('auditLog'));
    }

    // Clear old logs (optional - for maintenance)
    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:30', // Minimum 30 days to prevent accidental deletion
        ]);

        $date = now()->subDays($request->days);
        $count = AuditLog::where('created_at', '<', $date)->delete();

        return redirect()->route('super-admin.audit-logs.index')
            ->with('success', "Deleted {$count} audit logs older than {$request->days} days.");
    }
}