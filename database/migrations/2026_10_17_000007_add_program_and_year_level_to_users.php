<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // A student's CICT program (User::PROGRAMS) and year level (User::YEAR_LEVELS), picked at sign-up
            // and changeable in Settings. The admin dashboard counts students by both. Null for older accounts and faculty.
            $table->string('program', 8)->nullable()->after('codename');
            $table->unsignedTinyInteger('year_level')->nullable()->after('program');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['program', 'year_level']);
        });
    }
};
