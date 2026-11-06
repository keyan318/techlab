<?php

namespace App\Services;

use App\Models\LessonQuiz;
use Illuminate\Support\Facades\Cache;

/**
 * Lesson quizzes are written in the lesson Blade files (`.quiz-block` with a `.quiz-correct` option and a
 * `.quiz-explanation`). The answer key is read from there on the server, and stripped from the HTML the
 * browser receives, so the right answer is only revealed after a student has answered.
 *
 * The Blade quiz is the default. A course's faculty member may replace it with their own (a LessonQuiz row);
 * those questions are rendered back into the same markup, so everything that reads the quiz works unchanged.
 */
class LessonQuizService
{
    /**
     * @return array<int, array{correct: string, explanation: string}> question number => key
     */
    public static function key(string $html): array
    {
        $key = [];

        if (! preg_match_all('/<div class="quiz-block" data-q="(\d+)">(.*?)<\/div>\s*(?=<div class="quiz-block"|<h2|<\/div>|$)/s', $html, $blocks, PREG_SET_ORDER)) {
            return $key;
        }

        foreach ($blocks as $b) {
            $letter = null;
            if (preg_match('/<li class="quiz-option quiz-correct">\s*([A-D])\./', $b[2], $m)) {
                $letter = $m[1];
            }
            $explanation = preg_match('/<p class="quiz-explanation">(.*?)<\/p>/s', $b[2], $m) ? trim($m[1]) : '';

            if ($letter !== null) {
                $key[(int) $b[1]] = ['correct' => $letter, 'explanation' => $explanation];
            }
        }

        return $key;
    }

