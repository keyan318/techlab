<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // When this user first logged in. Astro's tour plays by itself only in that first session,
            // so people who have logged in before aren't toured again (the Tour button still works).
            $table->timestamp('first_login_at')->nullable()->after('tour_seen');
        });

        // Everyone already here has logged in before: no surprise tours for them.
        DB::table('users')->update(['first_login_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_login_at');
        });
    }
};
