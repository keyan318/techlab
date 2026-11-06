<?php

namespace App\Services;

use App\Models\LessonProgress;
use App\Models\StudyActivity;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The numbers behind the admin dashboard's charts: sign-ups and lessons finished week by week,
 * who studied this week, and each planet's leaderboard. Kept to a handful of queries because
 * every round trip to the database costs real time.
 */
class AdminAnalyticsService
{
    /** How many weeks the charts look back. */
    public const WEEKS = 12;

    /** The planets the dashboard charts, in catalog order. */
    public static function planets(): array
    {
        return collect(config('course-catalog'))->map(fn (array $p) => $p['title'])->all();
    }

    public static function build(): array
    {
        $start = now()->startOfWeek()->subWeeks(self::WEEKS - 1);
        $thisWeek = now()->startOfWeek();
        $lastWeek = $thisWeek->copy()->subWeek();
        $week = fn (Carbon $at) => intdiv((int) $start->diffInDays($at), 7);

        // People: one query for everyone, counted by role and week of sign-up.
        $people = User::whereIn('role', ['student', 'faculty'])->get(['id', 'role', 'created_at']);
        $growth = [];
        foreach (['student', 'faculty'] as $role) {
            $mine = $people->where('role', $role);
            $before = $mine->filter(fn (User $u) => $u->created_at < $start)->count();
            $perWeek = array_fill(0, self::WEEKS, 0);
            foreach ($mine as $u) {
                if ($u->created_at >= $start) {
                    $perWeek[min(self::WEEKS - 1, $week($u->created_at))]++;
                }
            }
            $running = $before;
            $growth[$role] = [
                'total' => $mine->count(),
                'perWeek' => $perWeek,
                'cumulative' => array_map(function (int $n) use (&$running) {
                    return $running += $n;
                }, $perWeek),
                'thisWeek' => $perWeek[self::WEEKS - 1],
                'lastWeek' => $perWeek[self::WEEKS - 2],
            ];
        }

        // Lessons finished, per planet per week (shelved tracks count too: the work was done).
        $planets = array_keys(self::planets());
        $lessons = array_fill_keys($planets, array_fill(0, self::WEEKS, 0));
        $recent = LessonProgress::withoutGlobalScope('active')->where('completed_at', '>=', $start)->get(['course', 'completed_at']);
        foreach ($recent as $row) {
            $planet = CourseProgressService::planetFor($row->course);
            if (isset($lessons[$planet])) {
                $lessons[$planet][min(self::WEEKS - 1, $week($row->completed_at))]++;
            }
        }
        $lessonsPerWeek = array_map(fn (int $i) => array_sum(array_column($lessons, $i)), range(0, self::WEEKS - 1));

        $active = StudyActivity::where('date', '>=', now()->subDays(6)->toDateString())->distinct()->count('user_id');

        return [
            'labels' => array_map(fn (int $i) => $start->copy()->addWeeks($i)->format('M j'), range(0, self::WEEKS - 1)),
            'students' => $growth['student'],
            'faculty' => $growth['faculty'],
            'lessons' => [
                'byPlanet' => $lessons,
                'perWeek' => $lessonsPerWeek,
                'total' => LessonProgress::withoutGlobalScope('active')->count(),
                'thisWeek' => $lessonsPerWeek[self::WEEKS - 1],
                'lastWeek' => $lessonsPerWeek[self::WEEKS - 2],
            ],
            'active' => $active,
            'boards' => self::boards($planets),
        ];
    }

    /**
     * Every planet's leaderboard (students on any of its courses, ranked like the student home page)
     * with the XP for all of them fetched in one go.
     *
     * @return array<string, array{count: int, rows: array}>
     */
    private static function boards(array $planets): array
    {
        $members = DB::table('crew_members')->join('crews', 'crews.id', '=', 'crew_members.crew_id')
            ->where('crew_members.role', 'student')->distinct()->get(['crew_members.user_id', 'crews.planet']);
        $students = User::where('role', 'student')->whereIn('id', $members->pluck('user_id')->unique())->get()->keyBy('id');
        $earned = StudentDashboardService::earnedXpFor($students->keys()->all());

        return collect($planets)->mapWithKeys(function (string $planet) use ($members, $students, $earned) {
            $onPlanet = $students->only($members->where('planet', $planet)->pluck('user_id')->all())->values();

            // The admin sees who is behind each codename (students' own boards show the codename only).
            $rows = array_map(fn ($row) => $row + ['real_name' => $students[$row['id']]->name],
                StudentDashboardService::rank($onPlanet, null, $earned));

            return [$planet => ['count' => $onPlanet->count(), 'rows' => $rows]];
        })->all();
    }
}
