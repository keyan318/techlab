<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A faculty member's own version of a lesson's coding lab ("Code it yourself"): what the editor tells the
        // student to do and the output their program must print. No row = the lab as the lesson file writes it.
        Schema::create('lesson_labs', function (Blueprint $table) {
            $table->id();
            $table->string('course');
            $table->string('module');   // normalized: m1
            $table->string('lesson');   // normalized: lesson01
            $table->string('title');
            $table->string('difficulty')->default('Easy');
            $table->text('instructions');
            $table->text('starter_code')->nullable();
            $table->text('expected_output');
            $table->text('hint_body')->nullable();
            $table->text('hint_code')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['course', 'module', 'lesson']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_labs');
    }
};
