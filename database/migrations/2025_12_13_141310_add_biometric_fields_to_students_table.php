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
            $table->text('fingerprint_template')->nullable()->after('picture');
            $table->string('fingerprint_image')->nullable()->after('fingerprint_template');
            $table->string('test_photo')->nullable()->after('fingerprint_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['fingerprint_template', 'fingerprint_image', 'test_photo']);
        });
    }
};