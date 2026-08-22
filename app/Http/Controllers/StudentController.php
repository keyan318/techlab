<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Student dashboard — only reachable when signed in. A teacher who lands
     * here is bounced to their own dashboard, so a teacher never ends up
     * viewing the student dashboard.
     */
    public function dashboard(): View|RedirectResponse
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
            'name'        => Auth::user()->name ?? 'Explorer',
            'level'       => 7,
            'levelTitle'  => 'Explorer',
            'xp'          => 720,
            'xpForNext'   => 1000,
            'streak'      => 5,
            'dailyQuest'  => ['label' => 'Complete 1 lesson', 'progress' => 0, 'goal' => 1],
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

        // PLACEHOLDER course data. The real Module / Quiz / Lab / Grade models
        // and teacher-uploaded materials do not exist yet, so these arrays
        // mirror the shape those models should expose. When the backend lands,
        // swap this block for a real query (e.g. $crew->modules()->with('materials'))
        // and the Blade below can be wired up without a rewrite.
        $course = $crew ? [
            'description' => 'Learn the fundamentals through guided lessons, quizzes, and hands-on laboratory activities.',
            'modules' => [
                [
                    'number' => '01',
                    'title' => 'Introduction to Programming',
                    'description' => 'Learn the basic concepts of programming and problem solving.',
                    'materials' => [
                        ['name' => 'Programming Fundamentals.pdf', 'ext' => 'pdf'],
                        ['name' => 'Lecture 01.pptx', 'ext' => 'pptx'],
                        ['name' => 'Starter Code.zip', 'ext' => 'zip'],
                    ],
                    'material_count' => 3,
                ],
                [
                    'number' => '02',
                    'title' => 'Variables & Data Types',
                    'description' => 'How computers store and manipulate information.',
                    'materials' => [
                        ['name' => 'Variables Cheat Sheet.pdf', 'ext' => 'pdf'],
                        ['name' => 'Worksheet.docx', 'ext' => 'docx'],
                    ],
                    'material_count' => 2,
                ],
                [
                    'number' => '03',
                    'title' => 'Control Flow',
                    'description' => 'Conditions, loops, and logical decision making.',
                    'materials' => [
                        ['name' => 'Control Flow Notes.pdf', 'ext' => 'pdf'],
                        ['name' => 'Flowcharts.png', 'ext' => 'img'],
                    ],
                    'material_count' => 2,
                ],
            ],
            'quizzes' => [
                [
                    'number' => '01',
                    'title' => 'Programming Fundamentals',
                    'questions' => 20,
                    'minutes' => 15,
                    'status' => 'completed',
                    'score' => 18,
                    'total' => 20,
                    'percent' => 90,
                ],
                [
                    'number' => '02',
                    'title' => 'Variables & Types',
                    'questions' => 15,
                    'minutes' => 12,
                    'status' => 'available',
                    'score' => null,
                    'total' => 15,
                    'percent' => null,
                ],
                [
                    'number' => '03',
                    'title' => 'Control Flow',
                    'questions' => 18,
                    'minutes' => 15,
                    'status' => 'locked',
                    'score' => null,
                    'total' => 18,
                    'percent' => null,
                ],
            ],
            'labs' => [
                [
                    'number' => '01',
                    'title' => 'Build Your First Program',
                    'description' => 'Apply the concepts from Module 01.',
                    'due' => 'September 12',
                    'status' => 'graded',
                    'score' => 95,
                    'total' => 100,
                ],
                [
                    'number' => '02',
                    'title' => 'Temperature Converter',
                    'description' => 'Practice variables and operators.',
                    'due' => 'September 19',
                    'status' => 'not_submitted',
                    'score' => null,
                    'total' => 100,
                ],
                [
                    'number' => '03',
                    'title' => 'Loop Art',
                    'description' => 'Generate patterns using loops.',
                    'due' => 'September 26',
                    'status' => 'late',
                    'score' => null,
                    'total' => 100,
                ],
            ],
            // Grading categories are placeholders; replace with real weights from
            // a Grading model once it exists. Only show categories the system uses.
            'grades' => [
                'current' => 92.5,
                'label' => 'Excellent',
                'breakdown' => [
                    ['label' => 'Quizzes', 'weight' => 30, 'score' => 92, 'total' => 100],
                    ['label' => 'Lab Activities', 'weight' => 40, 'score' => 95, 'total' => 100],
                    ['label' => 'Exams', 'weight' => 30, 'score' => 90, 'total' => 100],
                ],
                'overall' => 92.5,
            ],
        ] : null;

        return view('student.studentCrew', compact('crew', 'course'));
    }

    /**
     * Join a crew by its invite code.
     */
    public function joinCrew(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }

        $data = $request->validate(['code' => ['required', 'string']]);
        $crew = Crew::where('code', strtoupper(trim($data['code'])))->first();

        if (! $crew) {
            return back()->withInput()->withErrors(['code' => 'That crew code was not found. Check with your captain.']);
        }

        Auth::user()->update(['crew_id' => $crew->id]);
        if (! $crew->roster()->where('user_id', Auth::id())->exists()) {
            $crew->roster()->attach(Auth::id(), ['role' => 'student']);
        }

        return redirect(route('student.crew'));
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
