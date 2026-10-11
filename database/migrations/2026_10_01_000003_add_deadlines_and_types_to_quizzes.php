<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_quizzes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            // Past this moment a student who has not started it can no longer take it.
            $table->timestamp('due_at')->nullable()->after('minutes');
            // Students only see a published quiz; until then it is the faculty's draft.
            $table->timestamp('published_at')->nullable()->after('due_at');
        });

        // Everything that already exists was visible to students, so it counts as published.
        DB::table('crew_quizzes')->whereNull('published_at')->update(['published_at' => now()]);

        Schema::table('crew_quiz_questions', function (Blueprint $table) {
            // multiple_choice | true_false | checkboxes | dropdown
            $table->string('type', 20)->default('multiple_choice')->after('options');
            // Checkboxes can have several right answers; the other types use correct_index.
            $table->json('correct_indexes')->nullable()->after('correct_index');
            $table->boolean('required')->default(true)->after('correct_indexes');
        });

        Schema::table('crew_members', function (Blueprint $table) {
            // When this student last opened the course's Quiz tab, for the "new quiz" badge.
            $table->timestamp('quizzes_seen_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('crew_quizzes', fn (Blueprint $t) => $t->dropColumn(['description', 'due_at', 'published_at']));
        Schema::table('crew_quiz_questions', fn (Blueprint $t) => $t->dropColumn(['type', 'correct_indexes', 'required']));
        Schema::table('crew_members', fn (Blueprint $t) => $t->dropColumn('quizzes_seen_at'));
    }
};
