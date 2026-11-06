<?php

namespace App\Services;

use App\Http\Controllers\PlanetController;
use App\Models\Course;
use App\Models\LessonLab;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

/**
 * Faculty-written coding labs ("Code it yourself"). A lesson's lab is the hidden form in its page that opens the
 * code editor (title, instructions, starter code, hint), plus the output the student's program must print, which
 * lives in config/course-structure.php. A LessonLab row overrides both: apply() rewrites the form in the lesson's
 * HTML (so the editor, the hint and Astro see the new lab) and expected() replaces the answer key.
 *
 * Programming courses only for now (their labs run in the in-browser code editor).
 */
class LessonLabService
{
    /** The editor fields a lab form carries, in the order a new form lists them. */
    public const FIELDS = ['title', 'filename', 'difficulty', 'xp', 'starter_code', 'instructions', 'hint_title', 'hint_body', 'hint_code'];

    public const DIFFICULTIES = ['Easy', 'Medium', 'Hard'];

    private const FORM = '#<form method="POST" action="[^"]*/editor/launch"[^>]*>.*?</form>#s';

    public static function supports(string $course): bool
    {
        if (! CourseProgressService::exists($course)) {
            return false;
        }

        return CourseProgressService::planetFor($course) === 'programming' || Course::labKindFor($course) === 'code';
    }

    /**
     * The faculty's lab for a lesson, or null.
     *
     * @return array{title: string, difficulty: string, instructions: string, starter_code: string, expected_output: string, hint_body: string, hint_code: string}|null
     */
    public static function row(string $course, string $module, string $lesson): ?array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        // A lab belongs to one class: self-paced students (null) always get the lesson file's.
        if (($crew = CourseTrack::classId($course)) === null) {
            return null;
        }

        try {
            $all = Cache::memo()->rememberForever(self::cacheKey($course), fn () => LessonLab::where('course', $course)->whereNotNull('crew_id')->get()
                ->groupBy('crew_id')
                ->map(fn ($rows) => $rows->mapWithKeys(fn (LessonLab $l) => [$l->module.'/'.$l->lesson => [
                    'title' => $l->title,
                    'difficulty' => $l->difficulty,
                    'instructions' => $l->instructions,
                    'starter_code' => (string) $l->starter_code,
                    'expected_output' => $l->expected_output,
                    'hint_body' => (string) $l->hint_body,
                    'hint_code' => (string) $l->hint_code,
                ]])->all())
                ->all());
        } catch (QueryException) {
            return null;
        }

