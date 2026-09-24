<?php

namespace App\Services;

use App\Models\Crew;
use App\Models\LessonProgress;
use App\Models\QuizAnswer;
use App\Models\StudyActivity;
use App\Models\User;
use App\Models\XpSpend;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Everything the student dashboard shows, computed from existing data:
 * `lesson_progress` (completions), `crews`/`users.crew_id` (membership) and
 * `study_activity` (active minutes). XP is derived, not stored: every
 * completed lesson is worth XP_PER_LESSON, so the leaderboard, the profile
 * total and the trend chart can never disagree.
 */
class StudentDashboardService
{
    public const XP_PER_LESSON = 100;

    /** A correct first answer on a lesson quiz. A 3-question quiz is worth 30, so a lesson's coding challenge (100) stays the big prize. */
    public const XP_PER_QUIZ_QUESTION = 10;

    /** Price of Astro's hint on an exercise. Bought once per lesson; a fifth of what finishing that lesson pays. */
    public const XP_HINT_COST = 20;

    /**
     * Price of Astro's live lab-help chat (Citadel Sim). Bought once per lab attempt, then the student can ask
     * follow-up questions for free. Deliberately steeper than the static hint (3x) — Astro can see the student's
     * exact terminal transcript here, so it's real diagnostic help, not a canned tip, and should cost accordingly.
     */
    public const XP_LAB_HELP_COST = 60;

    /** Earned XP per level. Level is derived, like XP itself, so nothing new is stored. */
    public const XP_PER_LEVEL = 300;

    /** Planets shown on the dashboard. Only those with a config blueprint have trackable lessons. */
    private const PLANETS = [
        'programming' => ['name' => 'Programming',   'world' => 'Python Planet',      'accent' => '#73b6ff'],
        'networking' => ['name' => 'Networking',    'world' => 'Network Nebula',     'accent' => '#5be1ff'],
        'cybersecurity' => ['name' => 'Cybersecurity', 'world' => 'Cybersecurity Citadel', 'accent' => '#9b6bff'],
    ];

    /** Net XP: earned (lessons + quiz answers) minus what was spent. Never negative. */
    public static function xpFor(int $completedLessons, int $quizXp = 0, int $spent = 0): int
    {
        return max(0, $completedLessons * self::XP_PER_LESSON + $quizXp - $spent);
    }

    /** What this student can spend right now. */
    public static function balance(User $user): int
    {
        return self::xpFor(
            $user->lessonProgress()->count(),
            (int) QuizAnswer::where('user_id', $user->id)->sum('xp'),
            (int) XpSpend::where('user_id', $user->id)->sum('cost'),
        );
    }

    public static function build(User $user): array
    {
        // Each query is a network round trip to the database, so read each table once
        // and let every section derive what it needs from the same rows.
        $progress = LessonProgress::where('user_id', $user->id)->get(['course', 'module', 'lesson', 'completed_at']);
        $activity = StudyActivity::where('user_id', $user->id)
            ->where('date', '>=', now()->startOfWeek(Carbon::SUNDAY)->subWeeks(7)->toDateString())->get();

        $planets = self::planets($user, $progress);

        $lessonsDone = array_sum(array_column($planets, 'completed'));
        $lessonsTotal = array_sum(array_column($planets, 'total'));
        $modulesDone = array_sum(array_column($planets, 'modulesDone'));
        $modulesTotal = array_sum(array_column($planets, 'modulesTotal'));

        return [
            'user' => $user,
            'xp' => self::xpFor($lessonsDone, (int) QuizAnswer::where('user_id', $user->id)->sum('xp'), (int) XpSpend::where('user_id', $user->id)->sum('cost')),
            'lessonsDone' => $lessonsDone,
            'lessonsTotal' => $lessonsTotal,
            'modulesDone' => $modulesDone,
            'modulesTotal' => $modulesTotal,
            'overall' => $lessonsTotal > 0 ? (int) round($lessonsDone / $lessonsTotal * 100) : 0,
            'planets' => $planets,
            'current' => self::currentLesson($user, $progress),
            'crew' => $user->crew_id ? Crew::find($user->crew_id) : null,
            'leaderboard' => self::leaderboard($user),
            'weekly' => self::weeklyActivity($user, $activity, $progress),
            'performance' => self::performance($user, 8, 400, 120, $progress, $activity),
        ];
    }

    /** XP earned, ignoring what was spent on hints, so buying a hint never lowers a student's level. */
    public static function earnedXp(User $user): int
    {
        return $user->lessonProgress()->count() * self::XP_PER_LESSON
            + (int) QuizAnswer::where('user_id', $user->id)->sum('xp');
    }

