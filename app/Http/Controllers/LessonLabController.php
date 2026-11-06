<?php

namespace App\Http\Controllers;

use App\Services\CourseProgressService;
use App\Services\CourseStructure;
use App\Services\QuizGateService;
use App\Services\SimLabService;
use Illuminate\Http\JsonResponse;
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
        // Networking 2's router-console labs (BGP, QoS, VPNs...): a Cisco-style console, no packets.
        'console' => [
            'path' => 'relay-console/index.html',
            'source' => 'console',
            'name' => 'Relay Console',
            'hint' => 'Type the commands at the router prompt. Commands can be shortened, like <code>sh ip int br</code>.',
        ],
        'citadel' => [
            'path' => 'citadel-sim/index.html',
            'source' => 'citadel',
            'name' => 'Citadel Defense Lab',
            'hint' => 'Clear every objective in the simulator to repel the attack.',
        ],
    ];

    /**
     * Courses whose labs play background music (public/citadel-sim/js/music.js). Citadel Sim plays it
     * inside the simulator (?music=1); NetSim and Relay Console labs, and the Programming code editor,
     * play it from the TechLab page around them. Add a course slug here to roll it out (IAS2 hasn't got it yet).
     */
    public const MUSIC_COURSES = ['cybersecurity', 'networking', 'networking-2', 'computer-programming-1', 'computer-programming-2'];

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

        // The lab opens once the lesson's Knowledge Check is passed (at most 2 mistakes).
        if (! app(QuizGateService::class)->allows($user, $slug, $module, $lesson)) {
            return redirect()->route('student.planet.module.lesson', $params);
        }

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
            // The faculty's version of the lab, when they rewrote it; otherwise the simulator loads its own file.
            'labFile' => SimLabService::edited($slug, $module, $lesson) ? route('student.planet.lab.file', $params, false) : null,
            'nextUrl' => $next ? route('student.planet.module.lesson', ['slug' => $slug] + $next) : null,
            'alreadyDone' => CourseProgressService::isCompleted($user, $module, $lesson, $slug),
            'music' => in_array($slug, self::MUSIC_COURSES, true),
        ], 200, ['Cache-Control' => 'no-store, private']);
    }

    /** GET /student/planet/{slug}/lab/{module}/{lesson}/file.json: the lab file with the faculty's changes. */
    public function file(string $slug, string $module, string $lesson): JsonResponse
    {
        abort_unless(CourseProgressService::exists($slug), 404);
        $file = SimLabService::file($slug, $module, $lesson);
        abort_unless($file, 404);

        return response()->json($file, 200, ['Cache-Control' => 'no-store, private']);
    }

    /** "m1" + "lesson03" => "1.3" */
    private function number(string $module, string $lesson): string
    {
        return (int) preg_replace('/\D/', '', $module).'.'.(int) preg_replace('/\D/', '', $lesson);
    }

    private function title(string $slug, string $module, string $lesson): string
    {
        foreach (CourseStructure::modules($slug) as $mKey => $moduleDef) {
            foreach ($moduleDef['lessons'] as $lKey => $def) {
                if (CourseProgressService::normalize($mKey, $lKey) === [$module, $lesson]) {
                    return $def['title'];
                }
            }
        }

        return 'Lab';
    }
}
