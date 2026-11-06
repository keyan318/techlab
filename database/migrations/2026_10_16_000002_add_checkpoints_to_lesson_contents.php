<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The edited page's checkpoint questions, by id; the sections place them with <div data-checkpoint="id">.
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->json('checkpoints')->nullable()->after('sections');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_contents', function (Blueprint $table) {
            $table->dropColumn('checkpoints');
        });
    }
};
