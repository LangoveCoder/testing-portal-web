<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class College extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'province',
        'division',
        'district',
        'address',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function testCenters()
    {
        return $this->hasMany(TestCenter::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }
}