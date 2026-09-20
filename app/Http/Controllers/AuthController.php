<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form (or bounce an already-authenticated user).
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect(Auth::user()->role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard'));
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
            'role' => ['sometimes', 'in:student,teacher'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->put('role', Auth::user()->role ?? 'student');

            // Always land a fresh login on the correct first page for their role.
            // Using an explicit redirect (not `intended()`) keeps students on the
            // student dashboard instead of bouncing them back to wherever they last
            // were (e.g. the crew home blade) before the session expired.
            $role = Auth::user()->role ?? 'student';

            return redirect($role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard'));
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
            return redirect(Auth::user()->role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard'));
        }

        return view('auth.register');
    }

    /**
     * Handle a registration, log the new user in, and send them to their dashboard.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', 'in:student,teacher'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->input('role', 'student'),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('role', $user->role);

        return redirect($user->role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard'));
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
}
