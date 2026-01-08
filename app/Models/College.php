<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;  // ← CORRECT import

class College extends Authenticatable
{
    use HasApiTokens, Notifiable;  // ← Add HasApiTokens here

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'district',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Test::class);
    }
}