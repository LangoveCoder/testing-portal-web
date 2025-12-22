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
        Schema::table('students', function (Blueprint $table) {
            // Add test_photo field if it doesn't exist
            if (!Schema::hasColumn('students', 'test_photo')) {
                $table->text('test_photo')->nullable()->after('picture');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'test_photo')) {
                $table->dropColumn('test_photo');
            }
        });
    }
};