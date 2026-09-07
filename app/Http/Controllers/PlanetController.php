<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PlanetController extends Controller
{
    /**
     * The available mission planets (student learning tracks).
     */
    private const PLANETS = ['programming', 'networking', 'cybersecurity'];

    /**
     * Per-planet course shells.
     *
     * Each planet exposes a small placeholder curriculum so the course player
     * UI has something to render before the real content lands. The shape is
     * intentionally simple so it can be swapped for a database-driven model
     * later without touching the view layer.
     *
     * @return array<string, mixed>
     */
    private function courseShell(string $slug): array
    {
        return match ($slug) {
            'programming' => [
                'id' => 'programming',
                'title' => 'Programming City',
                'tag' => 'Programming track',
                'blurb' => 'A guided tour through the building blocks of software — from your first variable to shipping a small project.',
                'sections' => [
                    ['title' => 'Section 1 — Introduction', 'lessons' => [
                        ['title' => 'Welcome to Programming'],
                        ['title' => 'How a Computer Reads Code'],
                        ['title' => 'Setting Up Your Workspace'],
                    ]],
                    ['title' => 'Section 2 — Fundamentals', 'lessons' => [
                        ['title' => 'Variables and Values'],
                        ['title' => 'Conditions and Logic'],
                        ['title' => 'Loops and Repetition'],
                        ['title' => 'Functions'],
                    ]],
                    ['title' => 'Section 3 — Practice', 'lessons' => [
                        ['title' => 'Working with Lists'],
                        ['title' => 'Strings and Text'],
                        ['title' => 'Reading Errors'],
                    ]],
                    ['title' => 'Section 4 — Build', 'lessons' => [
                        ['title' => 'Planning a Small Project'],
                        ['title' => 'Putting It Together'],
                        ['title' => 'Module Checkpoint'],
                    ]],
                ],
            ],
            'networking' => [
                'id' => 'networking',
                'title' => 'Networking Nebula',
                'tag' => 'Networking track',
                'blurb' => 'A guided tour of how machines talk — packets, protocols, and the paths between them.',
                'sections' => [
                    ['title' => 'Section 1 — Introduction', 'lessons' => [
                        ['title' => 'Welcome to Networking'],
                        ['title' => 'What Is a Network?'],
                        ['title' => 'A Tour of the Internet'],
                    ]],
                    ['title' => 'Section 2 — Fundamentals', 'lessons' => [
                        ['title' => 'IP Addresses'],
                        ['title' => 'Packets and Routing'],
                        ['title' => 'DNS Basics'],
                    ]],
                    ['title' => 'Section 3 — Practice', 'lessons' => [
                        ['title' => 'Reading a Trace'],
                        ['title' => 'Common Tools'],
                        ['title' => 'Diagnosing Issues'],
                    ]],
                    ['title' => 'Section 4 — Build', 'lessons' => [
                        ['title' => 'Designing a Small LAN'],
                        ['title' => 'Putting It Together'],
                        ['title' => 'Module Checkpoint'],
                    ]],
                ],
            ],
            'cybersecurity' => [
                'id' => 'cybersecurity',
                'title' => 'Cybersecurity Citadel',
                'tag' => 'Cybersecurity track',
                'blurb' => 'A guided tour of how to defend systems — threats, defenses, and the mindset of a defender.',
                'sections' => [
                    ['title' => 'Section 1 — Introduction', 'lessons' => [
                        ['title' => 'Welcome to Cybersecurity'],
                        ['title' => 'Threats in the Wild'],
                        ['title' => 'The Defender Mindset'],
                    ]],
                    ['title' => 'Section 2 — Fundamentals', 'lessons' => [
                        ['title' => 'Authentication Basics'],
                        ['title' => 'Encryption Concepts'],
                        ['title' => 'Common Attack Surfaces'],
                    ]],
                    ['title' => 'Section 3 — Practice', 'lessons' => [
                        ['title' => 'Reading Logs'],
                        ['title' => 'Spotting Suspicious Activity'],
                        ['title' => 'Hardening a System'],
                    ]],
                    ['title' => 'Section 4 — Build', 'lessons' => [
                        ['title' => 'Designing a Defense Plan'],
                        ['title' => 'Putting It Together'],
                        ['title' => 'Module Checkpoint'],
                    ]],
                ],
            ],
            default => [],
        };
    }

    /**
     * Normalize a course shell so the view can render it predictably:
     * flat lesson list with stable ids, progress states, and counts.
     *
     * @param  array<string, mixed>  $course
     * @return array<string, mixed>
     */
    private function normalizeCourse(array $course): array
    {
        $sections = [];
        $lessons = [];

        foreach ($course['sections'] as $sectionIndex => $section) {
            $sectionLessons = [];

            foreach ($section['lessons'] as $lessonIndex => $lesson) {
                $isFirstLesson = $sectionIndex === 0 && $lessonIndex === 0;
                $state = $isFirstLesson ? 'current' : 'not_started';

                $lessonId = $course['id'].'-s'.$sectionIndex.'-l'.$lessonIndex;
                $sectionLessons[] = [
                    'id' => $lessonId,
                    'title' => $lesson['title'],
                    'state' => $state,
                    'section' => $sectionIndex,
                    'resources' => [],
                ];
                $lessons[] = $lessonId;
            }

            $sections[] = [
                'title' => $section['title'],
                'lessons' => $sectionLessons,
                'completed' => 0,
                'total' => count($sectionLessons),
            ];
        }

        $total = count($lessons);
        $currentId = $lessons[0] ?? null;

        return [
            'id' => $course['id'],
            'title' => $course['title'],
            'tag' => $course['tag'],
            'blurb' => $course['blurb'],
            'sections' => $sections,
            'lessons' => $lessons,
            'current' => $currentId,
            'completed' => 0,
            'total' => $total,
            'percent' => 0,
        ];
    }

    /**
     * Show a planet's overview page. Unknown slugs 404; guests are sent to login.
     */
    public function show(string $slug): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        $course = $this->normalizeCourse($this->courseShell($slug));

        return view('student.planets.'.$slug, [
            'course' => $course,
        ]);
    }

    /**
     * Course overview: the learning-plan node path for a track.
     * Guests are sent to login; unknown slugs 404.
     */
    public function overview(string $slug): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        return view('student.planets.overview', [
            'slug' => $slug,
        ]);
    }

    /**
     * Lesson placeholder — the "Start learning" target until lessons are built.
     * Guests are sent to login; unknown slugs 404.
     */
    public function lesson(string $slug, string $lesson): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        $lessonView = 'student.planets.lesson-'.$lesson;

        return view(
            view()->exists($lessonView) ? $lessonView : 'student.planets.lesson-stub',
            ['slug' => $slug, 'lesson' => $lesson]
        );
    }

    /**
     * Chapter story — comic-panel intro before a lesson's interactive part.
     * Guests are sent to login; unknown slugs 404. Chapter metadata is
     * hardcoded per lesson for now (only Thinking in Code has a story).
     */
    public function story(string $slug, string $lesson): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        $chapters = [
            'thinking-in-code' => ['number' => '1', 'title' => 'The Landing'],
        ];

        return view('student.planets.story', [
            'slug' => $slug,
            'lesson' => $lesson,
            'chapterNumber' => $chapters[$lesson]['number'] ?? '1',
            'chapterTitle' => $chapters[$lesson]['title'] ?? ucfirst(str_replace('-', ' ', $lesson)),
        ]);
    }

    /**
     * POST /student/planet/{slug}/plan — learning-plan generation.
     * (Unchanged — placeholder payload for the onboarding carousel flow.)
     */
    public function generatePlan(string $slug): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if (! in_array($slug, self::PLANETS, true)) {
            return response()->json(['error' => 'Planet not found.'], 404);
        }

        $nodes = [
            ['label' => 'Thinking in Code', 'locked' => false],
            ['label' => 'Programming with Variables', 'locked' => true],
            ['label' => 'Programming with Functions', 'locked' => true],
            ['label' => 'Algorithmic Thinking', 'locked' => true],
            ['label' => 'Build Your First Project', 'locked' => true],
        ];

        return response()->json([
            'ok' => true,
            'plan' => [
                'slug' => $slug,
                'title' => $this->courseShell($slug)['title'],
                'nodes' => $nodes,
            ],
        ]);
    }

    /**
     * Resolve a module + lesson pair to its view name.
     *
     * Your lesson files are named "lesson-01" (with a dash), but the
     * sidebar/URL sends "lesson01" (no dash). Rather than force a
     * rename across every lesson file, this checks BOTH naming styles
     * and uses whichever one actually exists on disk. Returns null if
     * neither is found.
     */
    private function resolveLessonView(string $slug, string $module, string $lesson): ?string
    {
        $moduleKey = strtoupper($module);
        $base = "student.planets.{$slug}.python_course.{$moduleKey}.";

        $candidates = [$lesson];

        // "lesson01" -> also try "lesson-01"
        if (preg_match('/^lesson(\d+)$/', $lesson, $m)) {
            $candidates[] = 'lesson-'.$m[1];
        }

        // "lesson-01" -> also try "lesson01"
        if (preg_match('/^lesson-(\d+)$/', $lesson, $m)) {
            $candidates[] = 'lesson'.$m[1];
        }

        foreach ($candidates as $candidate) {
            $view = $base.$candidate;
            if (view()->exists($view)) {
                return $view;
            }
        }

        return null;
    }

    /**
     * GET /student/planet/{slug}/{module}/{lesson}
     *
     * Renders the FULL course shell (sidebar + main panel), with the
     * requested lesson already rendered server-side inside it. This is
     * the URL used for first load, browser refresh, and direct/shared
     * links — it must always work even with JavaScript disabled.
     */
    public function viewModuleLesson(string $slug, string $module, string $lesson): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        $view = $this->resolveLessonView($slug, $module, $lesson);

        // Shell renders an "empty-lesson" state itself if $view is null,
        // so we don't hard-404 here — a typo'd lesson id still shows the
        // course chrome instead of a blank error page.
        return view('student.planets.'.$slug, [
            'slug' => $slug,
            'module' => strtolower($module),
            'lesson' => $lesson,
            'lessonView' => $view,
        ]);
    }

    /**
     * GET /student/planet/{slug}/{module}/{lesson}/fragment
     *
     * Returns ONLY the compiled lesson HTML — no layout, no sidebar,
     * no <html>/<head>/<body>. This is what the shell's JavaScript
     * fetches and drops into #lesson-stage when a sidebar item (or the
     * prev/next arrows) is clicked. This is the endpoint that makes
     * lesson switching happen with zero page reloads.
     */
    public function lessonFragment(string $slug, string $module, string $lesson): Response
    {
        if (! Auth::check()) {
            abort(401);
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        $view = $this->resolveLessonView($slug, $module, $lesson);

        if (! $view) {
            // TEMPORARY DEBUG — remove once lessons are loading correctly.
            // Shows exactly which view names Laravel tried and the physical
            // file paths it expects, right on screen.
            $moduleKey = strtoupper($module);
            $base = "student.planets.{$slug}.python_course.{$moduleKey}.";
            $tried = [$lesson];

            if (preg_match('/^lesson(\d+)$/', $lesson, $m)) {
                $tried[] = 'lesson-'.$m[1];
            }
            if (preg_match('/^lesson-(\d+)$/', $lesson, $m)) {
                $tried[] = 'lesson'.$m[1];
            }

            $lines = array_map(function ($candidate) use ($base) {
                $viewName = $base.$candidate;
                $path = resource_path('views/'.str_replace('.', '/', $viewName).'.blade.php');

                return '<p style="margin-top:6px; font-size:13px;">Tried <code>'.e($viewName).'</code> → <code>'.e($path).'</code></p>';
            }, $tried);

            return response(
                '<div class="empty-lesson"><div>'
                .'<h1>Lesson not found</h1>'
                .implode('', $lines)
                .'</div></div>',
                404
            );
        }

        return response(view($view)->render());
    }

    /**
     * GET /student/planet/{slug}/editor
     *
     * The Pyodide-powered "Code it yourself" page. This method knows
     * nothing about specific lessons or modules — it just reads whatever
     * the linking lesson page put in the query string:
     *
     *   starter_code  - pre-fills the textarea (optional, defaults to blank)
     *   expected      - if present, shows the "Check Answer" button and
     *                   the editor compares printed output against this
     *                   (optional — omit it for open-ended challenges)
     *   return_to     - full URL the "Back to lesson" button should use
     *                   (optional, falls back to the planet overview)
     *
     * Every lesson builds its own link to this same route with its own
     * values. This method — and the view it renders — never need to
     * change again when new lessons are added.
     */
    public function editor(string $slug, Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        return view('student.planets.python-editor', [
            'slug' => $slug,
            'starterCode' => (string) $request->query('starter_code', ''),
            'expected' => $request->query('expected'),
            'returnTo' => $request->query('return_to'),
        ]);
    }
}