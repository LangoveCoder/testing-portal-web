<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerprintVerification extends Model
{
    protected $fillable = [
        'roll_number',
        'student_id',
        'test_id',
        'college_name',
        'is_matched',
        'match_score',
        'status',
        'captured_template',
        'captured_image',
        'capture_quality',
        'verified_by',
        'verified_at',
        'device_info',
        'notes',
    ];

    protected $casts = [
        'is_matched' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    // Accessors
    public function getCapturedTemplateBase64Attribute()
    {
        return $this->captured_template ? base64_encode($this->captured_template) : null;
    }

    public function getCapturedImageBase64Attribute()
    {
        return $this->captured_image ? base64_encode($this->captured_image) : null;
    }
}