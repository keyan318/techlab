<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crews', function (Blueprint $table) {
            $table->string('planet')->nullable();
            $table->string('course_slug')->nullable();
            $table->unique(['planet', 'course_slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crews', function (Blueprint $table) {
            $table->dropUnique(['planet', 'course_slug']);
            $table->dropColumn(['planet', 'course_slug']);
        });
    }
};
