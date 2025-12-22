<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class BiometricOperator extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'assigned_colleges',
        'assigned_tests',
        'status',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'assigned_colleges' => 'array',
        'assigned_tests' => 'array',
    ];

    /**
     * Relationship: Created by Super Admin
     */
    public function creator()
    {
        return $this->belongsTo(SuperAdmin::class, 'created_by');
    }

    /**
     * Get assigned colleges
     */
    public function colleges()
    {
        if (!$this->assigned_colleges) {
            return College::whereIn('id', [])->get();
        }
        return College::whereIn('id', $this->assigned_colleges)->get();
    }

    /**
     * Get assigned tests
     */
    public function tests()
    {
        if (!$this->assigned_tests) {
            return Test::whereIn('id', [])->get();
        }
        return Test::whereIn('id', $this->assigned_tests)->get();
    }

    /**
     * Check if operator has access to a specific college
     */
    public function hasAccessToCollege($collegeId)
    {
        return in_array($collegeId, $this->assigned_colleges ?? []);
    }

    /**
     * Check if operator has access to a specific test
     */
    public function hasAccessToTest($testId)
    {
        return in_array($testId, $this->assigned_tests ?? []);
    }
}