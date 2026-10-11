<?php

namespace App\Http\Controllers;

use App\Services\AvatarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Onboarding after sign-up: Astro says hello, the student builds their animal astronaut and
 * picks a codename. The same builder (without Astro's intro) is where they change both later.
 */
class OnboardingController extends Controller
{
    /** Right after sign-up: Astro's hello, the builder, the codename, then "welcome to the crew". */
    public function welcome(): View
    {
        return $this->builder(true);
    }

    /** Change your look or codename, any time (the sidebar account menu links here). */
    public function edit(): View
    {
        return $this->builder(false);
    }

    public function save(Request $request): RedirectResponse
    {
        $c = AvatarService::config();
        $user = Auth::user();
        $animal = $request->validate(['animal' => ['required', Rule::in(array_keys($c['animals']))]])['animal'];

        $data = $request->validate([
            'coat' => ['required', Rule::in(array_keys($c['animals'][$animal]['coats']))],
            'suit' => ['required', Rule::in(array_keys($c['suits']))],
            'suit_color' => ['required', Rule::in(array_keys($c['suit_colors']))],
            'patch' => ['required', Rule::in(array_keys($c['patches']))],
            'helmet' => ['required', 'boolean'],
            'visor' => ['required', Rule::in(array_keys($c['visors']))],
            'codename' => ['required', 'string', function (string $attr, mixed $value, \Closure $fail) use ($user) {
                if ($problem = AvatarService::codenameProblem((string) $value, $user)) {
                    $fail($problem);
                }
            }],
        ]);
        $codename = $data['codename'];
        unset($data['codename']);

        $user->update(['avatar' => AvatarService::normalize(['animal' => $animal] + $data), 'codename' => $codename]);

        return redirect()->route('student.dashboard')
            ->with('status', $request->boolean('onboarding') ? "You're all suited up!" : 'Looking sharp in the new suit!');
    }

    /** Live check while typing a codename in the builder: is it free? */
    public function checkCodename(Request $request): JsonResponse
    {
        $problem = AvatarService::codenameProblem((string) $request->query('codename', ''), Auth::user());

        return response()->json(['available' => $problem === null, 'message' => $problem]);
    }

    private function builder(bool $intro): View
    {
        $user = Auth::user();
        $c = AvatarService::config();
        // Back from a failed save: keep what they had picked instead of the saved look.
        $look = AvatarService::normalize(old('animal') ? old() : $user->avatar);

        return view('student.welcome', [
            'intro' => $intro,
            'user' => $user,
            'look' => $look,
            'codename' => old('codename', $user->codename ?? AvatarService::suggestCodename($look['animal'])),
            // Everything the browser needs to draw and offer the options (same data AvatarService draws from).
            'avatarConfig' => [
                'ink' => $c['ink'], 'glass' => $c['glass'], 'animals' => $c['animals'], 'suits' => $c['suits'],
                'suitColors' => $c['suit_colors'], 'patches' => $c['patches'], 'visors' => $c['visors'],
                'helmet' => $c['helmet'], 'codenames' => $c['codenames'],
            ],
        ]);
    }
}
