<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
    'college_id',
    'test_date',
    'test_time',
    'registration_deadline',
    'test_mode',
    'total_marks',
    'starting_roll_number',
    'current_roll_number',
    'roll_numbers_generated',
    'results_published',
    'result_publication_date',
];

    protected $casts = [
    'test_date' => 'date',
    'test_time' => 'datetime',
    'registration_deadline' => 'date',
    'result_publication_date' => 'datetime',
    'roll_numbers_generated' => 'boolean',
    'results_published' => 'boolean',
];

    // Relationships
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function testVenues()
    {
        return $this->hasMany(TestVenue::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
}