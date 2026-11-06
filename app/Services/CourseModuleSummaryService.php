<?php

namespace App\Services;

use App\Models\Crew;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * A short summary of each module in a course, for the faculty quiz builder: every lesson's
 * goal and its key terms, each with the sentence that explains it. The faculty member writes
 * their questions from it, so a quiz asks about what the course actually teaches.
 *
 * Nothing is generated: every lesson file has the same shape (an <h1 class="lesson-heading">,
 * a "Learning Objective:" paragraph, then a "Simple Explanation" section whose <strong> words
 * are the key terms), so the summary is read straight from the lessons and is always current.
 */
class CourseModuleSummaryService
{
    /** Bump when the way summaries are read changes, so cached ones are rebuilt. */
    private const VERSION = 2;

    /** Longest explanation sentence kept for a term. */
    private const MAX_EXPLAINS = 220;

    /** A <strong> longer than this is emphasis, not a term. */
    private const MAX_TERM = 60;

    /** @return array<int, array{key: string, number: int, title: string, lessons: array}> */
    public function forCrew(Crew $crew): array
    {
        return $this->forCourse(CourseProgressService::keyFor((string) $crew->planet, $crew->course_slug));
    }

    /** @return array<int, array{key: string, number: int, title: string, lessons: array}> */
    public function forCourse(string $key): array
    {
        $modules = CourseStructure::modules($key);
        $base = CourseProgressService::viewBase($key);

        if (! $modules || ! $base) {
            return [];
        }

        $dir = resource_path('views/'.str_replace('.', '/', $base));
        $files = [];
        foreach ($modules as $moduleKey => $module) {
            foreach (array_keys($module['lessons'] ?? []) as $lessonKey) {
                $files[$moduleKey][$lessonKey] = "{$dir}/{$moduleKey}/{$lessonKey}.blade.php";
            }
        }

        // Re-read only when a lesson file changes.
        $stamp = collect($files)->flatten()->map(fn ($f) => is_file($f) ? filemtime($f) : 0)->max();

        return Cache::rememberForever('module-summary:v'.self::VERSION.":{$key}:{$stamp}", function () use ($modules, $files) {
            $out = [];
            foreach ($modules as $moduleKey => $module) {
                $lessons = [];
                foreach ($files[$moduleKey] ?? [] as $lessonKey => $file) {
                    if (is_file($file)) {
                        $lessons[] = $this->lesson(file_get_contents($file), $module['lessons'][$lessonKey]['title'] ?? '');
                    }
                }

                $out[] = [
                    'key' => (string) $moduleKey,
                    'number' => count($out) + 1,
                    'title' => trim(preg_replace('/^M\d+\s*[—–-]\s*/u', '', $module['title'] ?? (string) $moduleKey)),
                    'lessons' => $lessons,
                ];
            }

            return $out;
        });
    }

    /** One lesson file → label, title, objective and key terms. */
    public function lesson(string $html, string $fallbackTitle = ''): array
    {
        $html = preg_replace('/\{\{--.*?--\}\}|\{\{.*?\}\}|\{!!.*?!!\}/s', '', $html);

        $heading = preg_match('#<h1[^>]*>(.*?)</h1>#is', $html, $m) ? $this->plain($m[1]) : $fallbackTitle;
        [$label, $title] = preg_match('/^(Lesson\s+[\d.]+)\s*:\s*(.+)$/i', $heading, $p)
            ? [$p[1], $p[2]]
            : ['', $heading];

        $objective = preg_match('#<strong>\s*Learning Objective:?\s*</strong>(.*?)</p>#is', $html, $m)
            ? $this->plain($m[1])
            : '';

        return [
            'label' => $label,
            'title' => $title,
            'objective' => $objective,
            'terms' => $this->terms($html),
        ];
    }

    /** The bold words of the "Simple Explanation" section, each with the sentence around it. */
    private function terms(string $html): array
    {
        if (! preg_match('#section-heading[^>]*>\s*Simple Explanation\s*</h2>(.*?)(?=<h2[^>]*section-heading|$)#is', $html, $m)) {
            return [];
        }

        $terms = [];
        preg_match_all('#<(p|li)\b[^>]*>(.*?)</\1>#is', $m[1], $blocks, PREG_SET_ORDER);

        foreach ($blocks as $block) {
            preg_match_all('#<strong>(.*?)</strong>#is', $block[2], $strongs);
            $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z"(])/u', $this->plain($block[2])) ?: [];

            foreach ($strongs[1] as $strong) {
                $term = trim($this->plain($strong), " \t\n:");
                $id = mb_strtolower($term);

                if ($term === '' || mb_strlen($term) > self::MAX_TERM || isset($terms[$id])) {
                    continue;
                }

                // The sentence that uses the term, or the block's first one if none quotes it as written.
                $sentence = collect($sentences)->first(fn ($x) => mb_stripos($x, $term) !== false) ?? ($sentences[0] ?? '');
                $terms[$id] = ['term' => $term, 'explains' => Str::limit($sentence, self::MAX_EXPLAINS, '…')];
            }
        }

        return array_values($terms);
    }

    private function plain(string $html): string
    {
        return trim(preg_replace('/\s+/u', ' ', LessonSourceService::plainText($html)));
    }
}
