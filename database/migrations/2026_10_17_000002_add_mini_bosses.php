<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mini bosses: a full-screen exam after every module. A crew quiz with a `module` ("m1") is that
 * module's mini boss. Each right answer is worth XP (`xp` on the attempt): in a class the student
 * keeps it whatever their score, self-paced only a pass keeps it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_quizzes', function (Blueprint $table) {
            $table->string('module', 16)->nullable()->after('is_final');
            $table->unique(['crew_id', 'module']);
        });

        Schema::table('crew_quiz_attempts', function (Blueprint $table) {
            $table->unsignedInteger('xp')->default(0)->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('crew_quiz_attempts', fn (Blueprint $t) => $t->dropColumn('xp'));
        Schema::table('crew_quizzes', function (Blueprint $t) {
            $t->dropUnique(['crew_id', 'module']);
            $t->dropColumn('module');
        });
    }
};
