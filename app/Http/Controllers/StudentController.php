<?php

namespace App\Http\Controllers;

use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Services\AvatarService;
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

        $data = StudentDashboardService::home(Auth::user());

        // Local preview only: with no crew yet the leaderboard would be empty, so show sample
        // classmates to see its design. Never in production, and gone once a real crew exists.
        if (app()->isLocal() && ! $data['leaderboard']) {
            $data['leaderboard'] = self::sampleLeaderboard(Auth::user(), $data);
            $data['leaderboardSample'] = true;
        }

        return view('student.dashboard', $data);
    }

    /** Made-up classmates for the local leaderboard preview, with the real student last. */
    private static function sampleLeaderboard($user, array $data): array
    {
        $rows = [
            ['CosmicPaws42', 1250, 5, 12], ['TurboWoof17', 1100, 4, 9], ['NovaChirp88', 1100, 4, 3],
            ['LaserOink23', 870, 3, 6], ['MegaMoo61', 640, 2, 2], ['Sasa Dela Cruz', 300, 1, 1],
        ];

        $board = [];
        foreach ($rows as $i => [$name, $xp, $gems, $streak]) {
            mt_srand($i + 1); // the same astronauts on every reload
            $board[] = ['id' => -1 - $i, 'name' => $name, 'avatar' => $i === 5 ? null : AvatarService::random(),
                'xp' => $xp, 'gems' => $gems, 'streak' => $streak, 'rank' => $i + 1, 'me' => false];
        }
        mt_srand();

        $board[] = ['id' => $user->id, 'name' => $user->display_name, 'avatar' => $user->avatar, 'xp' => $data['xp'],
            'gems' => $data['badges'], 'streak' => $data['streak'], 'rank' => 7, 'me' => true];

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
                $meta = config("course-catalog.{$crew->planet}.courses.{$crew->course_slug}", []);

                return [
                    'name' => $meta['title'] ?? $crew->name,
                    'blurb' => $meta['blurb'] ?? ($meta['description'] ?? ''),
                    'planet' => config("course-catalog.{$crew->planet}.title", ucfirst($crew->planet)),
                    'banner' => $meta['banner'] ?? '#1b1650',
                    'art' => $meta['art'] ?? null,
                    'scene' => $meta['scene'] ?? null,
                    'captain' => $crew->faculty?->name,
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

        Auth::user()->update(['crew_id' => $crew->id]);
        if (! $crew->roster()->where('user_id', Auth::id())->exists()) {
            $crew->roster()->attach(Auth::id(), ['role' => 'student']);
        }

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
