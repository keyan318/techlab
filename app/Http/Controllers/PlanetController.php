<?php

namespace App\Http\Controllers;

use App\Services\CourseEnrollmentService;
use App\Services\CourseProgressService;
use App\Services\CourseStructure;
use App\Services\LessonQuizService;
use App\Services\QuizGateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PlanetController extends Controller
{
    /**
     * The available mission planets (student learning tracks).
     */
    private const PLANETS = ['programming', 'networking', 'cybersecurity'];

    /**
     * Whether {slug} in a lesson/editor URL is something with real lessons behind it:
     * a planet's own key, or a planet's second+ course, keyed by its catalog id
     * (config/course-structure.php names its planet — see CourseProgressService::planetFor()).
     */
    private function isValidCourseKey(string $slug): bool
    {
        return in_array($slug, self::PLANETS, true) || CourseProgressService::exists($slug);
    }

    /**
     * The course-player Blade view for a course key. Every config course's own view file
     * (student.planets.<key>) is just a one-line `@include('student.planets.course-player', ...)`
     * (see resources/views/student/planets/programming.blade.php); a faculty-created course has
     * no such file, so render the shared player directly for it instead.
     */
    private function playerView(string $slug): string
    {
        return view()->exists('student.planets.'.$slug) ? 'student.planets.'.$slug : 'student.planets.course-player';
    }

    /**
     * Per-planet course shells.
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
                'blurb' => 'Information Assurance and Security 1: defend the Citadel against Planet Doom.',
                'sections' => [
                    ['title' => 'M1 — First Contact with the Enemy', 'lessons' => [
                        ['title' => 'Know Your Citadel'],
                    ]],
                ],
            ],
            'information-assurance-security-2' => [
                'id' => 'information-assurance-security-2',
                'title' => 'Cybersecurity Citadel',
                'tag' => 'Cybersecurity track',
                'blurb' => 'Information Assurance and Security 2: rebuild the Citadel so it holds.',
                'sections' => [
                    ['title' => 'M1 — Rebuilding After the Siege', 'lessons' => [
                        ['title' => 'Why Information Protection Matters'],
                    ]],
                ],
            ],
            'networking-2' => [
                'id' => 'networking-2',
                'title' => 'Networking Nebula',
                'tag' => 'Networking track',
                'blurb' => 'Networking 2: light the Grid across Codexia.',
                'sections' => [
                    ['title' => 'M1 — Back Online', 'lessons' => [
                        ['title' => 'Switches, Routers & VLANs Revisited'],
                    ]],
                ],
            ],
            'computer-programming-1' => [
                'id' => 'computer-programming-1',
                'title' => 'Computer Programming 1',
                'tag' => 'Programming track',
                'blurb' => 'The Dead Console: rebuild Codexia\'s systems in C.',
                'sections' => [
                    ['title' => 'M1 — The Dead Console', 'lessons' => [
                        ['title' => 'Orders Astro Can Follow'],
                    ]],
                ],
            ],
            'computer-programming-2' => [
                'id' => 'computer-programming-2',
                'title' => 'Computer Programming 2',
                'tag' => 'Programming track',
                'blurb' => 'Switch the City On: bring Programming City back to life in C.',
                'sections' => [
                    ['title' => 'M1 — Down Into the City', 'lessons' => [
                        ['title' => 'The First Street'],
                    ]],
                ],
            ],
            // A faculty-created course (app/Models/Course) has no hand-authored shell: this decorative
            // placeholder is unused by course-player.blade.php (it reads the real outline itself via
            // CourseStructure::modules()), it only needs to not crash normalizeCourse() below.
            default => [
                'id' => $slug,
                'title' => CourseStructure::card(CourseProgressService::planetFor($slug), CourseProgressService::catalogFor($slug) ?? $slug)['title'] ?? $slug,
                'tag' => '',
                'blurb' => '',
                'sections' => [],
            ],
        };
    }

    /**
     * Normalize a course shell so the view can render it predictably.
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
     * Course picker: the courses a planet offers, shown before entering one.
     */
    public function courses(string $slug): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $planet = config("course-catalog.{$slug}");
        if (! in_array($slug, self::PLANETS, true) || ! $planet) {
            abort(404);
        }

        $user = Auth::user();

        $courses = collect(CourseStructure::cards($slug))->reject(fn (array $c) => ! empty($c['hidden']))
            ->map(fn (array $c, string $id) => $c + CourseEnrollmentService::forCard($user, $slug, $id))->values()->all();

        return view('student.planets.courses', ['slug' => $slug, 'planet' => $planet, 'courses' => $courses]);
    }

    /**
     * Show a course's overview page.
     */
    public function show(string $slug, ?string $course = null): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        if ($course !== null) {
            $meta = CourseStructure::card($slug, $course);
            abort_if($meta === [] || ! empty($meta['coming_soon']), 404);
        }

        // A planet's second+ course is keyed by its own catalog id, with its own courseShell()
        // arm, its own lesson blade tree and its own one-line view (student.planets.<courseKey>).
        // Rendering the planet's own shell/view here would put the second course's lesson inside
        // the FIRST course's sidebar — use the resolved key everywhere below, not the raw $slug.
        $courseKey = CourseProgressService::keyFor($slug, $course);
        // Every per-course view (student.planets.<key>) only ever passes 'slug' in its own
        // @include — playerView() may skip that file for a faculty-created course, so it has to
        // be in $data itself too.
        $data = ['slug' => $courseKey, 'course' => $this->normalizeCourse($this->courseShell($courseKey))];

        // Planets with a lesson blueprint render a lesson inside the course shell. Without
        // these the view falls back to a guessed name that never matches the
        // lesson-01.blade.php files on disk, so the page renders "Lesson not found".
        // The first lesson comes from the blueprint; resolveLessonView() understands both spellings.
        // A freshly created, still-empty course (CourseProgressService::exists() is true but there
        // is no first lesson yet) falls through the same way — nothing for the player to show yet.
        $order = CourseProgressService::exists($courseKey) ? CourseProgressService::order($courseKey) : [];
        if ($order !== []) {
            $first = $order[0];
            $data += [
                'module' => $first['module'],
                'lesson' => $first['lesson'],
                'lessonView' => $this->resolveLessonView($courseKey, $first['module'], $first['lesson']),
            ];
        }

        return view($this->playerView($courseKey), $data);
    }

    /**
     * Course overview: the learning-plan node path for a track.
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
            ['label' => 'Thinking in Code',            'locked' => false],
            ['label' => 'Programming with Variables',  'locked' => true],
            ['label' => 'Programming with Functions',  'locked' => true],
            ['label' => 'Algorithmic Thinking',        'locked' => true],
            ['label' => 'Build Your First Project',    'locked' => true],
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
     * Checks both "lesson01" and "lesson-01" naming styles.
     */
    private function resolveLessonView(string $slug, string $module, string $lesson): ?string
    {
        $base = $this->lessonViewPrefix($slug, $module);
        if ($base === null) {
            return null;
        }

        foreach ($this->lessonCandidates($lesson) as $candidate) {
            $view = $base.$candidate;
            if (view()->exists($view)) {
                return $view;
            }
        }

        return null;
    }

    /** "student.planets.programming.python_course.M1." for a planet + module, or null if the planet has no lessons. */
    private function lessonViewPrefix(string $slug, string $module): ?string
    {
        $base = CourseProgressService::viewBase($slug);

        return $base === null ? null : $base.'.'.strtoupper($module).'.';
    }

    /** @return string[] both spellings of a lesson name ("lesson01" / "lesson-01") */
    private function lessonCandidates(string $lesson): array
    {
        $candidates = [$lesson];

        if (preg_match('/^lesson(\d+)$/', $lesson, $m)) {
            $candidates[] = 'lesson-'.$m[1];
        }

        if (preg_match('/^lesson-(\d+)$/', $lesson, $m)) {
            $candidates[] = 'lesson'.$m[1];
        }

        return $candidates;
    }

    /**
     * GET /student/planet/{slug}/{module}/{lesson}
     * Full page load — renders the course shell with the lesson inside.
     */
    public function viewModuleLesson(string $slug, string $module, string $lesson): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! $this->isValidCourseKey($slug)) {
            abort(404);
        }

        $view = $this->resolveLessonView($slug, $module, $lesson);

        return view($this->playerView($slug), [
            // The networking/cybersecurity shells read $course; show() passes it and
            // this action must too, or they die with "Undefined variable $course".
            'course' => $this->normalizeCourse($this->courseShell($slug)),
            'slug' => $slug,
            'module' => strtolower($module),
            'lesson' => $lesson,
            'lessonView' => $view,
        ]);
    }

    /**
     * GET /student/planet/{slug}/{module}/{lesson}/fragment
     * Returns only the compiled lesson HTML for AJAX fragment swaps.
     */
    public function lessonFragment(string $slug, string $module, string $lesson): Response
    {
        if (! Auth::check()) {
            abort(401);
        }

        if (! $this->isValidCourseKey($slug)) {
            abort(404);
        }

        // The faculty's edited page, or the lesson's Blade file (a lesson the faculty added has only the first).
        $html = LessonQuizService::render($slug, $module, $lesson);

        if ($html === null) {
            return response('<div class="empty-lesson"><div><h1>Lesson not found</h1></div></div>', 404);
        }

        return response($html);
    }

    /**
     * The starter language a course's editor runs. Only the original "programming"
     * course is Python (Pyodide); every other course is C, including the ones
     * faculty build themselves (their labs are main.c, see LessonLabService). This
     * is the one place that ever needs to change to add a third.
     */
    public static function editorLang(string $slug): string
    {
        return $slug === 'programming' ? 'python' : 'c';
    }

    /**
     * GET /student/planet/{slug}/editor
     * The in-browser code editor page (Pyodide for Python; JSCPP, or PicoC when the
     * course's blueprint sets 'c_runtime' => 'picoc', for C). Reads
     * exercise data from the session (flashed by launchEditor below). Falls back
     * gracefully if session is empty.
     */
    public function editor(string $slug, Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true) && ! CourseProgressService::exists($slug)) {
            abort(404);
        }

        return view('student.planets.code-editor', [
            'slug' => $slug,
            'lang' => $this->editorLang($slug),
            'cRuntime' => config("course-structure.{$slug}.c_runtime", 'jscpp'),
            // Same background music as the labs (LessonLabController::MUSIC_COURSES).
            'music' => in_array($slug, LessonLabController::MUSIC_COURSES, true),
        ]);
    }

    /**
     * POST /student/planet/{slug}/editor/launch
     *
     * Receives exercise data from a lesson's POST form, flashes it all to
     * the session, then redirects to the editor as a clean GET.
     * This avoids putting long strings in the URL (which caused data loss).
     */
    public function launchEditor(Request $request, string $slug): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! in_array($slug, self::PLANETS, true) && ! CourseProgressService::exists($slug)) {
            abort(404);
        }

        session()->flash('editor_title', $request->input('title', 'Exercise'));
        session()->flash('editor_difficulty', $request->input('difficulty', ''));
        session()->flash('editor_filename', $request->input('filename', ''));
        session()->flash('editor_xp', $request->input('xp', ''));
        session()->flash('editor_starter_code', $request->input('starter_code', ''));
        session()->flash('editor_instructions', $request->input('instructions', ''));
        session()->flash('editor_hint_title', $request->input('hint_title', "Astro's Hint"));
        session()->flash('editor_hint_body', $request->input('hint_body', ''));
        session()->flash('editor_hint_code', $request->input('hint_code', ''));
        session()->flash('editor_challenge', $request->input('challenge', ''));
        session()->flash('editor_return_to', $request->input('return_to', ''));

        // A lesson with an output-checkable challenge: hand the editor a
        // server-verified completion URL. The answer key never leaves the
        // server — the editor only sends its output to this URL and learns
        // pass/fail. The lesson is identified from the lesson page the student
        // came from (return_to is a full URL). Any course with a blueprint can
        // have challenge lessons, not just the original "programming" one —
        // expectedFor() is called with $slug itself so a course never gets
        // checked against another course's answer keys.
        if (CourseProgressService::exists($slug)) {
            $parts = explode('/', trim((string) parse_url((string) $request->input('return_to', ''), PHP_URL_PATH), '/'));

            // Expected: student / planet / {slug} / {module} / {lesson}
            if (count($parts) >= 5 && $parts[0] === 'student' && $parts[1] === 'planet' && $parts[2] === $slug) {
                [$module, $lesson] = CourseProgressService::normalize($parts[3], $parts[4]);

                // "Code it yourself" opens once the lesson's Knowledge Check is passed (at most 2 mistakes).
                if (! app(QuizGateService::class)->allows($request->user(), $slug, $module, $lesson)) {
                    return redirect()->route('student.planet.module.lesson', compact('slug', 'module', 'lesson'));
                }

                // Its hint is bought with XP through these URLs (the text is not in the lesson page any more).
                session()->flash('editor_hint_status_url', route('student.planet.hint.status', ['slug' => $slug, 'module' => $module, 'lesson' => $lesson]));
                session()->flash('editor_hint_buy_url', route('student.planet.hint.buy', ['slug' => $slug, 'module' => $module, 'lesson' => $lesson]));

                if (CourseProgressService::expectedFor($module, $lesson, $slug) !== null) {
                    session()->flash('editor_complete_url', route('student.planet.lesson.complete', [
                        'slug' => $slug,
                        'module' => $module,
                        'lesson' => $lesson,
                    ]));
                }

                // Already finished: open with the code that passed it, and no XP left to earn.
                if ($done = CourseProgressService::completion($request->user(), $module, $lesson, $slug)) {
                    session()->flash('editor_completed', true);
                    if ($next = CourseProgressService::nextLesson($module, $lesson, $slug)) {
                        session()->flash('editor_next_url', route('student.planet.module.lesson', ['slug' => $slug] + $next));
                    }
                    if (filled($done->code)) {
                        session()->flash('editor_starter_code', $done->code);
                    }
                }
            }
        }

        return redirect()->route('student.planet.editor', ['slug' => $slug]);
    }

    /**
     * Public entry point for other controllers (e.g. ChatController) that need
     * a normalized course array without going through the HTTP show() action.
     */
    public static function course(string $slug): array
    {
        if (! in_array($slug, self::PLANETS, true)) {
            return [];
        }

        $instance = new self;

        return $instance->normalizeCourse($instance->courseShell($slug));
    }
}
