<?php

namespace App\Services\ImageGeneration;

/**
 * Image provider contract for the "Draw Analogy" feature.
 *
 * A provider turns a structured analogy description (extracted from Astro's
 * reply) into a single visual asset and returns a URL the chat UI can render.
 *
 * Implement this interface to plug in any backend — a local SVG comic renderer
 * (see LocalComicProvider), a hosted image model (DALL·E / Stable Diffusion /
 * an NVIDIA NIM image model), or a third-party illustration API. Swap providers
 * via config/images.php without touching controllers or the chat UI.
 */
interface ImageProviderInterface
{
    /**
     * Generate an illustration for the given analogy and return its public URL.
     *
     * @param  array  $data  Structured analogy, e.g.
     *                       [
     *                         'title'    => string,
     *                         'elements' => [{name, role}, ...],
     *                         'flow'     => [string, ...],
     *                         'caption'  => string,
     *                       ]
     * @return string  A URL (absolute path or full) the browser can load in an <img>.
     */
    public function generate(array $data): string;
}
