<?php

namespace App\Http\Controllers;

use App\Models\CourseAssignment;
use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\StudyActivity;
use App\Models\TeacherClass;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\CourseStructure;
use App\Services\FacultyDashboardService;
use App\Services\StudentDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        // The self-paced run of each course this faculty member built from scratch: theirs to watch too.
        $selfPaced = $this->ownSelfPaced();

        $courses = CourseAssignment::where('faculty_id', Auth::id())->get()->map(function (CourseAssignment $a) use ($requests, $selfPaced) {
            $catalog = CourseStructure::card($a->planet, $a->course_slug);
            $crew = $a->crew();

            return [
                'assignment' => $a,
                'title' => $catalog['title'] ?? $a->course_slug,
                'planetTitle' => config("course-catalog.{$a->planet}.title", $a->planet),
                'crew' => $crew,
                'selfPaced' => $selfPaced->get($a->planet.'.'.$a->course_slug),
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

        // Students caught leaving a final exam or a mini boss that this faculty member hasn't looked at yet.
        $alerts = CrewQuizAttempt::with(['user', 'quiz.crew'])
            ->whereNotNull('violation')->whereNull('faculty_seen_at')
            // A mini boss that ran out of time isn't an alert (no rule was broken); the final's always was.
            ->where(fn ($a) => $a->where('violation', '!=', 'time_up')->orWhereHas('quiz', fn ($q) => $q->where('is_final', true)))
            ->whereHas('quiz', fn ($q) => $q->where(fn ($p) => $p->where('is_final', true)->orWhereNotNull('module'))
                ->whereHas('crew', fn ($c) => $c->where('teacher_id', Auth::id())))
            ->latest('finished_at')->get();

        // Charts, stat tiles and the student table: filtered by course and date range in the browser.
        $analytics = FacultyDashboardService::build($courses->pluck('crew')->concat($courses->pluck('selfPaced'))->filter()->values());
        $joined = collect($analytics['courses'])->pluck('students', 'id');

        return view('faculty.facultyDashboard', compact('courses', 'requests', 'alerts', 'analytics', 'joined'));
    }

    /** The faculty member's signature, printed on their class's certificates. */
    public function saveSignature(Request $request): RedirectResponse
    {
        $request->validate(['signature' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:1024']]);
        $user = Auth::user();

        $old = $user->signature_path;
        $user->forceFill(['signature_path' => $request->file('signature')->store('signatures', 'public')])->save();
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return redirect(route('faculty.dashboard'))->with('status', 'Signature saved. It goes on your class\'s certificates.');
    }

    public function deleteSignature(): RedirectResponse
    {
        $user = Auth::user();
        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
            $user->forceFill(['signature_path' => null])->save();
        }

        return redirect(route('faculty.dashboard'))->with('status', 'Signature removed. Certificates show your name instead.');
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
     * Open this course's class (and its class code, the class's ID) — the assigned faculty member's own action,
     * not admin's. Creates the Crew the moment the course actually goes live.
     */
    public function generateCode(Request $request, CourseAssignment $assignment): RedirectResponse
    {
        if (! Auth::check() || (Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('login'));
        }
        abort_unless($assignment->faculty_id === Auth::id(), 403);

        $assignment->openClass();

        // Generated from the Crew page: stay there so the new crew shows up.
        return redirect(route($request->input('return') === 'crew' ? 'faculty.crew' : 'faculty.dashboard'));
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

        // Just enough to show the "publish your final exam" banner; the Exams tab has the full picture.
        $final = $crew->finalExam()->withCount('questions')->first();

        return view('faculty.facultyCrew', compact('crew', 'final'));
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
        abort_unless($crew->isWatchedBy(Auth::user()), 403);

        $roster = $this->rosterFor($crew);

        return view('faculty.facultyCrewRoster', compact('crew', 'roster'));
    }

    /**
     * Everyone on the crew's roster, each with their course progress (percent of lessons done),
     * XP and gems. The captain is on the roster too, without numbers.
     */
    private function rosterFor(Crew $crew)
    {
        $crew->load(['roster']);

        $progressKey = $crew->planet && $crew->course_slug ? CourseProgressService::keyFor($crew->planet, $crew->course_slug) : null;
        $order = $progressKey ? CourseProgressService::order($progressKey) : [];

        // Day streaks for every student in two queries, not two per student (the database is remote).
        $studentIds = $crew->roster->filter(fn (User $m) => $m->pivot->role === 'student')->pluck('id');
        $progressDays = LessonProgress::whereIn('user_id', $studentIds)->whereNotNull('completed_at')->get(['user_id', 'completed_at'])->groupBy('user_id');
        $activityDays = StudyActivity::whereIn('user_id', $studentIds)->where('minutes', '>', 0)->get(['user_id', 'date'])->groupBy('user_id');

        $roster = $crew->roster->map(function (User $member) use ($order, $progressKey, $progressDays, $activityDays) {
            $percent = null;
            $xp = null;
            $gems = null;
            $streak = null;
            if ($member->pivot->role === 'student') {
                if ($order) {
                    $done = array_flip(CourseProgressService::completedKeys($member, $progressKey));
                    $completed = collect($order)->filter(fn ($i) => isset($done[$i['module'].'/'.$i['lesson']]))->count();
                    $percent = (int) round($completed / count($order) * 100);
                }
                $xp = StudentDashboardService::earnedXp($member);
                $gems = $progressKey ? CourseProgressService::completedModulesCount($member, $progressKey) * StudentDashboardService::GEMS_PER_MODULE : 0;
                $streak = StudentDashboardService::streak($member, $progressDays->get($member->id, collect()), $activityDays->get($member->id, collect()));
            }
            $member->progressPercent = $percent;
            $member->xp = $xp;
            $member->gems = $gems;
            $member->streak = $streak;

            return $member;
        });

        return $roster;
    }

    /**
     * Sidebar "Crew": with a single crew, straight to its roster; otherwise every crew the captain watches
     * (their classes, and the self-paced run of a course they built), grouped by planet, with each
     * crew's astronauts and their progress — or how to open a class for a course that has none yet.
     */
    public function crewShortcut(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return redirect(route('student.dashboard'));
        }

        $selfPaced = $this->ownSelfPaced();

        $courses = CourseAssignment::where('faculty_id', Auth::id())->latest()->get()->map(function (CourseAssignment $a) use ($selfPaced) {
            $crews = collect([$a->crew(), $selfPaced->get($a->planet.'.'.$a->course_slug)])->filter()
                ->map(fn (Crew $crew) => ['crew' => $crew, 'roster' => $this->rosterFor($crew)])->values();

            return [
                'assignment' => $a,
                'planet' => $a->planet,
                'title' => CourseStructure::card($a->planet, $a->course_slug)['title'] ?? Crew::courseTitle($a->planet, $a->course_slug),
                'planetTitle' => config("course-catalog.{$a->planet}.title", ucfirst($a->planet)),
                'hasClass' => $crews->contains(fn ($c) => ! $c['crew']->isSelfPaced()),
                'crews' => $crews,
            ];
        });

        $crews = $courses->flatMap(fn ($c) => $c['crews']);
        if ($courses->count() === 1 && $crews->count() === 1) {
            return redirect(route('faculty.course.roster', $crews->first()['crew']));
        }

        // Planets in catalog order, each with its courses.
        $planets = $courses->groupBy('planet')
            ->sortBy(fn ($group, $planet) => array_search($planet, array_keys(config('course-catalog')), true));

        return view('faculty.facultyCrews', compact('planets'));
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

    /** @return Collection<string, Crew> self-paced crews of courses this faculty member built, keyed "planet.slug" */
    private function ownSelfPaced()
    {
        return Crew::watchedBy(Auth::user())->where('type', Crew::SELF_PACED)->get()
            ->keyBy(fn (Crew $c) => $c->planet.'.'.$c->course_slug);
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
