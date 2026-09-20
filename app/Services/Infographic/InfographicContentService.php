<?php

namespace App\Services\Infographic;

use App\Exceptions\InfographicGenerationException;
use App\Exceptions\NvidiaNimException;

/**
 * Astro as the CONTENT / REASONING model for the Infographic feature.
 *
 * Responsibilities (and deliberately NOT others):
 *   - understand the source material
 *   - extract the important concepts
 *   - design a slide structure + space analogy system
 *   - emit a validated, structured JSON lesson plan
 *
 * It does NOT generate frontend code, full SVGs, or the whole illustration —
 * that is the frontend's and the image provider's job. The image model is
 * invoked separately and only for slides that genuinely need artwork.
 */
class InfographicContentService
{
    public function __construct(
        protected InfographicNimService $nim,
        protected InfographicImageService $images,
        protected InfographicSchema $schema
    ) {}

    /**
     * Turn learning material into a structured, image-enhanced infographic plan.
     *
     * @return array<string, mixed>
     *
     * @throws InfographicGenerationException on any model/schema failure
     */
    public function generate(string $source): array
    {
        $messages = [
            ['role' => 'system', 'content' => config('infographic.system_prompt', '')],
            [
                'role' => 'user',
                'content' => "Create a visual lesson (infographic deck) from this learning material:\n\n"
                    .mb_substr($source, 0, (int) config('infographic.max_source_chars', 14000)),
            ],
        ];

        // Astro is the reasoning model; thinking is OFF here for speed and
        // determinism (mirrors the Draw-Analogy JSON path).
        try {
            $raw = $this->nim->completeJson($messages, [
                'temperature' => (float) config('infographic.temperature', 0.4),
                'max_tokens' => (int) config('infographic.max_tokens_json', 4096),
            ]);
        } catch (NvidiaNimException $e) {
            throw new InfographicGenerationException(
                $e->getMessage(),
                $e->category,
                $e->status ?? 502,
                $e->requestId,
                $e
            );
        }

        if (! is_array($raw) || $raw === []) {
            throw new InfographicGenerationException(
                "Astro couldn't shape a lesson from that material just now. Please try again in a moment.",
                'NIM_EMPTY_RESPONSE',
                502
            );
        }

        $plan = $this->schema->normalize($raw);
        if ($plan === null) {
            throw new InfographicGenerationException(
                "Astro's response didn't form a usable lesson. Please try again — sometimes the model needs another pass.",
                'SCHEMA_ERROR',
                502
            );
        }

        // Attach generated artwork only to slides that explicitly need it.
        $plan['slides'] = array_map(function (array $slide) {
            if (! empty($slide['visual']['required'])) {
                $url = $this->images->generateForSlide($slide);
                $slide['image'] = $url;
                $slide['image_status'] = $url === null ? 'fallback' : 'generated';
            }

            return $slide;
        }, $plan['slides']);

        return $plan;
    }

    /**
     * Build the one-page poster: model plans content, image model illustrates.
     *
     * @return array<string, mixed>
     *
     * @throws InfographicGenerationException
     */
    public function generatePoster(string $source, string $style = 'cartoon'): array
    {
        $messages = [
            ['role' => 'system', 'content' => config('infographic.poster_prompt', '')],
            [
                'role' => 'user',
                'content' => "Design the infographic poster from this learning material:\n\n"
                    .mb_substr($source, 0, (int) config('infographic.max_source_chars', 14000)),
            ],
        ];

        // The model can spend part of max_tokens on hidden reasoning and cut the
        // JSON off mid-object; one retry recovers most of those.
        $poster = null;
        for ($attempt = 0; $attempt < 2 && $poster === null; $attempt++) {
            try {
                $raw = $this->nim->completeJson($messages, [
                    'temperature' => (float) config('infographic.temperature', 0.4),
                    'max_tokens' => 4096,
                ]);
            } catch (NvidiaNimException $e) {
                throw new InfographicGenerationException($e->getMessage(), $e->category, $e->status ?? 502, $e->requestId, $e);
            }

            $poster = $this->normalizePoster(is_array($raw) ? $raw : []);
        }

        if ($poster === null) {
            throw new InfographicGenerationException(
                "Astro's response didn't form a usable infographic. Please try again — sometimes the model needs another pass.",
                'SCHEMA_ERROR',
                502
            );
        }

        $prompts = array_map(fn ($s) => $s['image_prompt'], $poster['sections']);
        $images = $this->images->generatePosterImages($prompts, $style);
        foreach ($poster['sections'] as $i => &$section) {
            $section['image'] = $images[$i] ?? null;
            unset($section['image_prompt']);
        }
        unset($section);

        return $poster;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>|null
     */
    protected function normalizePoster(array $raw): ?array
    {
        $text = fn ($v, int $max) => is_string($v) ? mb_substr(trim(strip_tags($v)), 0, $max) : '';

        $sections = [];
        foreach (is_array($raw['sections'] ?? null) ? $raw['sections'] : [] as $s) {
            if (! is_array($s) || $text($s['heading'] ?? '', 80) === '') {
                continue;
            }
            $points = array_values(array_filter(array_map(
                fn ($p) => $text($p, 90),
                is_array($s['points'] ?? null) ? array_slice($s['points'], 0, 2) : []
            )));
            $heading = $text($s['heading'], 80);
            $sections[] = [
                'label' => $text($s['label'] ?? '', 30),
                'heading' => $heading,
                'detail' => $text($s['detail'] ?? '', 220),
                'points' => $points,
                'image_prompt' => $text($s['image_prompt'] ?? '', 200) ?: $heading,
            ];
        }
        $sections = array_slice($sections, 0, 4);
        if (count($sections) < 2) {
            return null;
        }

        $takeaways = [];
        foreach (is_array($raw['takeaways'] ?? null) ? $raw['takeaways'] : [] as $t) {
            if (is_array($t) && $text($t['heading'] ?? '', 60) !== '') {
                $takeaways[] = ['heading' => $text($t['heading'], 60), 'detail' => $text($t['detail'] ?? '', 140)];
            }
        }

        return [
            'title' => $text($raw['title'] ?? '', 90) ?: 'Your Visual Lesson',
            'subtitle' => $text($raw['subtitle'] ?? '', 160),
            'sections' => $sections,
            'takeaways' => array_slice($takeaways, 0, 3),
        ];
    }
}
