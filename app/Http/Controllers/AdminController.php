<?php

namespace App\Http\Controllers;

use App\Models\AdminActionLog;
use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\User;
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
            ->sum(fn (array $planet) => collect($planet['courses'])->reject(fn (array $c) => ! empty($c['coming_soon']))->count());
        $assigned = CourseAssignment::count();
        $facultyCount = User::where('role', 'faculty')->count();

        $activity = AdminActionLog::with('admin')->latest()->take(10)->get()->map(function (AdminActionLog $log) {
            [$planet, $courseSlug] = explode('.', $log->subject, 2);
            $courseTitle = config("course-catalog.{$planet}.courses.{$courseSlug}.title", $log->subject);
            $toFaculty = User::find($log->details['to_faculty_id'] ?? null)?->name ?? 'someone';

            return [
                'text' => ($log->admin->name ?? 'An admin').' '.($log->action === 'course.reassign' ? 'reassigned' : 'assigned').' '.$courseTitle.' to '.$toFaculty,
                'when' => $log->created_at->diffForHumans(),
            ];
        });

        return view('admin.dashboard', [
            'totalCourses' => $totalCourses,
            'assigned' => $assigned,
            'unassigned' => max(0, $totalCourses - $assigned),
            'facultyCount' => $facultyCount,
            'activity' => $activity,
        ]);
    }

    public function index(): View
    {
        $faculty = User::where('role', 'faculty')->orderBy('name')->get();
        $assignments = CourseAssignment::with('faculty')->get()->keyBy(fn (CourseAssignment $a) => $a->planet.'.'.$a->course_slug);

        $planets = collect(config('course-catalog'))->map(function (array $planet, string $planetSlug) use ($assignments) {
            $planet['courses'] = collect($planet['courses'])
                ->reject(fn (array $c) => ! empty($c['coming_soon']))
                ->map(function (array $c, string $courseSlug) use ($planetSlug, $assignments) {
                    $c['slug'] = $courseSlug;
                    $c['assignment'] = $assignments->get($planetSlug.'.'.$courseSlug);

                    return $c;
                })->values();

            return $planet;
        });

        return view('admin.home', compact('faculty', 'planets'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'planet' => ['required', 'string', 'in:'.implode(',', array_keys(config('course-catalog')))],
            'course_slug' => ['required', 'string'],
            'faculty_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        abort_unless(config("course-catalog.{$data['planet']}.courses.{$data['course_slug']}"), 404);
        abort_unless(User::where('id', $data['faculty_id'])->where('role', 'faculty')->exists(), 422);

        $subject = $data['planet'].'.'.$data['course_slug'];
        $previous = CourseAssignment::where('planet', $data['planet'])->where('course_slug', $data['course_slug'])->first();

        CourseAssignment::updateOrCreate(
            ['planet' => $data['planet'], 'course_slug' => $data['course_slug']],
            ['faculty_id' => $data['faculty_id']],
        );

        // Reassigning a live course (one that already has a code and roster) hands the
        // existing crew to the new captain rather than leaving it pointed at the old one —
        // otherwise the new faculty member would see the course on their dashboard but get
        // a 403 trying to actually manage it. The code and roster stay put either way.
        Crew::where('planet', $data['planet'])->where('course_slug', $data['course_slug'])
            ->update(['teacher_id' => $data['faculty_id']]);

        AdminActionLog::create([
            'admin_id' => Auth::id(),
            'action' => $previous ? 'course.reassign' : 'course.assign',
            'subject' => $subject,
            'details' => ['from_faculty_id' => $previous?->faculty_id, 'to_faculty_id' => $data['faculty_id']],
        ]);

        return redirect(route('admin.home'))->with('status', 'Assignment saved.');
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
