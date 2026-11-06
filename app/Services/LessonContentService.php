<?php

namespace App\Services;

use App\Models\LessonContent;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\Node;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

/**
 * Faculty-edited lesson pages.
 *
 * A lesson's Blade file is its default page. When the course's faculty member edits it, the page is saved as a
 * LessonContent row: a title, an objective and a list of sections ({heading, html}). Everything a student can
 * interact with — the "Code it yourself" form (with its hidden answer key and hint), lab buttons, checkpoints,
 * the quiz, hand-drawn SVG diagrams — is never copied into that row. The section html holds a placeholder
 * (<div data-keep="N"></div>) instead, and N is swapped back for block N of the freshly rendered Blade file. So
 * routes, CSRF tokens and answer keys always come from the file, and the faculty's HTML can be sanitized hard.
 *
 * The rebuilt page has exactly the shape of a Blade lesson (.lesson-fragment > h1 / objective / h2 + blocks), so
 * the course player, the quiz and the checkpoint code all work on it unchanged.
 */
class LessonContentService
{
    /** Interactive blocks a faculty member can move but not delete (removing them would break the lesson). */
    private const REQUIRED = ['exercise', 'lab', 'interactive'];

    public const LABELS = [
        'exercise' => '"Code it yourself" button',
        'lab' => 'Simulator lab button',
        'checkpoint' => 'Checkpoint question',
        'quiz' => 'Quiz questions',
        'diagram' => 'Built-in diagram',
        'interactive' => 'Interactive block',
    ];

    /** The lesson's page as the server sees it (answers included): the faculty's version, or the Blade file. */
    public static function raw(string $course, string $module, string $lesson): ?string
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $default = LessonQuizService::defaultSource($module, $lesson, $course);
        $row = self::row($course, $module, $lesson);

        if ($row === null && CourseStructure::isCustomLesson($course, $module, $lesson)) {
            $row = self::template($course, $module, $lesson);
        }

        if ($row === null) {
            return $default === null ? null : self::retitle($default, $course, $module, $lesson);
        }

