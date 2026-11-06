<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A faculty member's own version of a lesson's Knowledge Check. No row = the default quiz written in the lesson Blade.
        Schema::create('lesson_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_id')->constrained()->cascadeOnDelete();
            $table->string('course');
            $table->string('module');
            $table->string('lesson');
            $table->json('questions');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // One crew per course, so one edited quiz per lesson of a course.
            $table->unique(['course', 'module', 'lesson']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_quizzes');
    }
};
