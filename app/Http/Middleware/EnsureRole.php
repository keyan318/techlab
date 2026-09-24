<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets only accounts with one of the given roles through, e.g. `role:admin`.
 * Guests are sent to the login page; anyone else gets a 403.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        abort_unless(in_array($user->role, $roles, true), 403);

        return $next($request);
    }
}
