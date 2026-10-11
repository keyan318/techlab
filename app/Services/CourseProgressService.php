<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Carbon;

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
     * The course-structure.php key a catalog course resolves to.
     *
     * Historically config/course-structure.php had one blueprint per planet, because
     * each planet only ever offered one real course (Python, Networking 1). Those two
     * keep resolving to their planet slug so existing progress/URLs don't move. Any
     * other course (a planet's second+ course) resolves to its own catalog course id
     * instead, so it never inherits another course's lessons just for sharing a planet.
     */
    public static function keyFor(string $slug, ?string $course): string
    {
        return match (true) {
            $course === null => $slug,
            $slug === 'programming' && $course === 'python' => 'programming',
            $slug === 'networking' && $course === 'networking-fundamentals' => 'networking',
            $slug === 'cybersecurity' && $course === 'information-security-1' => 'cybersecurity',
            default => $course,
        };
    }

    /**
     * The planet a lesson-URL key belongs to.
     *
     * Lesson URLs carry one key ("/student/planet/{key}/{module}/{lesson}"). For the three
     * original courses that key is the planet slug itself, so it maps to itself. A planet's
     * second+ course is keyed by its catalog id instead and names its planet in the blueprint,
     * which is how two courses can share a planet without sharing lessons or progress.
     */
    public static function planetFor(string $key): string
    {
        return (string) config("course-structure.{$key}.planet", $key);
    }

    /**
     * The catalog course id a lesson-URL key belongs to, or null when the planet has none.
     */
    public static function catalogFor(string $key): ?string
    {
        if ($id = config("course-structure.{$key}.catalog")) {
            return (string) $id;
        }

        foreach (array_keys(config('course-catalog.'.self::planetFor($key).'.courses', [])) as $id) {
            if (self::keyFor($key, (string) $id) === $key) {
                return (string) $id;
            }
        }

        return null;
    }

    /**
     * Whether a lesson-URL key's catalog course is switched off ('hidden' in config/course-catalog.php).
     */
    public static function isHidden(string $key): bool
    {
        $id = self::catalogFor($key);

        return $id !== null && ! empty(config('course-catalog.'.self::planetFor($key).".courses.{$id}.hidden"));
    }

    /**
     * Every blueprint key that belongs to a planet (hidden courses left out), the planet's own key first.
     * A planet's percentage is the sum of all of them, not just the one named after it.
     *
     * @return string[]
     */
    public static function keysForPlanet(string $planet): array
    {
        $keys = [];

        foreach (array_keys(config('course-structure', [])) as $key) {
            if (self::planetFor((string) $key) === $planet && ! self::isHidden((string) $key)) {
                $keys[] = (string) $key;
            }
        }

        return $keys;
    }

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
     * Which simulator runs a lesson's lab: "netsim" (network labs, the default) or "citadel" (security labs).
     */
    public static function simFor(string $module, string $lesson, string $course = self::COURSE): string
    {
        $index = self::indexOf($module, $lesson, $course);

        return $index === null ? 'netsim' : (self::order($course)[$index]['sim'] ?? 'netsim');
    }

    /**
     * Flat, ordered lesson list: [['module' => 'm1', 'lesson' => 'lesson01', 'expected' => ?string, 'lab' => ?string, 'sim' => ?string], …]
     */
    public static function order(string $course = self::COURSE): array
    {
        $order = [];

        foreach (config("course-structure.{$course}.modules", []) as $moduleKey => $module) {
            foreach ($module['lessons'] ?? [] as $lessonKey => $lesson) {
                [$m, $l] = self::normalize($moduleKey, $lessonKey);

                $order[] = [
                    'module' => $m,
                    'lesson' => $l,
                    'expected' => $lesson['expected'] ?? null,
                    'lab' => $lesson['lab'] ?? null,
                    'sim' => $lesson['sim'] ?? null,
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

    /**
     * How much of one course a user has finished, 0–100. Only this course's own lessons count,
     * so a planet's other course never moves it (that's what a planet's percent is for).
     */
    public static function percent(User $user, string $course): int
    {
        $order = self::order($course);

        if (! $order) {
            return 0;
        }

        $done = array_flip(self::completedKeys($user, $course));
        $completed = count(array_filter($order, fn ($i) => isset($done[$i['module'].'/'.$i['lesson']])));

        return (int) round($completed / count($order) * 100);
    }

    public static function isCompleted(User $user, string $module, string $lesson, string $course = self::COURSE): bool
    {
        [$module, $lesson] = self::normalize($module, $lesson);

        return LessonProgress::where([
            'user_id' => $user->id,
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson,
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
        $order = self::order($course);
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
     * @return array{module: string, lesson: string}|null null when it is the last lesson
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
     * Gems earned: one per module where every lesson is complete.
     */
    public static function completedModulesCount(User $user, string $course = self::COURSE): int
    {
        $completed = array_flip(self::completedKeys($user, $course));
        $count = 0;

        foreach (config("course-structure.{$course}.modules", []) as $moduleKey => $module) {
            $lessonKeys = array_keys($module['lessons'] ?? []);

            if ($lessonKeys === []) {
                continue;
            }

            $allDone = true;
            foreach ($lessonKeys as $lessonKey) {
                [$m, $l] = self::normalize($moduleKey, $lessonKey);
                if (! isset($completed["{$m}/{$l}"])) {
                    $allDone = false;
                    break;
                }
            }

            $count += $allDone ? 1 : 0;
        }

        return $count;
    }

    /**
     * When the user finished the whole course — the moment their last lesson was completed —
     * or null while any lesson is still left. This is the completion certificate's issue date,
     * so it never moves once earned.
     */
    public static function finishedAt(User $user, string $course = self::COURSE): ?Carbon
    {
        $order = self::order($course);

        if ($order === []) {
            return null;
        }

        $done = LessonProgress::where('user_id', $user->id)
            ->where('course', $course)
            ->get(['module', 'lesson', 'completed_at', 'created_at'])
            ->keyBy(fn ($row) => $row->module.'/'.$row->lesson);

        $finishedAt = null;

        foreach ($order as $item) {
            $row = $done->get($item['module'].'/'.$item['lesson']);

            if ($row === null) {
                return null;
            }

            $at = $row->completed_at ?? $row->created_at;
            $finishedAt = $finishedAt === null || $at > $finishedAt ? $at : $finishedAt;
        }

        return $finishedAt;
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
