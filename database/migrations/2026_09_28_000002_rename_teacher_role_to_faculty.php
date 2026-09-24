<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'teacher')->update(['role' => 'faculty']);
        DB::table('crew_members')->where('role', 'teacher')->update(['role' => 'faculty']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'faculty')->update(['role' => 'teacher']);
        DB::table('crew_members')->where('role', 'faculty')->update(['role' => 'teacher']);
    }
};
