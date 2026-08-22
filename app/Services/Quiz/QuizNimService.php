<?php

namespace App\Services\Quiz;

use App\Exceptions\NvidiaNimException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Dedicated NVIDIA NIM client for Quiz generation.
 *
 * DELIBERATELY separate from:
 *   - App\Services\NvidiaNimService           (live Astro chat, streaming)
 *   - App\Services\Infographic\InfographicNimService (infographic deck, non-streaming)
 *
 * The quiz gets its OWN model / base URL / (optionally) key via
 * config/quiz.php → nim.* so the three AI paths are independent — a
 * different/tuned quiz model can be swapped without touching chat or
 * infographic, and they never share connection state or request shaping.
 *
 * Non-streaming only. Knows nothing about Laravel HTTP, controllers, or the DB.
 * Reuses NvidiaNimException so callers can share the existing error taxonomy.
 * Wired entirely off config/quiz.php → never config/nvidia_nim.* or config/infographic.*
 */
class QuizNimService
{
    protected Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'timeout' => 300,
            'connect_timeout' => 15,
        ]);
    }

    public function model(): string
    {
        return (string) config('quiz.nim.model', '');
    }

    protected function apiKey(): string
    {
        return (string) config('quiz.nim.api_key', '');
    }

    protected function baseUrl(): string
    {
        return (string) config('quiz.nim.base_url', 'https://integrate.api.nvidia.com/v1');
    }

    protected function assertConfigured(): void
    {
        if (empty($this->apiKey())) {
            throw new NvidiaNimException(
                "Quiz generation is unavailable because the NVIDIA API key is missing from the server configuration.",
                'CONFIGURATION_ERROR'
            );
        }

        if (empty($this->model())) {
            throw new NvidiaNimException(
                "Quiz generation is unavailable because the quiz model isn't configured. Set QUIZ_NIM_MODEL in your .env file.",
                'CONFIGURATION_ERROR'
            );
        }
    }

    /**
     * Single non-streaming chat completion; returns the assistant text.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function complete(array $messages, array $options = []): string
    {
        $this->assertConfigured();

        unset($options['enable_thinking']);
        $payload = array_merge([
            'model' => $this->model(),
            'messages' => $messages,
            'stream' => false,
            'max_tokens' => (int) config('quiz.max_tokens_json', 4096),
            'temperature' => (float) config('quiz.temperature', 0.3),
            'top_p' => (float) config('quiz.nim.top_p', 0.95),
        ], $options);

        $response = $this->sendRequest($payload);
        $body = json_decode((string) $response->getBody(), true);

        if (! is_array($body)) {
            throw new NvidiaNimException(
                "NVIDIA NIM responded, but the quiz service couldn't understand the response format.",
                'NIM_RESPONSE_ERROR'
            );
        }

        $content = $body['choices'][0]['message']['content'] ?? '';

        return is_string($content) ? $content : '';
    }

    /**
     * Like complete(), but parses the response as a JSON object.
     * Respects string literals so braces inside strings are ignored.
     */
    public function completeJson(array $messages, array $options = []): ?array
    {
        $raw = $this->complete($messages, $options);

        return $this->extractJsonObject($raw);
    }

    /**
     * Minimal connectivity check: tiny completion against the quiz model.
     * Never throws — reports not-reachable so the caller decides surfacing.
     */
    public function ping(): bool
    {
        try {
            $this->assertConfigured();
            $response = $this->client->request(
                'POST',
                rtrim($this->baseUrl(), '/').'/chat/completions',
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$this->apiKey(),
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => $this->model(),
                        'messages' => [['role' => 'user', 'content' => 'ping']],
                        'stream' => false,
                        'max_tokens' => 5,
                        'temperature' => 0,
                    ],
                    'timeout' => 30,
                    'connect_timeout' => 15,
                ]
            );

            return $response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            Log::warning('Quiz NIM ping failed', [
                'model' => $this->model(),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function extractJsonObject(string $text): ?array
    {
        $text = trim($text);
        if (str_starts_with($text, '```')) {
            $text = preg_replace('/^```[a-zA-Z]*\s*/', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);
            $text = trim($text);
        }

        $start = strpos($text, '{');
        if ($start === false) {
            return null;
        }

        $depth = 0;
        $inString = false;
        $escaped = false;
        $len = strlen($text);

        for ($i = $start; $i < $len; $i++) {
            $c = $text[$i];
            if ($inString) {
                if ($escaped) {
                    $escaped = false;
                } elseif ($c === '\\') {
                    $escaped = true;
                } elseif ($c === '"') {
                    $inString = false;
                }
                continue;
            }
            if ($c === '"') {
                $inString = true;
            } elseif ($c === '{') {
                $depth++;
            } elseif ($c === '}') {
                $depth--;
                if ($depth === 0) {
                    $candidate = substr($text, $start, $i - $start + 1);
                    $decoded = json_decode($candidate, true);

                    return is_array($decoded) ? $decoded : null;
                }
            }
        }

        return null;
    }

    protected function sendRequest(array $payload): \Psr\Http\Message\ResponseInterface
    {
        $requestId = bin2hex(random_bytes(6));
        $attempts = 3;

        Log::info('Quiz NIM request started', [
            'request_id' => $requestId,
            'model' => $this->model(),
        ]);

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $response = $this->client->request(
                    'POST',
                    rtrim($this->baseUrl(), '/').'/chat/completions',
                    [
                        'headers' => [
                            'Authorization' => 'Bearer '.$this->apiKey(),
                            'Content-Type' => 'application/json',
                        ],
                        'json' => $payload,
                        'timeout' => 300,
                        'connect_timeout' => 15,
                    ]
                );

                Log::info('Quiz NIM response received', [
                    'request_id' => $requestId,
                    'http_status' => $response->getStatusCode(),
                ]);

                return $response;
            } catch (RequestException $e) {
                $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
                $transient = ($status !== 0) && in_array($status, [500, 502, 503, 504], true);
                if ($transient && $attempt < $attempts) {
                    Log::warning('Quiz NIM transient HTTP error, retrying', [
                        'request_id' => $requestId,
                        'status' => $status,
                        'attempt' => $attempt,
                    ]);
                    usleep(400_000 * $attempt);
                    continue;
                }

                [$message, $category] = $this->classify($status, $e);
                Log::warning('Quiz NIM request failed', [
                    'request_id' => $requestId,
                    'category' => $category,
                    'http_status' => $status,
                    'error' => $message,
                ]);
                throw new NvidiaNimException($message, $category, $status !== 0 ? $status : null, $requestId, $e);
            } catch (GuzzleException $e) {
                [$message, $category] = $this->classify(0, $e);
                Log::warning('Quiz NIM request failed', [
                    'request_id' => $requestId,
                    'category' => $category,
                    'error' => $message,
                ]);
                throw new NvidiaNimException($message, $category, null, $requestId, $e);
            }
        }

        throw new NvidiaNimException(
            "Quiz generation couldn't respond because of an unexpected error.",
            'UNKNOWN_ERROR',
            null,
            $requestId
        );
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function classify(int $status, \Throwable $e): array
    {
        if ($status === 401 || $status === 403) {
            return ["Quiz generation couldn't connect because NVIDIA NIM rejected the server credentials.", 'AUTHENTICATION_ERROR'];
        }
        if ($status === 404) {
            return ["Quiz generation couldn't run because the configured NVIDIA model is unavailable.", 'NIM_MODEL_ERROR'];
        }
        if ($status === 429) {
            return ['Quiz generation is currently busy because NVIDIA NIM is rate-limiting requests. Please try again in a moment.', 'NIM_RATE_LIMIT'];
        }
        if ($status === 400) {
            return ["Quiz generation couldn't process the request because the AI request sent by the server was invalid.", 'NIM_REQUEST_ERROR'];
        }
        if ($status >= 500) {
            return ['NVIDIA NIM encountered a server error. Please try again shortly.', 'SERVER_ERROR'];
        }
        $errno = method_exists($e, 'getHandlerContext') ? ($e->getHandlerContext()['errno'] ?? null) : null;
        if ($errno === 28) {
            return ["Quiz generation couldn't respond because the NVIDIA NIM request timed out.", 'NIM_TIMEOUT'];
        }

        return ["Quiz generation couldn't connect to NVIDIA NIM because the AI service is unreachable.", 'NIM_CONNECTION_ERROR'];
    }
}
