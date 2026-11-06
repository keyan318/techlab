<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Crew;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

/**
 * Which run of a course ("track") a request is about: a student's own crew (self-paced or a class),
 * or a faculty member's own class. Two questions, both memoized on the request so a lesson page that
 * asks a dozen times costs one query:
 *
 *  - crew($user, $key): the crew a student's progress is saved under.
 *  - classId($key): the class whose lesson edits the signed-in user sees, or null for the default
 *    course. Self-paced students, admins and guests get null, so a teacher's edit never reaches
 *    anyone outside their class — except on a course a faculty member built from scratch, where
 *    the creator's class is the only version there is (builtCourseClass).
 */
class CourseTrack
{
    /** The crew this student is on for the course behind a lesson-URL key, or null. */
    public static function crew(User $user, string $key): ?Crew
    {
        return self::memo("crew.{$user->id}.{$key}", function () use ($user, $key) {
            $course = CourseProgressService::catalogFor($key);

            return $course ? Crew::forStudent($user, CourseProgressService::planetFor($key), $course) : null;
        });
    }

    /** Remember a student's crew for a course key, when the caller already looked it up (EnsureCourseMember). */
    public static function prime(User $user, string $key, ?Crew $crew): void
    {
        self::memo("crew.{$user->id}.{$key}", fn () => $crew);
    }

    /** The class whose edits of this course the signed-in user sees, or null for the default course. */
    public static function classId(string $key): ?int
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        return self::memo("class.{$user->id}.{$key}", function () use ($user, $key) {
            $course = CourseProgressService::catalogFor($key);
            if (! $course) {
                return null;
            }
            $planet = CourseProgressService::planetFor($key);

            $crew = match ($user->role ?? 'student') {
                'student' => self::crew($user, $key),
                'faculty' => Crew::classOf($user, $planet, $course),
                default => null,
            };

            return $crew && ! $crew->isSelfPaced() ? $crew->id : self::builtCourseClass($planet, $course);
        });
    }

    /**
     * A course a faculty member built from scratch (app/Models/Course) has no default version on disk:
     * everything in it is saved on its creator's class, so that class is the course for everyone else
     * too (self-paced students, other faculty, admins). Null for a config course: no query needed.
     */
    private static function builtCourseClass(string $planet, string $course): ?int
    {
        if (config("course-catalog.{$planet}.courses.{$course}") !== null) {
            return null;
        }

        try {
            return Crew::forCourse($planet, $course)->classes()
                ->whereIn('teacher_id', Course::select('created_by')->where('planet', $planet)->where('slug', $course))
                ->value('id');
        } catch (QueryException) {
            return null;   // courses table not migrated yet
        }
    }

    /** Drop what this request remembered (after a student changes track). */
    public static function forget(): void
    {
        if (app()->bound('request')) {
            request()->attributes->remove('course-track');
        }
    }

    private static function memo(string $key, callable $resolve): mixed
    {
        if (! app()->bound('request')) {
            return $resolve();
        }

        $attributes = request()->attributes;
        if (array_key_exists($key, $memo = $attributes->get('course-track', []))) {
            return $memo[$key];
        }

        $value = $resolve();
        // Re-read: resolving may have memoized something else (classId() asks crew()).
        $attributes->set('course-track', [$key => $value] + $attributes->get('course-track', []));

        return $value;
    }
}
