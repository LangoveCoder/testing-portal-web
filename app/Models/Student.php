<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
    'test_id',
    'test_district_id',
    'picture',
    'name',
    'cnic',
    'father_name',
    'father_cnic',
    'gender',
    'religion',
    'disability',
    'date_of_birth',
    'province',
    'division',
    'district',
    'address',
    'roll_number',
    'book_color',
    'hall_number',
    'zone_number',
    'row_number',
    'seat_number',
    'registration_id',
];
    protected $casts = [
        'date_of_birth' => 'date',
        'hall_number' => 'integer',
        'zone_number' => 'integer',
        'row_number' => 'integer',
        'seat_number' => 'integer',
    ];

   // Relationships
public function test()
{
    return $this->belongsTo(Test::class);
}

public function testDistrict()
{
    return $this->belongsTo(TestDistrict::class);
}

public function result()
{
    return $this->hasOne(Result::class);
}
}