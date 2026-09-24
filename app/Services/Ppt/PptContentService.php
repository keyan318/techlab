<?php

namespace App\Services\Ppt;

use App\Exceptions\NvidiaNimException;
use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Models\Conversation;
use App\Services\Quiz\QuizNimService;
use Illuminate\Support\Facades\Log;

/**
 * Astro as the content writer for faculty member PPT decks.
 *
 * The model only picks layouts and writes short text; everything it returns is
 * cleaned here (caps, allowed layouts, required fields) before the builder draws it.
 */
class PptContentService
{
    private const INSUFFICIENT = 'Tell Astro what the presentation is about (topic, class level, and the look you want) and try again.';

    public function __construct(protected QuizNimService $nim) {}

    /** Faculty/Astro transcript, newest part kept when it is too long (style wishes are usually recent). */
    public function transcript(Conversation $conversation): string
    {
        $lines = [];
        foreach ($conversation->messages()->orderBy('id')->get(['role', 'content']) as $m) {
            $text = trim(preg_replace('/\s+/', ' ', (string) $m->content));
            if ($text !== '') {
                $lines[] = ($m->role === 'assistant' ? 'Astro' : 'Faculty').': '.$text;
            }
        }

        $all = implode("\n\n", $lines);
        $max = (int) config('ppt.max_source_chars', 12000);

        return mb_strlen($all) > $max ? '…'.mb_substr($all, -$max) : $all;
    }

    /**
     * @return array{title: string, theme: string, accent: ?string, slides: array<int, array<string, mixed>>}
     *
     * @throws QuizSourceException
     * @throws QuizGenerationException
     */
    public function generate(string $source): array
    {
        if (mb_strlen(trim($source)) < 40) {
            throw new QuizSourceException(self::INSUFFICIENT, 422);
        }

        $themes = collect(config('ppt.themes', []))->map(fn ($d, $k) => "{$k} ({$d})")->implode('; ');
        $system = str_replace(
            ['{themes}', '{min}', '{max}'],
            [$themes, (string) config('ppt.min_slides', 6), (string) config('ppt.max_slides', 14)],
            (string) config('ppt.system_prompt', '')
        );

        try {
            $raw = $this->nim->completeJson([
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => "Build the slide deck from this conversation:\n\n".$source],
            ], [
                'temperature' => (float) config('ppt.temperature', 0.5),
                'max_tokens' => (int) config('ppt.max_tokens', 7000),
                'timeout' => (int) config('ppt.timeout', 90),
            ]);
        } catch (NvidiaNimException $e) {
            throw new QuizGenerationException($e->getMessage(), $e->category, $e->status ?? 502, $e->requestId, $e);
        }

        if (! is_array($raw) || $raw === []) {
            throw new QuizGenerationException("Astro couldn't shape a deck just now. Please try again in a moment.", 'NIM_EMPTY_RESPONSE', 502);
        }

        if (! empty($raw['insufficient_content'])) {
            $reason = is_string($raw['reason'] ?? null) && trim($raw['reason']) !== '' ? trim($raw['reason']) : self::INSUFFICIENT;
            throw new QuizSourceException($reason, 422);
        }

        $deck = $this->normalize($raw);
        if ($deck === null) {
            Log::warning('PPT model output unusable', ['keys' => array_keys($raw), 'head' => mb_substr(json_encode($raw), 0, 500)]);

            throw new QuizGenerationException("Astro's response didn't form a usable deck. Please try again — sometimes the model needs another pass.", 'SCHEMA_ERROR', 502);
        }

