<?php

namespace App\Services\Infographic\Providers;

use App\Services\Infographic\InfographicImageProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Real NVIDIA image-model provider (illustrator).
 *
 * CONFIG-GATED. Only used when config/infographic.php sets image_provider =
 * 'nvidia' AND NVIDIA_IMAGE_MODEL (+ NVIDIA_IMAGE_BASE_URL) are configured and
 * reachable. Shares the same NVIDIA_NIM_API_KEY as Astro (no second key).
 *
 * This provider is decoupled from the chat reasoning model: the prompt it
 * receives is a compact, slide-derived concept (visualConcept / visual.prompt),
 * NOT the full conversation transcript.
 *
 * Two endpoint families are supported automatically:
 *   1. Hosted Visual GenAI (ai.api.nvidia.com/v1/genai/{org}/{model}) — e.g.
 *      black-forest-labs/flux.1-dev. Uses {artifacts: [{base64}]} response.
 *   2. Generic / OpenAI-compatible fallback — tries {images:[{url|b64_json}]}.
 *
 * Fails SOFT: any error (unconfigured, 401/403/404/429/5xx, timeout, empty
 * response, malformed body) returns null so the orchestrator falls back to the
 * offline SVG provider and the deck still renders. Never throws.
 */
class NvidiaImageProvider implements InfographicImageProviderInterface
{
    public function generate(array $artSpec): ?string
    {
        $model = (string) config('infographic.image_model', '');
        $baseUrl = (string) config('infographic.image_base_url', '');
        $key = (string) config('nvidia_nim.api_key', '');

        if ($model === '' || $baseUrl === '' || $key === '') {
            // Not configured — let the caller fall back to local SVG art.
            return null;
        }

        $prompt = $this->prompt($artSpec);
        if ($prompt === '') {
            return null;
        }

        // Normalize base URL + model into the final endpoint. The config stores
        // either a full endpoint (hosted) or a base; we avoid double slashes.
        // For hosted Visual GenAI: baseUrl is https://ai.api.nvidia.com and
        // model is org/slug, so url becomes .../v1/genai/org/slug (no suffix).
        // For legacy: baseUrl + "/" + model + "/images/generations".
        $url = $this->endpointUrl($model, $baseUrl);

        try {
            $client = new Client(['timeout' => 120, 'connect_timeout' => 15]);
            $payload = ['prompt' => $prompt];

            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$key,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $body = json_decode((string) $response->getBody(), true);
            if (! is_array($body)) {
                return null;
            }

            // Hosted Visual GenAI (artifacts/base64) — e.g. flux.1-dev, qwen
            if (isset($body['artifacts']) && is_array($body['artifacts']) && $body['artifacts'] !== []) {
                $first = $body['artifacts'][0];
                if (is_array($first) && isset($first['base64']) && is_string($first['base64']) && $first['base64'] !== '') {
                    return $this->storeBase64($first['base64']);
                }
            }

            // OpenAI-compatible / legacy shapes
            $images = $body['images'] ?? $body['data'] ?? [];
            if (is_array($images) && $images !== []) {
                $first = $images[0];
                if (is_array($first) && isset($first['url']) && is_string($first['url']) && $first['url'] !== '') {
                    return $first['url'];
                }
                if (is_array($first) && isset($first['b64_json']) && is_string($first['b64_json']) && $first['b64_json'] !== '') {
                    return $this->storeBase64($first['b64_json']);
                }
                if (is_array($first) && isset($first['base64']) && is_string($first['base64']) && $first['base64'] !== '') {
                    return $this->storeBase64($first['base64']);
                }
                if (is_string($first) && $first !== '') {
                    return $first;
                }
            }

            return null;
        } catch (GuzzleException $e) {
            Log::warning('NVIDIA image model unavailable, using SVG fallback', [
                'model' => $model,
                'error' => $e->getMessage(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::warning('NVIDIA image generation failed, using SVG fallback', [
                'model' => $model,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function endpointUrl(string $model, string $baseUrl): string
    {
        $base = rtrim($baseUrl, '/');
        // If base already contains /v1/genai, append model directly.
        if (str_contains($base, '/v1/genai')) {
            return $base.'/'.ltrim($model, '/');
        }
        // Legacy fallback: base/model/images/generations (will 404 for hosted,
        // but provider then falls back to local_art).
        return $base.'/'.ltrim($model, '/').'/images/generations';
    }

    protected function prompt(array $artSpec): string
    {
        // If the content model already authored a visual prompt, honor it (the
        // reasoning model is the source of truth for what the slide needs).
        if (is_string($artSpec['prompt'] ?? null) && trim($artSpec['prompt']) !== '') {
            return 'TechLab educational illustration, deep navy space background, clean vector style, glowing stars, no text, no watermark: '.trim($artSpec['prompt']);
        }

        $title = is_string($artSpec['title'] ?? null) ? trim($artSpec['title']) : '';
        $concept = is_string($artSpec['visualConcept'] ?? null) ? trim($artSpec['visualConcept']) : '';
        $theme = is_string($artSpec['theme'] ?? null) ? trim($artSpec['theme']) : 'space';
        $type = is_string($artSpec['type'] ?? null) ? trim($artSpec['type']) : 'concept';
        $subtitle = is_string($artSpec['subtitle'] ?? null) ? trim($artSpec['subtitle']) : '';

        // Build a compact, Flux-friendly visual description from slide visual
        // information only — NOT the full conversation transcript.
        $parts = [];
        if ($title !== '') $parts[] = $title;
        if ($subtitle !== '' && $subtitle !== $title) $parts[] = $subtitle;
        if ($concept !== '') $parts[] = $concept;

        // Add a type hint so Flux renders the right educational framing.
        $hint = match ($type) {
            'process', 'timeline' => 'sequential flow diagram',
            'architecture', 'diagram' => 'system architecture diagram',
            'comparison' => 'side-by-side comparison diagram',
            'analogy' => 'playful space analogy illustration',
            default => 'educational concept illustration',
        };
        $core = implode(' — ', $parts);
        if ($core === '') return '';

        return 'TechLab educational '.$hint.', '.$theme.' space theme, '
            .'deep navy background with glowing stars and nebula, clean vector style, '
            .'no text, no watermark, no labels: '.$core;
    }

    protected function storeBase64(string $b64): ?string
    {
        $decoded = base64_decode($b64, true);
        if ($decoded === false) {
            return null;
        }
        // Detect jpeg vs png by magic bytes
        $ext = str_starts_with($b64, '/9j/') ? 'jpg' : 'png';
        $filename = 'infographics/nvim_'.crc32($b64).'_'.uniqid('', true).'.'.$ext;
        Storage::disk('public')->makeDirectory('infographics');
        Storage::disk('public')->put($filename, $decoded);

        return Storage::disk('public')->url($filename);
    }
}
