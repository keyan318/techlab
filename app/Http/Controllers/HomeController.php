<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Landing page. Signed-in users skip the marketing page and go
     * straight to their role's dashboard.
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect(
                (Auth::user()->role ?? 'student') === 'teacher'
                    ? '/teacher/dashboard'
                    : '/dashboard'
            );
        }

        return view('landingpage');
    }
}
