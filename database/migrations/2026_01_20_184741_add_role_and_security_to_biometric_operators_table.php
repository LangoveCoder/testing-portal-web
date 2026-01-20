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
        Schema::table('biometric_operators', function (Blueprint $table) {
            // Add role column for operator vs college_admin
            $table->enum('role', ['operator', 'college_admin'])
                ->default('operator')
                ->after('status')
                ->comment('operator: registers fingerprints, college_admin: verifies fingerprints');
            
            // Add last login tracking for security
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->string('last_device_info', 200)->nullable()->after('last_login_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biometric_operators', function (Blueprint $table) {
            $table->dropColumn(['role', 'last_login_at', 'last_login_ip', 'last_device_info']);
        });
    }
};
