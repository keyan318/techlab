<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crew_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_id')->constrained('crews')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('minutes')->nullable();
            $table->timestamps();
        });

        Schema::create('crew_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_quiz_id')->constrained('crew_quizzes')->cascadeOnDelete();
            $table->text('prompt');
            $table->json('options');
            $table->unsignedTinyInteger('correct_index');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('crew_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_quiz_id')->constrained('crew_quizzes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('score');
            $table->unsignedSmallInteger('total');
            $table->json('answers');
            $table->timestamps();
            $table->unique(['crew_quiz_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crew_quiz_attempts');
        Schema::dropIfExists('crew_quiz_questions');
        Schema::dropIfExists('crew_quizzes');
    }
};
