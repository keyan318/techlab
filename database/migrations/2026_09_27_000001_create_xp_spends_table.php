<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // XP a student spent (e.g. buying Astro's hint on an exercise). Net XP = earned − spent.
        Schema::create('xp_spends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('course');
            $table->string('module');
            $table->string('lesson');
            $table->string('item', 20)->default('hint');
            $table->unsignedSmallInteger('cost');
            $table->timestamps();

            // Bought once per lesson: flipping back to the hint later is free, and XP can't be charged twice.
            $table->unique(['user_id', 'course', 'module', 'lesson', 'item']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xp_spends');
    }
};
