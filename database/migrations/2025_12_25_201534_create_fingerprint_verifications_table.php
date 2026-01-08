<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('roll_number', 10);
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('test_id')->nullable();
            $table->string('college_name')->nullable();
            
            // Verification result
            $table->boolean('is_matched')->default(false);
            $table->integer('match_score')->nullable(); // 0-200
            $table->enum('result', ['matched', 'rejected', 'failed'])->default('failed');
            
            // Captured fingerprint data
            $table->binary('captured_template')->nullable();
            $table->binary('captured_image')->nullable();
            $table->integer('capture_quality')->nullable();
            
            // Verification metadata
            $table->string('verified_by')->nullable(); // College admin username
            $table->timestamp('verified_at')->nullable();
            $table->string('device_info')->nullable();
            $table->text('failure_reason')->nullable(); // Why verification failed
            
            $table->timestamps();
            
            // Indexes
            $table->index('roll_number');
            $table->index('verified_at');
            $table->index(['roll_number', 'verified_at']);
            
            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_verifications');
    }
};