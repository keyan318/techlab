<?php

namespace App\Services\Infographic;

/**
 * Image provider contract for the Infographic feature.
 *
 * A provider turns a structured "art spec" (derived from a slide) into ONE
 * visual asset and returns a public URL the viewer can render, or null if it
 * cannot produce one (so the system falls back to frontend SVG/CSS).
 *
 * Implementations:
 *  - LocalInfographicArtProvider : offline, branded space SVG (default)
 *  - NvidiaImageProvider         : real NVIDIA image model (config-gated)
 *
 * Swap providers via config/infographic.php without touching the orchestrator
 * or the frontend.
 */
interface InfographicImageProviderInterface
{
    /**
     * @param  array<string, mixed>  $artSpec  e.g.
     *   [
     *     'title' => string, 'subtitle' => string, 'theme' => string,
     *     'type' => string, 'visualConcept' => string, 'prompt' => string,
     *     'keyPoints' => string[], 'elements' => [{name,detail}]
     *   ]
     * @return string|null  public URL, or null if generation is unavailable
     */
    public function generate(array $artSpec): ?string;
}
