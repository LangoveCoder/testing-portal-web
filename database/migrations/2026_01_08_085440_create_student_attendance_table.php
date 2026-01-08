<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('roll_number', 10)->index();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->enum('attendance_status', ['present', 'absent'])->default('absent');
            $table->timestamp('marked_at')->nullable();
            $table->string('marked_by')->nullable(); // Mobile app user/operator
            $table->string('device_info')->nullable(); // Mobile device info
            $table->string('ip_address', 45)->nullable();
            $table->text('notes')->nullable(); // Any additional notes
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['roll_number', 'test_id']);
            $table->index(['test_id', 'attendance_status']);
            $table->index('marked_at');
            
            // Unique constraint to prevent duplicate attendance records
            $table->unique(['roll_number', 'test_id'], 'unique_student_test_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
    }
};