    public static function levelFor(int $earnedXp): int
    {
        return 1 + intdiv($earnedXp, self::XP_PER_LEVEL);
    }

    /**
     * What the landing dashboard shows: the "Jump back in" course, the profile rail stats.
     */
    public static function home(User $user): array
    {
        $progress = LessonProgress::where('user_id', $user->id)->get(['course', 'module', 'lesson', 'completed_at']);
        $planets = self::planets($user, $progress);

        $lessonsDone = array_sum(array_column($planets, 'completed'));
        $quizXp = (int) QuizAnswer::where('user_id', $user->id)->sum('xp');
        $spent = (int) XpSpend::where('user_id', $user->id)->sum('cost');

        $board = self::leaderboard($user);
        $me = collect($board)->firstWhere('me', true);

        return [
            'user' => $user,
            'xp' => self::xpFor($lessonsDone, $quizXp, $spent),
            'level' => self::levelFor($lessonsDone * self::XP_PER_LESSON + $quizXp),
            'rank' => $me['rank'] ?? null,
            'crewSize' => count($board),
            'badges' => array_sum(array_column($planets, 'modulesDone')),
            'streak' => self::streak($user, $progress),
            'hero' => self::hero($planets, self::currentLesson($user, $progress)),
        ];
    }

    /**
     * The first course on a planet that is really open (not "coming soon"), with its catalog entry.
     */
    public static function catalogCourse(string $slug): ?array
    {
        foreach (config("course-catalog.{$slug}.courses", []) as $id => $course) {
            if (empty($course['coming_soon'])) {
                return $course + ['id' => $id];
            }
        }

        return null;
    }

    /**
     * The card behind "Jump back in": the course the student is in the middle of, or null
     * when there is nothing left to continue.
     */
    private static function hero(array $planets, ?array $current): ?array
    {
        $course = $current ? self::catalogCourse($current['planet']) : null;

        if (! $current || ! $course) {
            return null;
        }

        $planet = $planets[$current['planet']];

        return [
            'title' => $course['title'],
            'blurb' => $course['blurb'] ?? '',
            'banner' => $course['banner'] ?? '#1b1650',
            'scene' => $course['scene'] ?? null,
            'percent' => $planet['percent'],
            'started' => $planet['completed'] > 0,
            'next' => $current['title'],
            'continueUrl' => $current['url'],
            'overviewUrl' => route('student.course.overview', ['slug' => $current['planet'], 'course' => $course['id']]),
        ];
    }

    /**
     * Consecutive days of learning up to today. A day counts when the student studied
     * (active minutes) or finished a lesson. Not having studied *yet* today doesn't break the streak.
     */
    public static function streak(User $user, ?Collection $progress = null): int
    {
        $progress ??= LessonProgress::where('user_id', $user->id)->get(['completed_at']);

        $days = StudyActivity::where('user_id', $user->id)->where('minutes', '>', 0)->get(['date'])
            ->toBase()
            ->map(fn ($a) => $a->date->toDateString())
            ->merge($progress->filter(fn ($r) => $r->completed_at)->toBase()->map(fn ($r) => $r->completed_at->toDateString()))
            ->unique()
            ->flip();

        $day = now()->startOfDay();

        if (! $days->has($day->toDateString())) {
            $day->subDay();
        }

        $streak = 0;

        while ($days->has($day->toDateString())) {
            $streak++;
            $day->subDay();
        }

        return $streak;
    }

    /**
     * Per-planet progress. A planet with no blueprint in config/course-structure.php
     * has no tracked lessons yet — it is reported as untracked rather than as 0 of 0.
     */
    public static function planets(User $user, ?Collection $progress = null): array
    {
        $progress ??= LessonProgress::where('user_id', $user->id)->get(['course', 'module', 'lesson']);
        $done = $progress->groupBy('course');
        $out = [];

        foreach (self::PLANETS as $slug => $meta) {
            if (! $user->isEnrolledIn($slug)) {
                continue;
            }

            $order = config("course-structure.{$slug}.modules") ? CourseProgressService::order($slug) : [];
            $keys = $done->get($slug, collect())->map(fn ($r) => $r->module.'/'.$r->lesson)->flip();
            $modules = collect($order)->groupBy('module');

            $completed = collect($order)->filter(fn ($i) => $keys->has($i['module'].'/'.$i['lesson']))->count();
            $modulesDone = $modules->filter(fn ($lessons) => $lessons->every(fn ($i) => $keys->has($i['module'].'/'.$i['lesson'])))->count();
            $total = count($order);

            $out[$slug] = $meta + [
                'slug' => $slug,
                'tracked' => $total > 0,
                'total' => $total,
                'completed' => $completed,
                'modulesTotal' => $modules->count(),
                'modulesDone' => $modulesDone,
                'percent' => $total > 0 ? (int) round($completed / $total * 100) : 0,
            ];
        }

        return $out;
    }

