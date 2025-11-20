<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->constrained()->onDelete('cascade');
            $table->date('test_date');
            $table->time('test_time');
            $table->enum('test_mode', ['mode_1', 'mode_2', 'mode_3']);
            $table->integer('starting_roll_number');
            $table->integer('current_roll_number')->nullable();
            $table->boolean('roll_numbers_generated')->default(false);
            $table->boolean('results_published')->default(false);
            $table->dateTime('result_publication_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};