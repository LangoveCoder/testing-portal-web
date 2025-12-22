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
        Schema::create('biometric_operators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('assigned_colleges')->nullable(); // JSON array
            $table->text('assigned_tests')->nullable(); // JSON array
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('super_admins')->onDelete('set null');
            $table->timestamps();
        });

        // Biometric logs table
        Schema::create('biometric_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('roll_number', 10);
            $table->enum('log_type', ['registration', 'verification']);
            $table->enum('action', ['capture', 'verify', 'match', 'no_match']);
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->string('operator_type', 50)->nullable(); // 'biometric_operator' or 'college_admin'
            $table->integer('confidence_score')->nullable(); // 0-100
            $table->text('device_info')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'log_type']);
            $table->index('roll_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_logs');
        Schema::dropIfExists('biometric_operators');
    }
};