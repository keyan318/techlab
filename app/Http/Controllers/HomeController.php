<?php

namespace App\Http\Controllers;

use App\Services\CourseProgressService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // The same three planet cards as the in-app planet picker. Lesson and course counts are
        // real (every course on the planet, from config/course-structure.php), so the page matches the app.
        $art = [
            'programming' => ['images/programming-city-pixel.png', 1250, 500, 'prog'],
            'networking' => ['images/networking-nebula.png', 1250, 500, 'net'],
            'cybersecurity' => ['images/cybersecurity-citadel.png', 1250, 500, 'cyber'],
        ];
        $blurbs = [
            'programming' => 'Build with code: logic, apps, and the foundations of software.',
            'networking' => 'How the internet works: routing, protocols, and connections.',
            'cybersecurity' => 'Defend systems: threats, encryption, and secure design.',
        ];

        $skills = collect($art)->map(function (array $a, string $slug) use ($blurbs) {
            [$src, $w, $h, $class] = $a;
            $lessons = collect(CourseProgressService::keysForPlanet($slug))
                ->sum(fn ($key) => count(CourseProgressService::order($key)));

            return [
                'name' => config("course-catalog.{$slug}.title", ucfirst($slug)),
                'blurb' => $blurbs[$slug],
                'courses' => collect(config("course-catalog.{$slug}.courses", []))->reject(fn ($c) => ! empty($c['hidden']))->count(),
                'lessons' => $lessons,
                'art' => $src, 'w' => $w, 'h' => $h, 'class' => $class,
            ];
        })->values()->all();

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
