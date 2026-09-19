<?php

namespace App\Http\Middleware;

use App\Services\CourseProgressService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks direct access to a Programming lesson until the previous lesson's
 * coding challenge has been completed (verified server-side).
 *
 * Only the Programming course is gated; other planets pass straight through.
 * Guests pass through too — the controllers already handle unauthenticated
 * requests (login redirect / 401) and there is no progress to check.
 */
class EnsureLessonUnlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $course = CourseProgressService::COURSE;
        $user   = $request->user();

        if ($request->route('slug') !== $course || ! $user) {
            return $next($request);
        }

        $module = (string) $request->route('module');
        $lesson = (string) $request->route('lesson');

        // Not part of the course blueprint → nothing to serve.
        if (CourseProgressService::indexOf($module, $lesson, $course) === null) {
            abort(404);
        }

        if (! CourseProgressService::isUnlocked($user, $module, $lesson, $course)) {
            // The fragment route is fetched by JS — a redirect would be followed
            // and the wrong lesson silently swapped in, so refuse it outright.
            if ($request->routeIs('student.planet.module.lesson.fragment') || $request->expectsJson()) {
                abort(403, 'This lesson is locked. Complete the previous challenge first.');
            }

            $target = CourseProgressService::furthestUnlocked($user, $course);

            return redirect()->route('student.planet.module.lesson', [
                'slug'   => $course,
                'module' => $target['module'],
                'lesson' => $target['lesson'],
            ]);
        }

        // A lesson with no coding challenge has nothing to gate on, so opening
        // it (which required the previous one to be complete) counts as done.
        // Otherwise the course would dead-end at the first challenge-less lesson.
        if (CourseProgressService::expectedFor($module, $lesson, $course) === null) {
            CourseProgressService::markComplete($user, $module, $lesson, $course);
        }

        return $next($request);
    }
}
