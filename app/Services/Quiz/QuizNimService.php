<?php

namespace App\Services\Quiz;

use App\Exceptions\NvidiaNimException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
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

    /** @return array<int, string> ordered failover list */
    protected function models(): array
    {
        $models = array_values(array_filter((array) config('quiz.nim.models', []), 'is_string'));

        return $models !== [] ? $models : array_filter([$this->model()]);
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
                'Quiz generation is unavailable because the NVIDIA API key is missing from the server configuration.',
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

        return $this->chain($this->payload($messages, $options), fn (string $text) => $text)->wait();
    }

    /**
     * Like complete(), but parses the response as a JSON object.
     */
    public function completeJson(array $messages, array $options = []): ?array
    {
        $this->assertConfigured();

        return $this->jsonAsync($messages, $options)->wait();
    }

    /**
     * Non-blocking JSON completion. Walks the model list in order: a model that
     * is retired (410/404), overloaded (503), slow (per-model timeout), returns
     * empty text, or returns unparseable JSON hands the job to the next model.
     * Resolves to the decoded object; rejects with the most informative
     * NvidiaNimException.
     */
    public function jsonAsync(array $messages, array $options = []): PromiseInterface
    {
        return $this->chain($this->payload($messages, $options), function (string $text) {
            $json = $this->extractJsonObject($text);
            if ($json === null) {
                Log::warning('Quiz model returned unparseable JSON', ['chars' => strlen($text), 'head' => mb_substr($text, 0, 200), 'tail' => mb_substr($text, -120)]);
                throw new NvidiaNimException(
                    "NVIDIA NIM responded, but the quiz service couldn't understand the response format.",
                    'NIM_RESPONSE_ERROR'
                );
            }

            return $json;
        });
    }

    /**
     * Run several independent JSON jobs concurrently. Failed jobs come back as
     * NvidiaNimException so the caller can accept a partial result.
     *
     * @param  array<int, array{messages: array, options?: array}>  $jobs
     * @return array<int, array|NvidiaNimException>
     */
    public function completeJsonMany(array $jobs): array
    {
        $this->assertConfigured();

        $promises = [];
        foreach ($jobs as $i => $job) {
            $promises[$i] = $this->jsonAsync($job['messages'], $job['options'] ?? []);
        }

        $out = [];
        foreach (Utils::settle($promises)->wait() as $i => $r) {
            $reason = $r['reason'] ?? null;
            $out[$i] = $r['state'] === 'fulfilled'
                ? $r['value']
                : ($reason instanceof NvidiaNimException
                    ? $reason
                    : new NvidiaNimException('Quiz generation is unavailable right now.', 'UNKNOWN_ERROR', null, '', $reason instanceof \Throwable ? $reason : null));
        }

        return $out;
    }

    protected function payload(array $messages, array $options): array
    {
        unset($options['enable_thinking']);

        return array_merge([
            'messages' => $messages,
            'stream' => false,
            'max_tokens' => (int) config('quiz.max_tokens_json', 8192),
            'temperature' => (float) config('quiz.temperature', 0.3),
            'top_p' => (float) config('quiz.nim.top_p', 0.95),
        ], $options);
    }

    /**
     * Per-job failover across the model list, resolved through $parse. A job
     * only tries the next model when the previous one failed (racing models in
     * parallel made every call wait for the slowest loser and doubled load).
     */
    protected function chain(array $payload, callable $parse): PromiseInterface
    {
        $requestId = bin2hex(random_bytes(6));
        $errors = [];
        $promise = null;

        foreach ($this->models() as $model) {
            $attempt = fn () => $this->requestAsync($payload, $model, $requestId)->then($parse);

            $promise = $promise === null
                ? $attempt()
                : $promise->otherwise(function ($e) use (&$errors, $attempt) {
                    $errors[] = $e;

                    return $attempt();
                });
        }

        return $promise->otherwise(function ($e) use (&$errors, $requestId) {
            $errors[] = $e;
            $errors = array_values(array_filter($errors, fn ($x) => $x instanceof NvidiaNimException));
            // Prefer a real outage cause (auth/rate-limit/timeout) over "model retired".
            usort($errors, fn ($x, $y) => (int) ($x->category === 'NIM_MODEL_ERROR') <=> (int) ($y->category === 'NIM_MODEL_ERROR'));

            throw $errors[0] ?? new NvidiaNimException('Quiz generation is unavailable right now.', 'UNKNOWN_ERROR', null, $requestId);
        });
    }

    /**
     * Reasoning off/low: quiz JSON needs no chain-of-thought. Thinking roughly
     * doubled tokens (2837 vs 1548) and wall time for the same 10 questions.
     */
    protected function modelOptions(string $model): array
    {
        if (str_contains($model, 'gpt-oss')) {
            return ['reasoning_effort' => 'low'];
        }
        if (str_contains($model, 'nemotron')) {
            return ['chat_template_kwargs' => ['enable_thinking' => false]];
        }

        return [];
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
        // Reasoning models may leak <think>…</think> (with braces) before the answer.
        $text = trim(preg_replace('#<think>.*?</think>#s', '', $text) ?? $text);

        $best = null;
        $len = strlen($text);
        $pos = 0;

        // Scan every top-level {...} (string-aware); keep the one that looks like a
        // quiz (has "questions" or the insufficient_content sentinel), else the first.
        while (($start = strpos($text, '{', $pos)) !== false) {
            $depth = 0;
            $inString = false;
            $escaped = false;
            $end = null;

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
                } elseif ($c === '}' && --$depth === 0) {
                    $end = $i;
                    break;
                }
            }

            if ($end === null) {
                break;
            }

            $decoded = json_decode(substr($text, $start, $end - $start + 1), true);
            if (is_array($decoded)) {
                if (isset($decoded['questions']) || ! empty($decoded['insufficient_content'])) {
                    return $decoded;
                }
                $best ??= $decoded;
            }
            $pos = $end + 1;
        }

        return $best;
    }

    /**
     * One non-blocking completion. Resolves to the assistant text; rejects with
     * a classified NvidiaNimException (never a raw provider message).
     */
    protected function requestAsync(array $payload, string $model, string $requestId): PromiseInterface
    {
        $timeout = (int) ($payload['timeout'] ?? config('quiz.nim.timeout', 60));
        unset($payload['timeout']);   // client-side option, not part of the API body
        $payload = array_merge($payload, ['model' => $model], $this->modelOptions($model));
        Log::info('Quiz NIM request started', ['request_id' => $requestId, 'model' => $model]);

        return $this->client->requestAsync(
            'POST',
            rtrim($this->baseUrl(), '/').'/chat/completions',
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->apiKey(),
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => $timeout,
                'connect_timeout' => 10,
            ]
        )->then(
            function ($response) use ($model, $requestId) {
                $body = json_decode((string) $response->getBody(), true);
                $content = is_array($body) ? ($body['choices'][0]['message']['content'] ?? '') : '';
                Log::info('Quiz NIM response received', ['request_id' => $requestId, 'model' => $model, 'http_status' => $response->getStatusCode()]);

                if (! is_string($content) || trim($content) === '') {
                    throw new NvidiaNimException(
                        "NVIDIA NIM responded, but the quiz service couldn't understand the response format.",
                        'NIM_RESPONSE_ERROR', null, $requestId
                    );
                }

                return $content;
            },
            function ($e) use ($model, $requestId) {
                if ($e instanceof NvidiaNimException) {
                    throw $e;
                }
                $status = ($e instanceof RequestException && $e->hasResponse()) ? $e->getResponse()->getStatusCode() : 0;
                [$message, $category] = $this->classify($status, $e);
                Log::warning('Quiz NIM request failed', ['request_id' => $requestId, 'model' => $model, 'category' => $category, 'http_status' => $status]);

                throw new NvidiaNimException($message, $category, $status !== 0 ? $status : null, $requestId, $e);
            }
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
        if ($status === 404 || $status === 410) {
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
