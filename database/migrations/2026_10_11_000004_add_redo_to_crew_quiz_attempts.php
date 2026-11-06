<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Failing the final exam sends the student back through the whole course: their lesson
 * progress is wiped and kept here, so the faculty member's "Allow retake" can give it back.
 * `try_number` counts how many times they have faced the Final Boss.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_quiz_attempts', function (Blueprint $table) {
            $table->json('wiped_progress')->nullable()->after('violation');
            $table->unsignedSmallInteger('try_number')->default(1)->after('wiped_progress');
        });
    }

    public function down(): void
    {
        Schema::table('crew_quiz_attempts', fn (Blueprint $t) => $t->dropColumn(['wiped_progress', 'try_number']));
    }
};
