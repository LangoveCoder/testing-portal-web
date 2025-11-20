<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->integer('min_age')->nullable()->after('address');
            $table->integer('max_age')->nullable()->after('min_age');
            $table->enum('gender_policy', ['Male Only', 'Female Only', 'Both'])->default('Both')->after('max_age');
        });
    }

    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropColumn(['min_age', 'max_age', 'gender_policy']);
        });
    }
};