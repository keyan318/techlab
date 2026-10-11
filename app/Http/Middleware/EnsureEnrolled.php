<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\CourseProgressService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only lets a student into the planets they enrolled in at sign-up.
 * Guests pass through (the controllers already send them to login).
 */
class EnsureEnrolled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // A route's {slug} may be a planet's own key, or a planet's second+
        // course (keyed by its catalog id) — either way, enrolment is checked
        // against the planet it belongs to.
        $slug = CourseProgressService::planetFor((string) $request->route('slug'));

        // Unknown slugs fall through so the controller answers 404.
        if ($user && in_array($slug, User::PLANETS, true) && ! $user->isEnrolledIn($slug)) {
            if ($request->expectsJson()) {
                abort(403, 'You are not enrolled in this planet.');
            }

            return redirect()->route('student.planets')
                ->with('status', 'You did not enroll in that planet.');
        }

        return $next($request);
    }
}
