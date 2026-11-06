<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\Crew;
use App\Services\CourseProgressService;
use App\Services\CourseTrack;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a student out of a course until they are on one of its crews: they started it
 * Self-paced, or a faculty member accepted them into their class. Covers every way in
 * (course page, overview, lesson URLs, lesson actions), not just the course card.
 * Guests, faculty and admins pass through.
 */
class EnsureCourseMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ($user->role ?? 'student') !== 'student') {
            return $next($request);
        }

        // A route's {slug} may be a planet's own key, or (for a lesson URL) a
        // planet's second+ course keyed by its catalog id. Either way, the crew
        // lives under the planet the key belongs to.
        $key = (string) $request->route('slug');
        $planet = CourseProgressService::planetFor($key);
        $course = $request->route('course') ?? CourseProgressService::catalogFor($key);

        // An unknown or "coming soon" course isn't this middleware's call — that's a 404
        // the controller itself decides, same as before this gate existed. Only a real,
        // live catalog course is something to actually gate.
        // A plain config lookup first, with no query — CourseStructure::card() would work
        // too, but it pulls in CourseTrack::classId() (another crew lookup) for its override
        // layer, which this check doesn't need and would double the query below.
        $meta = $course ? config("course-catalog.{$planet}.courses.{$course}") : null;
        $isRealCourse = $meta !== null;
        if (! $isRealCourse && $course !== null) {
            try {
                $isRealCourse = Course::where('planet', $planet)->where('slug', $course)->exists();
            } catch (QueryException) {
                $isRealCourse = false;
            }
        }
        if (! $isRealCourse || ! empty($meta['coming_soon'] ?? null)) {
            return $next($request);
        }

        // The student's track for this course: the self-paced crew or the class they joined.
        $crew = Crew::forStudent($user, $planet, $course);

        if (! $crew) {
            // The fragment route is fetched by JS, so a redirect would swap the course picker in as a "lesson".
            if ($request->routeIs('student.planet.module.lesson.fragment') || $request->expectsJson()) {
                abort(403, 'Pick Self-paced or join a class first.');
            }

            return redirect()->route('student.planet', $planet)
                ->with('status', 'Pick Self-paced or join a class to open that course.');
        }

        // Handed on so the course hub and the lesson services don't look the same crew up again.
        $request->attributes->set('crew', $crew);
        CourseTrack::prime($user, CourseProgressService::keyFor($planet, $course), $crew);

        return $next($request);
    }
}
