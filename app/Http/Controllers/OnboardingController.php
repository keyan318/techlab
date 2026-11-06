<?php

namespace App\Http\Controllers;

use App\Services\AvatarService;
use App\Services\StudentDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Onboarding after sign-up: Astro says hello, the student builds their animal astronaut and
 * picks a codename. The same builder (without Astro's intro) is where they change both later.
 * Captains (faculty) get their own version: captain-only animals and suits, and no codename.
 * The cat and the Explorer suit are free; a student buys the other animals and suits with XP (prices in
 * config/avatar.php, sold by ShopController, which the builder calls too) before wearing them.
 */
class OnboardingController extends Controller
{
    /** Right after sign-up: Astro's hello, the builder, the codename, then "welcome to the crew". */
    public function welcome(): View
    {
        return $this->builder(true);
    }

    /** Change your look or codename, any time (Settings links here). */
    public function edit(): View
    {
        return $this->builder(false);
    }

    public function save(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $look = $this->validLook($request, 'student');
        $purchased = AvatarService::purchased($user);
        foreach (['animal' => 'animals', 'suit' => 'suits'] as $kind => $list) {
            if (! in_array($look[$kind], AvatarService::owned($user, $kind, $purchased), true)) {
                $label = AvatarService::config()[$list][$look[$kind]]['label'];

                return back()->withInput()->withErrors([$kind => "Get the {$label} {$kind} in the Shop before you wear it."]);
            }
        }
        ['codename' => $codename] = $request->validate([
            'codename' => ['required', 'string', function (string $attr, mixed $value, \Closure $fail) use ($user) {
                if ($problem = AvatarService::codenameProblem((string) $value, $user)) {
                    $fail($problem);
                }
            }],
        ]);

        $user->update(['avatar' => $look, 'codename' => $codename]);

        return redirect()->route('student.dashboard')
            ->with('status', $request->boolean('onboarding') ? "You're all suited up!" : 'Looking sharp in the new suit!');
    }

    /** A new captain: Astro's hello, then they build their captain (no codename: crews see their real name). */
    public function captainWelcome(): View
    {
        return $this->builder(true, 'faculty');
    }

    public function captainEdit(): View
    {
        return $this->builder(false, 'faculty');
    }

    public function captainSave(Request $request): RedirectResponse
    {
        Auth::user()->update(['avatar' => $this->validLook($request, 'faculty')]);

        return redirect()->route('faculty.dashboard')
            ->with('status', $request->boolean('onboarding') ? 'Welcome to the bridge, Captain!' : 'Looking sharp, Captain!');
    }

    /** Admins wear a captain too: same builder and choices as faculty, saved back to the admin dashboard. */
    public function adminEdit(): View
    {
        return $this->builder(false, 'admin');
    }

    public function adminSave(Request $request): RedirectResponse
    {
        Auth::user()->update(['avatar' => $this->validLook($request, 'admin')]);

        return redirect()->route('admin.dashboard')->with('status', 'Looking sharp!');
    }

    /** Live check while typing a codename in the builder: is it free? */
    public function checkCodename(Request $request): JsonResponse
    {
        $problem = AvatarService::codenameProblem((string) $request->query('codename', ''), Auth::user());

        return response()->json(['available' => $problem === null, 'message' => $problem]);
    }

    /** The posted look, checked against the animals and suits $role can pick. */
    private function validLook(Request $request, string $role): array
    {
        $c = AvatarService::config();
        $choices = AvatarService::choicesFor($role);
        $animal = $request->validate(['animal' => ['required', Rule::in(array_keys($choices['animals']))]])['animal'];

        $data = $request->validate([
            'coat' => ['required', Rule::in(array_keys($c['animals'][$animal]['coats']))],
            'suit' => ['required', Rule::in(array_keys($choices['suits']))],
            'suit_color' => ['required', Rule::in(array_keys($c['suit_colors']))],
            'patch' => ['required', Rule::in(array_keys($c['patches']))],
            'helmet' => ['required', 'boolean'],
            'visor' => ['required', Rule::in(array_keys($c['visors']))],
        ]);

        return AvatarService::normalize(['animal' => $animal] + $data, $role);
    }

    private function builder(bool $intro, string $role = 'student'): View
    {
        $user = Auth::user();
        $c = AvatarService::config();
        $choices = AvatarService::choicesFor($role);
        $captain = $role !== 'student';
        // Back from a failed save: keep what they had picked instead of the saved look.
        $look = AvatarService::normalize(old('animal') ? old() : $user->avatar, $role);

        return view('student.welcome', [
            'intro' => $intro,
            'captain' => $captain,
            'user' => $user,
            'look' => $look,
            'codename' => $captain ? null : old('codename', $user->codename ?? AvatarService::suggestCodename($look['animal'])),
            'saveUrl' => route($role.'.avatar.save'),
            'homeUrl' => route($role.'.dashboard'),
            // Animals and suits for XP, bought through the Shop (captains own all of theirs and never pay).
            'shop' => $captain ? null : [
                'xp' => StudentDashboardService::balance($user),
                'owned' => ['animal' => AvatarService::ownedAnimals($user, $purchased = AvatarService::purchased($user)),
                    'suit' => AvatarService::ownedSuits($user, $purchased)],
                'buyUrl' => route('student.shop.buy', ['kind' => '__kind__', 'key' => '__key__']),
                'shopUrl' => route('student.shop'),
            ],
            // Everything the browser needs to draw and offer the options (same data AvatarService draws from).
            'avatarConfig' => [
                'ink' => $c['ink'], 'glass' => $c['glass'], 'animals' => $choices['animals'], 'suits' => $choices['suits'],
                'suitColors' => $c['suit_colors'], 'patches' => $c['patches'], 'visors' => $c['visors'],
                'helmet' => $c['helmet'], 'codenames' => $c['codenames'],
            ],
        ]);
    }
}
