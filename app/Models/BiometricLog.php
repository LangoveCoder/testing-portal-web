<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'roll_number',
        'log_type',
        'action',
        'operator_id',
        'operator_type',
        'confidence_score',
        'device_info',
        'ip_address',
        'notes',
    ];

    /**
     * Relationship: Log belongs to student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get operator (polymorphic - can be BiometricOperator or College)
     */
    public function operator()
    {
        if ($this->operator_type === 'biometric_operator') {
            return BiometricOperator::find($this->operator_id);
        } elseif ($this->operator_type === 'college_admin') {
            return College::find($this->operator_id);
        }
        return null;
    }

    /**
     * Scope: Registration logs only
     */
    public function scopeRegistrations($query)
    {
        return $query->where('log_type', 'registration');
    }

    /**
     * Scope: Verification logs only
     */
    public function scopeVerifications($query)
    {
        return $query->where('log_type', 'verification');
    }

    /**
     * Scope: Successful matches
     */
    public function scopeMatches($query)
    {
        return $query->where('action', 'match');
    }

    /**
     * Scope: Failed matches
     */
    public function scopeNoMatches($query)
    {
        return $query->where('action', 'no_match');
    }
}