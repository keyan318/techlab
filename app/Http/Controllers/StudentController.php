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
            return redirect('/login');
        }
        if ((Auth::user()->role ?? 'student') === 'teacher') {
            return redirect('/teacher/dashboard');
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

        return view('student.studentdashboard', compact('nodes', 'student'));
    }

    /**
     * Student crew page — join (or view) a crew.
     */
    public function crew(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $crew = Auth::user()->crew_id ? Crew::find(Auth::user()->crew_id) : null;

        return view('student.studentCrew', compact('crew'));
    }

    /**
     * Join a crew by its invite code.
     */
    public function joinCrew(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $data = $request->validate(['code' => ['required', 'string']]);
        $crew = Crew::where('code', strtoupper(trim($data['code'])))->first();

        if (! $crew) {
            return back()->withInput()->withErrors(['code' => 'That crew code was not found. Check with your captain.']);
        }

        Auth::user()->update(['crew_id' => $crew->id]);
        $crew->roster()->attach(Auth::id(), ['role' => 'student']);

        return redirect('/student/crew');
    }

    /**
     * Student crew home — the full TechLab student dashboard (modules attached later).
     */
    public function crewHome(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        return view('student.crew.crewhome');
    }
}
