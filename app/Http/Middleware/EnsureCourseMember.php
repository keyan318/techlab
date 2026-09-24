<?php

namespace App\Http\Middleware;

use App\Models\Crew;
use App\Services\CourseProgressService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a student out of a code-gated course (one whose faculty member has generated
 * a code) until they are on its roster: asked to join, got accepted, redeemed the code.
 * Covers every way in (course page, overview, lesson URLs, lesson actions), not just
 * the course card. Courses nobody has claimed yet stay open, as before.
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

        $planet = (string) $request->route('slug');
        $course = $request->route('course') ?? self::lessonCourse($planet);
        $crew = $course ? Crew::where('planet', $planet)->where('course_slug', $course)->first() : null;

        if ($crew && ! $user->belongsToCrew($crew->id)) {
            // The fragment route is fetched by JS, so a redirect would swap the course picker in as a "lesson".
            if ($request->routeIs('student.planet.module.lesson.fragment') || $request->expectsJson()) {
                abort(403, 'Ask to join this course first.');
            }

            return redirect()->route('student.planet', $planet)
                ->with('status', 'Ask to join that course first.');
        }

        return $next($request);
    }

    /**
     * Lesson URLs only name the planet. Its lessons belong to the planet's catalog course
     * whose blueprint key is the planet slug itself (networking → networking-fundamentals).
     */
    private static function lessonCourse(string $planet): ?string
    {
        foreach (array_keys(config("course-catalog.{$planet}.courses", [])) as $id) {
            if (CourseProgressService::keyFor($planet, $id) === $planet) {
                return $id;
            }
        }

        return null;
    }
}
