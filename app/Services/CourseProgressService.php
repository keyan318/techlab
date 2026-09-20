<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\User;

/**
 * Server-side lesson progression for every planet that has a blueprint in
 * `config/course-structure.php` (Programming/Python and Networking today).
 *
 * `config/course-structure.php` is the single source of lesson order and the
 * per-lesson answer key (`expected`). Lessons are addressed everywhere in the
 * compact form the sidebar/URLs use — module "m1", lesson "lesson01" — and any
 * other spelling ("M1", "lesson-01") is normalized to it first.
 *
 * A lesson is unlocked when it is the very first one, or the lesson immediately
 * before it (course-wide, across module boundaries) has been completed.
 */
class CourseProgressService
{
    public const COURSE = 'programming';

    /**
     * Whether a planet slug has a lesson blueprint (and so tracked, gated lessons).
     */
    public static function exists(string $course): bool
    {
        return (bool) config("course-structure.{$course}.modules");
    }

    /**
     * The view folder a planet's lesson files live in, e.g.
     * "student.planets.networking.net_course" (lesson views are <base>.M1.lesson-01).
     */
    public static function viewBase(string $course): ?string
    {
        return config("course-structure.{$course}.view_base");
    }

    /**
     * The simulator lab a lesson is completed by, or null (Python lessons use a coding challenge instead).
     */
    public static function labFor(string $module, string $lesson, string $course = self::COURSE): ?string
    {
        $index = self::indexOf($module, $lesson, $course);

        return $index === null ? null : (self::order($course)[$index]['lab'] ?? null);
    }

    /**
     * Flat, ordered lesson list: [['module' => 'm1', 'lesson' => 'lesson01', 'expected' => ?string, 'lab' => ?string], …]
     */
    public static function order(string $course = self::COURSE): array
    {
        $order = [];

        foreach (config("course-structure.{$course}.modules", []) as $moduleKey => $module) {
            foreach ($module['lessons'] ?? [] as $lessonKey => $lesson) {
                [$m, $l] = self::normalize($moduleKey, $lessonKey);

                $order[] = [
                    'module'   => $m,
                    'lesson'   => $l,
                    'expected' => $lesson['expected'] ?? null,
                    'lab'      => $lesson['lab'] ?? null,
                ];
            }
        }

        return $order;
    }

    /**
     * "M1" / "lesson-01" → ["m1", "lesson01"].
     */
    public static function normalize(string $module, string $lesson): array
    {
        return [
            strtolower($module),
            str_replace('-', '', strtolower($lesson)),
        ];
    }

    /**
     * Position of a lesson in the course, or null if it isn't part of it.
     */
    public static function indexOf(string $module, string $lesson, string $course = self::COURSE): ?int
    {
        [$module, $lesson] = self::normalize($module, $lesson);

        foreach (self::order($course) as $i => $item) {
            if ($item['module'] === $module && $item['lesson'] === $lesson) {
                return $i;
            }
        }

        return null;
    }

    /**
     * The completed lessons for a user as "m1/lesson01" keys.
     *
     * @return string[]
     */
    public static function completedKeys(User $user, string $course = self::COURSE): array
    {
        return LessonProgress::where('user_id', $user->id)
            ->where('course', $course)
            ->get(['module', 'lesson'])
            ->map(fn ($row) => $row->module.'/'.$row->lesson)
            ->all();
    }

    public static function isCompleted(User $user, string $module, string $lesson, string $course = self::COURSE): bool
    {
        [$module, $lesson] = self::normalize($module, $lesson);

        return LessonProgress::where([
            'user_id' => $user->id,
            'course'  => $course,
            'module'  => $module,
            'lesson'  => $lesson,
        ])->exists();
    }

    /**
     * Unknown lessons are never unlocked.
     */
    public static function isUnlocked(User $user, string $module, string $lesson, string $course = self::COURSE): bool
    {
        $index = self::indexOf($module, $lesson, $course);

        if ($index === null) {
            return false;
        }

        if ($index === 0) {
            return true;
        }

        $prev = self::order($course)[$index - 1];

        return self::isCompleted($user, $prev['module'], $prev['lesson'], $course);
    }

    /**
     * The first lesson the user has not completed — where a blocked student
     * is sent back to. Falls back to the last lesson once everything is done.
     *
     * @return array{module: string, lesson: string}
     */
    public static function furthestUnlocked(User $user, string $course = self::COURSE): array
    {
        $order     = self::order($course);
        $completed = array_flip(self::completedKeys($user, $course));

        foreach ($order as $item) {
            if (! isset($completed[$item['module'].'/'.$item['lesson']])) {
                return ['module' => $item['module'], 'lesson' => $item['lesson']];
            }
        }

        $last = end($order);

        return ['module' => $last['module'], 'lesson' => $last['lesson']];
    }

    /**
     * @return array{module: string, lesson: string}|null  null when it is the last lesson
     */
    public static function nextLesson(string $module, string $lesson, string $course = self::COURSE): ?array
    {
        $index = self::indexOf($module, $lesson, $course);
        $order = self::order($course);

        if ($index === null || ! isset($order[$index + 1])) {
            return null;
        }

        return ['module' => $order[$index + 1]['module'], 'lesson' => $order[$index + 1]['lesson']];
    }

    /**
     * The answer key for a lesson's coding challenge, or null when the lesson
     * has no output-checkable challenge (it is then not gated on a challenge).
     */
    public static function expectedFor(string $module, string $lesson, string $course = self::COURSE): ?string
    {
        $index = self::indexOf($module, $lesson, $course);

        return $index === null ? null : (self::order($course)[$index]['expected'] ?? null);
    }

    /**
     * Compare a student's captured output to the answer key, ignoring
     * surrounding whitespace and line-ending style.
     */
    public static function outputMatches(string $expected, string $output): bool
    {
        $clean = fn (string $s) => trim(str_replace("\r\n", "\n", $s));

        return $clean($expected) === $clean($output);
    }

    /**
     * Record a completion. Idempotent thanks to the unique index.
     */
    public static function markComplete(User $user, string $module, string $lesson, string $course = self::COURSE): void
    {
        [$module, $lesson] = self::normalize($module, $lesson);

        LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'course' => $course, 'module' => $module, 'lesson' => $lesson],
            ['completed_at' => now()]
        );
    }
}
