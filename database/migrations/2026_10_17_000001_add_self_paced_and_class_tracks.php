<?php

use App\Services\CourseProgressService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Two ways into a course: Self-paced (one system crew per course, no teacher, the default
 * course, watched by admin) or Join Class (one crew per teacher, many teachers per course).
 *
 * - A teacher's edits belong to their own class (crew_id), so self-paced always sees the default.
 * - Progress belongs to a track (crew_id). Switching track shelves the old rows (shelved_at)
 *   instead of deleting them, so a class starts fresh and self-paced progress comes back later.
 */
return new class extends Migration
{
    private const OVERRIDES = ['course_customizations', 'lesson_contents', 'lesson_labs', 'lesson_sim_labs'];

    public function up(): void
    {
        Schema::table('crews', function (Blueprint $table) {
            $table->dropUnique(['planet', 'course_slug']);
        });
        Schema::table('crews', function (Blueprint $table) {
            $table->string('type')->default('class');   // class | self_paced
            $table->string('code')->nullable()->change();
            $table->unsignedBigInteger('teacher_id')->nullable()->change();
            $table->index(['planet', 'course_slug', 'type']);
        });

        Schema::table('course_assignments', function (Blueprint $table) {
            $table->dropUnique(['planet', 'course_slug']);
        });
        Schema::table('course_assignments', function (Blueprint $table) {
            $table->unique(['planet', 'course_slug', 'faculty_id']);
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'course', 'module', 'lesson']);
        });
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->foreignId('crew_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('shelved_at')->nullable();
            $table->unique(['user_id', 'course', 'module', 'lesson', 'crew_id']);
            $table->index(['user_id', 'shelved_at']);
        });

        Schema::table('course_customizations', function (Blueprint $table) {
            $table->dropUnique(['course']);
        });
        foreach (['lesson_contents', 'lesson_labs', 'lesson_sim_labs', 'lesson_quizzes'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropUnique(['course', 'module', 'lesson']);
            });
        }
        Schema::table('course_customizations', function (Blueprint $table) {
            $table->foreignId('crew_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unique(['course', 'crew_id']);
        });
        foreach (['lesson_contents', 'lesson_labs', 'lesson_sim_labs'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->foreignId('crew_id')->nullable()->constrained()->cascadeOnDelete();
                $table->unique(['course', 'module', 'lesson', 'crew_id']);
            });
        }
        Schema::table('lesson_quizzes', function (Blueprint $table) {
            $table->unique(['course', 'module', 'lesson', 'crew_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('signature_path')->nullable();
        });

        $this->backfill();
    }

    /** Every crew that exists today is a teacher's class: its students' progress and its teacher's edits move onto it. */
    private function backfill(): void
    {
        $crews = DB::table('crews')->whereNotNull('planet')->whereNotNull('course_slug')->get(['id', 'planet', 'course_slug', 'teacher_id']);

        foreach ($crews as $crew) {
            $key = CourseProgressService::keyFor($crew->planet, $crew->course_slug);
            $students = DB::table('crew_members')->where('crew_id', $crew->id)->where('role', 'student')->pluck('user_id');

            DB::table('lesson_progress')->where('course', $key)->whereNull('crew_id')->whereIn('user_id', $students)
                ->update(['crew_id' => $crew->id]);

            foreach (self::OVERRIDES as $table) {
                DB::table($table)->where('course', $key)->whereNull('crew_id')->update(['crew_id' => $crew->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('signature_path'));

        Schema::table('lesson_quizzes', function (Blueprint $table) {
            $table->dropUnique(['course', 'module', 'lesson', 'crew_id']);
            $table->unique(['course', 'module', 'lesson']);
        });
        foreach (['lesson_contents', 'lesson_labs', 'lesson_sim_labs'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropUnique(['course', 'module', 'lesson', 'crew_id']);
                $table->dropConstrainedForeignId('crew_id');
                $table->unique(['course', 'module', 'lesson']);
            });
        }
        Schema::table('course_customizations', function (Blueprint $table) {
            $table->dropUnique(['course', 'crew_id']);
            $table->dropConstrainedForeignId('crew_id');
            $table->unique('course');
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'course', 'module', 'lesson', 'crew_id']);
            $table->dropIndex(['user_id', 'shelved_at']);
            $table->dropConstrainedForeignId('crew_id');
            $table->dropColumn('shelved_at');
            $table->unique(['user_id', 'course', 'module', 'lesson']);
        });

        Schema::table('course_assignments', function (Blueprint $table) {
            $table->dropUnique(['planet', 'course_slug', 'faculty_id']);
            $table->unique(['planet', 'course_slug']);
        });

        Schema::table('crews', function (Blueprint $table) {
            $table->dropIndex(['planet', 'course_slug', 'type']);
            $table->dropColumn('type');
            $table->unique(['planet', 'course_slug']);
        });
    }
};
