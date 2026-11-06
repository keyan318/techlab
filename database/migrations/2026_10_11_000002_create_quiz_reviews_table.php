<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A student who failed a lesson quiz reviews their mistakes with Astro before retrying.
        // One row per lesson: the chat it opened, and which mistakes it was about (so clicking
        // the button again reopens the same chat instead of asking the AI twice).
        Schema::create('quiz_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course');
            $table->string('module');
            $table->string('lesson');
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mistakes_hash', 64);
            $table->timestamps();

            $table->unique(['user_id', 'course', 'module', 'lesson']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_reviews');
    }
};
