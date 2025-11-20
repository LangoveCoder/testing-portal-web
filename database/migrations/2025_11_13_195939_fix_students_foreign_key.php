<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the old foreign key
            $table->dropForeign(['test_center_id']);
            
            // Rename column to test_district_id
            $table->renameColumn('test_center_id', 'test_district_id');
        });
        
        Schema::table('students', function (Blueprint $table) {
            // Add new foreign key to test_districts
            $table->foreign('test_district_id')->references('id')->on('test_districts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['test_district_id']);
            $table->renameColumn('test_district_id', 'test_center_id');
        });
        
        Schema::table('students', function (Blueprint $table) {
            $table->foreign('test_center_id')->references('id')->on('test_centers')->onDelete('cascade');
        });
    }
};