        return $deck;
    }

    /** Validate + clean model output. Returns null when fewer than the minimum usable slides survive. */
    public function normalize(array $raw): ?array
    {
        $rawSlides = $raw['slides'] ?? null;
        if (! is_array($rawSlides)) {
            return null;
        }

        $slides = [];
        foreach (array_values($rawSlides) as $s) {
            $clean = is_array($s) ? $this->slide($s) : null;
            if ($clean !== null) {
                $slides[] = $clean;
            }
            if (count($slides) >= (int) config('ppt.max_slides', 14)) {
                break;
            }
        }

        if (count($slides) < (int) config('ppt.min_slides', 6)) {
            return null;
        }

        $title = $this->line($raw['title'] ?? null, 60) ?: ($slides[0]['title'] ?? 'Presentation');
        $theme = strtolower((string) ($raw['theme'] ?? ''));
        $accent = ltrim((string) ($raw['accent'] ?? ''), '#');

        return [
            'title' => $title,
            'theme' => array_key_exists($theme, config('ppt.themes', [])) ? $theme : 'midnight',
            'accent' => preg_match('/^[0-9A-Fa-f]{6}$/', $accent) ? strtoupper($accent) : null,
            'slides' => $slides,
        ];
    }

    private function slide(array $s): ?array
    {
        $layout = (string) ($s['layout'] ?? '');
        $notes = $this->line($s['notes'] ?? null, 400);
        $out = match ($layout) {
            'cover' => ($t = $this->line($s['title'] ?? null, 70)) === '' ? null : [
                'kicker' => $this->line($s['kicker'] ?? null, 40), 'title' => $t, 'subtitle' => $this->line($s['subtitle'] ?? null, 90),
            ],
            'statement' => ($t = $this->line($s['text'] ?? null, 140)) === '' ? null : [
                'kicker' => $this->line($s['kicker'] ?? null, 30), 'text' => $t,
            ],
            'section' => ($t = $this->line($s['title'] ?? null, 55)) === '' ? null : [
                'number' => max(1, (int) ($s['number'] ?? 1)), 'title' => $t, 'subtitle' => $this->line($s['subtitle'] ?? null, 90),
            ],
            'split' => $this->split($s),
            'cards' => $this->items($s, 'cards', 3, 4, 26, 110),
            'steps' => $this->items($s, 'steps', 3, 5, 22, 80),
            'bigStat' => $this->stats($s),
            'compare' => $this->compare($s),
            'code' => $this->code($s),
            'quote' => ($t = $this->line($s['text'] ?? null, 180)) === '' ? null : [
                'text' => $t, 'by' => $this->line($s['by'] ?? null, 50),
            ],
            'closing' => $this->closing($s),
            default => null,
        };

        if ($out === null) {
            return null;
        }

        return ['layout' => $layout, 'notes' => $notes] + $out;
    }

    private function split(array $s): ?array
    {
        $title = $this->line($s['title'] ?? null, 70);
        $points = $this->list($s['points'] ?? null, 4, 100);
        $aside = $this->line($s['aside'] ?? null, 150);

        return ($title === '' || count($points) < 2 || $aside === '') ? null : [
            'title' => $title, 'points' => $points, 'asideLabel' => $this->line($s['asideLabel'] ?? null, 16) ?: 'Remember', 'aside' => $aside,
        ];
    }

    private function items(array $s, string $layout, int $min, int $max, int $titleMax, int $textMax): ?array
    {
        $title = $this->line($s['title'] ?? null, 70);
        $items = [];
        foreach ((array) ($s['items'] ?? []) as $it) {
            if (! is_array($it)) {
                continue;
            }
            $t = $this->line($it['title'] ?? null, $titleMax);
            $x = $this->line($it['text'] ?? null, $textMax);
            if ($t !== '' && $x !== '') {
                $items[] = ['title' => $t, 'text' => $x];
            }
        }
        $items = array_slice($items, 0, $max);

        return ($title === '' || count($items) < $min) ? null : ['title' => $title, 'items' => $items];
    }

    private function stats(array $s): ?array
    {
        $title = $this->line($s['title'] ?? null, 70);
        $stats = [];
        foreach ((array) ($s['stats'] ?? []) as $st) {
            if (! is_array($st)) {
                continue;
            }
            $v = $this->line($st['value'] ?? null, 8);
            $l = $this->line($st['label'] ?? null, 60);
            if ($v !== '' && $l !== '') {
                $stats[] = ['value' => $v, 'label' => $l];
            }
        }
        $stats = array_slice($stats, 0, 4);

        return ($title === '' || count($stats) < 2) ? null : ['title' => $title, 'stats' => $stats];
    }

    private function compare(array $s): ?array
    {
        $title = $this->line($s['title'] ?? null, 70);
        $col = function ($c) {
            $c = is_array($c) ? $c : [];
            $h = $this->line($c['heading'] ?? null, 24);
            $p = $this->list($c['points'] ?? null, 4, 80);

            return ($h === '' || count($p) < 1) ? null : ['heading' => $h, 'points' => $p];
        };
        $left = $col($s['left'] ?? null);
        $right = $col($s['right'] ?? null);

        return ($title === '' || ! $left || ! $right) ? null : ['title' => $title, 'left' => $left, 'right' => $right];
    }

    private function code(array $s): ?array
    {
        $title = $this->line($s['title'] ?? null, 70);
        $code = is_string($s['code'] ?? null) ? str_replace(["\r\n", "\t"], ["\n", '    '], strip_tags($s['code'])) : '';
        $lines = array_map(fn ($l) => mb_substr(rtrim($l), 0, 70), explode("\n", trim($code, "\n")));
        $code = implode("\n", array_slice($lines, 0, 12));

        return ($title === '' || trim($code) === '') ? null : ['title' => $title, 'code' => $code, 'caption' => $this->line($s['caption'] ?? null, 110)];
    }

    private function closing(array $s): ?array
    {
        $title = $this->line($s['title'] ?? null, 55);
        $points = $this->list($s['points'] ?? null, 4, 90);

        return ($title === '' || count($points) < 2) ? null : ['kicker' => $this->line($s['kicker'] ?? null, 30) ?: 'Key takeaways', 'title' => $title, 'points' => $points];
    }

    /** @return string[] */
    private function list(mixed $v, int $max, int $len): array
    {
        $out = [];
        foreach ((array) $v as $x) {
            $x = $this->line($x, $len);
            if ($x !== '') {
                $out[] = $x;
            }
        }

        return array_slice($out, 0, $max);
    }

    private function line(mixed $v, int $max): string
    {
        if (! is_string($v) && ! is_numeric($v)) {
            return '';
        }
        $v = trim(preg_replace('/\s+/', ' ', strip_tags((string) $v)));

        return mb_strlen($v) > $max ? rtrim(mb_substr($v, 0, $max - 1)).'…' : $v;
    }
}
