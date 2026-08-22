<?php

namespace App\Services\Infographic;

use App\Services\Infographic\Providers\LocalInfographicArtProvider;
use App\Services\Infographic\Providers\NvidiaImageProvider;

/**
 * Decides which image provider renders a slide's artwork and applies the
 * mandatory fallback: if the configured (NVIDIA) provider returns null, the
 * offline SVG provider is used so the deck always has visuals.
 */
class InfographicImageService
{
    /**
     * @param  array<string, mixed>  $slide
     */
    public function generateForSlide(array $slide): ?string
    {
        $spec = $this->artSpec($slide);

        $providerName = config('infographic.image_provider', 'local_art');
        $primary = $this->provider($providerName);

        $url = $primary->generate($spec);

        // Mandatory fallback: a configured-but-failing image model must not
        // break the infographic. Drop to offline SVG art.
        if ($url === null && $providerName === 'nvidia') {
            $url = $this->localArt()->generate($spec);
        }

        return $url;
    }

    /**
     * @param  array<string, mixed>  $slide
     * @return array<string, mixed>
     */
    protected function artSpec(array $slide): array
    {
        return [
            'title' => $slide['title'] ?? '',
            'subtitle' => $slide['subtitle'] ?? '',
            'theme' => $slide['theme'] ?? 'space_mission_control',
            'type' => $slide['type'] ?? 'concept',
            'visualConcept' => $slide['visualConcept'] ?? '',
            'prompt' => $slide['visual']['prompt'] ?? '',
            'keyPoints' => $slide['keyPoints'] ?? [],
            'elements' => $slide['elements'] ?? [],
        ];
    }

    protected function provider(string $name): InfographicImageProviderInterface
    {
        return match ($name) {
            'nvidia' => new NvidiaImageProvider(),
            default => new LocalInfographicArtProvider(),
        };
    }

    protected function localArt(): LocalInfographicArtProvider
    {
        return new LocalInfographicArtProvider();
    }
}
