<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseCustomization;
use App\Models\Crew;
use App\Models\LessonContent;
use App\Models\LessonLab;
use App\Models\LessonSimLab;
use App\Services\CourseProgressService;
use App\Services\CourseStructure;
use App\Services\LessonContentService;
use App\Services\LessonLabService;
use App\Services\SimLabService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Faculty course editing. The faculty member opens their course in the same course player students use; there
 * every lesson's text, diagram images, quiz and the module/lesson outline can be changed, and on the Courses page
 * the course card. Changes are stored on top of the course's files (see CourseStructure, LessonContentService),
 * so any of them can be reset to the default.
 *
 * Only a faculty member an admin assigned to the course (CourseAssignment) may edit it, and their edits are saved
 * onto their own class: only students who joined that class see them. Self-paced students and other faculty
 * members' classes keep the default course.
 */
class FacultyCourseController extends Controller
{
    public const LEVELS = ['Beginner', 'Intermediate', 'Advanced'];

    public const PLANETS = ['programming', 'networking', 'cybersecurity'];

    /** The simulator a networking/cybersecurity course's labs run in ('none' for a text-only course). */
    public const LAB_KINDS = ['netsim', 'relay', 'citadel', 'none'];

    /** GET: the "build your own course" form — pick a planet, name it. */
    public function create(): View
    {
        $this->faculty();

        return view('faculty.course-create', ['planets' => self::PLANETS, 'labKinds' => self::LAB_KINDS]);
    }

    /**
     * POST {planet, title, blurb, lab_kind?}: a brand-new course, scoped entirely to DB rows (app/Models/Course)
     * rather than config/course-structure.php — CourseProgressService falls back to it everywhere a config
     * course resolves, so from here on it's edited exactly like one. This faculty member is made its teacher
     * immediately (self-service — unlike CourseAssignment rows for config courses, which only an admin creates),
     * and both tracks (their own class, and self-paced) are opened right away so either can be joined once published.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->faculty();

        $data = $request->validate([
            'planet' => ['required', Rule::in(self::PLANETS)],
            'title' => ['required', 'string', 'max:80'],
            'blurb' => ['nullable', 'string', 'max:160'],
            'lab_kind' => ['required_if:planet,networking,cybersecurity', Rule::in(self::LAB_KINDS)],
        ], [
            'title.required' => 'Give the course a name.',
        ]);

        $slug = $this->uniqueSlug($data['planet'], $data['title']);

        $course = Course::create([
            'planet' => $data['planet'],
            'slug' => $slug,
            'title' => trim($data['title']),
            'blurb' => trim((string) ($data['blurb'] ?? '')),
            // Programming's lab is always the in-browser code editor; only networking/cybersecurity choose a simulator.
            'lab_kind' => $data['planet'] === 'programming' ? 'code' : $data['lab_kind'],
            'status' => Course::DRAFT,
            'created_by' => Auth::id(),
        ]);

        $assignment = CourseAssignment::create([
            'planet' => $course->planet,
            'course_slug' => $course->slug,
            'faculty_id' => Auth::id(),
        ]);
        $assignment->openClass();
        Crew::selfPaced($course->planet, $course->slug);
        Crew::forgetNav(Auth::id());

        return redirect()->route('faculty.courses.index')
            ->with('status', "\"{$course->title}\" is created. Add your first module below to start building it — it stays hidden from students until you publish it.");
    }

    /** POST: show the course to students (self-paced and anyone who joins a class for it). Needs at least one lesson. */
    public function publish(string $course): RedirectResponse
    {
        $this->faculty();
        $row = Course::where('slug', $course)->where('created_by', Auth::id())->firstOrFail();

        if (CourseProgressService::order($course) === []) {
            return redirect()->route('faculty.courses.index')->with('status', "Add at least one lesson to \"{$row->title}\" before you publish it.");
        }

        $row->update(['status' => Course::PUBLISHED]);

        return redirect()->route('faculty.courses.index')->with('status', "\"{$row->title}\" is published.");
    }

    /** A free, readable slug for a new course: its title, planet-unique, numbered if it collides. */
    private function uniqueSlug(string $planet, string $title): string
    {
        $base = Str::slug($title) ?: 'course';
        $slug = $base;
        $n = 2;
        while (Course::where('planet', $planet)->where('slug', $slug)->exists() || CourseProgressService::exists($slug)) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }

    /** The Courses page: the faculty member's courses, as the same cards students see. */
    public function index(): View
    {
        $this->faculty();

        $courses = CourseAssignment::where('faculty_id', Auth::id())->get()
            ->map(function (CourseAssignment $a) {
                $key = CourseProgressService::keyFor($a->planet, $a->course_slug);
                $card = CourseStructure::card($a->planet, $a->course_slug);
                if ($card === [] || ! CourseProgressService::exists($key)) {
                    return null;
                }
                $first = CourseProgressService::order($key)[0] ?? null;
                $edited = CourseStructure::customization($key);
                $class = $a->crew();
                // A course this faculty member built from scratch (app/Models/Course) — null for a config course.
                $own = Course::where('planet', $a->planet)->where('slug', $a->course_slug)->first();

                return $card + [
                    'id' => $a->course_slug,
                    'key' => $key,
                    'planet' => $a->planet,
                    'planet_title' => config("course-catalog.{$a->planet}.title", ucfirst($a->planet)),
                    // No lesson yet (a just-created course): open the course page itself, where the
                    // outline editor lives, instead of a lesson URL that doesn't exist.
                    'url' => $first
                        ? route('student.planet.module.lesson', ['slug' => $key, 'module' => $first['module'], 'lesson' => $first['lesson']])
                        : route('student.planet.play', ['slug' => $a->planet, 'course' => $a->course_slug]),
                    'lessons' => count(CourseProgressService::order($key)),
                    'modules' => count(CourseStructure::modules($key)),
                    'edited_lessons' => $class ? LessonContent::where('course', $key)->where('crew_id', $class->id)->count() : 0,
                    'card_edited' => ! empty($edited['card']),
                    'card_url' => route('faculty.courses.card', $key),
                    'faculty_name' => Auth::user()->name,
                    'level' => $card['level'] ?? 'Beginner',
                    'own' => $own !== null,
                    'draft' => $own?->status === Course::DRAFT,
                    'publish_url' => $own ? route('faculty.courses.publish', $key) : null,
                ];
            })->filter()->values()->all();

        return view('faculty.courses', ['courses' => $courses, 'levels' => self::LEVELS]);
    }

    /** POST (multipart) {title, blurb, level, art?, reset?}: the course card. */
    public function updateCard(Request $request, string $course): JsonResponse
    {
        $crew = $this->owner($course);
        $row = CourseCustomization::firstOrNew(['course' => $course, 'crew_id' => $crew]);
        $card = $row->card ?? [];

        if ($request->boolean('reset')) {
            if (! empty($card['art_path'])) {
                Storage::disk('public')->delete($card['art_path']);
            }
            $row->card = null;
            $row->updated_by = Auth::id();
            $row->save();

            return response()->json(['ok' => true]);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'blurb' => ['nullable', 'string', 'max:160'],
            'level' => ['required', 'string', 'in:'.implode(',', self::LEVELS)],
            'art' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:4096'],
        ]);

        if ($request->hasFile('art')) {
            if (! empty($card['art_path'])) {
                Storage::disk('public')->delete($card['art_path']);
            }
            $card['art_path'] = $request->file('art')->store('course-art', 'public');
        }
        $card['title'] = trim($data['title']);
        $card['blurb'] = trim((string) ($data['blurb'] ?? ''));
        $card['level'] = $data['level'];

        $row->card = $card;
        $row->updated_by = Auth::id();
        $row->save();