        return self::build($row, self::split((string) $default), $course, $module, $lesson);
    }

    /**
     * What the faculty's editor works on: the lesson's title, objective and sections, plus the locked blocks
     * their placeholders stand for.
     *
     * @return array{title: string, objective: string, sections: list<array{heading: string, html: string}>, locked: list<array{index: int, kind: string, label: string, required: bool, preview: ?string}>, edited: bool, custom: bool}
     */
    public static function document(string $course, string $module, string $lesson): array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $split = self::split((string) LessonQuizService::defaultSource($module, $lesson, $course));
        $row = self::row($course, $module, $lesson);
        $custom = CourseStructure::isCustomLesson($course, $module, $lesson);
        $doc = $row ?? ($custom ? self::template($course, $module, $lesson) : null) ?? [
            'title' => $split['title'], 'objective' => $split['objective'], 'sections' => $split['sections'],
            'checkpoints' => $split['checkpoints'],
        ];
        $doc = self::liftCheckpoints($doc, $split);

        // Which checkpoint the page's data-cp="N" is: the file's own numbers on an unedited lesson, else
        // numbered in page order (that is how build() numbers them).
        $byNumber = [];
        if ($row === null && ! $custom) {
            foreach (array_keys($split['checkpoints']) as $id) {
                $byNumber[(int) substr($id, 2)] = $id;
            }
        } else {
            $n = 0;
            foreach ($doc['sections'] as $section) {
                preg_match_all('/data-checkpoint="([a-z0-9_-]+)"/', (string) $section['html'], $ids);
                foreach ($ids[1] as $id) {
                    if (isset($doc['checkpoints'][$id])) {
                        $byNumber[++$n] = $id;
                    }
                }
            }
        }

        $locked = [];
        foreach ($split['locked'] as $i => $block) {
            $locked[] = [
                'index' => $i,
                'kind' => $block['kind'],
                'label' => self::LABELS[$block['kind']],
                'required' => in_array($block['kind'], self::REQUIRED, true),
                // A diagram shows its picture in the editor; it is the lesson's own SVG, written by us.
                'preview' => $block['kind'] === 'diagram' ? $block['html'] : null,
            ];
        }

        return [
            'title' => (string) $doc['title'],
            'objective' => (string) $doc['objective'],
            'sections' => array_values($doc['sections']),
            'checkpoints' => (object) $doc['checkpoints'],
            'checkpointsByNumber' => (object) $byNumber,
            'locked' => $locked,
            'edited' => $row !== null,
            'custom' => $custom,
        ];
    }

    /**
     * Cut a rendered Blade lesson into its title, objective and sections. Interactive blocks are lifted out
     * into `locked` and replaced in the section html by <div data-keep="N"></div>.
     *
     * Works on the raw string, not a parsed DOM, so a locked block is put back byte for byte: the quiz, hint and
     * checkpoint readers are regexes over exactly that markup.
     *
     * @return array{attrs: string, title: string, objective: string, sections: list<array{heading: string, html: string}>, locked: list<array{kind: string, html: string}>}
     */
    public static function split(string $html): array
    {
        $out = ['attrs' => '', 'title' => '', 'objective' => '', 'sections' => [], 'locked' => [], 'checkpoints' => []];

        if (! preg_match('/<div class="lesson-fragment"([^>]*)>/', $html, $open, PREG_OFFSET_CAPTURE)) {
            return $out;
        }
        $out['attrs'] = $open[1][0];
        $inner = substr($html, $open[0][1] + strlen($open[0][0]));

        $current = null;
        $prevQuiz = false;
        foreach (self::children($inner) as $node) {
            $class = ' '.$node['class'].' ';

            if ($node['tag'] === 'h1' && str_contains($class, ' lesson-heading ')) {
                $out['title'] = self::plain($node['inner']);

                continue;
            }
            if ($node['tag'] === 'p' && str_contains($class, ' lesson-objective ')) {
                $out['objective'] = trim(preg_replace('/^\s*<strong>\s*Learning Objective:?\s*<\/strong>\s*/i', '', $node['inner']));

                continue;
            }
            if ($node['tag'] === 'h2' && str_contains($class, ' section-heading ')) {
                $out['sections'][] = ['heading' => self::plain($node['inner']), 'html' => ''];
                $current = array_key_last($out['sections']);
                $prevQuiz = false;

                continue;
            }

            if ($current === null) {
                // Content before the first heading: a heading-less section of its own.
                $out['sections'][] = ['heading' => '', 'html' => ''];
                $current = array_key_last($out['sections']);
            }

            $kind = self::lockedKind($node);
            if ($kind === null) {
                $out['sections'][$current]['html'] .= $node['outer']."\n";
                $prevQuiz = false;

                continue;
            }

            // A run of quiz questions is one block: the faculty edits the questions in the quiz editor.
            if ($kind === 'quiz' && $prevQuiz) {
                $out['locked'][array_key_last($out['locked'])]['html'] .= "\n\n    ".$node['outer'];

                continue;
            }
            $out['locked'][] = ['kind' => $kind, 'html' => $node['outer']];

            // A checkpoint is a question the faculty edits as text (it stays in `locked` too, so the numbering
            // of the other locked blocks never shifts).
            if ($kind === 'checkpoint' && preg_match('/data-cp="(\d+)"/', $node['outer'], $cp)) {
                $id = 'cp'.$cp[1];
                $out['checkpoints'][$id] = self::parseCheckpoint($node['outer']);
                $out['sections'][$current]['html'] .= '<div data-checkpoint="'.$id.'"></div>'."\n";
                $prevQuiz = false;

                continue;
            }
            $out['sections'][$current]['html'] .= '<div data-keep="'.array_key_last($out['locked']).'"></div>'."\n";
            $prevQuiz = $kind === 'quiz';
        }

        foreach ($out['sections'] as &$section) {
            $section['html'] = trim($section['html']);
        }

        return $out;
    }

    /**
     * Put a lesson page back together from the faculty's sections and the locked blocks of the Blade file.
     *
     * @param  array{title: ?string, objective: ?string, sections: array}  $doc
     */
    public static function build(array $doc, array $split, string $course, string $module, string $lesson): string
    {
        $locked = $split['locked'] ?? [];
        $checkpoints = $doc['checkpoints'] ?? [];
        $used = [];
        $sections = [];

        foreach (array_values($doc['sections'] ?? []) as $i => $section) {
            $quiz = '';
            $body = preg_replace_callback('/<div[^>]*\bdata-keep="(\d+)"[^>]*>\s*<\/div>/', function ($m) use ($locked, &$used, &$quiz) {
                $n = (int) $m[1];
                if (! isset($locked[$n]) || isset($used[$n])) {
                    return '';
                }
                $used[$n] = true;
                if ($locked[$n]['kind'] === 'quiz') {
                    // The quiz readers expect questions to be followed by a heading or the end, so they go last.
                    $quiz .= "\n".$locked[$n]['html'];

                    return '';
                }

                return $locked[$n]['html'];
            }, (string) ($section['html'] ?? ''));

            $body = preg_replace_callback('/<div[^>]*\bdata-checkpoint="([a-z0-9_-]+)"[^>]*>\s*<\/div>/', function ($m) use ($checkpoints) {
                return isset($checkpoints[$m[1]]) ? self::checkpointHtml($checkpoints[$m[1]]) : '';
            }, $body);

            $heading = trim((string) ($section['heading'] ?? ''));

            // A quiz section with no questions of its own (a lesson the faculty added, or one whose Blade
            // quiz was removed) shows the faculty's quiz, if they wrote one.
            if ($quiz === '' && preg_match('/quiz/i', $heading)) {
                $edited = LessonQuizService::edited($course, $module, $lesson);
                $quiz = $edited ? "\n".LessonQuizService::blocks($edited) : '';
            }

            $sections[$i] = ['heading' => $heading, 'body' => trim($body), 'quiz' => $quiz];
        }

        // Required interactive blocks the faculty's version no longer places: back at the end, so the lesson
        // can still be completed.
        foreach ($locked as $n => $block) {
            if (! isset($used[$n]) && in_array($block['kind'], self::REQUIRED, true)) {
                if ($sections === []) {
                    $sections[] = ['heading' => '', 'body' => '', 'quiz' => ''];
                }
                $last = array_key_last($sections);
                $sections[$last]['body'] .= "\n".$block['html'];
            }
        }

        $attrs = $split['attrs'] ?? '';
        if ($attrs === '') {
            $attrs = ' data-module="'.e($module).'" data-lesson="'.e(substr(preg_replace('/\D/', '', $lesson), -2)).'"';
        }

        $html = '<div class="lesson-fragment"'.$attrs.' data-edited="1">'."\n\n";
        $html .= '    <h1 class="lesson-heading">'.e((string) ($doc['title'] ?? '')).'</h1>'."\n\n";
        if (trim((string) ($doc['objective'] ?? '')) !== '') {
            $html .= '    <p class="lesson-objective"><strong>Learning Objective:</strong> '.$doc['objective'].'</p>'."\n\n";
        }
        foreach ($sections as $i => $s) {
            if ($s['heading'] !== '') {
                $html .= '    <h2 class="section-heading">'.e($s['heading']).'</h2>'."\n\n";
            }
            $html .= $s['body']."\n".$s['quiz']."\n\n";
        }

        // Checkpoints are numbered in page order: the student's passed checkpoints are stored by number.
        $n = 0;
        $html = preg_replace_callback('/<div class="checkpoint-block" data-cp="\d+">/', function () use (&$n) {
            return '<div class="checkpoint-block" data-cp="'.(++$n).'">';
        }, $html);

        return $html."</div>\n";
    }

    /**
     * One checkpoint question in editable form.
     *
     * @return array{prompt: string, options: list<string>, correct: string, explanation: string}
     */
    public static function parseCheckpoint(string $html): array
    {
        $prompt = preg_match('/<p class="quiz-prompt">(.*?)<\/p>/s', $html, $m) ? LessonQuizService::text($m[1]) : '';
        $options = [];
        $correct = 'A';
        preg_match_all('/<li class="quiz-option([^"]*)">(.*?)<\/li>/s', $html, $opts, PREG_SET_ORDER);
        foreach ($opts as $i => $opt) {
            $options[] = trim(preg_replace('/^[A-D]\.\s*/', '', str_replace('✓', '', LessonQuizService::text($opt[2]))));
            if (str_contains($opt[1], 'quiz-correct')) {
                $correct = chr(65 + $i);
            }
        }

        return [
            'prompt' => $prompt,
            'options' => $options,
            'correct' => $correct,
            'explanation' => preg_match('/<p class="quiz-explanation">(.*?)<\/p>/s', $html, $m) ? LessonQuizService::text($m[1]) : '',
        ];
    }

    /** A checkpoint question as lesson markup (the same markup the Blade lessons use; build() numbers it). */
    public static function checkpointHtml(array $q): string
    {
        $lines = ['<div class="checkpoint-block" data-cp="0">', '        <p class="quiz-prompt">'.LessonQuizService::html((string) ($q['prompt'] ?? '')).'</p>', '        <ul class="quiz-options">'];
        foreach (array_values($q['options'] ?? []) as $j => $option) {
            $letter = chr(65 + $j);
            $lines[] = $letter === ($q['correct'] ?? 'A')
                ? "            <li class=\"quiz-option quiz-correct\">{$letter}. ".LessonQuizService::html((string) $option).' ✓</li>'
                : "            <li class=\"quiz-option\">{$letter}. ".LessonQuizService::html((string) $option).'</li>';
        }
        $lines[] = '        </ul>';
        if (trim((string) ($q['explanation'] ?? '')) !== '') {
            $lines[] = '        <p class="quiz-explanation"><em>'.LessonQuizService::html((string) $q['explanation']).'</em></p>';
        }
        $lines[] = '    </div>';

        return implode("\n", $lines);
    }

    /**
     * A page saved before checkpoints were editable holds them as locked blocks (data-keep): turn those into
     * editable checkpoints, so the editor always shows them as questions.
     */
    private static function liftCheckpoints(array $doc, array $split): array
    {
        $doc['checkpoints'] = is_array($doc['checkpoints'] ?? null) ? $doc['checkpoints'] : [];
        foreach ($doc['sections'] as &$section) {
            $section['html'] = preg_replace_callback('/<div[^>]*\bdata-keep="(\d+)"[^>]*>\s*<\/div>/', function ($m) use ($split, &$doc) {
                $block = $split['locked'][(int) $m[1]] ?? null;
                if (($block['kind'] ?? null) !== 'checkpoint') {
                    return $m[0];
                }
                $id = 'k'.$m[1];
                $doc['checkpoints'][$id] = self::parseCheckpoint($block['html']);

                return '<div data-checkpoint="'.$id.'"></div>';
            }, (string) ($section['html'] ?? ''));
        }

        return $doc;
    }

    /**
     * Clean faculty HTML down to text formatting, lists, links, code, tables and images. Anything else is
     * dropped (script, style, iframe, svg, forms…) or unwrapped to its text, and every attribute that is not
     * plainly safe goes. Placeholders (<div data-keep="N">) survive, so locked blocks keep their place.
     */
    public static function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $doc = HTMLDocument::createFromString('<!DOCTYPE html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);
        $body = $doc->body;
        self::clean($body);

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $doc->saveHtml($child);
        }

        return trim($out);
    }

    /** Inline-only HTML for the objective (bold, italic, code, links). */
    public static function sanitizeInline(string $html): string
    {
        $html = self::sanitize($html);

        return trim(strip_tags($html, '<strong><b><em><i><u><code><a><br>'));
    }

    /**
     * The saved page for a lesson in the signed-in user's class, or null for the Blade file. Edits belong to
     * one class (CourseTrack::classId), so self-paced students always get null. All of a course's rows,
     * every class's, are cached together.
     */
    public static function row(string $course, string $module, string $lesson): ?array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if (($crew = CourseTrack::classId($course)) === null) {
            return null;
        }

        try {
            $all = Cache::memo()->rememberForever(self::cacheKey($course), fn () => LessonContent::where('course', $course)->whereNotNull('crew_id')
                ->get(['crew_id', 'module', 'lesson', 'title', 'objective', 'sections', 'checkpoints'])
                ->groupBy('crew_id')
                ->map(fn ($rows) => $rows->mapWithKeys(fn (LessonContent $c) => [$c->module.'/'.$c->lesson => [
                    'title' => $c->title, 'objective' => $c->objective, 'sections' => $c->sections ?? [], 'checkpoints' => $c->checkpoints ?? [],
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

    /** The starting page of a lesson the faculty added: the usual stages, with nothing in them yet. */
    public static function template(string $course, string $module, string $lesson): array
    {
        // Numbered by its place in the module, the way the sidebar numbers it.
        $number = (int) preg_replace('/\D/', '', $module).'.'.(int) preg_replace('/\D/', '', $lesson);
        foreach (CourseStructure::modules($course) as $mk => $m) {
            if (strtolower((string) $mk) === $module) {
                $keys = array_map(fn ($lk) => CourseProgressService::normalize((string) $mk, (string) $lk)[1], array_keys($m['lessons'] ?? []));
                $pos = array_search($lesson, $keys, true);
                $number = (int) preg_replace('/\D/', '', $module).'.'.($pos === false ? count($keys) + 1 : $pos + 1);
            }
        }
        $title = CourseStructure::lessonTitle($course, $module, $lesson) ?? 'New lesson';

        return [
            'title' => "Lesson {$number}: {$title}",
            'objective' => 'By the end of this lesson, you can …',
            'sections' => self::stages(CourseProgressService::planetFor($course)),
        ];
    }

    /** The stages every built-in lesson of a planet has, in their order, with a hint of what goes in each. */
    public static function stages(?string $planet): array
    {
        $p = fn (string $text) => '<p class="body-text">'.$text.'</p>';
        $explain = ['heading' => 'Simple Explanation', 'html' => $p('Explain the idea in plain words. Put the key words in <strong>bold</strong>.')];
        $diagram = ['heading' => 'Field Diagram', 'html' => $p('Upload a diagram image that shows the idea.')];
        $quiz = ['heading' => 'Quiz', 'html' => ''];

        return match ($planet) {
            'networking' => [
                $explain,
                ['heading' => 'Crew Briefing', 'html' => $p('Tell it as a short scene: what the crew sees on the network and why it matters.')],
                $diagram,
                $quiz,
                ['heading' => 'Relay Lab', 'html' => $p('Say what the student will configure in the simulator.')],
            ],
            'cybersecurity' => [
                $explain,
                ['heading' => 'Astro Says', 'html' => $p('Astro tells it as a short story: what the attack or defense looks like in real life.')],
                $diagram,
                $quiz,
                ['heading' => 'Defense Lab', 'html' => $p('Say what the student will defend in the simulator.')],
            ],
            default => [
                $explain,
                ['heading' => 'Picture It', 'html' => $p('Compare the idea to something from everyday life.')],
                $diagram,
                ['heading' => 'Code Example', 'html' => '<pre class="code-block"><code>// a short example program</code></pre>'.$p('Explain what each line does.')],
                ['heading' => 'Interactive Coding Exercise', 'html' => $p('Tell the student what to code, then add it with “+ Coding lab”.')],
                $quiz,
            ],
        };
    }

    /** A renamed lesson keeps its Blade page, but the page heading follows the new name. */
    private static function retitle(string $html, string $course, string $module, string $lesson): string
    {
        $title = CourseStructure::lessonTitle($course, $module, $lesson);
        $original = null;
        foreach (config("course-structure.{$course}.modules", []) as $mk => $m) {
            foreach ($m['lessons'] ?? [] as $lk => $l) {
                if (CourseProgressService::normalize((string) $mk, (string) $lk) === [$module, $lesson]) {
                    $original = $l['title'] ?? null;
                }
            }
        }
        if ($title === null || $original === null || $title === $original) {
            return $html;
        }

        return preg_replace_callback('/(<h1 class="lesson-heading">)(.*?)(<\/h1>)/s', function ($m) use ($original, $title) {
            $replaced = str_replace(e($original), e($title), $m[2], $count);

            return $m[1].($count ? $replaced : $m[2]).$m[3];
        }, $html, 1);
    }

    /** Which kind of locked block an element is, or null when the faculty may edit it as text. */
    private static function lockedKind(array $node): ?string
    {
        $class = ' '.$node['class'].' ';
        $outer = $node['outer'];

        return match (true) {
            str_contains($class, ' quiz-block ') => 'quiz',
            str_contains($class, ' checkpoint-block ') => 'checkpoint',
            str_contains($class, ' relay-lab-cta ') || str_contains($outer, 'data-lab=') => 'lab',
            str_contains($class, ' cta-wrap ') || str_contains($outer, '<form') => 'exercise',
            (bool) preg_match('/<(script|style|iframe|input|button|textarea|select|template)\b|\sx-data=|\son[a-z]+=/i', $outer) => 'interactive',
            (bool) preg_match('/<svg\b/i', $outer) => 'diagram',
            default => null,
        };
    }

    /**
     * Top-level elements of an HTML string, up to the end of their parent: [{tag, class, inner, outer}].
     * Text between elements is ignored (lesson files only hold elements at this level).
     *
     * @return list<array{tag: string, class: string, inner: string, outer: string}>
     */
    private static function children(string $html): array
    {
        static $void = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'source', 'track', 'wbr'];
        $tag = '/<!--.*?-->|<(\/?)([a-zA-Z][\w:-]*)((?:\s+[^\s"\'>\/=]+(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+))?)*)\s*(\/?)>/s';

        $out = [];
        $depth = 0;
        $start = null;
        $open = null;
        $offset = 0;

        while (preg_match($tag, $html, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $pos = $m[0][1];
            $len = strlen($m[0][0]);
            $offset = $pos + $len;

            if (! isset($m[2]) || $m[2][0] === '') {
                continue;   // a comment
            }

            $closing = $m[1][0] === '/';
            $name = strtolower($m[2][0]);
            $selfClosing = ($m[4][0] ?? '') === '/' || in_array($name, $void, true);

            // <pre>/<code> text is escaped in the lessons, but raw-text elements are skipped whole to be safe.
            if (! $closing && in_array($name, ['script', 'style'], true)) {
                $end = stripos($html, '</'.$name, $offset);
                $close = $end === false ? strlen($html) : strpos($html, '>', $end) + 1;
                if ($depth === 0) {
                    $out[] = ['tag' => $name, 'class' => self::attr($m[3][0], 'class'), 'inner' => '', 'outer' => substr($html, $pos, $close - $pos)];
                }
                $offset = $close;

                continue;
            }

            if ($closing) {
                if ($depth === 0) {
                    break;   // the parent's own closing tag
                }
                $depth--;
                if ($depth === 0 && $open !== null) {
                    $out[] = [
                        'tag' => $open['tag'],
                        'class' => $open['class'],
                        'inner' => substr($html, $open['innerStart'], $pos - $open['innerStart']),
                        'outer' => substr($html, $start, $offset - $start),
                    ];
                    $open = null;
                }

                continue;
            }

            if ($depth === 0) {
                $start = $pos;
                if ($selfClosing) {
                    $out[] = ['tag' => $name, 'class' => self::attr($m[3][0], 'class'), 'inner' => '', 'outer' => $m[0][0]];

                    continue;
                }
                $open = ['tag' => $name, 'class' => self::attr($m[3][0], 'class'), 'innerStart' => $offset];
            }
            if (! $selfClosing) {
                $depth++;
            }
        }

        return $out;
    }

    private static function attr(string $attrs, string $name): string
    {
        return preg_match('/\s'.$name.'\s*=\s*(?:"([^"]*)"|\'([^\']*)\')/i', $attrs, $m) ? ($m[1] !== '' ? $m[1] : ($m[2] ?? '')) : '';
    }

    private static function plain(string $html): string
    {
        return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5)));
    }

    /** Elements kept as they are (with their safe attributes). */
    private const ALLOWED = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'code', 'pre', 'ul', 'ol', 'li', 'a', 'h3', 'h4',
        'blockquote', 'figure', 'figcaption', 'img', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'span', 'div', 'hr', 'sub', 'sup',
    ];

    /** Elements removed together with everything inside them. */
    private const DROPPED = [
        'script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'form', 'input', 'button', 'textarea',
        'select', 'template', 'link', 'meta', 'base', 'frame', 'frameset', 'noscript', 'video', 'audio', 'canvas',
    ];

    /** Class the lesson styles expect on a bare element. */
    private const DEFAULT_CLASS = ['p' => 'body-text', 'ul' => 'body-list', 'ol' => 'body-list', 'pre' => 'code-block', 'figure' => 'net-diagram'];

    private static function clean(Node $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node->nodeType === XML_COMMENT_NODE) {
                $node->remove();

                continue;
            }
            if (! $node instanceof Element) {
                continue;
            }

            $name = strtolower($node->localName);

            if (in_array($name, self::DROPPED, true) || $node->namespaceURI !== 'http://www.w3.org/1999/xhtml') {
                $node->remove();

                continue;
            }

            self::clean($node);

            if (! in_array($name, self::ALLOWED, true)) {
                // Unknown element (h1, h2, font, …): keep its contents, lose the wrapper.
                while ($node->firstChild) {
                    $node->parentNode->insertBefore($node->firstChild, $node);
                }
                $node->remove();

                continue;
            }

            self::cleanAttributes($node, $name);
        }
    }

    private static function cleanAttributes(Element $el, string $name): void
    {
        foreach (iterator_to_array($el->attributes) as $attr) {
            $attrName = strtolower($attr->name);
            $value = trim($attr->value);

            $keep = match ($attrName) {
                'class' => (bool) preg_match('/^[a-z0-9 _-]*$/i', $value),
                'data-keep' => $name === 'div' && ctype_digit($value),
                'data-checkpoint' => $name === 'div' && (bool) preg_match('/^[a-z0-9_-]{1,24}$/', $value),
                'href' => $name === 'a' && self::safeUrl($value, true),
                'src' => $name === 'img' && self::safeUrl($value, false),
                'alt', 'title' => true,
                'colspan', 'rowspan' => ctype_digit($value),
                default => false,
            };
            if (! $keep) {
                $el->removeAttribute($attr->name);
            }
        }

        if ($name === 'a' && $el->hasAttribute('href')) {
            $el->setAttribute('target', '_blank');
            $el->setAttribute('rel', 'noopener noreferrer');
        }
        if (isset(self::DEFAULT_CLASS[$name]) && ! $el->hasAttribute('class')) {
            $el->setAttribute('class', self::DEFAULT_CLASS[$name]);
        }
        if ($name === 'img') {
            $el->setAttribute('loading', 'lazy');
        }
    }

    /** http(s) anywhere, or a path on this site; links may also be mailto. Never javascript:/data:. */
    private static function safeUrl(string $url, bool $link): bool
    {
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        return (bool) preg_match($link ? '/^(https?:\/\/|mailto:)/i' : '/^https?:\/\//i', $url);
    }

    private static function cacheKey(string $course): string
    {
        return "lesson-contents:v2:{$course}";
    }
}
