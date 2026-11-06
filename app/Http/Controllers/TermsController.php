<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The TechLab Terms and Conditions. Anyone can read them; a signed-in student or captain who
 * hasn't agreed to this version yet (EnsureTermsAccepted) gets the "I agree" box, and agreeing
 * carries them on to where they were going (onboarding, right after sign-up).
 */
class TermsController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('terms', [
            'user' => $user,
            'mustAgree' => $user && $user->role !== 'admin' && ! $user->hasAcceptedTerms(),
            'version' => config('terms.version'),
        ]);
    }

    public function accept(Request $request): RedirectResponse
    {
        $request->validate(['agree' => ['accepted']], ['agree.accepted' => 'Tick the box to agree to the Terms before you continue.']);

        $user = $request->user();
        $user->forceFill(['terms_accepted_at' => now(), 'terms_version' => config('terms.version')])->save();

        return redirect()->intended(AuthController::homeFor($user));
    }
}
