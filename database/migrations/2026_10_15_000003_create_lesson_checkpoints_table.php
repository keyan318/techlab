<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row = a student's progress on one in-lesson checkpoint (the question that unlocks the next stage).
        // Wrong tries are counted so each retry waits a little longer; passed_at is set once it is answered right.
        Schema::create('lesson_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course');
            $table->string('module');
            $table->string('lesson');
            $table->unsignedTinyInteger('checkpoint');
            $table->unsignedSmallInteger('misses')->default(0);
            $table->timestamp('last_miss_at')->nullable();
            $table->timestamp('passed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course', 'module', 'lesson', 'checkpoint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_checkpoints');
    }
};
