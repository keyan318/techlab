<?php

namespace App\Http\Controllers;

use App\Services\CourseProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The full-page simulator for a lesson's hands-on lab (the "Configure it yourself" button).
 *
 * The lesson page only carries a button; the simulator itself needs the whole screen, so it lives on its own
 * page. This page listens to the simulator, reports a pass through LessonProgressController::completeLab(),
 * and offers the way back to the lesson (and on to the next one).
 */
class LessonLabController extends Controller
{
    /** GET /student/planet/{slug}/lab/{module}/{lesson}  (gated by the lesson.unlocked middleware) */
    public function show(Request $request, string $slug, string $module, string $lesson): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! CourseProgressService::exists($slug)) {
            abort(404);
        }

        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $lab = CourseProgressService::labFor($module, $lesson, $slug);

        // Only lessons wired to a lab have a simulator page.
        if ($lab === null) {
            abort(404);
        }

        $next = CourseProgressService::nextLesson($module, $lesson, $slug);
        $params = ['slug' => $slug, 'module' => $module, 'lesson' => $lesson];

        return view('student.planets.lab', [
            'slug' => $slug,
            'lab' => $lab,
            'number' => $this->number($module, $lesson),
            'title' => $this->title($slug, $module, $lesson),
            'lessonUrl' => route('student.planet.module.lesson', $params),
            'completeUrl' => route('student.planet.lab.complete', $params),
            'nextUrl' => $next ? route('student.planet.module.lesson', ['slug' => $slug] + $next) : null,
            'alreadyDone' => CourseProgressService::isCompleted($user, $module, $lesson, $slug),
        ]);
    }

    /** "m1" + "lesson03" => "1.3" */
    private function number(string $module, string $lesson): string
    {
        return (int) preg_replace('/\D/', '', $module).'.'.(int) preg_replace('/\D/', '', $lesson);
    }

    private function title(string $slug, string $module, string $lesson): string
    {
        foreach (config("course-structure.{$slug}.modules", []) as $mKey => $moduleDef) {
            foreach ($moduleDef['lessons'] as $lKey => $def) {
                if (CourseProgressService::normalize($mKey, $lKey) === [$module, $lesson]) {
                    return $def['title'];
                }
            }
        }

        return 'Relay Lab';
    }
}
