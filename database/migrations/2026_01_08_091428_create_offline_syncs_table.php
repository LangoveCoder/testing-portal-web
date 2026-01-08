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
        Schema::create('offline_syncs', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 100)->index();
            $table->enum('sync_type', ['upload', 'download', 'update'])->default('upload');
            $table->enum('data_type', ['attendance', 'fingerprint', 'photo', 'student_data'])->index();
            $table->string('record_id', 50)->nullable(); // Roll number or other identifier
            $table->json('sync_data'); // The actual data to sync
            $table->enum('sync_status', ['pending', 'completed', 'failed'])->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('created_offline_at')->nullable(); // When record was created offline
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['device_id', 'sync_status']);
            $table->index(['data_type', 'sync_status']);
            $table->index('created_offline_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_syncs');
    }
};