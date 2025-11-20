<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->string('roll_number');
            $table->enum('book_color', ['Yellow', 'Green', 'Blue', 'Pink']);
            
            // Mode 1: MCQ + Subjective (40% + 60%)
            $table->decimal('english_obj', 5, 2)->nullable();
            $table->decimal('urdu_obj', 5, 2)->nullable();
            $table->decimal('math_obj', 5, 2)->nullable();
            $table->decimal('science_obj', 5, 2)->nullable();
            $table->decimal('english_subj', 5, 2)->nullable();
            $table->decimal('urdu_subj', 5, 2)->nullable();
            $table->decimal('math_subj', 5, 2)->nullable();
            $table->decimal('science_subj', 5, 2)->nullable();
            
            // Mode 2: Only MCQ (4 sections)
            $table->decimal('english', 5, 2)->nullable();
            $table->decimal('urdu', 5, 2)->nullable();
            $table->decimal('math', 5, 2)->nullable();
            $table->decimal('science', 5, 2)->nullable();
            
            // Mode 3: General MCQs
            $table->decimal('marks', 5, 2)->nullable();
            
            // Total
            $table->decimal('total_marks', 5, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};