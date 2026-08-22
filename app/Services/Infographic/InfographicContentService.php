<?php

namespace App\Services\Infographic;

use App\Exceptions\InfographicGenerationException;
use App\Exceptions\NvidiaNimException;
use App\Services\Infographic\InfographicNimService;

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
     * @throws InfographicGenerationException  on any model/schema failure
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
}
