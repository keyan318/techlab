<?php

namespace App\Services\ImageGeneration;

use Illuminate\Support\Facades\Storage;

/**
 * Offline, dependency-free illustration provider.
 *
 * Renders a friendly, TechLab-branded "comic" SVG that visualizes the EXACT
 * analogy Astro used: the title, the key elements as little characters/cards in
 * story order, arrows labelled with the flow between them, and a caption.
 *
 * This is the default provider so the Draw Analogy feature works with zero
 * external credentials. Swap it for a hosted image model by implementing
 * ImageProviderInterface and changing config/images.php — no other code changes.
 */
class LocalComicProvider implements ImageProviderInterface
{
    protected const WIDTH = 820;
    protected const HEIGHT = 480;

    protected const ACCENTS = ['#73b6ff', '#9b6bff', '#5be1ff', '#ffb86b', '#7cffb2'];

    public function generate(array $data): string
    {
        $title = $this->asText($data['title'] ?? '');
        $caption = $this->asText($data['caption'] ?? '');
        $elements = $this->normalizeElements($data['elements'] ?? []);
        $flow = array_values(array_filter(array_map([$this, 'asText'], $data['flow'] ?? []), 'strlen'));

        $svg = $this->buildSvg($title, $caption, $elements, $flow);

        $filename = 'analogies/astralogy_'.uniqid('', true).'.svg';
        Storage::disk('public')->makeDirectory('analogies');
        Storage::disk('public')->put($filename, $svg);

        return Storage::disk('public')->url($filename);
    }

    /**
     * @param  array<int, array{name?: string, role?: string}>  $raw
     * @return array<int, array{name: string, role: string}>
     */
    protected function normalizeElements(array $raw): array
    {
        $elements = [];
        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }
            $name = $this->asText($item['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $elements[] = ['name' => $name, 'role' => $this->asText($item['role'] ?? '')];
        }

        // Guarantee at least one element so the canvas is never empty.
        if ($elements === []) {
            $elements[] = ['name' => 'The idea', 'role' => ''];
        }

        // Cap to keep the layout readable.
        return array_slice($elements, 0, 5);
    }

    protected function buildSvg(string $title, string $caption, array $elements, array $flow): string
    {
        $n = count($elements);
        $pad = 40;
        $usable = self::WIDTH - $pad * 2;
        $gap = 28;
        $cardW = ($usable - ($n - 1) * $gap) / $n;
        $cardY = 150;
        $cardH = 150;

        $cards = '';
        $arrows = '';

        foreach ($elements as $i => $el) {
            $x = $pad + $i * ($cardW + $gap);
            $cx = $x + $cardW / 2;
            $accent = self::ACCENTS[$i % count(self::ACCENTS)];

            $cards .= $this->card($x, $cardY, $cardW, $cardH, $cx, $el, $accent);

            if ($i < $n - 1) {
                $label = $flow[$i] ?? '';
                $arrows .= $this->arrow(
                    $x + $cardW,
                    $cardY + $cardH / 2,
                    $x + $cardW + $gap,
                    $cardY + $cardH / 2,
                    $label
                );
            }
        }

        $defs = $this->defs();

        $titleBlock = $this->textBlock(
            self::WIDTH / 2, 52, $title ?: 'Analogy', 46, 22, '#eaeeff', '700', 28, 'middle'
        );

        $captionBlock = '';
        if ($caption !== '') {
            $cy = self::HEIGHT - 78;
            $captionBlock =
                '<rect x="'.($pad - 10).'" y="'.($cy - 26).'" width="'.($usable + 20).'" height="60" rx="14" '
                .'fill="rgba(6,6,26,0.45)" stroke="rgba(150,170,255,0.25)" stroke-width="1"/>'
                .$this->textBlock(self::WIDTH / 2, $cy, $caption, 70, 13, '#c9d2ff', '400', 18, 'middle');
        }

        return
            '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.self::WIDTH.' '.self::HEIGHT.'" '
            .'width="'.self::WIDTH.'" height="'.self::HEIGHT.'" role="img" '
            .'aria-label="'.($this->esc('Astro analogy illustration: '.$title)).'">'."\n"
            .$defs."\n"
            .'<rect x="0" y="0" width="'.self::WIDTH.'" height="'.self::HEIGHT.'" rx="28" fill="url(#bg)"/>'."\n"
            .$this->badge()."\n"
            .$titleBlock."\n"
            .$arrows."\n"
            .$cards."\n"
            .$captionBlock."\n"
            .'</svg>'."\n";
    }

