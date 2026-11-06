<?php

namespace App\Http\Controllers;

use App\Mail\TemporaryPassword;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * "Forgot password?" for students and faculty: we email a temporary password that
 * works for TEMP_MINUTES. The old password keeps working until the temporary one is
 * used, so someone typing another person's email can't lock them out. Signing in with
 * the temporary password sends you straight to "choose a new password".
 * Admins are left out on purpose: they're made (and reset) with `php artisan`.
 */
class PasswordResetController extends Controller
{
    public const TEMP_MINUTES = 60;

    private const AUDIENCES = ['student', 'faculty'];

    public function showForgot(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(AuthController::homeFor(Auth::user()));
        }

        return view('auth.forgot-password', ['audience' => $this->audience($request->query('for'))]);
    }

    public function sendTemporary(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'audience' => ['nullable', Rule::in(self::AUDIENCES)],
        ]);

        $user = User::where('email', $request->email)->whereIn('role', self::AUDIENCES)->first();

        if ($user) {
            $temporary = self::makeTemporaryPassword();

            // Keep every unexpired one: asking twice shouldn't kill the password in the
            // first email (Gmail threads them together, so the older one is easy to grab).
            $valid = array_filter(Cache::get(self::cacheKey($user), []), fn ($t) => $t['expires_at'] > now()->timestamp);
            $valid[] = ['hash' => Hash::make($temporary), 'expires_at' => now()->addMinutes(self::TEMP_MINUTES)->timestamp];
            Cache::put(self::cacheKey($user), array_slice(array_values($valid), -5), now()->addMinutes(self::TEMP_MINUTES));

            try {
                Mail::to($user->email)->send(new TemporaryPassword($user, $temporary, self::TEMP_MINUTES));
            } catch (\Throwable $e) {
                Log::error('Temporary password email failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        // Same answer whether or not the account exists, so this page can't be used
        // to find out who has a TechLab account.
        return back()->withInput($request->only('email'))->with('status',
            'If that email has a TechLab account, a temporary password is on its way. Check your inbox (and spam). It works for '.self::TEMP_MINUTES.' minutes.');
    }

    /**
     * Called by the login forms when the normal password didn't match. Signs the user
     * in if they typed a valid, unexpired temporary password, and sends them to pick a
     * new one. Returns null when it isn't a temporary password either.
     */
    public static function attemptTemporary(Request $request, ?string $role = null): ?RedirectResponse
    {
        $user = User::where('email', $request->input('email'))->whereIn('role', self::AUDIENCES)->first();
        if (! $user || ($role && $user->role !== $role)) {
            return null;
        }

        // Copying from an email can drag a space along, so ignore spaces at the ends.
        $typed = trim((string) $request->input('password'));
        $match = collect(Cache::get(self::cacheKey($user), []))
            ->first(fn ($t) => $t['expires_at'] > now()->timestamp && Hash::check($typed, $t['hash']));
        if (! $match) {
            return null;
        }

        Cache::forget(self::cacheKey($user));

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->put('role', $user->role);

        return redirect()->route('password.new');
    }

    public function showNew(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return view('auth.new-password', ['audience' => $this->audience(Auth::user()->role)]);
    }

    public function saveNew(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = $request->password;
        $user->save();

        return redirect(AuthController::homeFor($user));
    }

    private static function cacheKey(User $user): string
    {
        return 'temporary-passwords:'.$user->id;
    }

    /**
     * Easy to read and type from an email: no 0/O or 1/l/I look-alikes.
     */
    private static function makeTemporaryPassword(): string
    {
        $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';
        for ($i = 0; $i < 12; $i++) {
            $out .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $out;
    }

    private function audience(?string $value): string
    {
        return in_array($value, self::AUDIENCES, true) ? $value : 'student';
    }
}
