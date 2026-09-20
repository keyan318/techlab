<?php

namespace App\Services\Infographic\Providers;

use App\Services\Infographic\InfographicImageProviderInterface;
use Illuminate\Support\Facades\Storage;

/**
 * Offline, dependency-free space-art generator.
 *
 * Turns a slide's structured art spec into a branded TechLab "galaxy"
 * illustration: deep-space gradient, scattered stars, an orbit, a central
 * planet/motif labelled with the topic, and orbiting chips derived from the
 * slide's key points or elements. This is the DEFAULT provider, so the
 * Infographic deck always renders with real, topic-aware artwork — no API key,
 * no external call, no network dependency.
 *
 * Swap in a hosted image model by implementing InfographicImageProviderInterface
 * and changing config/infographic.php; nothing else changes.
 */
class LocalInfographicArtProvider implements InfographicImageProviderInterface
{
    protected const WIDTH = 900;

    protected const HEIGHT = 520;

    protected const ACCENTS = [
        'space_mission_control' => '#73b6ff',
        'constellation_network' => '#9b6bff',
        'data_planet' => '#5be1ff',
        'orbital_firewall' => '#ff8aa0',
        'navigation_route' => '#7cffb2',
        'spacecraft_structure' => '#ffb86b',
        'cosmic_lab' => '#c9b8ff',
    ];

    public function generate(array $artSpec): ?string
    {
        $title = $this->asText($artSpec['title'] ?? '');
        if ($title === '') {
            return null;
        }

        $theme = $this->asText($artSpec['theme'] ?? 'space_mission_control');
        $accent = $this->accent($theme);
        $type = $this->asText($artSpec['type'] ?? 'concept');

        $nodes = $this->nodes($artSpec);
        $svg = $this->buildSvg($title, $accent, $type, $nodes, $theme);

        $filename = 'infographics/astroart_'.crc32($title.$theme.time()).'_'.uniqid('', true).'.svg';
        Storage::disk('public')->makeDirectory('infographics');
        Storage::disk('public')->put($filename, $svg);

        return Storage::disk('public')->url($filename);
    }

    /**
     * @return array<int, string>
     */
    protected function nodes(array $artSpec): array
    {
        $from = $artSpec['keyPoints'] ?? [];
        if (! is_array($from) || $from === []) {
            $from = $artSpec['elements'] ?? [];
            if (is_array($from)) {
                $from = array_map(fn ($e) => is_array($e) ? ($e['name'] ?? '') : '', $from);
            }
        }
        if (! is_array($from)) {
            $from = [];
        }
        $out = [];
        foreach ($from as $item) {
            $t = $this->asText($item);
            if ($t !== '') {
                $out[] = $t;
            }
        }

        return array_slice($out, 0, 4);
    }

    protected function buildSvg(string $title, string $accent, string $type, array $nodes, string $theme): string
    {
        $seed = crc32($title.$theme) ?: 1;
        mt_srand($seed);

        $defs = $this->defs($accent);
        $stars = $this->stars();
        $badge = $this->badge();

        $cx = self::WIDTH / 2;
        $cy = self::HEIGHT / 2 - 20;

        $scene = match ($type) {
            'comparison' => $this->sceneComparison($cx, $cy, $accent, $nodes),
            'architecture', 'diagram' => $this->sceneHub($cx, $cy, $accent, $nodes),
            'process', 'timeline' => $this->sceneRail($cx, $cy, $accent, $nodes),
            default => $this->sceneGalaxy($cx, $cy, $accent, $nodes),
        };

        $planetTitle = $this->planetTitle($cx, $cy - 8, $title);

        return
            '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.self::WIDTH.' '.self::HEIGHT.'" '
            .'width="'.self::WIDTH.'" height="'.self::HEIGHT.'" role="img" '
            .'aria-label="'.('Astro illustration: '.$this->esc($title)).'">'."\n"
            .$defs."\n"
            .'<rect x="0" y="0" width="'.self::WIDTH.'" height="'.self::HEIGHT.'" rx="28" fill="url(#bg)"/>'."\n"
            .$stars."\n"
            .$scene."\n"
            .$planetTitle."\n"
            .$badge."\n"
            .'</svg>'."\n";
    }

