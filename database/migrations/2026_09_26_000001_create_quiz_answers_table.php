<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row = a student's (first, final) answer to one lesson quiz question. Correct answers carry the XP earned.
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course');
            $table->string('module');
            $table->string('lesson');
            $table->unsignedTinyInteger('question');
            $table->string('choice', 1);
            $table->boolean('is_correct');
            $table->unsignedSmallInteger('xp')->default(0);
            $table->timestamps();

            // First answer counts: one row per question, so XP can't be farmed by re-answering.
            $table->unique(['user_id', 'course', 'module', 'lesson', 'question']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
    }
};
