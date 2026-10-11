<?php

namespace App\Http\Controllers;

use App\Models\CourseAssignment;
use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\TeacherClass;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\StudentDashboardService;
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

        // Every course's final exam in one query: none yet, a draft, or live. Until it is live
        // the course's students cannot earn a certificate, so the dashboard says so.
        $finals = CrewQuiz::where('is_final', true)
            ->whereIn('crew_id', $courses->pluck('crew.id')->filter())
            ->get(['crew_id', 'published_at'])->keyBy('crew_id');
        $courses = $courses->map(fn ($c) => $c + [
            'final' => ! $c['crew'] ? null : match (true) {
                ! $finals->has($c['crew']->id) => 'none',
                $finals[$c['crew']->id]->published_at === null => 'draft',
                default => 'live',
            },
        ]);

        // Students caught leaving a final exam that this faculty member hasn't looked at yet.
        $alerts = CrewQuizAttempt::with(['user', 'quiz.crew'])
            ->whereNotNull('violation')->whereNull('faculty_seen_at')
            ->whereHas('quiz', fn ($q) => $q->where('is_final', true)->whereHas('crew', fn ($c) => $c->where('teacher_id', Auth::id())))
            ->latest('finished_at')->get();

        return view('faculty.facultyDashboard', compact('courses', 'requests', 'alerts'));
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
     * A course's management page: learning modules and quizzes.
     */
    public function course(Crew $crew): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.courses'));
        }
        abort_unless($crew->teacher_id === Auth::id(), 403);

        $crew->load(['modules.materials']);

        $final = $crew->finalExam()->withCount('questions')->first();
        $finalResults = $final ? $this->finalResults($crew, $final) : [];

        // Opening this page is how the faculty member sees who was caught, so those alerts clear.
        if ($final) {
            $final->attempts()->whereNotNull('violation')->whereNull('faculty_seen_at')->update(['faculty_seen_at' => now()]);
        }

        return view('faculty.facultyCrew', compact('crew', 'final', 'finalResults'));
    }

    /**
     * One row per student for the final exam: still on the lessons, ready to take it, taking it
     * right now, passed, failed, or caught leaving. Lesson progress for the whole crew comes in
     * one query (the database is remote, so a query per student would be slow).
     *
     * @return array<int, array<string, mixed>>
     */
    private function finalResults(Crew $crew, CrewQuiz $final): array
    {
        $students = $crew->roster()->wherePivot('role', 'student')->orderBy('name')->get();
        $attempts = $final->attempts()->get()->keyBy('user_id');

        $key = CourseProgressService::keyFor($crew->planet, $crew->course_slug);
        $lessons = collect(CourseProgressService::order($key))->map(fn ($i) => $i['module'].'/'.$i['lesson'])->flip();
        $done = LessonProgress::where('course', $key)->whereIn('user_id', $students->pluck('id'))
            ->get(['user_id', 'module', 'lesson'])
            ->filter(fn ($row) => $lessons->has($row->module.'/'.$row->lesson))
            ->countBy('user_id');

        return $students->map(function (User $student) use ($attempts, $done, $lessons) {
            $attempt = $attempts->get($student->id);
            $percent = $lessons->count() ? (int) round(($done[$student->id] ?? 0) / $lessons->count() * 100) : 0;

            return [
                'student' => $student,
                'attempt' => $attempt,
                'lessonsPercent' => $percent,
                'state' => match (true) {
                    $attempt?->inProgress() => 'taking',
                    $attempt?->passed() => 'passed',
                    $attempt?->violation !== null => 'caught',
                    $attempt !== null => 'failed',
                    $percent >= 100 => 'ready',
                    default => 'studying',
                },
            ];
        })->all();
    }

    /**
     * A course's crew roster: each student's progress, XP and Gems in this specific course.
     */
    public function roster(Crew $crew): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.courses'));
        }
        abort_unless($crew->teacher_id === Auth::id(), 403);

        $crew->load(['roster']);

        $progressKey = $crew->planet && $crew->course_slug ? CourseProgressService::keyFor($crew->planet, $crew->course_slug) : null;
        $order = $progressKey ? CourseProgressService::order($progressKey) : [];

        $roster = $crew->roster->map(function (User $member) use ($order, $progressKey) {
            $percent = null;
            $xp = null;
            $gems = null;
            if ($member->pivot->role === 'student') {
                if ($order) {
                    $done = array_flip(CourseProgressService::completedKeys($member, $progressKey));
                    $completed = collect($order)->filter(fn ($i) => isset($done[$i['module'].'/'.$i['lesson']]))->count();
                    $percent = (int) round($completed / count($order) * 100);
                }
                $xp = StudentDashboardService::earnedXp($member);
                $gems = $progressKey ? CourseProgressService::completedModulesCount($member, $progressKey) : 0;
            }
            $member->progressPercent = $percent;
            $member->xp = $xp;
            $member->gems = $gems;

            return $member;
        });

        return view('faculty.facultyCrewRoster', compact('crew', 'roster'));
    }

    /** Sidebar shortcut: jump to the faculty's active crew's roster. */
    public function crewShortcut(): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.dashboard'));
        }

        $crew = $this->activeCrewOrNull();
        if (! $crew) {
            return redirect(route('faculty.dashboard'))->with('status', 'Generate a course code first.');
        }

        return redirect(route('faculty.course.roster', $crew));
    }

    /** Sidebar shortcut: jump to the faculty's active crew's learning materials. */
    public function materialsShortcut(): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.dashboard'));
        }

        $crew = $this->activeCrewOrNull();
        if (! $crew) {
            return redirect(route('faculty.dashboard'))->with('status', 'Generate a course code first.');
        }

        return redirect(route('faculty.course', $crew));
    }

    /** The faculty member's most-recently-assigned course that already has a crew, if any. */
    private function activeCrewOrNull(): ?Crew
    {
        return CourseAssignment::where('faculty_id', Auth::id())
            ->latest()->get()
            ->map(fn (CourseAssignment $a) => $a->crew())
            ->filter()->first();
    }
}
