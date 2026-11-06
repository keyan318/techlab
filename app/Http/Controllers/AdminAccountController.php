<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Settings → Admins: the first admin (User::isOwnerAdmin) adds other admins, making their
 * name, email and password, and can remove them again. Other admins only see the list.
 */
class AdminAccountController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->isOwnerAdmin(), 403);

        $data = $request->validateWithBag('admin', [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            // Same rule as `php artisan admin:create`.
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ], [
            'email.unique' => 'An account with that email already exists.',
        ]);

        User::create([
            'name' => User::joinName($data['first_name'], $data['last_name']),
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'role' => 'admin',
        ]);

        return back()->with('settingsStatus', "Admin {$data['email']} added. They sign in at /admin/login.");
    }

    public function destroy(User $admin): RedirectResponse
    {
        abort_unless(Auth::user()->isOwnerAdmin(), 403);
        abort_unless($admin->role === 'admin' && ! $admin->isOwnerAdmin(), 404);

        $admin->delete();

        return back()->with('settingsStatus', "Admin {$admin->email} removed.");
    }
}
