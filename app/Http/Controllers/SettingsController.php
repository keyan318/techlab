<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Settings (sidebar account menu → Settings), for students, faculty and admins: their name (and a student's program and year level),
 * password, look (links to the astronaut / captain builder), sound and theme (saved in
 * the browser, like the sidebar toggles) and deleting the account. Admins can't delete
 * themselves here; they get the Admins list instead (AdminAccountController).
 */
class SettingsController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();

        // Accounts from before first/last names were asked for: guess the split (last word = last name) until they save.
        $first = $user->first_name;
        $last = $user->last_name;
        if ($first === null && $last === null) {
            $parts = preg_split('/\s+/', trim((string) $user->name));
            $last = count($parts) > 1 ? array_pop($parts) : '';
            $first = implode(' ', $parts);
        }

        return view($user->role.'.settings', [
            'user' => $user, 'firstName' => $first, 'lastName' => $last,
        ] + ($user->role === 'admin' ? [
            'admins' => User::where('role', 'admin')->orderBy('id')->get(['id', 'name', 'email', 'created_at']),
            'isOwner' => $user->isOwnerAdmin(),
        ] : []));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $student = $user->role === 'student';

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            // Students keep their program and year up to date (the admin dashboard counts them); faculty have neither.
            'program' => [Rule::requiredIf($student), Rule::prohibitedIf(! $student), Rule::in(array_keys(User::PROGRAMS))],
            'year_level' => [Rule::requiredIf($student), Rule::prohibitedIf(! $student), 'integer', Rule::in(array_keys(User::YEAR_LEVELS))],
        ], [
            'program.required' => 'Pick your program.',
            'year_level.required' => 'Pick your year level.',
        ]);

        $user->update([
            'name' => User::joinName($data['first_name'], $data['last_name']),
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
        ] + ($student
            ? ['program' => $data['program'], 'year_level' => (int) $data['year_level']]
            : []));

        return back()->with('settingsStatus', 'Profile saved.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            // Admins keep the longer minimum `php artisan admin:create` asks for.
            'password' => ['required', 'string', 'min:'.(Auth::user()->role === 'admin' ? 12 : 8), 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => "That isn't your current password.",
            'password.different' => 'Pick a password different from your current one.',
        ]);

        $user = Auth::user();
        $user->password = $request->password;
        $user->save();

        return back()->with('settingsStatus', 'Password changed.');
    }
}
