<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gems a student spent (e.g. buying a suit for their astronaut). Gems to spend = modules finished − spent.
        Schema::create('gem_spends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item', 40);   // e.g. "suit:pilot"
            $table->unsignedSmallInteger('cost');
            $table->timestamps();

            // Bought once: a student can't be charged twice for the same thing.
            $table->unique(['user_id', 'item']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gem_spends');
    }
};
