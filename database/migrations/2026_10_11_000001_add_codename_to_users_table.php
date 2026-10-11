<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The student's codename (picked in onboarding next to their animal astronaut): what
     * classmates see on leaderboards instead of their full name. Null until they pick one.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('codename', 20)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['codename']);
            $table->dropColumn('codename');
        });
    }
};
