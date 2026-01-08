<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $table = 'student_attendance';

    protected $fillable = [
        'roll_number',
        'student_id',
        'test_id',
        'attendance_status',
        'marked_at',
        'marked_by',
        'device_info',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
        'attendance_status' => 'string',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('attendance_status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('attendance_status', 'absent');
    }

    public function scopeForTest($query, $testId)
    {
        return $query->where('test_id', $testId);
    }

    public function scopeByRollNumber($query, $rollNumber)
    {
        return $query->where('roll_number', $rollNumber);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return $this->attendance_status === 'present' 
            ? '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Present</span>'
            : '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Absent</span>';
    }

    // Static methods for easy attendance management
    public static function markAttendance($rollNumber, $testId, $status, $markedBy = null, $deviceInfo = null, $ipAddress = null, $notes = null)
    {
        // Find student by roll number
        $student = Student::where('roll_number', $rollNumber)->first();
        
        return self::updateOrCreate(
            [
                'roll_number' => $rollNumber,
                'test_id' => $testId,
            ],
            [
                'student_id' => $student ? $student->id : null,
                'attendance_status' => $status,
                'marked_at' => now(),
                'marked_by' => $markedBy,
                'device_info' => $deviceInfo,
                'ip_address' => $ipAddress,
                'notes' => $notes,
            ]
        );
    }

    public static function getAttendanceStats($testId)
    {
        $total = self::where('test_id', $testId)->count();
        $present = self::where('test_id', $testId)->where('attendance_status', 'present')->count();
        $absent = $total - $present;
        
        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'present_percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }
}