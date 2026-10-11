<?php

namespace App\Http\Middleware;

use App\Models\Crew;
use App\Services\CourseProgressService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a student out of a course until they are a member of its crew: a faculty
 * captain must have claimed it (generated a code) AND accepted this student's request.
 * No captain assigned at all is just as closed as one whose code you haven't redeemed —
 * there's simply nobody to have accepted you. Covers every way in (course page,
 * overview, lesson URLs, lesson actions), not just the course card.
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
        $catalogCourse = $course ? config("course-catalog.{$planet}.courses.{$course}") : null;
        if (! $catalogCourse || ! empty($catalogCourse['coming_soon'])) {
            return $next($request);
        }

        // The crew and "is this student on its roster" in one round trip.
        $crew = Crew::where('planet', $planet)->where('course_slug', $course)
            ->withExists(['roster as is_member' => fn ($q) => $q->whereKey($user->id)])
            ->first();

        if (! $crew || ! $crew->is_member) {
            // The fragment route is fetched by JS, so a redirect would swap the course picker in as a "lesson".
            if ($request->routeIs('student.planet.module.lesson.fragment') || $request->expectsJson()) {
                abort(403, $crew ? 'Ask to join this course first.' : 'No captain has claimed this course yet.');
            }

            return redirect()->route('student.planet', $planet)
                ->with('status', $crew ? 'Ask to join that course first.' : 'That course doesn\'t have a captain yet.');
        }

        // Handed on so the course hub doesn't look the same crew up again.
        $request->attributes->set('crew', $crew);

        return $next($request);
    }
}
