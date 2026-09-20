<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Turns the existing Planet lesson files into Astro "sources".
 *
 * Nothing is duplicated or hardcoded: the catalog is built by scanning the
 * lesson Blade files on disk (titles come from each file / config), and a
 * lesson's text is read from the same file at chat time.
 *
 * Every planet with lessons uses the same format: Blade HTML fragments
 * (<h1 class="lesson-heading">…), in the folder named by `view_base` in
 * config/course-structure.php. Cybersecurity has no lesson files yet and
 * yields an empty module list.
 */
class LessonSourceService
{
    public const PLANETS = [
        'programming' => 'Programming',
        'networking' => 'Networking',
        'cybersecurity' => 'Cybersecurity',
    ];

    /** Max characters of a single lesson sent to the model. */
    private const MAX_LESSON_CHARS = 6000;

    /** Max lessons attached to one message. */
    public const MAX_SOURCES = 5;

    /** planet => directory holding M{n}/lesson-{nn}.blade.php */
    private function baseDir(string $planet): ?string
    {
        $viewBase = CourseProgressService::viewBase($planet);
        $dir = $viewBase
            ? resource_path('views/'.str_replace('.', '/', $viewBase))
            : resource_path("views/student/planets/{$planet}");

        return is_dir($dir) ? $dir : null;
    }

    /** Full catalog for the picker: [{slug,title,modules:[{key,title,lessons:[{key,title}]}]}] */
    public function catalog(): array
    {
        $out = [];
        foreach (self::PLANETS as $slug => $label) {
            $out[] = ['slug' => $slug, 'title' => $label, 'modules' => $this->modules($slug)];
        }

        return $out;
    }

    private function modules(string $planet): array
    {
        $base = $this->baseDir($planet);
        if (! $base) {
            return [];
        }

        $modules = [];
        $dirs = collect(File::directories($base))
            ->filter(fn ($d) => preg_match('/^M\d+$/', basename($d)))
            ->sortBy(fn ($d) => (int) substr(basename($d), 1));

        foreach ($dirs as $dir) {
            $key = basename($dir);
            $lessons = [];
            foreach (collect(File::glob($dir.'/lesson-*.blade.php'))->sort() as $file) {
                $lessonKey = Str::before(basename($file), '.blade.php');
                $lessons[] = [
                    'key' => $lessonKey,
                    'title' => $this->title($planet, $key, $lessonKey) ?? Str::headline($lessonKey),
                ];
            }
            if ($lessons) {
                $modules[] = [
                    'key' => $key,
                    'title' => $this->moduleTitle($planet, $key),
                    'lessons' => $lessons,
                ];
            }
        }

        return $modules;
    }

    private function moduleTitle(string $planet, string $module): string
    {
        $title = config("course-structure.{$planet}.modules.{$module}.title");

        return $title ?: 'Module '.substr($module, 1);
    }

    private function path(string $planet, string $module, string $lesson): ?string
    {
        if (! isset(self::PLANETS[$planet])
            || ! preg_match('/^M\d+$/', $module)
            || ! preg_match('/^lesson-\d+$/', $lesson)) {
            return null;
        }
        $base = $this->baseDir($planet);
        $file = $base ? "{$base}/{$module}/{$lesson}.blade.php" : null;

        return $file && is_file($file) ? $file : null;
    }

    private function title(string $planet, string $module, string $lesson): ?string
    {
        $file = $this->path($planet, $module, $lesson);
        if (! $file) {
            return null;
        }
        if (preg_match('#<h1[^>]*>(.*?)</h1>#s', File::get($file), $m)) {
            return trim(html_entity_decode(strip_tags($m[1])));
        }

        return null;
    }

    /**
     * Resolve requested [{planet,module,lesson}] into sources with plain text.
     * Unknown/invalid entries are dropped. Returns [{label,text}].
     */
    public function resolve(array $requested): array
    {
        $sources = [];
        foreach (array_slice($requested, 0, self::MAX_SOURCES) as $r) {
            $planet = (string) ($r['planet'] ?? '');
            $module = (string) ($r['module'] ?? '');
            $lesson = (string) ($r['lesson'] ?? '');
            $file = $this->path($planet, $module, $lesson);
            if (! $file) {
                continue;
            }
            $title = $this->title($planet, $module, $lesson) ?? Str::headline($lesson);
            $sources[] = [
                'label' => self::PLANETS[$planet].' › '.$this->moduleTitle($planet, $module).' › '.$title,
                'text' => Str::limit($this->text($file), self::MAX_LESSON_CHARS, '…'),
            ];
        }

        return $sources;
    }

    private function text(string $file): string
    {
        // Drop Blade comments, forms (exercise plumbing), scripts, diagrams and the lab iframe; keep the teaching text.
        $html = File::get($file);
        $html = preg_replace('/\{\{--.*?--\}\}/s', '', $html);
        $html = preg_replace('#<(form|script|style|svg|iframe)\b.*?</\1>#is', '', $html);
        $html = preg_replace('/@(csrf|php.*?@endphp)/s', '', $html);
        $html = preg_replace('#</(p|h[1-6]|li|pre|div)>#i', "\n", $html);
        $text = html_entity_decode(strip_tags($html));

        return trim(preg_replace("/\n{3,}/", "\n\n", preg_replace('/[ \t]+/', ' ', $text)));
    }

    /** System-prompt block that grounds Astro in the selected lessons. */
    public function promptBlock(array $sources): string
    {
        if (! $sources) {
            return '';
        }
        $blocks = [];
        foreach ($sources as $i => $s) {
            $n = $i + 1;
            $blocks[] = "[Source {$n}: {$s['label']}]\n{$s['text']}";
        }

        return 'The student has connected these TechLab lessons as sources. Prefer them when answering, '
            .'and when you use one, name it (e.g. "From {first source label}"). If the answer is not in them, '
            ."say so briefly and answer from general knowledge.\n\n".implode("\n\n---\n\n", $blocks);
    }
}
