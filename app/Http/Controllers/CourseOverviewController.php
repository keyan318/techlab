<?php

namespace App\Http\Controllers;

use App\Services\CourseOverviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseOverviewController extends Controller
{
    /**
     * A course's overview: its chapters and what the student has done in each.
     */
    public function show(string $slug, string $course): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') === 'faculty') {
            return redirect(route('faculty.dashboard'));
        }

        $data = CourseOverviewService::build(Auth::user(), $slug, $course);

        abort_if($data === null, 404);

        return view('student.course-overview', $data);
    }
}
