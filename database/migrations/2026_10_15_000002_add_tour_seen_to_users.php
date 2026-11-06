<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // The chapters of Astro's tour this student has finished or skipped (config/astro-tour.php keys),
            // so each page's tour plays by itself only once, on any device.
            $table->json('tour_seen')->nullable()->after('codename');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tour_seen');
        });
    }
};
