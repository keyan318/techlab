<?php

namespace App\Services;

/**
 * Lesson quizzes are written in the lesson Blade files (`.quiz-block` with a `.quiz-correct` option and a
 * `.quiz-explanation`). The answer key is read from there on the server, and stripped from the HTML the
 * browser receives, so the right answer is only revealed after a student has answered.
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

    /** The rendered Blade of a lesson (server-side only), or null if it does not exist. */
    public static function source(string $module, string $lesson, string $course = CourseProgressService::COURSE): ?string
    {
        $base = CourseProgressService::viewBase($course);
        if ($base === null) {
            return null;
        }

        foreach (['lesson-'.preg_replace('/\D/', '', $lesson), $lesson] as $name) {
            $view = $base.'.'.strtoupper($module).'.'.$name;
            if (view()->exists($view)) {
                return view($view)->render();
            }
        }

        return null;
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
