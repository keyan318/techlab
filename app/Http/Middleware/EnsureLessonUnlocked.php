<?php

namespace App\Http\Middleware;

use App\Services\CourseProgressService;
use App\Services\MiniBossService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks direct access to a lesson until the previous lesson's challenge (a
 * coding challenge, or a simulator lab) has been completed (verified server-side).
 *
 * Only planets with a blueprint in config/course-structure.php are gated; other
 * planets pass straight through.
 * Guests pass through too — the controllers already handle unauthenticated
 * requests (login redirect / 401) and there is no progress to check.
 */
class EnsureLessonUnlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $course = (string) $request->route('slug');
        $user = $request->user();

        if (! CourseProgressService::exists($course) || ! $user) {
            return $next($request);
        }

        $module = (string) $request->route('module');
        $lesson = (string) $request->route('lesson');

        // Not part of the course blueprint → nothing to serve.
        if (CourseProgressService::indexOf($module, $lesson, $course) === null) {
            abort(404);
        }

        // Faculty and admins read (and edit) every lesson: no locks, and opening one isn't progress.
        if (($user->role ?? 'student') !== 'student') {
            return $next($request);
        }

        if (! CourseProgressService::isUnlocked($user, $module, $lesson, $course)) {
            // The fragment route is fetched by JS — a redirect would be followed
            // and the wrong lesson silently swapped in, so refuse it outright.
            if ($request->routeIs('student.planet.module.lesson.fragment') || $request->expectsJson()) {
                abort(403, 'This lesson is locked. Complete the previous challenge first (and, self-paced, beat the mini boss of the module before).');
            }

            // Self-paced, held at the end of a module: the way on is through its mini boss.
            if ($boss = CourseProgressService::blockingBoss($user, $module, $lesson, $course)) {
                return redirect(MiniBossService::urlForKey($course, $boss) ?? route('student.planet', CourseProgressService::planetFor($course)));
            }

            $target = CourseProgressService::furthestUnlocked($user, $course);
            // The first lesson not done is itself held by a mini boss: go there instead.
            if ($boss = CourseProgressService::blockingBoss($user, $target['module'], $target['lesson'], $course)) {
                return redirect(MiniBossService::urlForKey($course, $boss) ?? route('student.planet', CourseProgressService::planetFor($course)));
            }

            return redirect()->route('student.planet.module.lesson', [
                'slug' => $course,
                'module' => $target['module'],
                'lesson' => $target['lesson'],
            ]);
        }

        // A lesson with neither a coding challenge nor a lab has nothing to gate on,
        // so opening it (which required the previous one to be complete) counts as
        // done. Otherwise the course would dead-end at the first challenge-less
        // lesson. A lab lesson is only completed by passing its lab.
        if (CourseProgressService::expectedFor($module, $lesson, $course) === null
            && CourseProgressService::labFor($module, $lesson, $course) === null) {
            CourseProgressService::markComplete($user, $module, $lesson, $course);
        }

        return $next($request);
    }
}
