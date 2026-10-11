<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studio_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('uid', 64);            // id the browser gave the output (e.g. "qz-lx3k9")
            $table->string('type', 24);
            $table->longText('payload');          // the whole output as JSON
            $table->timestamps();

            $table->unique(['user_id', 'uid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studio_outputs');
    }
};
