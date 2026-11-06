<?php

namespace App\Http\Controllers;

use App\Models\AdminActionLog;
use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\AdminAnalyticsService;
use App\Services\CourseProgressService;
use App\Services\FacultyDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * MVP admin panel: assigning a registered faculty member as the captain of a
 * catalog course. Nothing else (course codes, rosters, content) is admin's job —
 * the assigned faculty member takes it from here (see FacultyController).
 */
class AdminController extends Controller
{
    /** The at-a-glance overview: how many courses are covered, and what changed recently. */
    public function dashboard(): View
    {
        $totalCourses = collect(config('course-catalog'))
            ->sum(fn (array $planet) => collect($planet['courses'])->reject(fn (array $c) => ! empty($c['coming_soon']) || ! empty($c['hidden']))->count());
        $assigned = CourseAssignment::get(['planet', 'course_slug'])->unique(fn (CourseAssignment $a) => $a->planet.'.'.$a->course_slug)->count();
        $facultyCount = User::where('role', 'faculty')->count();

        $logs = AdminActionLog::with('admin')->latest()->take(8)->get();
        $names = User::whereIn('id', $logs->pluck('details.to_faculty_id')->filter()->unique())->pluck('name', 'id');
        $activity = $logs->map(function (AdminActionLog $log) use ($names) {
            [$planet, $courseSlug] = explode('.', $log->subject, 2);
            $courseTitle = config("course-catalog.{$planet}.courses.{$courseSlug}.title", $log->subject);
            $toFaculty = $names[$log->details['to_faculty_id'] ?? null] ?? 'someone';

            return [
                'action' => $log->action,
                'planet' => $planet,
                'text' => ($log->admin->name ?? 'An admin').' '.match ($log->action) {
                    'course.reassign' => 'reassigned '.$courseTitle.' to '.$toFaculty,
                    'course.unassign' => 'took '.$toFaculty.' off '.$courseTitle,
                    default => 'assigned '.$courseTitle.' to '.$toFaculty,
                },
                'when' => $log->created_at->diffForHumans(),
            ];
        });

        // Students per program × year level, from what they picked at sign-up or in Settings.
        // Older accounts that never picked land under "Not set" (the '' key).
        $counts = User::where('role', 'student')
            ->selectRaw('program, year_level, count(*) as total')
            ->groupBy('program', 'year_level')
            ->get();
        $programs = User::PROGRAMS + ['' => 'Not set'];
        $years = User::YEAR_LEVELS + [0 => 'Not set'];
        $grid = [];
        foreach ($counts as $row) {
            $program = array_key_exists((string) $row->program, User::PROGRAMS) ? $row->program : '';
            $year = array_key_exists((int) $row->year_level, User::YEAR_LEVELS) ? (int) $row->year_level : 0;
            $grid[$program][$year] = ($grid[$program][$year] ?? 0) + (int) $row->total;
        }

        return view('admin.dashboard', [
            'studentCount' => (int) $counts->sum('total'),
            'programs' => $programs,
            'years' => $years,
            'grid' => $grid,
            'totalCourses' => $totalCourses,
            'assigned' => $assigned,
            'unassigned' => max(0, $totalCourses - $assigned),
            'facultyCount' => $facultyCount,
            'activity' => $activity,
            'stats' => AdminAnalyticsService::build(),
            'planetTitles' => AdminAnalyticsService::planets(),
        ]);
    }

    public function index(): View
    {
        $faculty = User::where('role', 'faculty')->orderBy('name')->get();
        $assignments = CourseAssignment::with('faculty')->get()->groupBy(fn (CourseAssignment $a) => $a->planet.'.'.$a->course_slug);
        // Each course's self-paced run, which the admin looks after (it has no faculty member).
        $selfPaced = Crew::where('type', Crew::SELF_PACED)->withCount(['roster as learners' => fn ($q) => $q->where('crew_members.role', 'student')])
            ->get()->keyBy(fn (Crew $c) => $c->planet.'.'.$c->course_slug);

        $planets = collect(config('course-catalog'))->map(function (array $planet, string $planetSlug) use ($assignments, $selfPaced) {
            $planet['courses'] = collect($planet['courses'])
                ->reject(fn (array $c) => ! empty($c['coming_soon']) || ! empty($c['hidden']))
                ->map(function (array $c, string $courseSlug) use ($planetSlug, $assignments, $selfPaced) {
                    $c['slug'] = $courseSlug;
                    $c['assignments'] = $assignments->get($planetSlug.'.'.$courseSlug, collect());
                    $c['self_paced'] = $selfPaced->get($planetSlug.'.'.$courseSlug);

                    return $c;
                })->values();

            return $planet;
        });

        return view('admin.home', compact('faculty', 'planets'));
    }

    /** Add a faculty member to a course. A course can have several; each one runs their own class. */
    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'planet' => ['required', 'string', 'in:'.implode(',', array_keys(config('course-catalog')))],
            'course_slug' => ['required', 'string'],
            'faculty_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        abort_unless(config("course-catalog.{$data['planet']}.courses.{$data['course_slug']}"), 404);
        abort_unless(User::where('id', $data['faculty_id'])->where('role', 'faculty')->exists(), 422);

        $assignment = CourseAssignment::firstOrCreate($data);
        if (! $assignment->wasRecentlyCreated) {
            return redirect(route('admin.home'))->with('status', 'They already teach that course.');
        }