    protected function defs(): string
    {
        return
            '<defs>'
            .'<linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">'
            .'<stop offset="0%" stop-color="#120a33"/>'
            .'<stop offset="55%" stop-color="#1e1259"/>'
            .'<stop offset="100%" stop-color="#241456"/>'
            .'</linearGradient>'
            .'<marker id="arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">'
            .'<path d="M0,0 L10,5 L0,10 z" fill="#9b6bff"/>'
            .'</marker>'
            .'</defs>';
    }

    protected function badge(): string
    {
        return
            '<g transform="translate(28,18)">'
            .'<rect x="0" y="0" width="150" height="26" rx="13" fill="rgba(123,142,220,0.16)" stroke="rgba(150,170,255,0.3)"/>'
            .'<circle cx="15" cy="13" r="7" fill="#5be1ff"/>'
            .'<text x="30" y="17" font-family="Space Grotesk, sans-serif" font-size="12" font-weight="600" fill="#eaeeff">Astro · analogy</text>'
            .'</g>';
    }

    /**
     * @param  array{name: string, role: string}  $el
     */
    protected function card(float $x, float $y, float $w, float $h, float $cx, array $el, string $accent): string
    {
        $headCy = $y + 46;
        $face =
            '<circle cx="'.$cx.'" cy="'.$headCy.'" r="24" fill="'.$accent.'" opacity="0.92"/>'
            .'<circle cx="'.($cx - 8).'" cy="'.($headCy - 4).'" r="3" fill="#0b1030"/>'
            .'<circle cx="'.($cx + 8).'" cy="'.($headCy - 4).'" r="3" fill="#0b1030"/>'
            .'<path d="M'.($cx - 9).','.($headCy + 7).' Q'.$cx.','.($headCy + 16).' '.($cx + 9).','.($headCy + 7).'" '
            .'stroke="#0b1030" stroke-width="2.4" fill="none" stroke-linecap="round"/>';

        $nameBlock = $this->textBlock($cx, $y + 96, $el['name'], (int) (($w - 14) / 8.2), 15, '#eaeeff', '700', 18, 'middle');
        $roleBlock = $el['role'] !== ''
            ? $this->textBlock($cx, $y + 118, $el['role'], (int) (($w - 14) / 6.4), 11, '#98a2d4', '400', 14, 'middle')
            : '';

        return
            '<rect x="'.$x.'" y="'.$y.'" width="'.$w.'" height="'.$h.'" rx="16" '
            .'fill="#1b1450" stroke="'.$accent.'" stroke-width="2"/>'
            .$face."\n".$nameBlock."\n".$roleBlock;
    }

    protected function arrow(float $x1, float $y, float $x2, float $y2, string $label): string
    {
        $midX = ($x1 + $x2) / 2;
        $line =
            '<line x1="'.($x1 + 4).'" y1="'.$y.'" x2="'.($x2 - 6).'" y2="'.$y2.'" '
            .'stroke="#9b6bff" stroke-width="2.5" marker-end="url(#arrow)"/>';

        $text = $label !== ''
            ? '<text x="'.$midX.'" y="'.($y - 8).'" font-family="Space Grotesk, sans-serif" font-size="11" '
            .'font-weight="600" fill="#c9b8ff" text-anchor="middle">'.$this->esc($label).'</text>'
            : '';

        return $line."\n".$text;
    }

    /**
     * Build a <text> element with word-wrapped <tspan>s.
     */
    protected function textBlock(float $x, float $y, string $text, int $maxChars, float $fontSize, string $fill, string $weight, float $lineHeight, string $anchor): string
    {
        $lines = $this->wrap($text, max(8, $maxChars));
        $spans = '';
        foreach ($lines as $i => $line) {
            $dy = $i === 0 ? '0' : $lineHeight;
            $spans .= '<tspan x="'.$x.'" dy="'.$dy.'">'.$this->esc($line).'</tspan>';
        }

        return '<text x="'.$x.'" y="'.$y.'" font-family="Inter, system-ui, sans-serif" font-size="'.$fontSize
            .'" font-weight="'.$weight.'" fill="'.$fill.'" text-anchor="'.$anchor.'">'.$spans.'</text>';
    }

    /**
     * Wrap text into lines of roughly $maxChars characters (word-aware).
     *
     * @return string[]
     */
    protected function wrap(string $text, int $maxChars): array
    {
        $text = preg_replace('/\s+/', ' ', trim($text));
        if ($text === '') {
            return [''];
        }

        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            if (mb_strlen($candidate) > $maxChars && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    protected function asText($value): string
    {
        if (is_string($value)) {
            return trim(preg_replace('/\s+/', ' ', $value));
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return '';
    }

    /**
     * Escape text for safe inclusion inside SVG/XML (no attribute injection).
     */
    protected function esc(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
