<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew_members', function (Blueprint $table) {
            // When the dashboard showed this student "Accepted into X's class" (it shows once).
            $table->timestamp('accepted_seen_at')->nullable();
        });

        // Students already on a crew have seen the badge; don't show it to them again.
        DB::table('crew_members')->update(['accepted_seen_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('crew_members', fn (Blueprint $t) => $t->dropColumn('accepted_seen_at'));
    }
};
