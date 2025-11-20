<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_center_id')->constrained()->onDelete('cascade');
            $table->string('picture')->nullable();
            $table->string('name');
            $table->string('cnic')->unique();
            $table->string('father_name');
            $table->string('father_cnic');
            $table->date('date_of_birth');
            $table->string('province');
            $table->string('division')->nullable();
            $table->string('district');
            $table->text('address');
            $table->string('roll_number')->nullable();
            $table->enum('book_color', ['Yellow', 'Green', 'Blue', 'Pink'])->nullable();
            $table->integer('hall_number')->nullable();
            $table->integer('zone_number')->nullable();
            $table->integer('row_number')->nullable();
            $table->integer('seat_number')->nullable();
            $table->string('registration_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};