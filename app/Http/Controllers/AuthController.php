<?php

namespace App\Http\Controllers;

use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Where each kind of account lands after signing in.
     */
    public static function homeFor(User $user): string
    {
        return match ($user->role) {
            'faculty' => route('faculty.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('student.dashboard'),
        };
    }

    /**
     * Show the login form (or bounce an already-authenticated user).
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(self::homeFor(Auth::user()));
        }

        return view('auth.login');
    }

    /**
     * Handle a login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->put('role', Auth::user()->role ?? 'student');

            // Always land a fresh login on the correct first page for their role.
            // Using an explicit redirect (not `intended()`) keeps students on the
            // student dashboard instead of bouncing them back to wherever they last
            // were (e.g. the crew home blade) before the session expired.
            return redirect(self::homeFor(Auth::user()));
        }

        if ($temporary = PasswordResetController::attemptTemporary($request)) {
            return $temporary;
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    /**
     * Show the registration form (or bounce an already-authenticated user).
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(self::homeFor(Auth::user()));
        }

        return view('auth.register');
    }

    /**
     * Handle a registration, log the new user in, and send them to their dashboard.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'planets' => ['required', 'array', 'min:1', 'max:1'],
            'planets.*' => ['string', Rule::in(User::PLANETS)],
            'program' => ['required', Rule::in(array_keys(User::PROGRAMS))],
            'year_level' => ['required', 'integer', Rule::in(array_keys(User::YEAR_LEVELS))],
        ], [
            'program.required' => 'Pick your program.',
            'program.in' => 'Pick your program.',
            'year_level.required' => 'Pick your year level.',
            'year_level.in' => 'Pick your year level.',
            'planets.required' => 'Pick the planet you want to start with.',
            'planets.min' => 'Pick the planet you want to start with.',
            'planets.max' => 'Pick only one planet to start — you unlock the rest by earning gems.',
        ]);

        // Public sign-up only ever makes students. Faculty accounts are never
        // created from this form, so nobody can pick their own role.
        $user = User::create([
            'name' => User::joinName($request->first_name, $request->last_name),
            'first_name' => trim($request->first_name),
            'last_name' => trim($request->last_name),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'student',
            'planets' => array_values(array_unique($request->input('planets'))),
            'program' => $request->program,
            'year_level' => (int) $request->year_level,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('role', $user->role);
        // A freshly created account is the only time Astro's tour (components/astro-tour) starts by itself.
        $request->session()->put('tour_fresh', true);

        // A brand-new student meets Astro and builds their astronaut before the dashboard.
        return redirect()->route('student.welcome');
    }

    /**
     * Log the user out and return to the landing page so they can log back in
     * as a different crew member.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }

    /**
     * Permanently delete the signed-in account. Faculty who still captain a crew or
     * course are blocked first — deleting them out from under enrolled students would
     * orphan the crew's roster and content instead of cleanly handing it off.
     */
    public function destroyAccount(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }

        $user = Auth::user();

        if ($user->role === 'admin') {
            return back()->with('accountDeleteError', "Admin accounts can't be deleted here — ask another admin.");
        }

        if ($user->role === 'faculty' && (
            Crew::where('teacher_id', $user->id)->exists()
            || CourseAssignment::where('faculty_id', $user->id)->exists()
        )) {
            return back()->with('accountDeleteError', 'You still captain a crew or course — reassign it before deleting your account.');
        }

        // Log out first: Auth::logout() re-saves the user's remember token, which would
        // otherwise reinsert the row if it ran after delete() (Eloquent's delete() flips
        // `exists` to false, so a save() afterwards performs an insert, not an update).
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }

    public function showFacultyLogin(): View|RedirectResponse
    {
        return Auth::check() ? redirect(self::homeFor(Auth::user())) : view('auth.faculty-login');
    }

    public function facultyLogin(Request $request): RedirectResponse
    {
        return $this->loginAs('faculty', $request);
    }

    public function showFacultyRegister(): View|RedirectResponse
    {
        return Auth::check() ? redirect(self::homeFor(Auth::user())) : view('auth.faculty-register');
    }

    /**
     * Faculty create their own account. For now the only check is the school email domain.
     */
    public function facultyRegister(Request $request): RedirectResponse
    {
        $domain = strtolower((string) config('faculty.email_domain'));

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', function ($attribute, $value, $fail) use ($domain) {
                if ($domain !== '' && ! str_ends_with(strtolower($value), '@'.$domain)) {
                    $fail("Use your @{$domain} email to create a faculty account.");
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => User::joinName($request->first_name, $request->last_name),
            'first_name' => trim($request->first_name),
            'last_name' => trim($request->last_name),
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'faculty',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('role', 'faculty');
        $request->session()->put('tour_fresh', true);

        // Straight into onboarding: Astro says hello and they build their captain.
        return redirect()->route('faculty.welcome');
    }

    public function showAdminLogin(): View|RedirectResponse
    {
        return Auth::check() ? redirect(self::homeFor(Auth::user())) : view('auth.admin-login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        return $this->loginAs('admin', $request);
    }

    /**
     * Sign in, but only if the account really has this role. A student trying the
     * faculty or admin door gets the exact same error as a wrong password, so
     * the page never reveals which accounts exist.
     */
    private function loginAs(string $role, Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password') + ['role' => $role], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->put('role', $role);

            return redirect(self::homeFor(Auth::user()));
        }

        if ($role !== 'admin' && $temporary = PasswordResetController::attemptTemporary($request, $role)) {
            return $temporary;
        }

        if ($role === 'admin') {
            Log::warning('Failed admin login attempt', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }
}
