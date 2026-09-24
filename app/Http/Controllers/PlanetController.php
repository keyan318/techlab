<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
use App\Services\StudentDashboardService;
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
                'blurb' => 'Information Security 1: defend the Citadel against Planet Doom.',
                'sections' => [
                    ['title' => 'M1 — First Contact with the Enemy', 'lessons' => [
                        ['title' => 'Know Your Citadel'],
                    ]],
                ],
            ],
            default => [],
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

        $percent = StudentDashboardService::planets(Auth::user())[$slug]['percent'] ?? 0;
        $user = Auth::user();

        $courses = collect($planet['courses'])->map(function (array $c, string $id) use ($slug, $percent, $user) {
            $soon = ! empty($c['coming_soon']);
            $crew = $soon ? null : Crew::where('planet', $slug)->where('course_slug', $id)->with('faculty')->first();

            // A course nobody has claimed yet stays open, exactly like before this feature
            // existed — the gate only switches on once a faculty captain actually claims it
            // (generates a code), so shipping this never locks students out of a course
            // that was already freely accessible.
            $locked = ! $soon && $crew && ! $user->belongsToCrew($crew->id);

            return $c + [
                'id' => $id,
                'soon' => $soon,
                'url' => ($soon || $locked) ? null : route('student.planet.play', ['slug' => $slug, 'course' => $id]),
                'percent' => $soon ? null : $percent,
                'faculty_name' => $crew?->faculty?->name,
                'locked' => $locked,
                // Where the student's "Ask to join" stands: null (not asked), pending, accepted, declined.
                'join_status' => $locked ? $crew->joinRequests()->where('user_id', $user->id)->value('status') : null,
                'join_url' => $locked ? route('student.join-request.store', $crew) : null,
            ];
        })->values()->all();

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

        if ($course !== null && ! config("course-catalog.{$slug}.courses.{$course}") || config("course-catalog.{$slug}.courses.{$course}.coming_soon")) {
            abort(404);
        }

        $data = ['course' => $this->normalizeCourse($this->courseShell($slug))];

        // Planets with a lesson blueprint render a lesson inside the course shell. Without
        // these the view falls back to a guessed name that never matches the
        // lesson-01.blade.php files on disk, so the page renders "Lesson not found".
        // The first lesson comes from the blueprint; resolveLessonView() understands both spellings.
        $courseKey = CourseProgressService::keyFor($slug, $course);
        if (CourseProgressService::exists($courseKey)) {
            $first = CourseProgressService::order($courseKey)[0];
            $data += [
                'slug' => $slug,
                'module' => $first['module'],
                'lesson' => $first['lesson'],
                'lessonView' => $this->resolveLessonView($courseKey, $first['module'], $first['lesson']),
            ];
        }

        return view('student.planets.'.$slug, $data);
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

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        $view = $this->resolveLessonView($slug, $module, $lesson);

        return view('student.planets.'.$slug, [
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

        if (! in_array($slug, self::PLANETS, true)) {
            abort(404);
        }

        $view = $this->resolveLessonView($slug, $module, $lesson);

        if (! $view) {
            // TEMPORARY DEBUG — remove once lessons are confirmed working.
            $base = $this->lessonViewPrefix($slug, $module) ?? "student.planets.{$slug}.";
            $tried = $this->lessonCandidates($lesson);

            $lines = array_map(function ($candidate) use ($base) {
                $viewName = $base.$candidate;
                $path = resource_path('views/'.str_replace('.', '/', $viewName).'.blade.php');

                return '<p style="margin-top:6px;font-size:13px;">Tried <code>'.e($viewName).'</code> → <code>'.e($path).'</code></p>';
            }, $tried);

            return response(
                '<div class="empty-lesson"><div>'
                .'<h1>Lesson not found</h1>'
                .implode('', $lines)
                .'</div></div>',
                404
            );
        }

        return response(LessonQuizService::strip(view($view)->render()));
    }

    /**
     * GET /student/planet/{slug}/editor
     * The Pyodide-powered editor page. Reads exercise data from the session
     * (flashed by launchEditor below). Falls back gracefully if session is empty.
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

        if (! in_array($slug, self::PLANETS, true)) {
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

        // Programming lessons: hand the editor a server-verified completion URL.
        // The answer key never leaves the server — the editor only sends its
        // output to this URL and learns pass/fail. The lesson is identified from
        // the lesson page the student came from (return_to is a full URL).
        if ($slug === CourseProgressService::COURSE) {
            $parts = explode('/', trim((string) parse_url((string) $request->input('return_to', ''), PHP_URL_PATH), '/'));

            // Expected: student / planet / {slug} / {module} / {lesson}
            if (count($parts) >= 5 && $parts[0] === 'student' && $parts[1] === 'planet' && $parts[2] === $slug) {
                [$module, $lesson] = CourseProgressService::normalize($parts[3], $parts[4]);

                // Its hint is bought with XP through these URLs (the text is not in the lesson page any more).
                session()->flash('editor_hint_status_url', route('student.planet.hint.status', ['slug' => $slug, 'module' => $module, 'lesson' => $lesson]));
                session()->flash('editor_hint_buy_url', route('student.planet.hint.buy', ['slug' => $slug, 'module' => $module, 'lesson' => $lesson]));

                if (CourseProgressService::expectedFor($module, $lesson) !== null) {
                    session()->flash('editor_complete_url', route('student.planet.lesson.complete', [
                        'slug' => $slug,
                        'module' => $module,
                        'lesson' => $lesson,
                    ]));
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
