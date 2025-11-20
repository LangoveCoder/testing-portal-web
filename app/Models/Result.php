<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'test_id',
        'roll_number',
        'book_color',
        'english_obj',
        'urdu_obj',
        'math_obj',
        'science_obj',
        'english_subj',
        'urdu_subj',
        'math_subj',
        'science_subj',
        'english',
        'urdu',
        'math',
        'science',
        'marks',
        'total_marks',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
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
}