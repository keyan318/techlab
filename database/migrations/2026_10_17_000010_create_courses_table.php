<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A course a faculty member invented from scratch, instead of one baked into
        // config/course-structure.php + config/course-catalog.php. Everything else about it
        // (modules, lessons, labs, quizzes) lives in the same override tables a config course's
        // faculty edits use — this row is just the "it exists" shell those tables need to resolve.
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('planet');   // programming | networking | cybersecurity
            $table->string('slug');     // the course key everywhere else calls "course"
            $table->string('title');
            $table->text('blurb')->nullable();
            $table->string('level')->nullable();
            $table->string('art_path')->nullable();
            $table->string('lab_kind')->default('none');   // code | netsim | relay | citadel | none
            $table->string('status')->default('draft');    // draft | published
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['planet', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
