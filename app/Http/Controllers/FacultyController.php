<?php

namespace App\Http\Controllers;

use App\Models\CourseAssignment;
use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Models\TeacherClass;
use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FacultyController extends Controller
{
    /**
     * Faculty dashboard — "My Courses": every course an admin has put this faculty
     * member in charge of, whether or not they have generated a code for it yet.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.dashboard'));
        }

        // Students waiting for this faculty member to accept or decline them, oldest first.
        $requests = CourseJoinRequest::with(['student', 'crew'])
            ->where('status', CourseJoinRequest::PENDING)
            ->whereHas('crew', fn ($q) => $q->where('teacher_id', Auth::id()))
            ->oldest()->get();

        $courses = CourseAssignment::where('faculty_id', Auth::id())->get()->map(function (CourseAssignment $a) use ($requests) {
            $catalog = config("course-catalog.{$a->planet}.courses.{$a->course_slug}", []);
            $crew = $a->crew();

            return [
                'assignment' => $a,
                'title' => $catalog['title'] ?? $a->course_slug,
                'planetTitle' => config("course-catalog.{$a->planet}.title", $a->planet),
                'crew' => $crew,
                'waiting' => $crew ? $requests->where('crew_id', $crew->id)->count() : 0,
            ];
        });

        return view('faculty.facultyDashboard', compact('courses', 'requests'));
    }

    /** Classes page — the faculty member's weekly schedule (upload it, Astro organizes it; "View Calendar" opens the drawer). */
    public function classes(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.dashboard'));
        }

        $classes = TeacherClass::where('user_id', Auth::id())
            ->orderBy('day')->orderBy('starts_at')->get()->map->toCard();

        return view('faculty.facultyClasses', compact('classes'));
    }

    /**
     * Generate this course's join code — the assigned faculty member's own action,
     * not admin's. Creates the Crew the moment the course actually goes live.
     */
    public function generateCode(CourseAssignment $assignment): RedirectResponse
    {
        if (! Auth::check() || (Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('login'));
        }
        abort_unless($assignment->faculty_id === Auth::id(), 403);

        if (! $assignment->crew()) {
            do {
                $code = strtoupper(Str::random(6));
            } while (Crew::where('code', $code)->exists());

            $title = config("course-catalog.{$assignment->planet}.courses.{$assignment->course_slug}.title", $assignment->course_slug);

            $crew = Crew::create([
                'name' => $title,
                'code' => $code,
                'teacher_id' => Auth::id(),
                'planet' => $assignment->planet,
                'course_slug' => $assignment->course_slug,
            ]);

            $crew->roster()->attach(Auth::id(), ['role' => 'faculty']);
        }

        return redirect(route('faculty.dashboard'));
    }

    /**
     * A course's management page: roster (with each student's progress in this
     * specific course), learning modules and quizzes.
     */
    public function course(Crew $crew): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.crew'));
        }
        abort_unless($crew->teacher_id === Auth::id(), 403);

        $crew->load(['modules.materials', 'roster']);

        $progressKey = $crew->planet && $crew->course_slug ? CourseProgressService::keyFor($crew->planet, $crew->course_slug) : null;
        $order = $progressKey ? CourseProgressService::order($progressKey) : [];

        $roster = $crew->roster->map(function (User $member) use ($order, $progressKey) {
            $percent = null;
            if ($member->pivot->role === 'student' && $order) {
                $done = array_flip(CourseProgressService::completedKeys($member, $progressKey));
                $completed = collect($order)->filter(fn ($i) => isset($done[$i['module'].'/'.$i['lesson']]))->count();
                $percent = (int) round($completed / count($order) * 100);
            }
            $member->progressPercent = $percent;

            return $member;
        });

        return view('faculty.facultyCrew', compact('crew', 'roster'));
    }
}
