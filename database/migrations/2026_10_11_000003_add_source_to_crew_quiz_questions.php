<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where a quiz question came from ("Lesson 2.2 · TCP", "Week3.pdf"), so the faculty member
 * can see what each question is based on. Only the builder shows it; students never do.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_quiz_questions', function (Blueprint $table) {
            $table->string('source')->nullable()->after('prompt');
        });
    }

    public function down(): void
    {
        Schema::table('crew_quiz_questions', fn (Blueprint $t) => $t->dropColumn('source'));
    }
};
