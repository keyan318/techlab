<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CrewQuizAttempt;
use App\Models\User;

/**
 * Season 2 of a planet waits for Season 1: a course with `requires` in config/course-catalog.php
 * (Computer Programming 2 → 1, Networking 2 → 1, IAS 2 → 1) can't be joined until the student
 * has finished the required course, by the same rule as its certificate: every lesson done and
 * the final exam passed.
 *
 * A faculty-created course (App\Models\Course, no `requires` entry of its own) has a lighter gate
 * instead: the planet's base course's Module 1 mini boss, passed — a quick taste of the basics
 * rather than the whole course.
 *
 * It gates joining only (asking, the faculty member's Accept, redeeming the code). A student
 * already on a course's roster stays there.
 */
class CoursePrerequisiteService
{
    /** The planet's own course, whose Module 1 mini boss gates self-paced on any faculty-created course there. */
    private const BASE_COURSE = [
        'programming' => 'computer-programming-1',
        'networking' => 'networking-fundamentals',
        'cybersecurity' => 'information-security-1',
    ];

    /**
     * The course this student still has to finish before joining this one (its catalog entry
     * plus `id`), or null when there's nothing in the way.
     */
    public static function missing(User $user, string $planet, string $course): ?array
    {
        if (($user->role ?? 'student') !== 'student') {
            return null;
        }

        $required = self::requirement($planet, $course);

        return $required && ! self::satisfied($user, $planet, $required) ? $required : null;
    }

    /** The course that must be finished before this one (its catalog entry plus `id`), or null when there is none. */
    public static function required(string $planet, string $course): ?array
    {
        $id = config("course-catalog.{$planet}.courses.{$course}.requires");
        $meta = $id ? config("course-catalog.{$planet}.courses.{$id}") : null;

        // A hidden required course can't be finished, so it can't block anyone either.
        return $meta && empty($meta['hidden']) ? $meta + ['id' => $id] : null;
    }

    /**
     * `required()`, or, for a faculty-created course with no `requires` of its own, the planet's
     * base course as a Module 1 gate (`module_gate: true`). Null when neither applies.
     */
    public static function requirement(string $planet, string $course): ?array
    {
        if ($required = self::required($planet, $course)) {
            return $required;
        }

        $baseId = self::BASE_COURSE[$planet] ?? null;
        if (! $baseId || $baseId === $course || ! Course::where('planet', $planet)->where('slug', $course)->exists()) {
            return null;
        }

        $base = config("course-catalog.{$planet}.courses.{$baseId}");

        return $base ? ['id' => $baseId, 'title' => $base['title'], 'module_gate' => true] : null;
    }

    /** Whichever check `requirement()`'s entry calls for: the full course finished, or just its Module 1 mini boss passed. */
    public static function satisfied(User $user, string $planet, array $requirement): bool
    {
        return ($requirement['module_gate'] ?? false)
            ? self::passedModule($user, $planet, $requirement['id'], 'm1')
            : self::finished($user, $planet, $requirement['id']);
    }

    /** Has this student earned the course's certificate: every lesson done and its final exam passed? */
    public static function finished(User $user, string $planet, string $course): bool
    {
        // The exam check first: one cheap query settles it for most students.
        return CrewQuizAttempt::where('user_id', $user->id)
            ->where('status', CrewQuizAttempt::PASSED)
            ->whereHas('quiz', fn ($q) => $q->where('is_final', true)
                ->whereHas('crew', fn ($c) => $c->where('planet', $planet)->where('course_slug', $course)))
            ->exists()
            && CourseProgressService::finishedAt($user, CourseProgressService::keyFor($planet, $course)) !== null;
    }

    /** Has this student passed this module's mini boss, on any of their crews for this course? */
    public static function passedModule(User $user, string $planet, string $course, string $module): bool
    {
        return CrewQuizAttempt::where('user_id', $user->id)
            ->where('status', CrewQuizAttempt::PASSED)
            ->whereHas('quiz', fn ($q) => $q->where('module', $module)
                ->whereHas('crew', fn ($c) => $c->where('planet', $planet)->where('course_slug', $course)))
            ->exists();
    }

    /** "Finish Computer Programming 1 first: …" — the line a student (or their faculty member) sees. */
    public static function message(array $required): string
    {
        return ($required['module_gate'] ?? false)
            ? "Finish Module 1 of {$required['title']} first. Self-paced on faculty-made courses opens once you've passed its mini boss."
            : "Finish {$required['title']} first. Self-paced opens once you've passed its final exam.";
    }
}
