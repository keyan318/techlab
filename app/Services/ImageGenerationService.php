<?php

namespace App\Services;

use App\Exceptions\NvidiaNimException;
use App\Services\ImageGeneration\ImageProviderInterface;
use App\Services\ImageGeneration\LocalComicProvider;
use Illuminate\Support\Facades\Log;

/**
 * Draw Analogy pipeline — kept entirely separate from the live chat stream.
 *
 * Given Astro's last assistant message, it:
 *   1. asks the model (non-streaming) to extract a structured analogy,
 *   2. hands that structure to the configured image provider,
 *   3. returns the illustration URL plus the structured data for display.
 *
 * The live chat path never calls this, so normal messages never wait on (or pay
 * for) image generation. Providers are swappable via config/images.php.
 */
class ImageGenerationService
{
    public function __construct(
        protected NvidiaNimService $nim
    ) {}

    /**
     * Extract the analogy from Astro's text and generate its illustration.
     *
     * @return array{image_url: string, title: string, caption: string, elements: array, flow: array}
     *
     * @throws \RuntimeException  when extraction or rendering fails.
     */
    public function drawFromMessage(string $assistantText): array
    {
        $data = $this->extractAnalogy($assistantText);

        $provider = $this->provider();
        $imageUrl = $provider->generate($data);

        return [
            'image_url' => $imageUrl,
            'title' => $data['title'] ?? '',
            'caption' => $data['caption'] ?? '',
            'elements' => $data['elements'] ?? [],
            'flow' => $data['flow'] ?? [],
        ];
    }

    /**
     * Ask the model to turn Astro's explanation into a structured analogy.
     *
     * @return array{title: string, elements: array, flow: array, caption: string}
     */
    protected function extractAnalogy(string $assistantText): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->nim->systemPromptForAnalogy()],
            [
                'role' => 'user',
                'content' => "Here is Astro's explanation:\n\n".mb_substr($assistantText, 0, 6000)."\n\n"
                    .'Extract the analogy as JSON.',
            ],
        ];

        try {
            $parsed = $this->nim->completeJson($messages, [
                'temperature' => 0.3,
                'max_tokens' => 700,
            ]);
        } catch (NvidiaNimException $e) {
            // Extraction failing shouldn't crash the feature — degrade gracefully.
            Log::warning('Analogy extraction failed', ['message' => $e->getMessage()]);
            $parsed = null;
        }

        if (! is_array($parsed)) {
            return [
                'title' => 'The key idea',
                'elements' => [['name' => 'Concept', 'role' => 'What Astro explained']],
                'flow' => [],
                'caption' => 'A visual summary of Astro\'s explanation.',
            ];
        }

        return [
            'title' => is_string($parsed['title'] ?? null) ? $parsed['title'] : 'The key idea',
            'elements' => is_array($parsed['elements'] ?? null) ? $parsed['elements'] : [],
            'flow' => is_array($parsed['flow'] ?? null) ? $parsed['flow'] : [],
            'caption' => is_string($parsed['caption'] ?? null) ? $parsed['caption'] : '',
        ];
    }

    /**
     * Resolve the configured image provider.
     */
    protected function provider(): ImageProviderInterface
    {
        $name = config('images.provider', 'local_comic');

        return match ($name) {
            'local_comic' => new LocalComicProvider(),
            default => new LocalComicProvider(),
        };
    }
}
