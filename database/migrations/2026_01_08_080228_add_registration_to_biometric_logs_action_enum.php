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
        // This migration was created to add 'registration' to biometric_logs action enum
        // but the action column was already changed to VARCHAR in previous migration
        // So this migration is now redundant and does nothing
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to rollback
    }
};
