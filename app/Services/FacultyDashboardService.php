<?php

namespace App\Services;

use App\Models\Crew;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\StudyActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The numbers behind the faculty dashboard's charts, for every course this faculty member runs.
 *
 * The page filters by course and date range in the browser, so this returns raw per-day series
 * (one row per student per course) instead of pre-summed totals. Everything comes from four
 * queries whatever the class size: the database is remote, so a query per student would be slow.
 */
class FacultyDashboardService
{
    /** Days of history sent to the page: enough for the 30-day range plus the 30 days before it to compare with. */
    public const DAYS = 60;

    /**
     * @param  Collection<int, Crew>  $crews
     * @return array{courses: array, students: array, minutes: array<int, int[]>, days: array}
     */
    public static function build(Collection $crews): array
    {
        $first = now()->startOfDay()->subDays(self::DAYS - 1);
        $days = collect(range(0, self::DAYS - 1))->map(fn ($i) => $first->copy()->addDays($i));

        $rows = $crews->isEmpty() ? collect() : DB::table('crew_members')
            ->join('users', 'users.id', '=', 'crew_members.user_id')
            ->whereIn('crew_members.crew_id', $crews->pluck('id'))
            ->where('crew_members.role', 'student')
            ->orderBy('users.name')
            ->get(['crew_members.crew_id', 'users.id', 'users.name', 'users.email']);
        $userIds = $rows->pluck('id')->unique()->values();

        // Each course's lesson list, as "module/lesson" => true, and the progress key it is stored under.
        $courses = $crews->mapWithKeys(function (Crew $crew) {
            $key = $crew->planet && $crew->course_slug ? CourseProgressService::keyFor($crew->planet, $crew->course_slug) : null;

            return [$crew->id => [
                'crew' => $crew,
                'key' => $key,
                'lessons' => $key ? collect(CourseProgressService::order($key))->map(fn ($i) => $i['module'].'/'.$i['lesson'])->flip() : collect(),
            ]];
        });

        $progress = $userIds->isEmpty() ? collect() : LessonProgress::whereIn('user_id', $userIds)
            ->whereIn('course', $courses->pluck('key')->filter()->unique())
            ->get(['user_id', 'course', 'module', 'lesson', 'completed_at'])
            ->groupBy(fn ($r) => $r->course.'|'.$r->user_id);

        $activity = $userIds->isEmpty() ? collect() : StudyActivity::whereIn('user_id', $userIds)
            ->where('date', '>=', $first->toDateString())->where('minutes', '>', 0)
            ->get(['user_id', 'date', 'minutes']);

        $finals = $crews->isEmpty() ? collect() : CrewQuizAttempt::join('crew_quizzes', 'crew_quizzes.id', '=', 'crew_quiz_attempts.crew_quiz_id')
            ->where('crew_quizzes.is_final', true)
            ->whereIn('crew_quizzes.crew_id', $crews->pluck('id'))
            ->get(['crew_quiz_attempts.user_id', 'crew_quiz_attempts.status', 'crew_quiz_attempts.violation', 'crew_quiz_attempts.wiped_progress', 'crew_quiz_attempts.try_number', 'crew_quiz_attempts.strikes', 'crew_quizzes.crew_id'])
            ->keyBy(fn ($a) => $a->crew_id.'|'.$a->user_id);

        // Active minutes per student per day. Kept per student (not per course) because that is how
        // they are recorded, so a student in two of these courses is never counted twice.
        $minutes = [];
        $lastSeen = [];
        foreach ($activity as $row) {
            $i = (int) $first->diffInDays($row->date, false);
            if ($i >= 0 && $i < self::DAYS) {
                $minutes[$row->user_id] ??= array_fill(0, self::DAYS, 0);
                $minutes[$row->user_id][$i] += $row->minutes;
            }
            $lastSeen[$row->user_id] = max($lastSeen[$row->user_id] ?? '', $row->date->toDateString());
        }

        $students = $rows->map(function ($row) use ($courses, $progress, $finals, $first, &$lastSeen) {
            $course = $courses[$row->crew_id];
            $done = ($progress[$course['key'].'|'.$row->id] ?? collect())
                ->filter(fn ($r) => $course['lessons']->has($r->module.'/'.$r->lesson));

            $daily = array_fill(0, self::DAYS, 0);
            foreach ($done as $r) {
                if ($r->completed_at) {
                    $i = (int) $first->diffInDays($r->completed_at->copy()->startOfDay(), false);
                    if ($i >= 0 && $i < self::DAYS) {
                        $daily[$i]++;
                    }
                    $lastSeen[$row->id] = max($lastSeen[$row->id] ?? '', $r->completed_at->toDateString());
                }
            }

            $total = $course['lessons']->count();
            $percent = $total ? (int) round($done->count() / $total * 100) : 0;

            return [
                'id' => $row->id,
                'name' => $row->name,
                'email' => $row->email,
                'crew' => $row->crew_id,
                'done' => $done->count(),
                'percent' => $percent,
                'lessons' => $daily,
                'final' => self::finalState($attempt = $finals[$row->crew_id.'|'.$row->id] ?? null, $percent),
                // What they were caught doing, in a few words, and how many strikes they have.
                'reason' => $attempt?->violationShort(),
                'strikes' => $attempt?->strikes ?? 0,
            ];
        })->map(fn ($s) => $s + ['lastSeen' => $lastSeen[$s['id']] ?? null])->values();

        return [
            'courses' => $courses->map(fn ($c) => [
                'id' => $c['crew']->id,
                'name' => $c['crew']->name,
                'lessons' => $c['lessons']->count(),
                'students' => $students->where('crew', $c['crew']->id)->count(),
            ])->values()->all(),
            'students' => $students->all(),
            'minutes' => $minutes,
            'days' => $days->map(fn ($d) => ['date' => $d->toDateString(), 'label' => $d->format('M j'), 'weekday' => $d->format('D')])->all(),
        ];
    }

    /**
     * Where a student stands on the course's final exam: still on the lessons, ready to take it,
     * taking it right now, passed, failed, caught cheating once, or banned (caught twice).
     */
    public static function finalState(?CrewQuizAttempt $attempt, int $lessonsPercent): string
    {
        return match (true) {
            $attempt?->inProgress() => 'taking',
            $attempt?->passed() => 'passed',
            $attempt?->banned() => 'banned',
            // Failed, redid every lesson, and can face the Final Boss again.
            $attempt !== null && FinalExamService::sentBack($attempt) && $lessonsPercent >= 100 => 'ready',
            $attempt?->cheated() => 'caught',
            $attempt !== null => 'failed',
            $lessonsPercent >= 100 => 'ready',
            default => 'studying',
        };
    }
}