    protected function defs(string $accent): string
    {
        return
            '<defs>'
            .'<radialGradient id="bg" cx="50%" cy="38%" r="80%">'
            .'<stop offset="0%" stop-color="#1a1147"/>'
            .'<stop offset="55%" stop-color="#140b35"/>'
            .'<stop offset="100%" stop-color="#06061a"/>'
            .'</radialGradient>'
            .'<radialGradient id="planet" cx="38%" cy="32%" r="75%">'
            .'<stop offset="0%" stop-color="'.$accent.'"/>'
            .'<stop offset="100%" stop-color="#241456"/>'
            .'</radialGradient>'
            .'<marker id="arrow'.$this->safeId($accent).'" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">'
            .'<path d="M0,0 L10,5 L0,10 z" fill="'.$accent.'"/>'
            .'</marker>'
            .'</defs>';
    }

    protected function safeId(string $accent): string
    {
        return preg_replace('/[^a-z0-9]/', '', $accent) ?: 'a';
    }

    protected function stars(): string
    {
        $out = '';
        for ($i = 0; $i < 70; $i++) {
            $x = mt_rand(0, self::WIDTH);
            $y = mt_rand(0, self::HEIGHT);
            $r = mt_rand(4, 14) / 10;
            $o = (mt_rand(30, 90) / 100);
            $out .= '<circle cx="'.$x.'" cy="'.$y.'" r="'.$r.'" fill="#eaeeff" opacity="'.$o.'"/>';
        }

        return $out;
    }

    protected function sceneGalaxy(float $cx, float $cy, string $accent, array $nodes): string
    {
        $orbitR = 150;
        $out = '<ellipse cx="'.$cx.'" cy="'.$cy.'" rx="'.($orbitR + 40).'" ry="'.($orbitR * 0.62).'" fill="none" stroke="'.$accent.'" stroke-opacity="0.35" stroke-width="1.5" stroke-dasharray="4 7"/>';
        $out .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="74" fill="url(#planet)"/>';
        $out .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="74" fill="none" stroke="'.$accent.'" stroke-opacity="0.6" stroke-width="2"/>';
        // ring
        $out .= '<ellipse cx="'.$cx.'" cy="'.$cy.'" rx="104" ry="34" fill="none" stroke="'.$accent.'" stroke-opacity="0.5" stroke-width="3" transform="rotate(-18 '.$cx.' '.$cy.')"/>';

        $n = count($nodes);
        foreach ($nodes as $i => $name) {
            $angle = (-90 + ($n === 1 ? 0 : $i * (360 / $n))) * M_PI / 180;
            $x = $cx + cos($angle) * ($orbitR + 40);
            $y = $cy + sin($angle) * ($orbitR * 0.62);
            $out .= $this->chip($x, $y, $name, $accent);
        }

        return $out;
    }

    protected function sceneHub(float $cx, float $cy, string $accent, array $nodes): string
    {
        $out = '';
        $n = count($nodes);
        foreach ($nodes as $i => $name) {
            $angle = (-90 + ($n === 1 ? 0 : $i * (360 / max(1, $n)))) * M_PI / 180;
            $x = $cx + cos($angle) * 210;
            $y = $cy + sin($angle) * 140;
            $out .= '<line x1="'.$cx.'" y1="'.$cy.'" x2="'.$x.'" y2="'.$y.'" stroke="'.$accent.'" stroke-opacity="0.45" stroke-width="1.5" marker-end="url(#arrow'.$this->safeId($accent).')"/>';
            $out .= $this->chip($x, $y, $name, $accent);
        }
        $out .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="58" fill="url(#planet)" stroke="'.$accent.'" stroke-width="2"/>';

        return $out;
    }

    protected function sceneRail(float $cx, float $cy, string $accent, array $nodes): string
    {
        if ($nodes === []) {
            return $this->sceneGalaxy($cx, $cy, $accent, $nodes);
        }
        $n = count($nodes);
        $gap = min(170, (self::WIDTH - 160) / max(1, $n));
        $startX = $cx - ($gap * ($n - 1)) / 2;
        $out = '<line x1="'.($startX - 30).'" y1="'.$cy.'" x2="'.($startX + $gap * ($n - 1) + 30).'" y2="'.$cy.'" stroke="'.$accent.'" stroke-opacity="0.5" stroke-width="2" marker-end="url(#arrow'.$this->safeId($accent).')"/>';
        foreach ($nodes as $i => $name) {
            $x = $startX + $gap * $i;
            $out .= '<circle cx="'.$x.'" cy="'.$cy.'" r="30" fill="#1b1450" stroke="'.$accent.'" stroke-width="2"/>';
            $out .= '<text x="'.$x.'" y="'.($cy + 5).'" font-family="Space Grotesk, sans-serif" font-size="20" font-weight="700" fill="#eaeeff" text-anchor="middle">'.($i + 1).'</text>';
            $out .= $this->chip($x, $cy + 64, $name, $accent);
        }

        return $out;
    }