        AdminActionLog::create([
            'admin_id' => Auth::id(),
            'action' => 'course.assign',
            'subject' => $data['planet'].'.'.$data['course_slug'],
            'details' => ['to_faculty_id' => $data['faculty_id']],
        ]);

        return redirect(route('admin.home'))->with('status', 'Faculty member added. They can open their class from their dashboard.');
    }

    /** Take a faculty member off a course — only while their class has no students, so nobody is left without a teacher. */
    public function unassign(CourseAssignment $assignment): RedirectResponse
    {
        $crew = $assignment->crew();
        if ($crew && $crew->roster()->wherePivot('role', 'student')->exists()) {
            return redirect(route('admin.home'))->with('status', "{$assignment->faculty->name}'s class still has students, so they stay on this course.");
        }

        $crew?->delete();
        $assignment->delete();

        AdminActionLog::create([
            'admin_id' => Auth::id(),
            'action' => 'course.unassign',
            'subject' => $assignment->planet.'.'.$assignment->course_slug,
            'details' => ['to_faculty_id' => $assignment->faculty_id],
        ]);

        return redirect(route('admin.home'))->with('status', 'Faculty member removed from the course.');
    }

    /**
     * Self-paced learners, course by course: nobody's class, so the admin looks after them — how far each one is,
     * and where they stand with the default final exam (strikes, bans, and "Allow retake").
     */
    public function selfPaced(): View
    {
        $crews = Crew::where('type', Crew::SELF_PACED)->with(['finalExam', 'roster' => fn ($q) => $q->wherePivot('role', 'student')->orderBy('name')])
            ->get()->keyBy(fn (Crew $c) => $c->planet.'.'.$c->course_slug);

        // Every live course in every planet, in catalog order — even before its first self-paced learner shows up.
        $courses = collect(config('course-catalog'))->flatMap(fn (array $planet, string $planetSlug) => collect($planet['courses'])
            ->reject(fn (array $c) => ! empty($c['coming_soon']) || ! empty($c['hidden']))
            ->map(function (array $c, string $courseSlug) use ($planet, $planetSlug, $crews) {
                $crew = $crews->get($planetSlug.'.'.$courseSlug);
                if (! $crew) {
                    return ['title' => $c['title'], 'planet' => $planet['title'], 'planetSlug' => $planetSlug, 'final' => null, 'learners' => []];
                }

                $key = CourseProgressService::keyFor($crew->planet, $crew->course_slug);
                $lessons = collect(CourseProgressService::order($key))->map(fn ($i) => $i['module'].'/'.$i['lesson'])->flip();
                $done = LessonProgress::where('course', $key)->whereIn('user_id', $crew->roster->modelKeys())
                    ->get(['user_id', 'module', 'lesson'])
                    ->filter(fn ($row) => $lessons->has($row->module.'/'.$row->lesson))
                    ->countBy('user_id');
                $attempts = $crew->finalExam ? $crew->finalExam->attempts()->get()->keyBy('user_id') : collect();

                return [
                    'title' => $c['title'],
                    'planet' => $planet['title'],
                    'planetSlug' => $planetSlug,
                    'final' => $crew->finalExam,
                    'learners' => $crew->roster->map(function (User $student) use ($done, $lessons, $attempts) {
                        $percent = $lessons->count() ? (int) round(($done[$student->id] ?? 0) / $lessons->count() * 100) : 0;
                        $attempt = $attempts->get($student->id);

                        return [
                            'student' => $student,
                            'percent' => $percent,
                            'attempt' => $attempt,
                            'state' => FacultyDashboardService::finalState($attempt, $percent),
                        ];
                    })->all(),
                ];
            })->values())->values();

        return view('admin.self-paced', compact('courses'));
    }

    /**
     * Every open class, Google Classroom style, grouped one card per course: a course can be
     * taught by several faculty members at once, each running their own class (own code, own
     * roster), so the card lists them all. Self-paced isn't a "class" (no teacher), so it stays
     * on the Self-paced page instead.
     */
    public function classes(): View
    {
        $courses = Crew::classes()->with('faculty')
            ->withCount(['roster as learners' => fn ($q) => $q->where('crew_members.role', 'student')])
            ->get()
            ->groupBy(fn (Crew $crew) => $crew->planet.'.'.$crew->course_slug)
            ->map(function ($crews) {
                $first = $crews->first();

                return [
                    'title' => config("course-catalog.{$first->planet}.courses.{$first->course_slug}.title", $first->name),
                    'planet' => config("course-catalog.{$first->planet}.title", $first->planet),
                    'planetSlug' => $first->planet,
                    'learners' => $crews->sum('learners'),
                    'classes' => $crews->map(fn (Crew $crew) => [
                        'id' => $crew->id,
                        'faculty' => $crew->faculty,
                        'learners' => $crew->learners,
                        'code' => $crew->code,
                    ])->sortBy(fn ($c) => mb_strtolower($c['faculty']->name ?? '~'))->values(),
                ];
            })
            ->sortBy(fn ($c) => $c['planet'].'.'.$c['title'])
            ->values();

        return view('admin.classes', compact('courses'));
    }

    /** Read-only: the roster behind the "Captain" name on the assignment table. */
    public function crewRoster(Crew $crew): JsonResponse
    {
        $crew->load('roster');

        return response()->json([
            'title' => config("course-catalog.{$crew->planet}.courses.{$crew->course_slug}.title", $crew->name),
            'code' => $crew->code,
            'members' => $crew->roster->map(fn (User $m) => ['name' => $m->name, 'role' => $m->pivot->role])->values(),
        ]);
    }
}