        return response()->json(['ok' => true]);
    }

    /** GET: the editable outline (every module and lesson, hidden ones too). */
    public function outline(string $course): JsonResponse
    {
        $crew = $this->owner($course);

        return response()->json(['outline' => CourseStructure::mergedOutline($course)]);
    }

    /**
     * PUT {outline: [{key|null, title, hidden, lessons: [{key|null, title, hidden}]}]}: rename, reorder, hide, add.
     * A new module or lesson comes with key null and gets the next free key; keys are never reused or moved
     * between modules, so student progress always stays on the lesson it was earned on.
     */
    public function updateOutline(Request $request, string $course): JsonResponse
    {
        $crew = $this->owner($course);

        $data = $request->validate([
            'outline' => ['required', 'array', 'min:1', 'max:40'],
            'outline.*.key' => ['nullable', 'string', 'regex:/^M\d{1,2}$/'],
            'outline.*.title' => ['required', 'string', 'max:120'],
            'outline.*.hidden' => ['boolean'],
            'outline.*.lessons' => ['present', 'array', 'max:40'],
            'outline.*.lessons.*.key' => ['nullable', 'string', 'regex:/^lesson-\d{2,3}$/'],
            'outline.*.lessons.*.title' => ['required', 'string', 'max:120'],
            'outline.*.lessons.*.hidden' => ['boolean'],
        ], [
            'outline.*.title.required' => 'Every module needs a name.',
            'outline.*.lessons.*.title.required' => 'Every lesson needs a name.',
        ]);

        $known = collect(CourseStructure::mergedOutline($course))->keyBy('key');
        $usedModules = $known->keys()->map(fn ($k) => (int) substr($k, 1))->all();
        $nextModule = max([0, ...$usedModules]) + 1;

        $outline = [];
        $seen = [];
        foreach ($data['outline'] as $module) {
            $key = $module['key'] ?? null;
            if ($key === null || ! $known->has($key)) {
                $key = 'M'.$nextModule++;
            }
            if (isset($seen[$key])) {
                return response()->json(['message' => 'A module is listed twice.'], 422);
            }
            $seen[$key] = true;

            $knownLessons = collect($known->get($key)['lessons'] ?? [])->pluck('key');
            $nextLesson = max([0, ...$knownLessons->map(fn ($k) => (int) substr($k, 7))->all()]) + 1;
            $lessons = [];
            $seenLessons = [];
            foreach ($module['lessons'] as $lesson) {
                $lk = $lesson['key'] ?? null;
                if ($lk === null || ! $knownLessons->contains($lk)) {
                    $lk = sprintf('lesson-%02d', $nextLesson++);   // lessons never move between modules
                }
                if (isset($seenLessons[$lk])) {
                    return response()->json(['message' => 'A lesson is listed twice.'], 422);
                }
                $seenLessons[$lk] = true;
                $lessons[] = ['key' => $lk, 'title' => trim($lesson['title']), 'hidden' => (bool) ($lesson['hidden'] ?? false)];
            }
            $outline[] = ['key' => $key, 'title' => trim($module['title']), 'hidden' => (bool) ($module['hidden'] ?? false), 'lessons' => $lessons];
        }

        $visible = collect($outline)->reject(fn ($m) => $m['hidden'])->sum(fn ($m) => collect($m['lessons'])->reject(fn ($l) => $l['hidden'])->count());
        if ($visible === 0) {
            return response()->json(['message' => 'The course needs at least one lesson.'], 422);
        }

        $row = CourseCustomization::firstOrNew(['course' => $course, 'crew_id' => $crew]);
        $row->outline = $outline;
        $row->updated_by = Auth::id();
        $row->save();

        return response()->json(['ok' => true, 'outline' => CourseStructure::mergedOutline($course)]);
    }

    /** DELETE: the outline back to the course's default modules and lessons. */
    public function resetOutline(string $course): JsonResponse
    {
        $crew = $this->owner($course);
        CourseCustomization::where('course', $course)->where('crew_id', $crew)->first()?->forceFill(['outline' => null, 'updated_by' => Auth::id()])->save();

        return response()->json(['ok' => true]);
    }

    /** GET: a lesson's editable page. */
    public function content(string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->owner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);

        return response()->json(LessonContentService::document($course, $module, $lesson));
    }

    /** PUT {title, objective, sections: [{heading, html}], checkpoints: {id: question}}: save the faculty's version of the lesson page. */
    public function updateContent(Request $request, string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->owner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'objective' => ['nullable', 'string', 'max:4000'],
            'sections' => ['present', 'array', 'max:30'],
            'sections.*.heading' => ['nullable', 'string', 'max:120'],
            'sections.*.html' => ['nullable', 'string', 'max:200000'],
            'checkpoints' => ['nullable', 'array', 'max:30'],
            'checkpoints.*.prompt' => ['required', 'string', 'max:1000'],
            'checkpoints.*.options' => ['required', 'array', 'min:2', 'max:4'],
            'checkpoints.*.options.*' => ['required', 'string', 'max:500'],
            'checkpoints.*.correct' => ['required', 'string', 'in:A,B,C,D'],
            'checkpoints.*.explanation' => ['nullable', 'string', 'max:1000'],
        ], [
            'title.required' => 'The lesson needs a title.',
            'checkpoints.*.prompt.required' => 'Every checkpoint needs its question.',
            'checkpoints.*.options.*.required' => 'Fill in every checkpoint choice (or remove the empty one).',
        ]);

        // No `checkpoints` sent: keep the lesson's current ones.
        $sent = $request->has('checkpoints')
            ? ($data['checkpoints'] ?? [])
            : (array) LessonContentService::document($course, $module, $lesson)['checkpoints'];

        $checkpoints = [];
        foreach ($sent as $id => $q) {
            if (! preg_match('/^[a-z0-9_-]{1,24}$/', (string) $id)) {
                continue;
            }
            $options = array_values(array_map('trim', $q['options']));
            if (ord($q['correct']) - 65 >= count($options)) {
                return response()->json(['message' => 'Pick which choice is the right answer for every checkpoint.'], 422);
            }
            $checkpoints[$id] = ['prompt' => trim($q['prompt']), 'options' => $options, 'correct' => $q['correct'], 'explanation' => trim($q['explanation'] ?? '')];
        }

        $sections = [];
        foreach ($data['sections'] as $s) {
            $sections[] = [
                'heading' => trim((string) ($s['heading'] ?? '')),
                'html' => LessonContentService::sanitize((string) ($s['html'] ?? '')),
            ];
        }

        LessonContent::updateOrCreate(compact('course', 'module', 'lesson') + ['crew_id' => $crew], [
            'title' => trim($data['title']),
            'objective' => LessonContentService::sanitizeInline((string) ($data['objective'] ?? '')),
            'sections' => $sections,
            'checkpoints' => $checkpoints,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['ok' => true]);
    }

    /** DELETE: throw the faculty's version away; the lesson shows its default page again. */
    public function resetContent(string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->owner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        LessonContent::where(compact('course', 'module', 'lesson') + ['crew_id' => $crew])->get()->each->delete();

        return response()->json(['ok' => true]);
    }

    /** GET: the lesson's coding lab as the faculty's lab editor shows it (programming courses only). */
    public function lab(string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->labOwner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);

        // The lab editor runs the answer code in the same runtime as the students' editor, to check the expected output.
        $lang = PlanetController::editorLang($course);
        $worker = config("course-structure.{$course}.c_runtime", 'jscpp') === 'picoc' ? 'js/picoc-worker.js' : 'js/jscpp-worker.js';

        return response()->json(LessonLabService::document($course, $module, $lesson) + [
            'difficulties' => LessonLabService::DIFFICULTIES,
            'language' => $lang === 'c' ? 'C' : 'Python',
            'lang' => $lang,
            'worker' => $lang === 'c' ? asset($worker).'?v='.filemtime(public_path($worker)) : null,
        ]);
    }

    /** PUT {title, difficulty, instructions, starter_code, expected_output, hint_body, hint_code}: save the faculty's lab. */
    public function updateLab(Request $request, string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->labOwner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'difficulty' => ['required', Rule::in(LessonLabService::DIFFICULTIES)],
            'instructions' => ['required', 'string', 'max:5000'],
            'starter_code' => ['nullable', 'string', 'max:20000'],
            'expected_output' => ['required', 'string', 'max:5000'],
            'hint_body' => ['nullable', 'string', 'max:3000'],
            'hint_code' => ['nullable', 'string', 'max:20000'],
        ], [
            'instructions.required' => 'Tell students what to build.',
            'expected_output.required' => 'Add the output their program must print: that is how the lab checks their work.',
        ]);

        // The check ignores spaces around the output, so a blank one could never be passed.
        if (trim($data['expected_output']) === '') {
            return response()->json(['errors' => ['expected_output' => ['The expected output cannot be only spaces.']]], 422);
        }

        LessonLab::updateOrCreate(compact('course', 'module', 'lesson') + ['crew_id' => $crew], [
            'title' => trim($data['title']),
            'difficulty' => $data['difficulty'],
            'instructions' => rtrim(str_replace("\r\n", "\n", $data['instructions'])),
            'starter_code' => str_replace("\r\n", "\n", (string) ($data['starter_code'] ?? '')),
            'expected_output' => trim(str_replace("\r\n", "\n", $data['expected_output'])),
            'hint_body' => trim(str_replace("\r\n", "\n", (string) ($data['hint_body'] ?? ''))),
            'hint_code' => str_replace("\r\n", "\n", (string) ($data['hint_code'] ?? '')),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['ok' => true]);
    }

    /** DELETE: back to the lab the lesson file has (or no lab, for a lesson that had none). */
    public function resetLab(string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->labOwner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        LessonLab::where(compact('course', 'module', 'lesson') + ['crew_id' => $crew])->get()->each->delete();

        return response()->json(['ok' => true]);
    }

    /** GET: the lesson's simulator lab (NetSim / Relay Console) as the faculty's lab editor shows it (networking courses). */
    public function simLab(string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->simLabOwner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        $doc = SimLabService::document($course, $module, $lesson);
        abort_unless($doc, 404);
        $sim = LessonLabController::SIMS[$doc['sim']];

        // What the lab editor needs to play a draft in the simulator (testDraftSimLab).
        return response()->json($doc + [
            'lab' => SimLabService::base($course, $module, $lesson)['id'],
            'frame' => asset($sim['path']),
            'source' => $sim['source'],
        ]);
    }

    /**
     * POST {same body as updateSimLab}: a draft of the faculty's lab to play before saving it. The draft is kept for
     * a few hours; the simulator loads it from draftSimLab(), and draftPassed() marks it played through.
     */
    public function testSimLab(Request $request, string $course, string $module, string $lesson): JsonResponse
    {
        $this->simLabOwner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        $base = SimLabService::base($course, $module, $lesson);
        abort_unless($base, 404);

        [$patch, $errors] = SimLabService::patch($course, $module, $lesson, $request->all());
        if ($patch === null) {
            return response()->json(['message' => reset($errors), 'errors' => array_map(fn ($e) => [$e], $errors)], 422);
        }

        $token = Str::random(40);
        Cache::put(self::draftKey($token), [
            'user' => Auth::id(), 'course' => $course, 'module' => $module, 'lesson' => $lesson,
            'file' => SimLabService::apply($base['file'], $base['sim'], $patch), 'passed' => false,
        ], now()->addHours(6));

        return response()->json(['token' => $token, 'file' => route('faculty.courses.sim-lab.draft', ['course' => $course, 'token' => $token], false)]);
    }

    /** GET: a draft's lab file, for the simulator in the lab editor. */
    public function draftSimLab(string $course, string $token): JsonResponse
    {
        $this->simLabOwner($course);

        return response()->json($this->draft($course, $token)['file'], 200, ['Cache-Control' => 'no-store, private']);
    }

    /** POST: the simulator reported every objective of the draft cleared. */
    public function draftPassed(string $course, string $token): JsonResponse
    {
        $this->simLabOwner($course);
        $draft = $this->draft($course, $token);
        Cache::put(self::draftKey($token), ['passed' => true] + $draft, now()->addHours(6));

        return response()->json(['ok' => true]);
    }

    /** @return array{user: int, course: string, module: string, lesson: string, file: array<string, mixed>, passed: bool} */
    private function draft(string $course, string $token): array
    {
        $draft = Cache::get(self::draftKey($token));
        abort_unless(is_array($draft) && $draft['user'] === Auth::id() && $draft['course'] === $course, 404);

        return $draft;
    }

    private static function draftKey(string $token): string
    {
        return 'sim-lab-draft:'.$token;
    }

    /** GET ?sim=netsim|console|citadel: default labs a custom lesson (one with no lab yet) can start from. */
    public function simTemplates(Request $request, string $course): JsonResponse
    {
        $this->simLabOwner($course);
        $sim = $request->string('sim')->value();

        return response()->json(['templates' => SimLabService::templates($sim !== '' ? $sim : null)]);
    }

    /**
     * PUT {sim, lab}: give a custom lesson (one the faculty added, with no lab of its own) a
     * simulator lab by starting from one of the course's templates(). The template's file is never
     * copied — the lesson just points at it, like a config lesson's `lab`/`sim` would, so the normal
     * edit flow (document/updateSimLab) immediately works on it as a patch.
     */
    public function attachSimTemplate(Request $request, string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->simLabOwner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        abort_unless(CourseStructure::isCustomLesson($course, $module, $lesson), 404);

        $data = $request->validate([
            'sim' => ['required', Rule::in(array_keys(SimLabService::DIRS))],
            'lab' => ['required', 'string', 'regex:/^[a-z0-9-]{1,64}$/'],
        ]);
        abort_unless(SimLabService::load($data['sim'], $data['lab']), 404);

        $row = CourseCustomization::firstOrNew(['course' => $course, 'crew_id' => $crew]);
        $outline = is_array($row->outline) ? $row->outline : CourseStructure::mergedOutline($course);
        foreach ($outline as &$m) {
            if (strtolower($m['key']) !== $module) {
                continue;
            }
            foreach ($m['lessons'] as &$l) {
                if (str_replace('-', '', strtolower($l['key'])) === $lesson) {
                    $l['lab'] = $data['lab'];
                    $l['sim'] = $data['sim'];
                }
            }
        }
        unset($m, $l);

        $row->outline = $outline;
        $row->updated_by = Auth::id();
        $row->save();

        return response()->json(['ok' => true]);
    }

    /** PUT {title, blurb|victory, steps: [{title, body, objectives: [{label, values}]}]}: save the faculty's version. */
    public function updateSimLab(Request $request, string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->simLabOwner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        $base = SimLabService::base($course, $module, $lesson);
        abort_unless($base, 404);

        [$patch, $errors] = SimLabService::patch($course, $module, $lesson, $request->all());
        if ($patch === null) {
            return response()->json(['message' => reset($errors), 'errors' => array_map(fn ($e) => [$e], $errors)], 422);
        }

        // New wording saves straight away. New checks (an address, a port, a flag, a file) could make the lab
        // impossible to pass, so they are saved only once the faculty member has played this exact version through.
        $next = SimLabService::apply($base['file'], $base['sim'], $patch);
        $saved = LessonSimLab::where(compact('course', 'module', 'lesson') + ['crew_id' => $crew])->value('data');
        $current = $saved ? SimLabService::apply($base['file'], $base['sim'], $saved) : $base['file'];
        $grading = SimLabService::grading($next, $base['sim']);
        if ($grading != SimLabService::grading($current, $base['sim'])) {
            $draft = Cache::get(self::draftKey((string) $request->input('draft')));
            $tested = is_array($draft) && $draft['passed'] && $draft['user'] === Auth::id()
                && [$draft['course'], $draft['module'], $draft['lesson']] === [$course, $module, $lesson]
                && SimLabService::grading($draft['file'], $base['sim']) == $grading;
            if (! $tested) {
                return response()->json(['untested' => true, 'message' => 'You changed what the lab checks. Press "Test this version" and clear every objective once, so you know students can pass it. Then save.'], 422);
            }
        }

        LessonSimLab::updateOrCreate(compact('course', 'module', 'lesson') + ['crew_id' => $crew], ['data' => $patch, 'updated_by' => Auth::id()]);

        return response()->json(['ok' => true]);
    }

    /** DELETE: back to the lab as its file writes it. */
    public function resetSimLab(string $course, string $module, string $lesson): JsonResponse
    {
        $crew = $this->simLabOwner($course);
        [$module, $lesson] = $this->lesson($course, $module, $lesson);
        LessonSimLab::where(compact('course', 'module', 'lesson') + ['crew_id' => $crew])->get()->each->delete();

        return response()->json(['ok' => true]);
    }

    /** POST {image}: an image for a lesson (a diagram, usually). Returns the URL to put in the page. */
    public function uploadImage(Request $request, string $course): JsonResponse
    {
        $crew = $this->owner($course);
        $request->validate(['image' => ['required', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:5120']], [
            'image.image' => 'That file is not an image.',
            'image.max' => 'Images can be at most 5 MB.',
        ]);

        $path = $request->file('image')->store("lesson-images/{$course}", 'public');

        return response()->json(['url' => '/storage/'.$path]);
    }

    private function faculty(): void
    {
        abort_unless(Auth::check() && (Auth::user()->role ?? 'student') === 'faculty', 403);
    }

    /**
     * Only a faculty member assigned to the course. Returns their class's id: every edit is saved onto it,
     * so it reaches only their own students (self-paced and other classes keep the default course).
     */
    private function owner(string $course): int
    {
        abort_unless(CourseAssignment::canEdit(Auth::user(), $course), 403);

        return CourseAssignment::where('planet', CourseProgressService::planetFor($course))
            ->where('course_slug', CourseProgressService::catalogFor($course))
            ->where('faculty_id', Auth::id())->firstOrFail()->openClass()->id;
    }

    /** Coding labs: the course's faculty member, and only on a programming course for now. */
    private function labOwner(string $course): int
    {
        $crew = $this->owner($course);
        abort_unless(LessonLabService::supports($course), 404);

        return $crew;
    }

    /** Simulator labs: the course's faculty member, and only on a networking course for now. */
    private function simLabOwner(string $course): int
    {
        $crew = $this->owner($course);
        abort_unless(SimLabService::supports($course), 404);

        return $crew;
    }

    /** @return array{0: string, 1: string} the normalized module and lesson, or a 404 */
    private function lesson(string $course, string $module, string $lesson): array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        abort_if(CourseProgressService::indexOf($module, $lesson, $course) === null, 404);

        return [$module, $lesson];
    }
}