    /**
     * The next lesson to take, or null when there is nothing left. It is in the planet the student
     * touched most recently, so a networking student is sent back to networking. A student with no
     * progress yet starts in Programming.
     */
    public static function currentLesson(User $user, ?Collection $progress = null): ?array
    {
        $progress ??= LessonProgress::where('user_id', $user->id)->get(['course', 'module', 'lesson', 'completed_at']);

        // Only planets the student enrolled in, with a lesson blueprint, can be continued.
        $open = array_values(array_filter($user->enrolledPlanets(), fn ($p) => CourseProgressService::exists($p)));
        if (! $open) {
            return null;
        }

        $latest = $progress->sortByDesc('completed_at')->first()?->course;
        $course = in_array($latest, $open, true) ? $latest : (in_array(CourseProgressService::COURSE, $open, true) ? CourseProgressService::COURSE : $open[0]);

        return self::nextLessonIn($course, $progress) ?? ($course !== $open[0]
            ? self::nextLessonIn($open[0], $progress)
            : null);
    }

    private static function nextLessonIn(string $course, Collection $progress): ?array
    {
        $order = CourseProgressService::order($course);

        if (! $order) {
            return null;
        }

        $done = array_flip($progress->where('course', $course)->map(fn ($r) => $r->module.'/'.$r->lesson)->all());
        $next = collect($order)->first(fn ($i) => ! isset($done[$i['module'].'/'.$i['lesson']]));

        if (! $next) {
            return null;
        }

        foreach (config("course-structure.{$course}.modules") as $mKey => $module) {
            foreach ($module['lessons'] as $lKey => $lesson) {
                if (CourseProgressService::normalize($mKey, $lKey) === [$next['module'], $next['lesson']]) {
                    return [
                        'planet' => $course,
                        'module' => $module['title'],
                        'title' => $lesson['title'],
                        'url' => route('student.planet.module.lesson', [
                            'slug' => $course, 'module' => $next['module'], 'lesson' => $next['lesson'],
                        ]),
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Classmates ranked by XP (ties share a rank, then alphabetical). Empty without a crew.
     */
    public static function leaderboard(User $user): array
    {
        if (! $user->crew_id) {
            return [];
        }

        $rows = User::where('crew_id', $user->crew_id)
            ->where('role', 'student')
            ->withCount('lessonProgress')
            ->withSum('quizAnswers', 'xp')
            ->withSum('xpSpends', 'cost')
            ->get()
            ->map(fn (User $u) => ['id' => $u->id, 'name' => $u->name, 'xp' => self::xpFor($u->lesson_progress_count, (int) $u->quiz_answers_sum_xp, (int) $u->xp_spends_sum_cost)])
            ->sortBy([['xp', 'desc'], ['name', 'asc']])
            ->values();

        $rank = 0;
        $prev = null;

        return $rows->map(function ($row, $i) use ($user, &$rank, &$prev) {
            if ($row['xp'] !== $prev) {
                $rank = $i + 1;
                $prev = $row['xp'];
            }

            return $row + ['rank' => $rank, 'me' => $row['id'] === $user->id];
        })->all();
    }

    /**
     * Active minutes for each day of the current week (Sun–Sat) plus this-week totals.
     */
    public static function weeklyActivity(User $user, ?Collection $activity = null, ?Collection $progress = null): array
    {
        $start = now()->startOfWeek(Carbon::SUNDAY);
        $end = $start->copy()->addDays(6);

        $activity ??= StudyActivity::where('user_id', $user->id)->where('date', '>=', $start->toDateString())->get();
        $progress ??= LessonProgress::where('user_id', $user->id)->where('completed_at', '>=', $start)->get(['completed_at']);

        $minutes = $activity
            ->filter(fn ($a) => $a->date->toDateString() >= $start->toDateString() && $a->date->toDateString() <= $end->toDateString())
            ->mapWithKeys(fn ($a) => [$a->date->toDateString() => $a->minutes]);

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $start->copy()->addDays($i);
            $days[] = [
                'label' => $d->format('D')[0],
                'name' => $d->format('l'),
                'minutes' => (int) $minutes->get($d->toDateString(), 0),
                'today' => $d->isToday(),
            ];
        }

        $total = array_sum(array_column($days, 'minutes'));
        $peak = max(array_column($days, 'minutes'));

        return [
            'days' => $days,
            'totalMinutes' => $total,
            'axisMax' => max(1, (int) ceil($peak / 60)),
            'lessonsWeek' => $progress->filter(fn ($r) => $r->completed_at && $r->completed_at >= $start)->count(),
        ];
    }

    /**
     * XP earned and active minutes per week for the last 8 weeks, as ready-to-draw
     * smooth SVG paths (each series scaled to its own peak), plus the week-over-week XP change.
     */
    public static function performance(User $user, int $weeks = 8, int $w = 400, int $h = 120, ?Collection $progress = null, ?Collection $activity = null): array
    {
        $first = now()->startOfWeek(Carbon::SUNDAY)->subWeeks($weeks - 1);

        $progress ??= LessonProgress::where('user_id', $user->id)->where('completed_at', '>=', $first)->get(['completed_at']);
        $activity ??= StudyActivity::where('user_id', $user->id)->where('date', '>=', $first->toDateString())->get();

        $xp = array_fill(0, $weeks, 0);
        $progress->filter(fn ($r) => $r->completed_at && $r->completed_at >= $first)
            ->each(function ($row) use (&$xp, $first, $weeks) {
                $i = (int) floor($first->diffInDays($row->completed_at->copy()->startOfDay(), false) / 7);
                if ($i >= 0 && $i < $weeks) {
                    $xp[$i] += self::XP_PER_LESSON;
                }
            });

        QuizAnswer::where('user_id', $user->id)->where('xp', '>', 0)->where('created_at', '>=', $first)->get(['xp', 'created_at'])
            ->each(function ($row) use (&$xp, $first, $weeks) {
                $i = (int) floor($first->diffInDays($row->created_at->copy()->startOfDay(), false) / 7);
                if ($i >= 0 && $i < $weeks) {
                    $xp[$i] += $row->xp;
                }
            });

        XpSpend::where('user_id', $user->id)->where('created_at', '>=', $first)->get(['cost', 'created_at'])
            ->each(function ($row) use (&$xp, $first, $weeks) {
                $i = (int) floor($first->diffInDays($row->created_at->copy()->startOfDay(), false) / 7);
                if ($i >= 0 && $i < $weeks) {
                    $xp[$i] = max(0, $xp[$i] - $row->cost);
                }
            });

        $mins = array_fill(0, $weeks, 0);
        $activity->filter(fn ($r) => $r->date->toDateString() >= $first->toDateString())
            ->each(function ($row) use (&$mins, $first, $weeks) {
                $i = (int) floor($first->diffInDays($row->date->copy()->startOfDay(), false) / 7);
                if ($i >= 0 && $i < $weeks) {
                    $mins[$i] += $row->minutes;
                }
            });

        $labels = [];
        for ($i = 0; $i < $weeks; $i++) {
            $labels[] = $first->copy()->addWeeks($i)->format('M j');
        }

        $this_ = $xp[$weeks - 1];
        $last = $xp[$weeks - 2];

        return [
            'labels' => $labels,
            'xp' => $xp,
            'minutes' => $mins,
            'xpPath' => self::smoothPath($xp, $w, $h),
            'minPath' => self::smoothPath($mins, $w, $h),
            'hasData' => array_sum($xp) + array_sum($mins) > 0,
            'thisWeekXp' => $this_,
            'lastWeekXp' => $last,
            'changePct' => $last > 0 ? (int) round(($this_ - $last) / $last * 100) : null,
            'w' => $w, 'h' => $h,
        ];
    }

    /**
     * Catmull-Rom → cubic Bézier through the values, scaled to fit $w × $h (top = peak).
     */
    public static function smoothPath(array $values, int $w, int $h, int $pad = 8): string
    {
        $n = count($values);
        $peak = max(1, max($values));
        $pts = [];

        foreach ($values as $i => $v) {
            $pts[] = [$n > 1 ? $i / ($n - 1) * $w : 0, $pad + (1 - $v / $peak) * ($h - 2 * $pad)];
        }

        $d = sprintf('M%.1f %.1f', $pts[0][0], $pts[0][1]);

        for ($i = 0; $i < $n - 1; $i++) {
            $p0 = $pts[max($i - 1, 0)];
            $p1 = $pts[$i];
            $p2 = $pts[$i + 1];
            $p3 = $pts[min($i + 2, $n - 1)];

            $d .= sprintf(' C%.1f %.1f %.1f %.1f %.1f %.1f',
                $p1[0] + ($p2[0] - $p0[0]) / 6, $p1[1] + ($p2[1] - $p0[1]) / 6,
                $p2[0] - ($p3[0] - $p1[0]) / 6, $p2[1] - ($p3[1] - $p1[1]) / 6,
                $p2[0], $p2[1]);
        }

        return $d;
    }
}
