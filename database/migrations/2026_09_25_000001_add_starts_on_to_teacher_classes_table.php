<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A weekly slot repeats from this date onward, so the calendar starts on the day the schedule was added.
        Schema::table('teacher_classes', function (Blueprint $table) {
            $table->date('starts_on')->nullable()->after('room');
        });

        DB::table('teacher_classes')->whereNull('starts_on')->orderBy('id')->each(function ($row) {
            DB::table('teacher_classes')->where('id', $row->id)->update(['starts_on' => substr((string) $row->created_at, 0, 10) ?: null]);
        });
    }

    public function down(): void
    {
        Schema::table('teacher_classes', function (Blueprint $table) {
            $table->dropColumn('starts_on');
        });
    }
};
