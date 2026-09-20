<?php

namespace App\Services\Schedule;

use App\Exceptions\NvidiaNimException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\AggregateException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;

/**
 * Asks the vision model to copy a timetable picture into text — hedged.
 *
 * Attempt 1 goes out immediately. Attempt 2 (a different model when one is configured) is queued with a
 * `delay` of hedge_after seconds, so it only actually fires if attempt 1 is still silent by then. The first
 * valid answer wins and the loser is cancelled. A slow attempt never blocks the retry the way a serial retry did.
 */
class VisionCaller
{
    public function __construct(protected ?Client $client = null)
    {
        $this->client ??= new Client(['connect_timeout' => 8]);
    }

    /**
     * @param  string  $prompt  what to do with the image
     * @param  string  $imageUrl  data: URL of the (already downscaled) image
     *
     * @throws NvidiaNimException when neither attempt produced text
     */
    public function transcribe(string $prompt, string $imageUrl): string
    {
        $models = (array) config('schedule.vision_models', []);
        $models = $models !== [] ? array_values($models) : [(string) config('nvidia_nim.vision_model')];
        $hedgeMs = (int) round(max(0.0, (float) config('schedule.hedge_after', 5)) * 1000);
        $timeout = (int) config('schedule.vision_timeout', 12);
        $t0 = microtime(true);

        $attempts = [
            $this->request($models[0], $prompt, $imageUrl, $timeout, 0),
            $this->request($models[1] ?? $models[0], $prompt, $imageUrl, $timeout, $hedgeMs),
        ];

        try {
            $text = Utils::any($attempts)->wait();
        } catch (\Throwable $e) {
            // Utils::any() rejects with an AggregateException holding every attempt's reason; prefer one that carried an HTTP status.
            $reasons = $e instanceof AggregateException ? (array) $e->getReason() : [$e];
            $reason = collect($reasons)->first(fn ($r) => $r instanceof RequestException && $r->hasResponse()) ?? ($reasons[0] ?? $e);
            $status = $reason instanceof RequestException && $reason->hasResponse() ? $reason->getResponse()->getStatusCode() : null;
            Log::warning('Schedule vision failed', ['status' => $status, 'after_s' => round(microtime(true) - $t0, 1)]);

            throw new NvidiaNimException(
                "Astro couldn't read the picture in time.",
                $status === null ? 'NIM_TIMEOUT' : 'NIM_MODEL_ERROR',
                $status,
                '',
                $reason instanceof \Throwable ? $reason : null
            );
        } finally {
            foreach ($attempts as $p) {
                $p->cancel();   // no-op for a promise that already settled
            }
        }

        Log::info('Schedule vision answered', ['vision_s' => round(microtime(true) - $t0, 1), 'chars' => strlen($text)]);

        return $text;
    }

    private function request(string $model, string $prompt, string $imageUrl, int $timeout, int $delayMs)
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '.config('nvidia_nim.api_key'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $model,
                'stream' => false,
                'temperature' => 0,
                'max_tokens' => (int) config('schedule.vision_max_tokens', 700),
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]],
                    ],
                ]],
            ],
            'timeout' => $timeout,
        ];
        if ($delayMs > 0) {
            $options['delay'] = $delayMs;
        }

        return $this->client
            ->postAsync(rtrim((string) config('nvidia_nim.base_url'), '/').'/chat/completions', $options)
            ->then(function ($response) {
                $body = json_decode((string) $response->getBody(), true);
                $text = trim((string) ($body['choices'][0]['message']['content'] ?? ''));
                if ($text === '') {
                    throw new \RuntimeException('empty vision answer');   // lets the other attempt win
                }

                return $text;
            });
    }
}
