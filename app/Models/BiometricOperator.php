<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;  // ← CORRECT import (Laravel\Sanctum, not App\Models)

class BiometricOperator extends Authenticatable
{
    use HasApiTokens, Notifiable;  // ← Add HasApiTokens here

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'assigned_college_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function assignedCollege()
    {
        return $this->belongsTo(College::class, 'assigned_college_id');
    }

    public function tests()
    {
        return $this->belongsToMany(Test::class, 'biometric_operator_test');
    }
}