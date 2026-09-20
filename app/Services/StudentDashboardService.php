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
     * The next programming lesson to take, or null when there is nothing left.
     */
    public static function currentLesson(User $user, ?Collection $progress = null): ?array
    {
        $order = CourseProgressService::order();

        if (! $order) {
            return null;
        }

        $done = array_flip($progress
            ? $progress->where('course', CourseProgressService::COURSE)->map(fn ($r) => $r->module.'/'.$r->lesson)->all()
            : CourseProgressService::completedKeys($user));
        $next = collect($order)->first(fn ($i) => ! isset($done[$i['module'].'/'.$i['lesson']]));

        if (! $next) {
            return null;
        }

        foreach (config('course-structure.'.CourseProgressService::COURSE.'.modules') as $mKey => $module) {
            foreach ($module['lessons'] as $lKey => $lesson) {
                if (CourseProgressService::normalize($mKey, $lKey) === [$next['module'], $next['lesson']]) {
                    return [
                        'module' => $module['title'],
                        'title' => $lesson['title'],
                        'url' => route('student.planet.module.lesson', [
                            'slug' => CourseProgressService::COURSE, 'module' => $next['module'], 'lesson' => $next['lesson'],
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
