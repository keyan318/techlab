<?php

namespace App\Services\Infographic;

use App\Exceptions\NvidiaNimException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * Dedicated NVIDIA NIM client for Infographic content generation.
 *
 * This is DELIBERATELY a separate class from App\Services\NvidiaNimService
 * (used by live Astro chat). The infographic feature gets its OWN model, base
 * URL and (optionally) key, configured under config/infographic.php -> 'nim'.
 * That keeps the two AI paths independent: a heavier/tuned infographic model
 * can be swapped in without touching the chat client, and the two never share
 * connection state or request shaping.
 *
 * It only ever does NON-STREAMING chat completions (the infographic pipeline
 * needs a single structured JSON plan, not a token stream). It knows nothing
 * about Laravel HTTP, controllers, or the database.
 *
 * Reuses the shared NvidiaNimException type so the existing
 * InfographicContentService catch block stays valid, but it is wired entirely
 * off the infographic.nim.* configuration — never config/nvidia_nim.*.
 */
class InfographicNimService
{
    protected Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'timeout' => 300,
            'connect_timeout' => 15,
        ]);
    }

    /**
     * The configured dedicated infographic model id.
     */
    public function model(): string
    {
        return (string) config('infographic.nim.model', '');
    }

    protected function apiKey(): string
    {
        return (string) config('infographic.nim.api_key', '');
    }

    protected function baseUrl(): string
    {
        return (string) config('infographic.nim.base_url', 'https://integrate.api.nvidia.com/v1');
    }

    /**
     * Fail loudly and safely if the dedicated provider is not configured.
     *
     * @throws NvidiaNimException  CONFIGURATION_ERROR with a safe message.
     */
    protected function assertConfigured(): void
    {
        if (empty($this->apiKey())) {
            throw new NvidiaNimException(
                "Infographic generation is unavailable because the NVIDIA API key is missing from the server configuration.",
                'CONFIGURATION_ERROR'
            );
        }

        if (empty($this->model())) {
            throw new NvidiaNimException(
                "Infographic generation is unavailable because the infographic model isn't configured. Set INFOGRAPHIC_NIM_MODEL in your .env file.",
                'CONFIGURATION_ERROR'
            );
        }
    }

    /**
     * Perform a single (non-streaming) chat completion and return the text.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options  e.g. ['temperature' => 0.4, 'max_tokens' => 4096]
     */
    public function complete(array $messages, array $options = []): string
    {
        $this->assertConfigured();

        // Strip non-payload flags like enable_thinking that callers may pass.
        $cleanOptions = $options;
        unset($cleanOptions['enable_thinking']);
        $payload = array_merge([
            'model' => $this->model(),
            'messages' => $messages,
            'stream' => false,
            'max_tokens' => (int) config('infographic.max_tokens_json', 4096),
            'temperature' => (float) config('infographic.temperature', 0.4),
            'top_p' => (float) config('infographic.nim.top_p', 0.95),
        ], $cleanOptions);

        $response = $this->sendRequest($payload);

        $body = json_decode((string) $response->getBody(), true);

        if (! is_array($body)) {
            throw new NvidiaNimException(
                "NVIDIA NIM responded, but the infographic service couldn't understand the response format.",
                'NIM_RESPONSE_ERROR'
            );
        }

        $content = $body['choices'][0]['message']['content'] ?? '';

        return is_string($content) ? $content : '';
    }

    /**
     * Like complete(), but parses the response as JSON.
     *
     * Strips a leading/trailing ```json markdown fence and extracts the FIRST
     * balanced JSON object while correctly ignoring braces that appear INSIDE
     * string values (e.g. code snippets). The previous shared extractor used a
     * recursive regex that broke on such content and produced SCHEMA_ERRORs.
     *
     * @return array|null  decoded JSON, or null if it could not be parsed
     */
    public function completeJson(array $messages, array $options = []): ?array
    {
        $raw = $this->complete($messages, $options);

        return $this->extractJsonObject($raw);
    }

    /**
     * Minimal real connectivity check used by the backend.
     *
     * Issues a tiny completion against the dedicated model and returns true if
     * NVIDIA NIM answers 200. Never throws — a missing key, bad model, network
     * blip, or rate limit all simply report "not reachable" so the caller can
     * decide how to surface it.
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
            Log::warning('Infographic NIM ping failed', [
                'model' => $this->model(),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Extract the first balanced JSON object from arbitrary model text,
     * respecting string literals so braces inside strings are ignored.
     *
     * @return array|null
     */
    protected function extractJsonObject(string $text): ?array
    {
        $text = trim($text);

        // Strip a ```json (or bare ```) markdown fence if present.
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

    /**
     * Perform the HTTP request to NVIDIA NIM with retry for transient failures.
     *
     * Network-level errors and 5xx are retried a couple of times; fatal errors
     * (missing key, 400/401/403/404/429) surface immediately as a classified
     * NvidiaNimException. Only ever non-streaming here.
     *
     * @param  array  $payload  the full request body
     *
     * @throws NvidiaNimException  after classifying the failure.
     */
    protected function sendRequest(array $payload): \Psr\Http\Message\ResponseInterface
    {
        $requestId = bin2hex(random_bytes(6));
        $attempts = 3;
        $last = null;

        Log::info('Infographic NIM request started', [
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

                Log::info('Infographic NIM response received', [
                    'request_id' => $requestId,
                    'http_status' => $response->getStatusCode(),
                ]);

                return $response;
            } catch (RequestException $e) {
                $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
                $transient = ($status !== 0) && in_array($status, [500, 502, 503, 504], true);

                if ($transient && $attempt < $attempts) {
                    Log::warning('Infographic NIM transient HTTP error, retrying', [
                        'request_id' => $requestId,
                        'status' => $status,
                        'attempt' => $attempt,
                    ]);
                    usleep(400_000 * $attempt);

                    continue;
                }

                [$message, $category] = $this->classify($status, $e);

                Log::warning('Infographic NIM request failed', [
                    'request_id' => $requestId,
                    'category' => $category,
                    'http_status' => $status,
                    'error' => $message,
                ]);

                throw new NvidiaNimException(
                    $message,
                    $category,
                    $status !== 0 ? $status : null,
                    $requestId,
                    $e
                );
            } catch (GuzzleException $e) {
                [$message, $category] = $this->classify(0, $e);

                Log::warning('Infographic NIM request failed', [
                    'request_id' => $requestId,
                    'category' => $category,
                    'error' => $message,
                ]);

                throw new NvidiaNimException($message, $category, null, $requestId, $e);
            }
        }

        throw new NvidiaNimException(
            "Infographic generation couldn't respond because of an unexpected error.",
            'UNKNOWN_ERROR',
            null,
            $requestId
        );
    }

    /**
     * Map an HTTP status (or connection failure) to a safe message + category.
     * Never includes the API key or raw provider text.
     *
     * @return array{0: string, 1: string}
     */
    protected function classify(int $status, \Throwable $e): array
    {
        if ($status === 401 || $status === 403) {
            return [
                "Infographic generation couldn't connect because NVIDIA NIM rejected the server credentials.",
                'AUTHENTICATION_ERROR',
            ];
        }

        if ($status === 404) {
            return [
                "Infographic generation couldn't run because the configured NVIDIA model is unavailable.",
                'NIM_MODEL_ERROR',
            ];
        }

        if ($status === 429) {
            return [
                'Infographic generation is currently busy because NVIDIA NIM is rate-limiting requests. Please try again in a moment.',
                'NIM_RATE_LIMIT',
            ];
        }

        if ($status === 400) {
            return [
                "Infographic generation couldn't process the request because the AI request sent by the server was invalid.",
                'NIM_REQUEST_ERROR',
            ];
        }

        if ($status >= 500) {
            return [
                'NVIDIA NIM encountered a server error. Please try again shortly.',
                'SERVER_ERROR',
            ];
        }

        $errno = method_exists($e, 'getHandlerContext')
            ? ($e->getHandlerContext()['errno'] ?? null)
            : null;

        if ($errno === 28) {
            return [
                "Infographic generation couldn't respond because the NVIDIA NIM request timed out.",
                'NIM_TIMEOUT',
            ];
        }

        return [
            "Infographic generation couldn't connect to NVIDIA NIM because the AI service is unreachable.",
            'NIM_CONNECTION_ERROR',
        ];
    }
}
