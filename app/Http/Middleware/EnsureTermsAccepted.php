<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Students and faculty must agree to the current Terms (config/terms.php) before anything else,
 * onboarding (/welcome) included. Until they do, every page sends them to /terms, which then
 * returns them where they were headed. Admins are made by hand and skip it.
 */
class EnsureTermsAccepted
{
    /** Routes a user who hasn't agreed yet can still use. */
    private const OPEN = ['terms', 'terms.accept', 'logout', 'account.destroy'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role === 'admin' || $user->hasAcceptedTerms() || $request->routeIs(...self::OPEN)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Please agree to the TechLab Terms first.', 'terms' => route('terms')], 403);
        }

        if ($request->isMethod('GET')) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        return redirect()->route('terms');
    }
}
