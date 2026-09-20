<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewQuizAttempt;
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
     * crew XP leaderboard and activity charts. A teacher who lands here is
     * bounced to their own dashboard.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') === 'teacher') {
            return redirect(route('teacher.dashboard'));
        }

        return view('student.dashboard', StudentDashboardService::build(Auth::user()));
    }

    /**
     * Planet picker (the onboarding track choice), formerly served at /dashboard.
     */
    public function planets(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') === 'teacher') {
            return redirect(route('teacher.dashboard'));
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

        return view('student.studentOnboarding', compact('nodes', 'student'));
    }

    /**
     * Student crew page — join (or view) a crew.
     *
     * Once a student has joined a crew (their class), this renders the course
     * learning + performance hub: learning modules, quizzes, lab activities and
     * grades. Until then it shows the crew-code join flow.
     */
    public function crew(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }

        $crew = Auth::user()->crew_id ? Crew::find(Auth::user()->crew_id) : null;

        // Modules, materials and quizzes are real (teacher-authored); grades are computed from
        // the student's own quiz attempts. There are no lab/exam models yet, so none are shown.
        $modules = $crew
            ? $crew->modules()->with('materials')->get()->values()->map(fn ($m, $i) => [
                'number' => str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'title' => $m->title,
                'description' => $m->description,
                'materials' => $m->materials->map(fn ($f) => [
                    'name' => $f->name,
                    'ext' => $f->kind,
                    'url' => route('materials.download', $f),
                ])->all(),
                'material_count' => $m->materials->count(),
            ])->all()
            : [];

        $quizzes = [];
        $grades = ['current' => null, 'label' => 'No graded work yet', 'done' => 0, 'count' => 0];
        if ($crew) {
            $attempts = CrewQuizAttempt::where('user_id', Auth::id())->get()->keyBy('crew_quiz_id');
            $quizzes = $crew->quizzes()->withCount('questions')->get()->values()->map(function ($q, $i) use ($attempts) {
                $a = $attempts->get($q->id);

                return [
                    'id' => $q->id,
                    'number' => str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    'title' => $q->title,
                    'questions' => $q->questions_count,
                    'minutes' => $q->minutes,
                    'status' => $a ? 'completed' : 'available',
                    'score' => $a?->score,
                    'total' => $a?->total ?? $q->questions_count,
                    'percent' => $a?->percent(),
                ];
            })->all();

            $done = collect($quizzes)->where('status', 'completed');
            $grades['count'] = count($quizzes);
            $grades['done'] = $done->count();
            if ($done->isNotEmpty()) {
                $avg = round($done->avg('percent'), 1);
                $grades['current'] = $avg;
                $grades['label'] = match (true) {
                    $avg >= 90 => 'Excellent',
                    $avg >= 80 => 'Great work',
                    $avg >= 70 => 'Good',
                    $avg >= 60 => 'Keep going',
                    default => 'Needs practice',
                };
            }
        }

        $course = $crew ? [
            'description' => 'Learn the fundamentals through guided lessons, quizzes, and hands-on laboratory activities.',
            'modules' => $modules,
            'quizzes' => $quizzes,
            'grades' => $grades,
        ] : null;

        return view('student.studentCrew', compact('crew', 'course'));
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

        Auth::user()->update(['crew_id' => $crew->id]);
        if (! $crew->roster()->where('user_id', Auth::id())->exists()) {
            $crew->roster()->attach(Auth::id(), ['role' => 'student']);
        }

        return $request->expectsJson()
            ? response()->json(['redirect' => route('student.crew')])
            : redirect(route('student.crew'));
    }

    /**
     * TechLab Chat — the main student interface after login.
     *
     * This is the post-login destination. The actual AI conversation, persistent
     * chat history, and crew functionality are not built yet; the page renders a
     * ChatGPT-style shell with placeholder history so the UI/navigation is ready.
     * Students who land here are bounced to the teacher dashboard if misrouted.
     */
    public function chat(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }

        if ((Auth::user()->role ?? 'student') === 'teacher') {
            return redirect(route('teacher.dashboard'));
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
