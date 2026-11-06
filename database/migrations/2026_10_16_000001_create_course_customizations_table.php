<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A faculty member's changes to a whole course: the course card and the module/lesson outline.
        // No row = the course exactly as config/course-catalog.php and config/course-structure.php describe it.
        Schema::create('course_customizations', function (Blueprint $table) {
            $table->id();
            $table->string('course')->unique();   // course-structure key
            $table->json('card')->nullable();     // {title, blurb, level, art_path}
            $table->json('outline')->nullable();  // [{key, title, hidden, lessons: [{key, title, hidden, custom}]}]
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // A faculty member's own version of one lesson's page. No row = the lesson's Blade file.
        Schema::create('lesson_contents', function (Blueprint $table) {
            $table->id();
            $table->string('course');
            $table->string('module');   // normalized: m1
            $table->string('lesson');   // normalized: lesson01
            $table->string('title')->nullable();
            $table->text('objective')->nullable();
            $table->json('sections');   // [{type: custom, heading, html} | {type: original, index}]
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['course', 'module', 'lesson']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_contents');
        Schema::dropIfExists('course_customizations');
    }
};
