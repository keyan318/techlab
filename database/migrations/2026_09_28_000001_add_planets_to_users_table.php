<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Planets (programming / networking / cybersecurity) the student enrolled in.
            // NULL = an account made before enrollment existed: it keeps access to every planet.
            $table->json('planets')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('planets');
        });
    }
};
