<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Crew;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
 * before it (course-wide, across module boundaries) has been completed. Self-paced,
 * a module's first lesson also waits for the module before it's mini boss to be beaten
 * (MiniBossService::cleared); a lesson already done before then stays open.
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
        if (config()->has("course-structure.{$key}")) {
            return (string) config("course-structure.{$key}.planet", $key);
        }

        // Not a config blueprint: a faculty-created course names its own planet, since
        // its slug (unlike "programming"/"networking") is never the planet itself.
        try {
            return Course::where('slug', $key)->value('planet') ?? $key;
        } catch (QueryException) {
            return $key;   // table not migrated yet: no faculty-created course can exist
        }
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

        // A faculty-created course has no separate catalog id: its slug is its own catalog id.
        try {
            if (Course::where('slug', $key)->exists()) {
                return $key;
            }
        } catch (QueryException) {
            // table not migrated yet: no faculty-created course can exist
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

        try {
            foreach (Course::where('planet', $planet)->where('status', Course::PUBLISHED)->pluck('slug') as $slug) {
                $keys[] = (string) $slug;
            }
        } catch (QueryException) {
            // table not migrated yet: no faculty-created course can exist
        }

        return $keys;
    }

    /**
     * Whether a course key is real: a config blueprint, or a faculty-created course row (even one
     * with no lessons yet — it exists so its faculty member can start adding to it).
     */
    public static function exists(string $course): bool
    {
        if (CourseStructure::modules($course)) {
            return true;
        }

        try {
            return Course::where('slug', $course)->exists();
        } catch (QueryException) {
            return false;   // table not migrated yet
        }
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
     * Which simulator runs a lesson's lab: "netsim" (network labs, the default), "citadel" (security labs)
     * or "console" (Networking 2's Relay Console router labs).
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

        foreach (CourseStructure::modules($course) as $moduleKey => $module) {
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
     * isUnlocked() and isCompleted() for one lesson in a single query (both look at lesson_progress),
     * for requests where each database round trip shows (the lesson quiz).
     *
     * @return array{0: bool, 1: bool} [unlocked, completed]
     */
    public static function unlockedAndCompleted(User $user, string $module, string $lesson, string $course = self::COURSE): array
    {
        [$module, $lesson] = self::normalize($module, $lesson);
        $index = self::indexOf($module, $lesson, $course);
        if ($index === null) {
            return [false, false];
        }
        $prev = $index === 0 ? null : self::order($course)[$index - 1];

        $done = LessonProgress::where('user_id', $user->id)->where('course', $course)
            ->where(function ($q) use ($module, $lesson, $prev) {
                $q->where(fn ($w) => $w->where('module', $module)->where('lesson', $lesson));
                if ($prev) {
                    $q->orWhere(fn ($w) => $w->where('module', $prev['module'])->where('lesson', $prev['lesson']));
                }
            })
            ->get(['module', 'lesson'])
            ->map(fn ($row) => $row->module.'/'.$row->lesson)
            ->all();

        $completed = in_array($module.'/'.$lesson, $done, true);
        $unlocked = $prev === null || (in_array($prev['module'].'/'.$prev['lesson'], $done, true)
            && ($completed || $prev['module'] === $module || MiniBossService::cleared($user, $course, $prev['module'])));

        return [$unlocked, $completed];
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
        if (! self::isCompleted($user, $prev['module'], $prev['lesson'], $course)) {
            return false;
        }

        return $prev['module'] === self::order($course)[$index]['module']
            || self::isCompleted($user, $module, $lesson, $course)
            || MiniBossService::cleared($user, $course, $prev['module']);
    }

    /**
     * The module whose mini boss stands between the student and this lesson (self-paced, the module before
     * is done but its mini boss isn't beaten), or null when nothing does.
     */
    public static function blockingBoss(User $user, string $module, string $lesson, string $course = self::COURSE): ?string
    {
        $index = self::indexOf($module, $lesson, $course);
        if (! $index) {
            return null;
        }
        $order = self::order($course);
        $prev = $order[$index - 1];

        return $prev['module'] !== $order[$index]['module']
            && self::isCompleted($user, $prev['module'], $prev['lesson'], $course)
            && ! self::isCompleted($user, $module, $lesson, $course)
            && ! MiniBossService::cleared($user, $course, $prev['module'])
            ? $prev['module'] : null;
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
        if ($index === null) {
            return null;
        }

        // A lab the faculty wrote brings its own answer key.
        [$module, $lesson] = self::normalize($module, $lesson);

        return LessonLabService::expected($course, $module, $lesson) ?? (self::order($course)[$index]['expected'] ?? null);
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
     * Modules fully cleared (every lesson complete). Worth StudentDashboardService::GEMS_PER_MODULE gems each.
     */
    public static function completedModulesCount(User $user, string $course = self::COURSE): int
    {
        $completed = array_flip(self::completedKeys($user, $course));
        $count = 0;

        foreach (CourseStructure::modules($course) as $moduleKey => $module) {
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
     * Record a completion. Idempotent thanks to the unique index. True only the first time,
     * which is the one time the lesson's XP is earned (XP is counted from these rows).
     */
    public static function markComplete(User $user, string $module, string $lesson, string $course = self::COURSE, ?string $code = null): bool
    {
        [$module, $lesson] = self::normalize($module, $lesson);

        $where = ['user_id' => $user->id, 'course' => $course, 'module' => $module, 'lesson' => $lesson];
        $row = LessonProgress::where($where)->first() ?? LessonProgress::firstOrCreate(
            $where + ['crew_id' => CourseTrack::crew($user, $course)?->id],
            ['completed_at' => now(), 'code' => $code]
        );

        // Solving it again keeps the lesson (and its XP) as it was; only the saved code moves to the newest pass.
        if (! $row->wasRecentlyCreated && $code !== null && $row->code !== $code) {
            $row->update(['code' => $code]);
        }

        return $row->wasRecentlyCreated;
    }

    /** This lesson's completion row (with the code that passed it, when it has one), or null if it isn't finished. */
    public static function completion(User $user, string $module, string $lesson, string $course = self::COURSE): ?LessonProgress
    {
        [$module, $lesson] = self::normalize($module, $lesson);

        return LessonProgress::where([
            'user_id' => $user->id,
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson,
        ])->first();
    }

    /**
     * Move a student onto another track of a course (their self-paced crew or a class). The rows of
     * every other track are shelved, the new track's own rows (from an earlier stay) come back, and
     * rows from before tracks existed join a self-paced track but not a class: a class starts fresh.
     */
    public static function switchTrack(User $user, string $course, Crew $crew): void
    {
        $rows = fn () => LessonProgress::withoutGlobalScope('active')->where('user_id', $user->id)->where('course', $course);

        DB::transaction(function () use ($rows, $crew) {
            if ($crew->isSelfPaced()) {
                $rows()->whereNull('shelved_at')->whereNull('crew_id')->update(['crew_id' => $crew->id]);
            }
            $rows()->whereNull('shelved_at')->where(fn ($q) => $q->whereNull('crew_id')->orWhere('crew_id', '!=', $crew->id))
                ->update(['shelved_at' => now()]);
            $rows()->whereNotNull('shelved_at')->where('crew_id', $crew->id)->update(['shelved_at' => null]);
        });

        CourseTrack::forget();
    }
}
