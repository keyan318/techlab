<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crew_id')->constrained('crews')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('module_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('name');
            $table->string('ext', 16);
            $table->string('path');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_materials');
        Schema::dropIfExists('course_modules');
    }
};
