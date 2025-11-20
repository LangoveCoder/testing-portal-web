<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_center_id')->constrained()->onDelete('cascade');
            $table->integer('number_of_halls');
            $table->integer('zones_per_hall');
            $table->integer('rows_per_zone');
            $table->integer('seats_per_row');
            $table->integer('total_capacity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_venues');
    }
};