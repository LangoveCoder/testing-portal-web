<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_venues', function (Blueprint $table) {
            $table->string('venue_name')->after('test_center_id');
            $table->text('venue_address')->after('venue_name');
        });
    }

    public function down(): void
    {
        Schema::table('test_venues', function (Blueprint $table) {
            $table->dropColumn(['venue_name', 'venue_address']);
        });
    }
};