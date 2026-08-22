<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Teacher dashboard — only for teachers.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'teacher') {
            return redirect(route('student.dashboard'));
        }

        $crew = Crew::where('teacher_id', Auth::id())->first();

        return view('teacher.teacherDashboard', compact('crew'));
    }

    /**
     * Create a crew (a teacher can only own one). The teacher becomes its captain.
     */
    public function createCrew(Request $request): RedirectResponse
    {
        if (! Auth::check() || (Auth::user()->role ?? 'student') !== 'teacher') {
            return redirect(route('login'));
        }
        if (Crew::where('teacher_id', Auth::id())->exists()) {
            return redirect(route('teacher.dashboard'));
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        do {
            $code = strtoupper(Str::random(6));
        } while (Crew::where('code', $code)->exists());

        $crew = Crew::create([
            'name' => $request->name,
            'code' => $code,
            'teacher_id' => Auth::id(),
        ]);

        // The teacher is the first member of the crew (the captain).
        $crew->roster()->attach(Auth::id(), ['role' => 'teacher']);
        Auth::user()->update(['crew_id' => $crew->id]);

        return redirect(route('teacher.dashboard'));
    }
}