    /**
     * The wording of each question and its options, as plain text (for Astro's mistake review).
     *
     * @return array<int, array{prompt: string, options: array<string, string>}> question number => text
     */
    public static function questions(string $html): array
    {
        $out = [];

        if (! preg_match_all('/<div class="quiz-block" data-q="(\d+)">(.*?)<\/div>\s*(?=<div class="quiz-block"|<h2|<\/div>|$)/s', $html, $blocks, PREG_SET_ORDER)) {
            return $out;
        }

        $plain = fn (string $s) => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($s), ENT_QUOTES)));

        foreach ($blocks as $b) {
            $prompt = preg_match('/<p class="quiz-prompt">(.*?)<\/p>/s', $b[2], $m) ? $plain($m[1]) : '';
            $options = [];
            preg_match_all('/<li class="quiz-option[^"]*">(.*?)<\/li>/s', $b[2], $opts);
            foreach ($opts[1] as $opt) {
                $text = trim(str_replace('✓', '', $plain($opt)));
                if (preg_match('/^([A-D])\.\s*(.*)$/s', $text, $m)) {
                    $options[$m[1]] = $m[2];
                }
            }
            $out[(int) $b[1]] = ['prompt' => $prompt, 'options' => $options];
        }

        return $out;
    }

    /**
     * The lesson page (server-side only): the faculty's edited page if there is one (LessonContentService),
     * else the rendered Blade, with the course's edited quiz if it has one; null if the lesson does not exist.
     */
    public static function source(string $module, string $lesson, string $course = CourseProgressService::COURSE): ?string
    {
        $html = LessonContentService::raw($course, $module, $lesson);

        return $html === null ? null : self::withEdits(LessonLabService::apply($html, $course, $module, $lesson), $course, $module, $lesson);
    }

    /** The rendered Blade of a lesson exactly as written (the default quiz), or null if it does not exist. */
    public static function defaultSource(string $module, string $lesson, string $course = CourseProgressService::COURSE): ?string
    {
        $view = self::view($module, $lesson, $course);

        return $view === null ? null : view($view)->render();
    }

    /** The view name of a lesson, or null if it does not exist. */
    public static function view(string $module, string $lesson, string $course = CourseProgressService::COURSE): ?string
    {
        $base = CourseProgressService::viewBase($course);
        if ($base === null) {
            return null;
        }

        foreach (['lesson-'.preg_replace('/\D/', '', $lesson), $lesson] as $name) {
            $view = $base.'.'.strtoupper($module).'.'.$name;
            if (view()->exists($view)) {
                return $view;
            }
        }

        return null;
    }

    /** What a student's browser gets for a lesson: the course's page and quiz, with every answer stripped; null if it does not exist. */
    public static function render(string $course, string $module, string $lesson): ?string
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $html = self::source($module, $lesson, $course);

        return $html === null ? null : self::strip($html);
    }

    /** The lesson HTML with its quiz blocks swapped for the faculty's edited quiz (unchanged if there is none). */
    public static function withEdits(string $html, string $course, string $module, string $lesson): string
    {
        $questions = self::edited($course, $module, $lesson);

        return $questions === null ? $html : self::replace($html, $questions);
    }

    /**
     * The faculty's edited questions for a lesson, or null for the default quiz. Every edited quiz of a course
     * is cached in one entry (cleared whenever one is saved or reset): the database is remote, and a student
     * waits on the quiz after every answer click, so reading it normally costs no query at all.
     *
     * @return list<array{prompt: string, options: list<string>, correct: string, explanation: string}>|null
     */
    public static function edited(string $course, string $module, string $lesson): ?array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        // An edited quiz belongs to one class: self-paced students (null) always get the default quiz.
        if (($crew = CourseTrack::classId($course)) === null) {
            return null;
        }

        $all = Cache::rememberForever(self::cacheKey($course), fn () => LessonQuiz::where('course', $course)
            ->get(['crew_id', 'module', 'lesson', 'questions'])
            ->groupBy('crew_id')
            ->map(fn ($rows) => $rows->mapWithKeys(fn (LessonQuiz $q) => [$q->module.'/'.$q->lesson => $q->questions])->all())
            ->all());

        return $all[$crew][$module.'/'.$lesson] ?? null;
    }

    /** Called when a course's edited quiz is saved or reset. */
    public static function forget(string $course): void
    {
        Cache::forget(self::cacheKey($course));
    }

    private static function cacheKey(string $course): string
    {
        return "lesson-quizzes:v2:{$course}";
    }

    /** Has the lesson Blade got a quiz at all (read from the file, no render: cheap enough for a whole course)? */
    public static function defaultCount(string $module, string $lesson, string $course): int
    {
        $view = self::view($module, $lesson, $course);
        if ($view === null) {
            return 0;
        }

        return preg_match_all('/<div class="quiz-block" data-q="\d+">/', (string) file_get_contents(view()->getFinder()->find($view)));
    }

    /**
     * Every question of a lesson's quiz in editable form: plain text, with `backticks` for inline code.
     *
     * @return list<array{prompt: string, options: list<string>, correct: string, explanation: string}>
     */
    public static function parse(string $html): array
    {
        $out = [];
        if (! preg_match_all('/<div class="quiz-block" data-q="(\d+)">(.*?)<\/div>\s*(?=<div class="quiz-block"|<h2|<\/div>|$)/s', $html, $blocks, PREG_SET_ORDER)) {
            return $out;
        }

        foreach ($blocks as $b) {
            $prompt = preg_match('/<p class="quiz-prompt">(.*?)<\/p>/s', $b[2], $m) ? self::text($m[1]) : '';
            $options = [];
            $correct = 'A';
            preg_match_all('/<li class="quiz-option([^"]*)">(.*?)<\/li>/s', $b[2], $opts, PREG_SET_ORDER);
            foreach ($opts as $i => $opt) {
                $options[] = trim(preg_replace('/^[A-D]\.\s*/', '', str_replace('✓', '', self::text($opt[2]))));
                if (str_contains($opt[1], 'quiz-correct')) {
                    $correct = chr(65 + $i);
                }
            }
            $out[] = [
                'prompt' => preg_replace('/^Q\d+\.\s*/', '', $prompt),
                'options' => $options,
                'correct' => $correct,
                'explanation' => preg_match('/<p class="quiz-explanation">(.*?)<\/p>/s', $b[2], $m) ? self::text($m[1]) : '',
            ];
        }

        return $out;
    }

    /** Render questions as the lesson quiz markup (the same markup the Blade lessons use). */
    public static function blocks(array $questions): string
    {
        $html = [];
        foreach (array_values($questions) as $i => $q) {
            $n = $i + 1;
            $lines = ["<div class=\"quiz-block\" data-q=\"{$n}\">", "        <p class=\"quiz-prompt\">Q{$n}. ".self::html($q['prompt']).'</p>', '        <ul class="quiz-options">'];
            foreach (array_values($q['options']) as $j => $option) {
                $letter = chr(65 + $j);
                $lines[] = $letter === $q['correct']
                    ? "            <li class=\"quiz-option quiz-correct\">{$letter}. ".self::html($option).' ✓</li>'
                    : "            <li class=\"quiz-option\">{$letter}. ".self::html($option).'</li>';
            }
            $lines[] = '        </ul>';
            if (trim($q['explanation'] ?? '') !== '') {
                $lines[] = '        <p class="quiz-explanation"><em>'.self::html($q['explanation']).'</em></p>';
            }
            $lines[] = '    </div>';
            $html[] = implode("\n", $lines);
        }

        return implode("\n\n    ", $html);
    }

    /** Swap the run of quiz blocks in a lesson for these questions. */
    private static function replace(string $html, array $questions): string
    {
        $block = '<div class="quiz-block" data-q="\d+">.*?<\/div>';

        return preg_replace_callback("/{$block}(?:\s*{$block})*/s", fn () => self::blocks($questions), $html, 1);
    }

    /** Lesson HTML → editable plain text, with `backticks` for inline code. */
    public static function text(string $html): string
    {
        $html = preg_replace('/<code[^>]*>(.*?)<\/code>/s', '`$1`', $html);

        return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5)));
    }

    /** Editable plain text → lesson HTML (escaped, `backticks` become <code>). */
    public static function html(string $text): string
    {
        return preg_replace('/`([^`]+)`/', '<code>$1</code>', e(trim($text)));
    }

    /**
     * Astro's hint for the lesson's exercise, read from the hidden `hint_*` fields of its "Code it yourself" form.
     *
     * @return array{title: string, body: string, code: string}|null
     */
    public static function hint(string $html): ?array
    {
        if (! preg_match_all('/<input type="hidden" name="(hint_title|hint_body|hint_code)"\s+value="([^"]*)">/s', $html, $m, PREG_SET_ORDER)) {
            return null;
        }
        $f = [];
        foreach ($m as $row) {
            $f[$row[1]] = html_entity_decode($row[2], ENT_QUOTES | ENT_HTML5);
        }
        if (trim($f['hint_body'] ?? '') === '' && trim($f['hint_code'] ?? '') === '') {
            return null;
        }

        return ['title' => trim($f['hint_title'] ?? '') ?: "Astro's Hint", 'body' => $f['hint_body'] ?? '', 'code' => $f['hint_code'] ?? ''];
    }

    /** The HTML a student's browser gets: no marked-correct option, no ✓, no explanations, no hint text (that is bought). */
    public static function strip(string $html): string
    {
        $html = preg_replace('/\s*<input type="hidden" name="hint_(?:title|body|code)"\s+value="[^"]*">/s', '', $html);

        $html = preg_replace('/\s*<p class="quiz-explanation">.*?<\/p>/s', '', $html);
        $html = str_replace(' quiz-correct"', '"', $html);

        return preg_replace('/(<li class="quiz-option[^>]*>.*?)\s*✓\s*(<\/li>)/su', '$1$2', $html);
    }
}
