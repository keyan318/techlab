<?php

namespace App\Http\Controllers;

use App\Services\CourseProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The full-page simulator for a lesson's hands-on lab ("Configure it yourself" / "Defend it yourself").
 *
 * The lesson page only carries a button; the simulator itself needs the whole screen, so it lives on its own
 * page. This page listens to the simulator, reports a pass through LessonProgressController::completeLab(),
 * and offers the way back to the lesson (and on to the next one).
 */
class LessonLabController extends Controller
{
    /**
     * The simulators a lab can run in. `source` is the postMessage source the page accepts from that simulator.
     */
    public const SIMS = [
        'netsim' => [
            'path' => 'netsim-app/app.html',
            'source' => 'netsim',
            'name' => 'Relay Lab',
            'hint' => 'Press <strong>Check objectives</strong> in the simulator when you are ready.',
        ],
        'citadel' => [
            'path' => 'citadel-sim/index.html',
            'source' => 'citadel',
            'name' => 'Citadel Defense Lab',
            'hint' => 'Clear every objective in the simulator to repel the attack.',
        ],
    ];

    /** GET /student/planet/{slug}/lab/{module}/{lesson}  (gated by the lesson.unlocked middleware) */
    public function show(Request $request, string $slug, string $module, string $lesson): Response|RedirectResponse
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

        // no-store: a restored or cached copy of this page would carry an old CSRF token and could not save a pass.
        $sim = self::SIMS[CourseProgressService::simFor($module, $lesson, $slug)] ?? self::SIMS['netsim'];

        return response()->view('student.planets.lab', [
            'slug' => $slug,
            'lab' => $lab,
            'sim' => $sim,
            'number' => $this->number($module, $lesson),
            'title' => $this->title($slug, $module, $lesson),
            'lessonUrl' => route('student.planet.module.lesson', $params),
            'completeUrl' => route('student.planet.lab.complete', $params),
            // Astro's live help chat is Citadel Sim only for now (NetSim isn't wired to it).
            'labHelpStatusUrl' => $sim['source'] === 'citadel' ? route('student.planet.lab-help.status', $params) : null,
            'labHelpUnlockUrl' => $sim['source'] === 'citadel' ? route('student.planet.lab-help.unlock', $params) : null,
            'labHelpAskUrl' => $sim['source'] === 'citadel' ? route('student.planet.lab-help.ask', $params) : null,
            'nextUrl' => $next ? route('student.planet.module.lesson', ['slug' => $slug] + $next) : null,
            'alreadyDone' => CourseProgressService::isCompleted($user, $module, $lesson, $slug),
        ], 200, ['Cache-Control' => 'no-store, private']);
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

        return 'Lab';
    }
}
