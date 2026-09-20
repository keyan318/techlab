<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row = one weekly class slot in a teacher's schedule (extracted from their upload).
        Schema::create('teacher_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->string('class_name')->nullable();
            $table->unsignedTinyInteger('day');          // ISO weekday: 1 = Monday … 7 = Sunday
            $table->string('starts_at', 5);              // "HH:MM" 24h
            $table->string('ends_at', 5)->nullable();
            $table->string('room')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'day', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_classes');
    }
};
