<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The final exam ("Final Boss"): one per course, written by the faculty member, taken
 * full screen and proctored in the browser. Passing it is what earns the certificate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_quizzes', function (Blueprint $table) {
            // The course's final exam, rather than one of its ordinary quizzes.
            $table->boolean('is_final')->default(false)->after('published_at');
            // The score (in %) a student needs on the final to pass.
            $table->unsignedTinyInteger('pass_percent')->nullable()->after('is_final');
        });

        Schema::table('crew_quiz_attempts', function (Blueprint $table) {
            // Final exams only (null for ordinary quizzes): in_progress | passed | failed.
            $table->string('status', 20)->nullable()->after('answers');
            $table->timestamp('started_at')->nullable()->after('status');
            $table->timestamp('finished_at')->nullable()->after('started_at');
            // Why the exam was ended early (left full screen, switched tab, …), if it was.
            $table->string('violation')->nullable()->after('finished_at');
            // When the faculty member saw this result, for the "caught leaving" alert badge.
            $table->timestamp('faculty_seen_at')->nullable()->after('violation');
        });
    }

    public function down(): void
    {
        Schema::table('crew_quizzes', fn (Blueprint $t) => $t->dropColumn(['is_final', 'pass_percent']));
        Schema::table('crew_quiz_attempts', fn (Blueprint $t) => $t->dropColumn(['status', 'started_at', 'finished_at', 'violation', 'faculty_seen_at']));
    }
};
