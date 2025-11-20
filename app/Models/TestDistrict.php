<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestDistrict extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_id',
        'province',
        'division',
        'district',
    ];

    // Relationships
    public function college()
    {
        return $this->belongsTo(College::class);
    }
}