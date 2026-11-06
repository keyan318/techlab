<?php

namespace App\Services;

use App\Models\AiUsage;
use App\Models\Crew;
use App\Models\CrewQuizAttempt;
use App\Models\DailyReward;
use App\Models\GemExchange;
use App\Models\GemSpend;
use App\Models\LessonProgress;
use App\Models\QuizAnswer;
use App\Models\ShopPurchase;
use App\Models\StudyActivity;
use App\Models\User;
use App\Models\XpSpend;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    /** The daily login reward: the first visit of each day pays this much (claimDailyReward). */
    public const DAILY_LOGIN_XP = 10;

    /** Gems a student earns for finishing every lesson in a module. */
    public const GEMS_PER_MODULE = 10;

    /** A correct first answer on a lesson quiz. A 3-question quiz is worth 30, so a lesson's coding challenge (100) stays the big prize. */
    public const XP_PER_QUIZ_QUESTION = 10;

    /** Every lesson ends with a 5-question quiz. */
    public const QUIZ_QUESTIONS_PER_LESSON = 5;

    /** The most one lesson can pay: finishing it plus every quiz question right first time (100 + 5 x 10 = 150). */
    public const MAX_XP_PER_LESSON = self::XP_PER_LESSON + self::QUIZ_QUESTIONS_PER_LESSON * self::XP_PER_QUIZ_QUESTION;

    /** Price of Astro's hint on an exercise. Bought once per lesson; half of what finishing that lesson pays, so it's a real choice. */
    public const XP_HINT_COST = 50;

    /**
     * Price of Astro's live lab-help chat (Citadel Sim). Bought once per lab attempt, then the student can ask
     * follow-up questions for free. Deliberately steeper than the static hint — Astro can see the student's
     * exact terminal transcript here, so it's real diagnostic help, not a canned tip, and should cost accordingly.
     */
    public const XP_LAB_HELP_COST = 60;

    /**
     * What Astro's AI costs once the day's free uses are gone. A chat message costs 20
     * (so one lesson buys 5 messages); a Studio tool is a bigger AI job, so it costs more.
     */
    public const AI_COSTS = [
        'chat' => 20,
        'chat_file' => 30,      // a message with a file attached: Astro has to read it too
        'analogy' => 20,
        'keyterms' => 20,
        'flashcards' => 30,
        'quiz' => 30,
        'infographic' => 40,
        'report' => 40,
    ];

    /** Free uses per day, so a brand-new student with 0 XP can still talk to Astro. */
    public const AI_FREE_CHATS_PER_DAY = 10;

    public const AI_FREE_STUDIO_PER_DAY = 1;

    /**
     * The streak flame grows up every 10 days: orange, then redder and redder, then violet, then blue.
     * Colours run outline → base → mid → light → core (the pixel flame on the streak pop-up; the
     * small flame icon uses base, mid and core).
     */
    public const STREAK_TIERS = [
        ['name' => 'Spark',   'colors' => ['#7a1d06', '#e0510f', '#ff8a1f', '#ffb02e', '#ffd23a']],
        ['name' => 'Ember',   'colors' => ['#5c0b05', '#c8240f', '#ef4a1c', '#ff7d45', '#ffc58f']],
        ['name' => 'Blaze',   'colors' => ['#43020a', '#a50a16', '#e0141f', '#ff4646', '#ffaaa0']],
        ['name' => 'Inferno', 'colors' => ['#3d0430', '#a0104e', '#e0207a', '#ff5fae', '#ffd0ea']],
        ['name' => 'Plasma',  'colors' => ['#1e0850', '#5420c4', '#8a4bff', '#b98cff', '#efe4ff']],
        ['name' => 'Nova',    'colors' => ['#061c5c', '#1648d8', '#2f7bff', '#6ab8ff', '#e0f6ff']],
    ];

    public const STREAK_DAYS_PER_TIER = 10;

    /** Planets shown on the dashboard. Only those with a config blueprint have trackable lessons. */
    /** Each planet's pixel-art banner, shared with the planet picker. */
    private const PLANET_ART = [
        'programming' => 'images/programming-city-pixel.png',
        'networking' => 'images/networking-nebula.png',
        'cybersecurity' => 'images/cybersecurity-citadel.png',
    ];

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
        $totals = self::totals($user);

        return self::xpFor($totals['lessons'], $totals['quizXp'], $totals['spent']);
    }

    /**
     * The inputs to a student's XP — lessons done, quiz XP, XP spent (hints, lab help, Astro's AI, the Shop) — in one
     * round trip instead of several: the database is remote, so each query costs real time. XP from trading gems at
     * the Shop counts as negative spending: it adds to the balance but is not earned, so it never raises the level.
     *
     * @return array{lessons: int, quizXp: int, spent: int, gemsSpent: int}
     */
    private static function totals(User $user): array
    {
        $row = DB::query()
            ->selectSub($user->lessonProgress()->withoutGlobalScope('active')->selectRaw(LessonProgress::XP_COUNT), 'lessons')
            ->selectSub(QuizAnswer::where('user_id', $user->id)->selectRaw('coalesce(sum(xp), 0)'), 'quiz_xp')
            ->selectSub(CrewQuizAttempt::where('user_id', $user->id)->selectRaw('coalesce(sum(xp), 0)'), 'boss_xp')
            ->selectSub(DailyReward::where('user_id', $user->id)->selectRaw('coalesce(sum(xp), 0)'), 'daily_xp')
            ->selectSub(XpSpend::where('user_id', $user->id)->selectRaw('coalesce(sum(cost), 0)'), 'spent')
            ->selectSub(AiUsage::where('user_id', $user->id)->selectRaw('coalesce(sum(cost), 0)'), 'ai_spent')
            ->selectSub(ShopPurchase::where('user_id', $user->id)->selectRaw('coalesce(sum(cost), 0)'), 'shop_spent')
            ->selectSub(GemSpend::where('user_id', $user->id)->selectRaw('coalesce(sum(cost), 0)'), 'gems_spent')
            ->selectSub(GemExchange::where('user_id', $user->id)->selectRaw('coalesce(sum(gems), 0)'), 'gems_traded')
            ->selectSub(GemExchange::where('user_id', $user->id)->selectRaw('coalesce(sum(xp), 0)'), 'xp_traded')
            ->first();

        // Mini boss XP and the daily login reward count with the quiz XP: all earned, so they raise the level too.
        return ['lessons' => (int) $row->lessons, 'quizXp' => (int) $row->quiz_xp + (int) $row->boss_xp + (int) $row->daily_xp,
            'spent' => (int) $row->spent + (int) $row->ai_spent + (int) $row->shop_spent - (int) $row->xp_traded,
            'gemsSpent' => (int) $row->gems_spent + (int) $row->gems_traded];
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
        ['lessons' => $lessonsEver, 'quizXp' => $quizXp, 'spent' => $spent] = self::totals($user);
        // Every lesson ever finished counts toward the level, shelved tracks too (the same as earnedXp()).
        ['level' => $level, 'into' => $into, 'needed' => $needed] = LevelService::progress($lessonsEver * self::XP_PER_LESSON + $quizXp);

        return [
            'user' => $user,
            'xp' => self::xpFor($lessonsDone, $quizXp, $spent),
            'level' => $level,
            // Total XP earned so far, for the "to Level N" bar (same number as the XP stat above).
            'levelXp' => $into,
            'xpPerLevel' => $needed,
            'tier' => LevelService::rank($level),
            'frame' => LevelService::wornFrame($user->frame, $level),
            'streak' => self::streak($user, $progress),
            'lessonsDone' => $lessonsDone,
            'lessonsTotal' => $lessonsTotal,
            'modulesDone' => $modulesDone,
            'modulesTotal' => $modulesTotal,
            'overall' => $lessonsTotal > 0 ? (int) round($lessonsDone / $lessonsTotal * 100) : 0,
            'planets' => $planets,
            'current' => self::currentLesson($user, $progress),
            'crew' => $user->crew_id ? Crew::find($user->crew_id) : null,
            'leaderboard' => self::leaderboard($user, $boardPlanet = self::leaderboardPlanet($user)),
            'leaderboardTitle' => $boardPlanet ? config("course-catalog.{$boardPlanet}.title", ucfirst($boardPlanet)) : null,
            'weekly' => self::weeklyActivity($user, $activity, $progress),
            'performance' => self::performance($user, 8, 400, 120, $progress, $activity),
        ];
    }

    /** XP earned, ignoring what was spent on hints, so buying a hint never lowers a student's level. */
    public static function earnedXp(User $user): int
    {
        $totals = self::totals($user);

        return $totals['lessons'] * self::XP_PER_LESSON + $totals['quizXp'];
    }

    public static function levelOf(User $user): int
    {
        return LevelService::levelFor(self::earnedXp($user));
    }

    /** Gems the student can spend: GEMS_PER_MODULE per finished module, minus what went on suits (before the Shop) and XP trades. */
    public static function gems(User $user): int
    {
        $earned = array_sum(array_column(self::planets($user), 'modulesDone')) * self::GEMS_PER_MODULE;

        return max(0, $earned - (int) GemSpend::where('user_id', $user->id)->sum('cost') - (int) GemExchange::where('user_id', $user->id)->sum('gems'));
    }

    /**
     * The XP / gems / streak badge on lesson, lab and editor pages: the same numbers as the home page,
     * from as few queries as possible (lesson rows once, the totals in one round trip, study days).
     *
     * @return array{xp: int, gems: int, streak: int}
     */
    public static function chips(User $user): array
    {
        $progress = LessonProgress::where('user_id', $user->id)->get(['course', 'module', 'lesson', 'completed_at']);
        $planets = self::planets($user, $progress);
        ['quizXp' => $quizXp, 'spent' => $spent, 'gemsSpent' => $gemsSpent] = self::totals($user);

        return [
            'xp' => self::xpFor(array_sum(array_column($planets, 'completed')), $quizXp, $spent),
            'gems' => max(0, array_sum(array_column($planets, 'modulesDone')) * self::GEMS_PER_MODULE - $gemsSpent),
            'streak' => self::streak($user, $progress),
        ];
    }

    public static function levelFor(int $earnedXp): int
    {
        return LevelService::levelFor($earnedXp);
    }

    /**
     * What the landing dashboard shows: the "Jump back in" course, the profile rail stats.
     */
    public static function home(User $user): array
    {
        $progress = LessonProgress::where('user_id', $user->id)->get(['course', 'module', 'lesson', 'completed_at']);
        $planets = self::planets($user, $progress);

        $lessonsDone = array_sum(array_column($planets, 'completed'));
        ['lessons' => $lessonsEver, 'quizXp' => $quizXp, 'spent' => $spent, 'gemsSpent' => $gemsSpent] = self::totals($user);
        $level = LevelService::levelFor($lessonsEver * self::XP_PER_LESSON + $quizXp);

        $current = self::currentLesson($user, $progress);
        // The whole planet's leaderboard: the planet of the course the student is on.
        $boardPlanet = $current['planet'] ?? ($user->enrolledPlanets()[0] ?? null);
        $board = self::leaderboard($user, $boardPlanet);
        $me = collect($board)->firstWhere('me', true);

        return [
            'user' => $user,
            'xp' => self::xpFor($lessonsDone, $quizXp, $spent),
            'level' => $level,
            'tier' => LevelService::rank($level),
            'frame' => LevelService::wornFrame($user->frame, $level),
            'rank' => $me['rank'] ?? null,
            'leaderboard' => $board,
            'leaderboardTitle' => $boardPlanet ? config("course-catalog.{$boardPlanet}.title", ucfirst($boardPlanet)) : null,
            // Gems to spend: earned (GEMS_PER_MODULE per finished module) minus what went on suits. The leaderboard ranks by gems earned.
            'badges' => max(0, array_sum(array_column($planets, 'modulesDone')) * self::GEMS_PER_MODULE - $gemsSpent),
            'streak' => self::streak($user, $progress),
            // A finished course's final exam comes first; otherwise, the next lesson to do.
            'hero' => self::finalBoss($user, $progress) ?? self::hero($user, $planets, $current),
        ];
    }

    /**
     * The Final Boss card: when every lesson of the course the student worked on last is done, the
     * home page points at that course's final exam instead of "All caught up" or another course.
     * Null once the exam is beaten (or when the course has no crew this student is on), so
     * the usual card comes back.
     */
    private static function finalBoss(User $user, Collection $progress): ?array
    {
        $key = $progress->sortByDesc('completed_at')->first()?->course;
        if (! $key || ! CourseProgressService::exists($key) || self::nextLessonIn($key, $progress)) {
            return null;
        }

        $planet = CourseProgressService::planetFor($key);
        $id = CourseProgressService::catalogFor($key);
        $course = $id ? config("course-catalog.{$planet}.courses.{$id}") : null;
        $crew = $course ? Crew::forStudent($user, $planet, $id) : null;
        // Self-paced with a mini boss still to beat: that comes first (the course page shows it).
        if (! $crew || ! MiniBossService::allCleared($user, $crew)) {
            return null;
        }

        ['exam' => $exam, 'attempt' => $attempt] = FinalExamService::for($user, $crew);
        $state = FinalExamService::state($exam, $attempt, true);
        if ($state === 'passed') {
            return null;
        }

        return [
            'view' => 'boss',
            'state' => $state,
            'courseTitle' => $course['title'],
            'banner' => $course['banner'] ?? '#1b1650',
            'scene' => $course['scene'] ?? null,
            'art' => $course['art'] ?? null,
            'started' => true,
            'title' => $exam?->title,
            'questions' => $exam?->questions_count,
            'minutes' => $exam?->minutes,
            'passPercent' => $exam?->passPercent(),
            'percent' => $attempt?->percent(),
            'violation' => $attempt?->violationText(),
            'banned' => (bool) $attempt?->banned(),
            'captainName' => $crew->faculty?->name ?? ($crew->isSelfPaced() ? "TechLab's admin" : 'your captain'),
            'url' => route('student.course.final-exam', ['slug' => $planet, 'course' => $id]),
            'overviewUrl' => route('student.course.overview', ['slug' => $planet, 'course' => $id]),
        ];
    }

    /**
     * The first course on a planet that is really open (not "coming soon" or hidden), with its catalog entry.
     */
    public static function catalogCourse(string $slug): ?array
    {
        foreach (config("course-catalog.{$slug}.courses", []) as $id => $course) {
            if (empty($course['coming_soon']) && empty($course['hidden'])) {
                return $course + ['id' => $id];
            }
        }

        return null;
    }

    /**
     * The card behind "Jump back in": the course the student is in the middle of, or null
     * when there is nothing left to continue.
     *
     * The dashboard never presumes a specific course. Until the student has actually
     * started one — real LessonProgress rows, which for a captained course can only
     * exist once they've been accepted and redeemed the code — this card sends them to
     * the planet instead, the same place the "Ask to join" flow lives
     * (PlanetController::courses). Once they've started, it resumes exactly that
     * course, but still re-checks crew membership: a course that was open when they
     * started can be claimed by a captain later.
     */
    private static function hero(User $user, array $planets, ?array $current): ?array
    {
        // The course the lesson really belongs to — a planet can hold several (and hide some).
        $id = $current ? CourseProgressService::catalogFor($current['key']) : null;
        $course = $id ? CourseStructure::card($current['planet'], $id) + ['id' => $id] : null;

        if (! $current || ! $course) {
            return null;
        }

        $planet = $planets[$current['planet']];
        // The student's track for this course (self-paced or a class), if they're on one.
        $crew = Crew::forStudent($user, $current['planet'], $course['id']);
        $isMember = $crew !== null;
        // Accepted into a faculty member's class: the hero says so, with whose class it is —
        // once. The update only hits a row that hasn't shown it yet, so it doubles as the check.
        $joined = $crew && ! $crew->isSelfPaced()
            && DB::table('crew_members')->where('crew_id', $crew->id)->where('user_id', $user->id)
                ->whereNull('accepted_seen_at')->update(['accepted_seen_at' => now()])
            ? ['captain' => $crew->faculty?->name ?? 'your captain'] : null;

        if (! $isMember || $planet['completed'] === 0) {
            return [
                'view' => 'planet',
                'locked' => ! $isMember,
                'joined' => $joined,
                // Not on the course yet: the hero offers Self-paced and Join Class right there.
                'enroll' => $isMember ? null : CourseEnrollmentService::forCard($user, $current['planet'], $course['id']),
                'planetSlug' => $current['planet'],
                'planetTitle' => config("course-catalog.{$current['planet']}.title", $planet['name'] ?? $current['planet']),
                'planetBlurb' => config("course-catalog.{$current['planet']}.description", ''),
                'banner' => $course['banner'] ?? '#1b1650',
                'scene' => $course['scene'] ?? null,
                // Not in a course yet, so show the planet's own pixel art (same as the planet picker).
                'art' => self::PLANET_ART[$current['planet']] ?? null,
                'courseTitle' => $course['title'],
                'planetUrl' => route('student.planet', $current['planet']),
            ];
        }

        return [
            'view' => 'course',
            'locked' => false,
            'joined' => $joined,
            'title' => $course['title'],
            'blurb' => $course['blurb'] ?? '',
            'banner' => $course['banner'] ?? '#1b1650',
            'scene' => $course['scene'] ?? null,
            // The course being learned right now, so the card always shows its picture.
            'art' => $course['art'] ?? null,
            'percent' => $planet['percent'],
            'started' => true,
            'next' => $current['title'],
            'continueUrl' => $current['url'],
            'overviewUrl' => route('student.course.overview', ['slug' => $current['planet'], 'course' => $course['id']]),
        ];
    }

    /**
     * Consecutive days of learning up to today. A day counts when the student studied
     * (active minutes) or finished a lesson. Not having studied *yet* today doesn't break the streak.
     */
    public static function streak(User $user, ?Collection $progress = null, ?Collection $activity = null): int
    {
        $progress ??= LessonProgress::where('user_id', $user->id)->get(['completed_at']);
        $activity ??= StudyActivity::where('user_id', $user->id)->where('minutes', '>', 0)->get(['date']);

        $days = $activity
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

    /** Which flame a streak of this many days has earned (an index into STREAK_TIERS). */
    public static function streakTier(int $days): int
    {
        return min(count(self::STREAK_TIERS) - 1, intdiv(max(0, $days), self::STREAK_DAYS_PER_TIER));
    }

    /**
     * Everything the streak pop-up shows: the streak, the longest one ever, whether today already
     * counts, this week's days (Sunday first, like the weekly chart) and the flame. "evolved" is true
     * on the day a streak reaches a new flame (10, 20, …).
     *
     * $preview (local only, ?streak=N on the dashboard) fakes an N-day streak ending today to try the flames.
     */
    /**
     * The daily login reward: the first time the student shows up on a day, DAILY_LOGIN_XP lands in their XP.
     * Returns the XP just given, or 0 if today's was already claimed. The unique (user_id, date) key keeps two
     * tabs opening at once from paying twice.
     */
    public static function claimDailyReward(User $user): int
    {
        $now = now();
        $added = DailyReward::insertOrIgnore(['user_id' => $user->id, 'date' => $now->toDateString(), 'xp' => self::DAILY_LOGIN_XP,
            'created_at' => $now, 'updated_at' => $now]);

        return $added ? self::DAILY_LOGIN_XP : 0;
    }

    public static function streakCard(User $user, ?int $preview = null, int $reward = 0): array
    {
        $progress = LessonProgress::where('user_id', $user->id)->whereNotNull('completed_at')->get(['completed_at']);
        $activity = StudyActivity::where('user_id', $user->id)->where('minutes', '>', 0)->get(['date']);

        $days = $activity->toBase()->map(fn ($a) => $a->date->toDateString())
            ->merge($progress->toBase()->map(fn ($r) => $r->completed_at->toDateString()))
            ->unique()->sort()->values();

        $longest = 0;
        $run = 0;
        $prev = null;
        foreach ($days as $d) {
            $run = $prev && Carbon::parse($prev)->addDay()->toDateString() === $d ? $run + 1 : 1;
            $longest = max($longest, $run);
            $prev = $d;
        }

        $count = self::streak($user, $progress, $activity);
        $done = $days->flip();
        $today = now()->toDateString();
        if ($preview !== null) {
            $count = max(1, $preview);
            $done = collect(range(0, $count - 1))->mapWithKeys(fn ($i) => [now()->subDays($i)->toDateString() => true]);
        }
        $todayDone = $done->has($today);

        $start = now()->startOfWeek(Carbon::SUNDAY);
        $week = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $start->copy()->addDays($i);
            $week[] = [
                'label' => $d->format('D')[0],
                'name' => $d->format('l'),
                'done' => $done->has($d->toDateString()),
                'today' => $d->isToday(),
                'future' => $d->isFuture() && ! $d->isToday(),
            ];
        }

        $tier = self::streakTier($count);
        $last = count(self::STREAK_TIERS) - 1;

        return [
            'count' => $count,
            'longest' => max($longest, $count),
            'todayDone' => $todayDone,
            'week' => $week,
            'tier' => $tier,
            'evolved' => $todayDone && $tier > 0 && $count === $tier * self::STREAK_DAYS_PER_TIER,
            'nextName' => $tier < $last ? self::STREAK_TIERS[$tier + 1]['name'] : null,
            'nextIn' => $tier < $last ? ($tier + 1) * self::STREAK_DAYS_PER_TIER - $count : null,
            'tiers' => self::STREAK_TIERS,
            'reward' => $reward,
        ];
    }

    /**
     * Per-planet progress. A planet with no blueprint in config/course-structure.php
     * has no tracked lessons yet — it is reported as untracked rather than as 0 of 0.
     *
     * A planet's second+ course (e.g. computer-programming-1, which shares the
     * "programming" planet with Python) is not shown as its own card — its lessons
     * and completions are folded into the one planet card, via
     * CourseProgressService::keysForPlanet(), so "Programming" reflects both courses.
     */
    public static function planets(User $user, ?Collection $progress = null): array
    {
        $progress ??= LessonProgress::where('user_id', $user->id)->get(['course', 'module', 'lesson']);
        $out = [];

        foreach (self::PLANETS as $slug => $meta) {
            if (! $user->isEnrolledIn($slug)) {
                continue;
            }

            // Every course on this planet shares module ids ("m1", "m2", ...), so
            // completions and module grouping are qualified by course, not module
            // alone — otherwise Python's M1 and the C course's M1 would collide.
            $planetCourses = CourseProgressService::keysForPlanet($slug);
            $order = collect($planetCourses)
                ->flatMap(fn ($key) => collect(CourseProgressService::order($key))->map(fn ($i) => $i + ['course' => $key]))
                ->all();
            $keys = $progress->whereIn('course', $planetCourses)
                ->map(fn ($r) => $r->course.'/'.$r->module.'/'.$r->lesson)->flip();
            $modules = collect($order)->groupBy(fn ($i) => $i['course'].'/'.$i['module']);

            $completed = collect($order)->filter(fn ($i) => $keys->has($i['course'].'/'.$i['module'].'/'.$i['lesson']))->count();
            $modulesDone = $modules->filter(fn ($lessons) => $lessons->every(fn ($i) => $keys->has($i['course'].'/'.$i['module'].'/'.$i['lesson'])))->count();
            $total = count($order);

            $out[$slug] = $meta + [
                'slug' => $slug,
                'art' => self::PLANET_ART[$slug] ?? null,
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

        // Only courses on planets the student enrolled in, with a lesson blueprint, can be continued
        // (keysForPlanet() already leaves hidden courses out).
        $open = collect($user->enrolledPlanets())
            ->flatMap(fn ($p) => CourseProgressService::keysForPlanet($p))
            ->filter(fn ($key) => CourseProgressService::exists($key))
            ->values()->all();
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

        foreach (CourseStructure::modules($course) as $mKey => $module) {
            foreach ($module['lessons'] as $lKey => $lesson) {
                if (CourseProgressService::normalize($mKey, $lKey) === [$next['module'], $next['lesson']]) {
                    return [
                        'planet' => CourseProgressService::planetFor($course),
                        'key' => $course,
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
     * The planet leaderboard (home and progress pages): every student taking a course on this planet
     * (Self-paced or any class), ranked by level. Without a planet, it's the planet of the student's current course.
     */
    public static function leaderboard(User $user, ?string $planet = null): array
    {
        $planet ??= self::leaderboardPlanet($user);
        if (! $planet) {
            return [];
        }

        $students = User::where('role', 'student')
            ->whereHas('crewMemberships', fn ($c) => $c->where('crews.planet', $planet))
            ->get();

        return self::rank($students, $user);
    }

    /** The planet the home leaderboard shows: the one the student's current course is on. */
    public static function leaderboardPlanet(User $user): ?string
    {
        return self::currentLesson($user)['planet'] ?? ($user->enrolledPlanets()[0] ?? null);
    }

    /** One crew's leaderboard (the course hub's Crew tab): the crew's students, ranked by level. */
    public static function crewLeaderboard(Crew $crew, User $user): array
    {
        $students = $crew->roster()->wherePivot('role', 'student')->get();

        return $students->isEmpty() ? [] : self::rank($students, $user);
    }

    /**
     * XP earned by each of these students (lessons once each, shelved tracks too, plus quiz and mini boss XP),
     * in one round trip for the whole board.
     *
     * @param  list<int>  $ids
     * @return array<int, int>
     */
    public static function earnedXpFor(array $ids): array
    {
        if (! $ids) {
            return [];
        }

        $rows = LessonProgress::withoutGlobalScope('active')->whereIn('user_id', $ids)->groupBy('user_id')
            ->selectRaw('user_id, '.LessonProgress::XP_COUNT.' * '.self::XP_PER_LESSON.' as xp')
            ->toBase()
            ->unionAll(QuizAnswer::whereIn('user_id', $ids)->groupBy('user_id')->selectRaw('user_id, sum(xp) as xp')->toBase())
            ->unionAll(CrewQuizAttempt::whereIn('user_id', $ids)->groupBy('user_id')->selectRaw('user_id, sum(xp) as xp')->toBase())
            ->unionAll(DailyReward::whereIn('user_id', $ids)->groupBy('user_id')->selectRaw('user_id, sum(xp) as xp')->toBase())
            ->get();

        $xp = array_fill_keys($ids, 0);
        foreach ($rows as $row) {
            $xp[$row->user_id] += (int) $row->xp;
        }

        return $xp;
    }

    /**
     * Level first, then XP earned (it never goes down, so spending can't drop anyone); only a tie on both shares
     * a rank (then alphabetical). Each row carries the student's rank badge and the frame they wear.
     */
    public static function rank(Collection $students, ?User $user = null, ?array $earned = null): array
    {
        $earned ??= self::earnedXpFor($students->modelKeys());

        $rows = $students->map(function (User $u) use ($earned) {
            $xp = $earned[$u->id] ?? 0;
            $level = LevelService::levelFor($xp);

            return ['id' => $u->id, 'name' => $u->display_name, 'avatar' => $u->avatar, 'xp' => $xp, 'level' => $level,
                'tier' => LevelService::rank($level), 'frame' => LevelService::wornFrame($u->frame, $level)];
        })->sortBy([['level', 'desc'], ['xp', 'desc'], ['name', 'asc']])->values();

        $rank = 0;
        $prev = null;

        return $rows->map(function ($row, $i) use ($user, &$rank, &$prev) {
            $score = [$row['level'], $row['xp']];
            if ($score !== $prev) {
                $rank = $i + 1;
                $prev = $score;
            }

            return $row + ['rank' => $rank, 'me' => $row['id'] === $user?->id];
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
            $mins = (int) $minutes->get($d->toDateString(), 0);
            $days[] = [
                'label' => $d->format('D')[0],
                'name' => $d->format('l'),
                'minutes' => $mins,
                'today' => $d->isToday(),
                // Counts toward the streak, the same rule as streak(): studied or finished a lesson that day.
                'active' => $mins > 0 || $progress->contains(fn ($r) => $r->completed_at?->isSameDay($d)),
                'future' => $d->isFuture() && ! $d->isToday(),
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

        QuizAnswer::where('user_id', $user->id)->where('xp', '>', 0)->where('created_at', '>=', $first)->select(['xp', 'created_at'])
            ->unionAll(DailyReward::where('user_id', $user->id)->where('created_at', '>=', $first)->select(['xp', 'created_at']))
            ->get()
            ->each(function ($row) use (&$xp, $first, $weeks) {
                $i = (int) floor($first->diffInDays($row->created_at->copy()->startOfDay(), false) / 7);
                if ($i >= 0 && $i < $weeks) {
                    $xp[$i] += $row->xp;
                }
            });

        // Hints, lab help and Astro's AI, in one round trip.
        XpSpend::where('user_id', $user->id)->where('created_at', '>=', $first)->select(['cost', 'created_at'])
            ->unionAll(AiUsage::where('user_id', $user->id)->where('cost', '>', 0)->where('created_at', '>=', $first)->select(['cost', 'created_at']))
            ->get()
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