    protected function sceneComparison(float $cx, float $cy, string $accent, array $nodes): string
    {
        $left = $cx - 180;
        $right = $cx + 180;
        $out = '<line x1="'.$cx.'" y1="'.($cy - 110).'" x2="'.$cx.'" y2="'.($cy + 110).'" stroke="#98a2d4" stroke-opacity="0.4" stroke-width="1.5" stroke-dasharray="3 6"/>';
        $out .= '<circle cx="'.$left.'" cy="'.$cy.'" r="62" fill="url(#planet)" stroke="'.$accent.'" stroke-width="2"/>';
        $out .= '<circle cx="'.$right.'" cy="'.$cy.'" r="62" fill="#2a1a4d" stroke="#9b6bff" stroke-width="2"/>';
        if (isset($nodes[0])) {
            $out .= $this->chip($left, $cy + 92, $nodes[0], $accent);
        }
        if (isset($nodes[1])) {
            $out .= $this->chip($right, $cy + 92, $nodes[1], '#9b6bff');
        }

        return $out;
    }

    protected function chip(float $x, float $y, string $name, string $accent): string
    {
        $label = $this->wrap($name, 16);
        $w = max(96, (int) (mb_strlen($name) * 7.4) + 24);
        $w = min($w, 200);
        $lines = count($label);
        $h = 34 + ($lines - 1) * 15;
        $rx = $x - $w / 2;

        $spans = '';
        foreach ($label as $i => $ln) {
            $dy = ($i === 0 ? 0 : 15);
            $spans .= '<tspan x="'.$x.'" dy="'.($i === 0 ? '-'.($h / 2 - 10) : $dy).'">'.$this->esc($ln).'</tspan>';
        }

        return
            '<g>'
            .'<rect x="'.$rx.'" y="'.($y - $h / 2).'" width="'.$w.'" height="'.$h.'" rx="14" fill="#1b1450" stroke="'.$accent.'" stroke-opacity="0.7" stroke-width="1.5"/>'
            .'<text font-family="Inter, system-ui, sans-serif" font-size="13" font-weight="600" fill="#eaeeff" text-anchor="middle">'.$spans.'</text>'
            .'</g>';
    }

    protected function planetTitle(float $cx, float $cy, string $title): string
    {
        $label = $this->wrap($title, 22);
        $spans = '';
        foreach ($label as $i => $ln) {
            $dy = $i === 0 ? 0 : 24;
            $spans .= '<tspan x="'.$cx.'" dy="'.($i === 0 ? '-'.((count($label) - 1) * 12).'' : $dy).'">'.$this->esc($ln).'</tspan>';
        }

        return '<text font-family="Space Grotesk, sans-serif" font-size="22" font-weight="700" fill="#eaeeff" text-anchor="middle" y="'.($cy + 210).'">'.$spans.'</text>';
    }

    protected function badge(): string
    {
        return
            '<g transform="translate(28,'.($this->HEIGHT() - 40).')">'
            .'<rect x="0" y="0" width="170" height="26" rx="13" fill="rgba(123,142,220,0.16)" stroke="rgba(150,170,255,0.3)"/>'
            .'<circle cx="15" cy="13" r="7" fill="#5be1ff"/>'
            .'<text x="30" y="17" font-family="Space Grotesk, sans-serif" font-size="12" font-weight="600" fill="#eaeeff">Astro · illustration</text>'
            .'</g>';
    }

    protected function HEIGHT(): int
    {
        return self::HEIGHT;
    }

    /**
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

        return array_slice($lines, 0, 3);
    }

    protected function accent(string $theme): string
    {
        return self::ACCENTS[$theme] ?? '#73b6ff';
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

    protected function esc(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
