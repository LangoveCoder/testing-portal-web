<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class College extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The guard to use for authentication
     */
    protected $guard = 'college';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'registration_start_date' => 'date',
            'min_age' => 'integer',
            'max_age' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the tests for the college.
     */
    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    /**
     * Get the students for the college.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the operators for the college.
     */
    public function operators()
    {
        return $this->hasMany(Operator::class);
    }

    /**
     * Get the test districts for the college.
     */
    public function testDistricts()
    {
        return $this->hasMany(CollegeTestDistrict::class);
    }

    /**
     * Scope a query to only include active colleges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive colleges.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get the full address of the college.
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->district,
            $this->division,
            $this->province,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if college can accept students based on age.
     */
    public function canAcceptAge($age)
    {
        if ($this->min_age && $age < $this->min_age) {
            return false;
        }

        if ($this->max_age && $age > $this->max_age) {
            return false;
        }

        return true;
    }

    /**
     * Check if college can accept student based on gender.
     */
    public function canAcceptGender($gender)
    {
        if ($this->gender_policy === 'Both') {
            return true;
        }

        return strtolower($this->gender_policy) === strtolower($gender . ' Only');
    }

    /**
     * Check if registration is currently open.
     */
    public function isRegistrationOpen()
    {
        if (!$this->registration_start_date) {
            return true; // No restriction
        }

        return now()->gte($this->registration_start_date);
    }
}