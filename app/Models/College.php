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
    'min_age',
    'max_age',
    'gender_policy',
    'registration_start_date',
    'password',
    'is_active',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    'is_active' => 'boolean',
    'registration_start_date' => 'date',
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

    public function testDistricts()
    {
    return $this->hasMany(TestDistrict::class);
    }
}