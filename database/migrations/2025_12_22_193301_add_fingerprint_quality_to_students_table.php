<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->integer('fingerprint_quality')->nullable()->after('fingerprint_image');
            $table->timestamp('fingerprint_registered_at')->nullable()->after('fingerprint_quality');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['fingerprint_quality', 'fingerprint_registered_at']);
        });
    }
};