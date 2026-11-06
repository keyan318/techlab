<?php

namespace App\Http\Controllers;

use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Services\AvatarService;
use App\Services\CourseStructure;
use App\Services\LevelService;
use App\Services\StudentDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Student dashboard — overall learning progress across every planet, the
     * crew XP leaderboard and activity charts. A faculty member who lands here is
     * bounced to their own dashboard.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') === 'faculty') {
            return redirect(route('faculty.dashboard'));
        }

        // The daily login reward lands before the numbers are read, so today's XP already shows.
        StudentDashboardService::claimDailyReward(Auth::user());
        $data = StudentDashboardService::home(Auth::user());

        // Local preview only: with no crew yet the leaderboard would be empty, so show sample
        // classmates to see its design. Never in production, and gone once a real crew exists.
        if (app()->isLocal() && ! $data['leaderboard']) {
            $data['leaderboard'] = self::sampleLeaderboard(Auth::user(), $data);
            $data['leaderboardSample'] = true;
        }

        // A new rank's celebration comes first; the streak pop-up waits for the next visit.
        $data['rankUp'] = self::rankUp(Auth::user(), $data['level']);
        $data['streakPop'] = $data['rankUp'] ? null : self::streakPop(Auth::user(), $data['streak']);

        return view('student.dashboard', $data);
    }

    /**
     * Astro's streak pop-up: once a day, the first time the student lands here with a streak going.
     * It also shows the daily login XP (claimed in dashboard() on the day's first visit).
     * In a fresh first-login session it waits while Astro's home tour still has to play, so the two never pop up together.
     * Local only: /dashboard?streak=N previews an N-day streak (any day, any number of times).
     */
    private static function streakPop($user, int $streak): ?array
    {
        if (app()->isLocal() && request()->filled('streak')) {
            return StudentDashboardService::streakCard($user, (int) request('streak'), StudentDashboardService::DAILY_LOGIN_XP);
        }
        if ($streak < 1 || $user->streak_seen_on?->isToday()
            || (session('tour_fresh') && ! in_array('home', $user->tour_seen ?? [], true))) {
            return null;
        }
        $user->forceFill(['streak_seen_on' => today()])->save();

        return StudentDashboardService::streakCard($user, reward: StudentDashboardService::DAILY_LOGIN_XP);
    }

    /**
     * The "Rank up!" celebration: once per new rank, the first time the student lands here after reaching it
     * (users.rank_seen remembers the highest one shown). Like the streak pop-up, it waits while Astro's first home tour
     * still has to play. Local only: /dashboard?rankup=N previews reaching level N's rank.
     */
    private static function rankUp($user, int $level): ?array
    {
        if (app()->isLocal() && request()->filled('rankup')) {
            $level = max(1, (int) request('rankup'));

            return LevelService::rank($level) + ['frame' => LevelService::defaultFrame($level)];
        }
        $tier = LevelService::tierFor($level);
        if ($tier <= (int) $user->rank_seen
            || (session('tour_fresh') && ! in_array('home', $user->tour_seen ?? [], true))) {
            return null;
        }
        $user->forceFill(['rank_seen' => $tier])->save();

        return LevelService::rank($level) + ['frame' => LevelService::defaultFrame($level)];
    }

    /** XP, gems and streak for the badge in the lesson, lab and editor headers (fetched after the page loads). */
    public function stats(): JsonResponse
    {
        return response()->json(StudentDashboardService::chips(Auth::user()));
    }

    /** Made-up classmates for the local leaderboard preview, with the real student last. */
    private static function sampleLeaderboard($user, array $data): array
    {
        // [name, level, frame variant]: one of every few ranks, so the preview shows the frames and badges.
        $rows = [
            ['CosmicPaws42', 104, 'b'], ['TurboWoof17', 73, 'a'], ['NovaChirp88', 52, 'b'],
            ['LaserOink23', 34, 'default'], ['MegaMoo61', 18, 'a'], ['Sasa Dela Cruz', 4, 'default'],
        ];

        $board = [];
        foreach ($rows as $i => [$name, $level, $variant]) {
            mt_srand($i + 1); // the same astronauts on every reload
            $board[] = ['id' => -1 - $i, 'name' => $name, 'avatar' => $i === 5 ? null : AvatarService::random(),
                'xp' => LevelService::xpToReach($level), 'level' => $level, 'tier' => LevelService::rank($level),
                'frame' => LevelService::tiers()[LevelService::tierFor($level)]['key'].'-'.$variant, 'rank' => $i + 1, 'me' => false];
        }
        mt_srand();

        $board[] = ['id' => $user->id, 'name' => $user->display_name, 'avatar' => $user->avatar, 'xp' => 0, 'level' => $data['level'],
            'tier' => $data['tier'], 'frame' => $data['frame'], 'rank' => 7, 'me' => true];

        return $board;
    }

    /**
     * The full stats view: XP, lessons and modules done, weekly activity, performance,
     * every planet and the crew leaderboard. The dashboard links here.
     */
    public function progress(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') === 'faculty') {
            return redirect(route('faculty.dashboard'));
        }

        return view('student.progress', StudentDashboardService::build(Auth::user()));
    }

    /**
     * Planet picker (the onboarding track choice), formerly served at /dashboard.
     */
    public function planets(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') === 'faculty') {
            return redirect(route('faculty.dashboard'));
        }

        $nodes = [
            ['type' => 'completed', 'title' => 'System Bootcamp', 'sub' => 'Module 1 · Orientation'],
            ['type' => 'completed', 'title' => 'Variables & Types', 'sub' => 'Lesson 1'],
            ['type' => 'completed', 'title' => 'Control Flow', 'sub' => 'Lesson 2'],
            ['type' => 'milestone', 'title' => 'Module 1 Checkpoint', 'sub' => 'Project · Hello, Universe'],
            ['type' => 'completed', 'title' => 'Functions & Modules', 'sub' => 'Lesson 3'],
            ['type' => 'current',   'title' => 'Network Defense Basics', 'sub' => 'Lesson 4'],
            ['type' => 'locked',    'title' => 'Packet Analysis', 'sub' => 'Lesson 5'],
            ['type' => 'loot',      'title' => 'Crypto Capsule', 'sub' => 'Bonus practice'],
            ['type' => 'locked',    'title' => 'Firewalls', 'sub' => 'Lesson 6'],
            ['type' => 'locked',    'title' => 'Intrusion Detection', 'sub' => 'Lesson 7'],
            ['type' => 'milestone', 'title' => 'Module 2 Checkpoint', 'sub' => 'Project · Secure Net'],
            ['type' => 'locked',    'title' => 'Incident Response', 'sub' => 'Lesson 8'],
        ];

        // Progression data. Real values come from auth; level/XP/streak are
        // placeholders for now and can be wired to a Laravel model later.
        $student = [
            'name' => Auth::user()->name ?? 'Explorer',
            'level' => 7,
            'levelTitle' => 'Explorer',
            'xp' => 720,
            'xpForNext' => 1000,
            'streak' => 5,
            'dailyQuest' => ['label' => 'Complete 1 lesson', 'progress' => 0, 'goal' => 1],
            'achievements' => [
                ['icon' => '🏆', 'label' => 'First Mission'],
                ['icon' => '⚡', 'label' => 'Fast Learner'],
                ['icon' => '🔥', 'label' => '5 Day Streak'],
            ],
        ];

        // Progress for every planet the student is enrolled in, keyed by slug (missing = not enrolled).
        $planetProgress = StudentDashboardService::planets(Auth::user());

        return view('student.studentOnboarding', compact('nodes', 'student', 'planetProgress'));
    }

    /**
     * "My Courses" — every course this student is on the roster of.
     *
     * Reads the crew_members roster rather than the legacy users.crew_id pointer, so a
     * student enrolled in several courses sees all of them. Each one links into its hub.
     */
    public function courses(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') === 'faculty') {
            return redirect(route('faculty.dashboard'));
        }

        $courses = Auth::user()->crewMemberships()
            ->with('faculty')
            ->whereNotNull('planet')
            ->whereNotNull('course_slug')
            ->get()
            ->map(function (Crew $crew) {
                $meta = CourseStructure::card($crew->planet, $crew->course_slug);

                return [
                    'name' => $meta['title'] ?? $crew->name,
                    'blurb' => $meta['blurb'] ?? ($meta['description'] ?? ''),
                    'planet' => config("course-catalog.{$crew->planet}.title", ucfirst($crew->planet)),
                    'banner' => $meta['banner'] ?? '#1b1650',
                    'art' => $meta['art'] ?? null,
                    'scene' => $meta['scene'] ?? null,
                    'captain' => $crew->faculty?->name,
                    'self_paced' => $crew->isSelfPaced(),
                    'url' => route('student.course.overview', ['slug' => $crew->planet, 'course' => $crew->course_slug]),
                ];
            })
            ->values()
            ->all();

        return view('student.my-courses', compact('courses'));
    }

    /**
     * Join a crew by its invite code.
     */
    public function joinCrew(Request $request): RedirectResponse|JsonResponse
    {
        if (! Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Please log in first.'], 401)
                : redirect(route('login'));
        }

        $data = $request->validate(['code' => ['required', 'string']]);
        $crew = Crew::where('code', strtoupper(trim($data['code'])))->first();

        if (! $crew) {
            $message = 'That crew code was not found. Check with your captain.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->withInput()->withErrors(['code' => $message]);
        }

        // A course's code only works for a student its faculty member has accepted,
        // so a code shared around a group chat lets nobody else in.
        $user = Auth::user();
        if ($crew->course_slug && ($user->role ?? 'student') === 'student' && ! $user->belongsToCrew($crew->id)
            && ! $crew->joinRequests()->where('user_id', $user->id)->where('status', CourseJoinRequest::ACCEPTED)->exists()) {
            $message = "Ask to join {$crew->name} first. Your code works once ".($crew->faculty?->name ?? 'the faculty member').' accepts you.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->withInput()->withErrors(['code' => $message]);
        }

        $crew->admit(Auth::user());

        // Land them straight in the course they just unlocked, not on a generic list.
        $destination = $crew->planet && $crew->course_slug
            ? route('student.course.overview', ['slug' => $crew->planet, 'course' => $crew->course_slug])
            : route('student.courses');

        return $request->expectsJson()
            ? response()->json(['redirect' => $destination])
            : redirect($destination);
    }

    /**
     * TechLab Chat — the main student interface after login.
     *
     * This is the post-login destination. The actual AI conversation, persistent
     * chat history, and crew functionality are not built yet; the page renders a
     * ChatGPT-style shell with placeholder history so the UI/navigation is ready.
     * Students who land here are bounced to the faculty member dashboard if misrouted.
     */
    public function chat(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }

        if ((Auth::user()->role ?? 'student') === 'faculty') {
            return redirect(route('faculty.dashboard'));
        }

        // PLACEHOLDER chat history. Replace with a real query (e.g. auth()->user()
        // ->chats()->latest()) once persistence exists. Empty array → empty state.
        $chatHistory = [
            'Introduction to Networking',
            'What is an IP Address?',
            'Help with my Lab Activity',
            'Explain TCP/IP',
        ];

        return view('student.chat', compact('chatHistory'));
    }
}
