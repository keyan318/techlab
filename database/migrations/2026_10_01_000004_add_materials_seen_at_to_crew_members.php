<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_members', function (Blueprint $table) {
            // When this student last opened the course's Materials tab, for the "new material" badge.
            $table->timestamp('materials_seen_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('crew_members', fn (Blueprint $t) => $t->dropColumn('materials_seen_at'));
    }
};
