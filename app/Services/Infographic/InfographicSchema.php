<?php

namespace App\Services\Infographic;

/**
 * Validates and coerces the model's infographic JSON into a safe, normalized
 * structure the frontend can render without surprises.
 *
 * The model is NOT trusted. Every field is type-checked and coerced; invalid
 * slides are dropped; a missing cover is synthesized; counts are capped. If
 * the payload is fundamentally unusable (no slides), normalize() returns null
 * and the caller treats it as a generation failure.
 */
class InfographicSchema
{
    /**
     * @return array<string, mixed>|null normalized plan, or null if unusable
     */
    public function normalize(?array $raw): ?array
    {
        if (! is_array($raw)) {
            return null;
        }

        $title = $this->asText($raw['title'] ?? '');
        $subtitle = $this->asText($raw['subtitle'] ?? '');
        $theme = $this->asTheme($raw['theme'] ?? '');
        $sourceSummary = $this->asText($raw['source_summary'] ?? '');

        if ($title === '') {
            $title = 'Your Visual Lesson';
        }

        $slides = $raw['slides'] ?? null;
        if (! is_array($slides) || $slides === []) {
            return null;
        }

        $normalized = [];
        foreach ($slides as $slide) {
            $s = $this->normalizeSlide($slide);
            if ($s !== null) {
                $normalized[] = $s;
            }
        }

        if ($normalized === []) {
            return null;
        }

        // Guarantee a cover as the first slide.
        if (($normalized[0]['type'] ?? '') !== 'cover') {
            array_unshift($normalized, $this->cover($title, $subtitle, $theme));
        }

        $max = (int) config('infographic.max_slides', 12);
        if (count($normalized) > $max) {
            $normalized = array_slice($normalized, 0, $max);
        }

        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'theme' => $theme,
            'source_summary' => $sourceSummary,
            'slide_count' => count($normalized),
            'slides' => $normalized,
        ];
    }

    /**
     * @param  array<string, mixed>|mixed  $slide
     * @return array<string, mixed>|null
     */
    protected function normalizeSlide($slide): ?array
    {
        if (! is_array($slide)) {
            return null;
        }

        $type = $this->asType($slide['type'] ?? '');
        $title = $this->asText($slide['title'] ?? '');
        if ($title === '') {
            // A slide without a title can't be rendered meaningfully.
            return null;
        }

        $out = [
            'type' => $type,
            'title' => $title,
            'subtitle' => $this->asText($slide['subtitle'] ?? ''),
            'summary' => $this->asText($slide['summary'] ?? ''),
            'keyPoints' => $this->asTextList($slide['keyPoints'] ?? null),
            'analogy' => $this->asText($slide['analogy'] ?? ''),
            'visualConcept' => $this->asText($slide['visualConcept'] ?? ''),
            'caption' => $this->asText($slide['caption'] ?? ''),
            'elements' => $this->asElementList($slide['elements'] ?? null),
            'flow' => $this->asTextList($slide['flow'] ?? null),
            'comparison' => $this->asComparison($slide['comparison'] ?? null),
            'code' => $this->asCode($slide['code'] ?? null),
            'steps' => $this->asStepList($slide['steps'] ?? null),
            'visual' => $this->asVisual($slide['visual'] ?? null),
            'image' => null,
            'image_status' => 'none',
        ];

        return $out;
    }

    /**
     * @param  array<string, mixed>  $visual
     * @return array<string, mixed>
     */
    protected function asVisual($visual): array
    {
        if (! is_array($visual)) {
            return ['required' => false, 'type' => 'illustration', 'prompt' => ''];
        }

        return [
            'required' => (bool) ($visual['required'] ?? false),
            'type' => is_string($visual['type'] ?? null) ? $visual['type'] : 'illustration',
            'prompt' => $this->asText($visual['prompt'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>|mixed  $comparison
     * @return array<string, mixed>|null
     */
    protected function asComparison($comparison): ?array
    {
        if (! is_array($comparison)) {
            return null;
        }
        $left = $comparison['left'] ?? null;
        $right = $comparison['right'] ?? null;
        if (! is_array($left) && ! is_array($right)) {
            return null;
        }

        return [
            'left' => [
                'label' => $this->asText($left['label'] ?? 'Option A'),
                'points' => $this->asTextList($left['points'] ?? null),
            ],
            'right' => [
                'label' => $this->asText($right['label'] ?? 'Option B'),
                'points' => $this->asTextList($right['points'] ?? null),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|mixed  $code
     * @return array<string, mixed>|null
     */
    protected function asCode($code): ?array
    {
        if (! is_array($code)) {
            return null;
        }
        $snippet = $this->asText($code['snippet'] ?? '');
        if ($snippet === '') {
            return null;
        }

        return [
            'language' => $this->asText($code['language'] ?? 'text'),
            'snippet' => $snippet,
        ];
    }

    /**
     * @param  array<int, mixed>|mixed  $list
     * @return array<int, array{name: string, detail: string}>
     */
    protected function asElementList($list): array
    {
        if (! is_array($list)) {
            return [];
        }
        $out = [];
        foreach ($list as $item) {
            if (! is_array($item)) {
                continue;
            }
            $name = $this->asText($item['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $out[] = [
                'name' => $name,
                'detail' => $this->asText($item['detail'] ?? ''),
            ];
        }

        return array_slice($out, 0, 8);
    }

    /**
     * @param  array<int, mixed>|mixed  $list
     * @return array<int, array{title: string, detail: string}>
     */
    protected function asStepList($list): array
    {
        if (! is_array($list)) {
            return [];
        }
        $out = [];
        foreach ($list as $item) {
            if (! is_array($item)) {
                continue;
            }
            $title = $this->asText($item['title'] ?? '');
            if ($title === '') {
                continue;
            }
            $out[] = [
                'title' => $title,
                'detail' => $this->asText($item['detail'] ?? ''),
            ];
        }

        return array_slice($out, 0, 10);
    }

    /**
     * @param  mixed  $list
     * @return array<int, string>
     */
    protected function asTextList($list): array
    {
        if (! is_array($list)) {
            return [];
        }
        $out = [];
        foreach ($list as $item) {
            $t = $this->asText($item);
            if ($t !== '') {
                $out[] = $t;
            }
        }

        return array_slice($out, 0, 8);
    }

    protected function asType($type): string
    {
        $allowed = (array) config('infographic.allowed_types', ['concept']);
        $t = is_string($type) ? strtolower(trim($type)) : '';
        if (! in_array($t, $allowed, true)) {
            return 'concept';
        }

        return $t;
    }

    protected function asTheme($theme): string
    {
        $t = is_string($theme) ? strtolower(trim(preg_replace('/\s+/', '_', $theme))) : '';
        if ($t === '') {
            return 'space_mission_control';
        }

        return $t;
    }

    protected function asText($value): string
    {
        if (is_string($value)) {
            return trim(preg_replace('/\s+/', ' ', $value));
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        return '';
    }

    /**
     * @return array<string, mixed>
     */
    protected function cover(string $title, string $subtitle, string $theme): array
    {
        return [
            'type' => 'cover',
            'title' => $title,
            'subtitle' => $subtitle,
            'summary' => '',
            'keyPoints' => [],
            'analogy' => '',
            'visualConcept' => '',
            'caption' => '',
            'elements' => [],
            'flow' => [],
            'comparison' => null,
            'code' => null,
            'steps' => [],
            'visual' => ['required' => false, 'type' => 'illustration', 'prompt' => ''],
            'image' => null,
            'image_status' => 'none',
        ];
    }
}
