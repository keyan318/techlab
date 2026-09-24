<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Real module/lesson counts drive the skill bars, so the page matches the app.
        $skills = collect(['programming' => 'Coding', 'networking' => 'Networking'])
            ->map(function ($name, $slug) {
                $modules = config("course-structure.{$slug}.modules", []);

                return [
                    'name' => $name,
                    'modules' => count($modules),
                    'lessons' => collect($modules)->sum(fn ($m) => count($m['lessons'])),
                    'locked' => false,
                ];
            })->values()->all();

        $skills[] = ['name' => 'Cybersecurity', 'modules' => 3, 'lessons' => null, 'locked' => true];

        return view('landingpage', ['skills' => $skills]);
    }

    /**
     * The faculty front page. Signed-in users go straight to their own home.
     */
    public function faculty()
    {
        if (Auth::check()) {
            return redirect(AuthController::homeFor(Auth::user()));
        }

        return view('faculty.landing');
    }
}
