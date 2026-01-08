<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'user_id',
        'title',
        'message',
        'icon',
        'color',
        'action_url',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Scopes
    public function scopeForSuperAdmin($query)
    {
        return $query->where('type', 'super_admin');
    }

    public function scopeForCollege($query, $collegeId)
    {
        return $query->where('type', 'college')->where('user_id', $collegeId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Static methods for creating notifications
    public static function createForSuperAdmin($title, $message, $icon = 'notifications', $color = 'blue', $actionUrl = null)
    {
        return self::create([
            'type' => 'super_admin',
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            'action_url' => $actionUrl
        ]);
    }

    public static function createForCollege($collegeId, $title, $message, $icon = 'notifications', $color = 'blue', $actionUrl = null)
    {
        return self::create([
            'type' => 'college',
            'user_id' => $collegeId,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            'action_url' => $actionUrl
        ]);
    }

    public static function createForAllColleges($title, $message, $icon = 'notifications', $color = 'blue', $actionUrl = null)
    {
        $colleges = \App\Models\College::all();
        
        foreach ($colleges as $college) {
            self::createForCollege($college->id, $title, $message, $icon, $color, $actionUrl);
        }
    }
}
