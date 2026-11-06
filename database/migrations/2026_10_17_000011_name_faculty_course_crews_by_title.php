<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // A faculty-built course's crews were named after its slug ("dsa") because only config courses
        // had a catalog title to look up. Give them the course's own title, as Crew::courseTitle now does.
        foreach (DB::table('courses')->get(['planet', 'slug', 'title']) as $course) {
            DB::table('crews')->where('planet', $course->planet)->where('course_slug', $course->slug)
                ->where('type', 'class')->where('name', $course->slug)
                ->update(['name' => $course->title]);
            DB::table('crews')->where('planet', $course->planet)->where('course_slug', $course->slug)
                ->where('type', 'self_paced')->where('name', $course->slug.' · Self-paced')
                ->update(['name' => $course->title.' · Self-paced']);
        }
    }

    public function down(): void
    {
        // Names only; nothing to undo.
    }
};
