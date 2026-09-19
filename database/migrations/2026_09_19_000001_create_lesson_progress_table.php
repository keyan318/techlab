<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course');
            $table->string('module');
            $table->string('lesson');
            $table->timestamp('completed_at');
            $table->timestamps();

            // One completion row per user per lesson — makes completing idempotent.
            $table->unique(['user_id', 'course', 'module', 'lesson']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
