<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A faculty member's own wording of a lesson's simulator lab ("Configure it yourself"): its title, the
        // instructions of each step, the objectives and the values they check. The network itself stays the lab
        // file's. No row = the lab as its file (public/netsim-app/labs, public/relay-console/labs) writes it.
        Schema::create('lesson_sim_labs', function (Blueprint $table) {
            $table->id();
            $table->string('course');
            $table->string('module');   // normalized: m1
            $table->string('lesson');   // normalized: lesson01
            $table->json('data');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['course', 'module', 'lesson']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_sim_labs');
    }
};
