<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineSync extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'sync_type',
        'data_type',
        'record_id',
        'sync_data',
        'sync_status',
        'error_message',
        'synced_at',
        'created_offline_at',
    ];

    protected $casts = [
        'sync_data' => 'array',
        'synced_at' => 'datetime',
        'created_offline_at' => 'datetime',
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('sync_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('sync_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('sync_status', 'failed');
    }

    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('data_type', $type);
    }

    // Static methods for sync management
    public static function createSyncRecord($deviceId, $syncType, $dataType, $recordId, $data, $createdOfflineAt = null)
    {
        return self::create([
            'device_id' => $deviceId,
            'sync_type' => $syncType, // 'upload', 'download', 'update'
            'data_type' => $dataType, // 'attendance', 'fingerprint', 'photo'
            'record_id' => $recordId,
            'sync_data' => $data,
            'sync_status' => 'pending',
            'created_offline_at' => $createdOfflineAt ?? now(),
        ]);
    }

    public function markCompleted()
    {
        $this->update([
            'sync_status' => 'completed',
            'synced_at' => now(),
            'error_message' => null,
        ]);
    }

    public function markFailed($errorMessage)
    {
        $this->update([
            'sync_status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public static function getPendingSyncCount($deviceId = null)
    {
        $query = self::where('sync_status', 'pending');
        
        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }
        
        return $query->count();
    }

    public static function getSyncStats($deviceId = null)
    {
        $query = self::query();
        
        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }
        
        return [
            'total' => $query->count(),
            'pending' => $query->clone()->where('sync_status', 'pending')->count(),
            'completed' => $query->clone()->where('sync_status', 'completed')->count(),
            'failed' => $query->clone()->where('sync_status', 'failed')->count(),
            'last_sync' => $query->clone()->where('sync_status', 'completed')
                ->orderBy('synced_at', 'desc')->first()?->synced_at,
        ];
    }
}