        return $all[$crew][$module.'/'.$lesson] ?? null;
    }

    public static function forget(string $course): void
    {
        Cache::memo()->forget(self::cacheKey($course));
    }

    /** The output a lab's program must print when the faculty wrote the lab, else null (use the course file's). */
    public static function expected(string $course, string $module, string $lesson): ?string
    {
        return self::supports($course) ? (self::row($course, $module, $lesson)['expected_output'] ?? null) : null;
    }

    /**
     * What the lab editor starts from: the faculty's lab, or the lab in the lesson file, or (a lesson with no lab
     * yet) empty fields.
     *
     * @return array{exists: bool, edited: bool, lab: array<string, string>}
     */
    public static function document(string $course, string $module, string $lesson): array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $default = self::fromHtml((string) LessonContentService::raw($course, $module, $lesson));
        $expected = CourseProgressService::order($course)[CourseProgressService::indexOf($module, $lesson, $course) ?? -1]['expected'] ?? null;
        $row = self::row($course, $module, $lesson);

        $lab = $row ?? [
            'title' => $default['title'] ?? '',
            'difficulty' => in_array($default['difficulty'] ?? '', self::DIFFICULTIES, true) ? $default['difficulty'] : 'Easy',
            'instructions' => $default['instructions'] ?? '',
            'starter_code' => $default['starter_code'] ?? '',
            'expected_output' => (string) $expected,
            'hint_body' => $default['hint_body'] ?? '',
            'hint_code' => $default['hint_code'] ?? '',
        ];

        return ['exists' => $row !== null || $default !== null, 'edited' => $row !== null, 'lab' => $lab];
    }

    /** The faculty's lab written into a lesson's HTML: its form's fields replaced, or a new form when it has none. */
    public static function apply(string $html, string $course, string $module, string $lesson): string
    {
        if (! self::supports($course) || ! ($lab = self::row($course, $module, $lesson))) {
            return $html;
        }

        $values = [
            'title' => $lab['title'],
            'difficulty' => $lab['difficulty'],
            'starter_code' => $lab['starter_code'],
            'instructions' => $lab['instructions'],
            'hint_body' => $lab['hint_body'],
            'hint_code' => $lab['hint_code'],
        ];

        if (preg_match(self::FORM, $html, $m, PREG_OFFSET_CAPTURE)) {
            $form = $m[0][0];
            foreach ($values as $name => $value) {
                $input = self::input($name, $value);
                $pattern = '#<input type="hidden" name="'.$name.'"\s+value="[^"]*">#';
                $form = preg_match($pattern, $form)
                    ? preg_replace_callback($pattern, fn () => $input, $form, 1)
                    : preg_replace('#(\s*<input type="hidden" name="return_to")#', "\n            ".$input.'$1', $form, 1);
            }

            return substr_replace($html, $form, $m[0][1], strlen($m[0][0]));
        }

        // No lab in the lesson yet: add one at the end of its Code Example stage (just before the quiz).
        $form = self::newForm($course, $module, $lesson, $values);
        $quiz = '#<h2 class="section-heading">\s*Quiz\s*</h2>#';

        return preg_match($quiz, $html)
            ? preg_replace_callback($quiz, fn ($q) => $form."\n\n    ".$q[0], $html, 1)
            : $html."\n".$form;
    }

    /**
     * The fields of the lab form in a lesson's HTML (decoded), or null when it has none.
     *
     * @return array<string, string>|null
     */
    public static function fromHtml(string $html): ?array
    {
        if (! preg_match(self::FORM, $html, $m)) {
            return null;
        }
        preg_match_all('#<input type="hidden" name="(\w+)"\s+value="([^"]*)">#', $m[0], $inputs, PREG_SET_ORDER);
        $fields = [];
        foreach ($inputs as $row) {
            if (in_array($row[1], self::FIELDS, true)) {
                $fields[$row[1]] = html_entity_decode($row[2], ENT_QUOTES | ENT_HTML5);
            }
        }

        return $fields;
    }

    private static function input(string $name, string $value): string
    {
        return '<input type="hidden" name="'.$name.'" value="'.e($value).'">';
    }

    /** @param  array<string, string>  $values */
    private static function newForm(string $course, string $module, string $lesson, array $values): string
    {
        $c = PlanetController::editorLang($course) === 'c';
        $fields = $values + [
            'filename' => $c ? 'main.c' : 'main.py',
            'xp' => (string) StudentDashboardService::XP_PER_LESSON,
            'hint_title' => "Astro's Hint",
        ];
        $inputs = '';
        foreach (self::FIELDS as $name) {
            $inputs .= '            '.self::input($name, $fields[$name] ?? '')."\n";
        }
        $inputs .= '            '.self::input('return_to', route('student.planet.module.lesson', ['slug' => $course, 'module' => $module, 'lesson' => $lesson]))."\n";
        $logo = view($c ? 'components.c-logo' : 'components.python-logo')->render();

        return '<div class="cta-wrap" style="margin-top:2rem;">'."\n"
            .'        <form method="POST" action="'.e(route('student.planet.editor.launch', ['slug' => $course])).'">'."\n"
            .'            <input type="hidden" name="_token" value="'.e(csrf_token()).'" autocomplete="off">'."\n"
            .$inputs
            .'            <button type="submit" class="code-btn">'.$logo.'<span>Code it yourself</span><span class="code-btn-arrow" aria-hidden="true">→</span></button>'."\n"
            .'        </form>'."\n"
            .'    </div>';
    }

    private static function cacheKey(string $course): string
    {
        return "lesson-labs:v2:{$course}";
    }
}
