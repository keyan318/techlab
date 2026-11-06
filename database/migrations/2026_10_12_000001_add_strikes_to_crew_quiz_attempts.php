<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * How many times a student has been caught cheating on a final exam (leaving full screen,
 * switching tab or window, leaving the page, trying a screenshot), counted across tries: a new
 * try carries the count over. Caught twice means banned from the Final Boss.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_quiz_attempts', function (Blueprint $table) {
            $table->unsignedTinyInteger('strikes')->default(0)->after('try_number');
        });

        // Students already caught before strikes existed have their first one.
        DB::table('crew_quiz_attempts')->whereNotNull('violation')->where('violation', '!=', 'time_up')->update(['strikes' => 1]);
    }

    public function down(): void
    {
        Schema::table('crew_quiz_attempts', fn (Blueprint $t) => $t->dropColumn('strikes'));
    }
};
