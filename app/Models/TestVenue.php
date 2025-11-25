<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestVenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'test_district_id',
        'venue_name',
        'venue_address',
        'google_maps_link',
        'number_of_halls',
        'zones_per_hall',
        'rows_per_zone',
        'seats_per_row',
        'total_capacity',
    ];

    protected $casts = [
        'number_of_halls' => 'integer',
        'zones_per_hall' => 'integer',
        'rows_per_zone' => 'integer',
        'seats_per_row' => 'integer',
        'total_capacity' => 'integer',
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
}