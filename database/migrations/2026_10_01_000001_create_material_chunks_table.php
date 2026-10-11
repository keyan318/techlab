<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A faculty upload, split into searchable pieces. crew_id is denormalized so a
        // student's search can be scoped to their own courses in one indexed lookup.
        Schema::create('material_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crew_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->longText('text');
            $table->timestamps();
            $table->index(['crew_id', 'module_material_id']);
        });

        Schema::table('module_materials', function (Blueprint $table) {
            // Set for a material that is a link rather than a file; path stays empty then.
            $table->string('url', 2048)->nullable()->after('path');
            // When the text was last pulled out. Null = not read yet (or not readable).
            $table->timestamp('indexed_at')->nullable()->after('size');
            // Why a material has no chunks, so the faculty page can say so plainly.
            $table->string('index_error')->nullable()->after('indexed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_chunks');
        Schema::table('module_materials', function (Blueprint $table) {
            $table->dropColumn(['url', 'indexed_at', 'index_error']);
        });
    }
};
