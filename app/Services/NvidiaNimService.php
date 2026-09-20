<?php

namespace App\Services;

use App\Exceptions\NvidiaNimException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

/**
 * Thin, dedicated client for the NVIDIA NIM Chat Completions API.
 *
 * NVIDIA NIM exposes an OpenAI-compatible endpoint and is Astro's SOLE AI
 * provider. Responsibilities:
 *  - selecting the configured model
 *  - authenticating server-side only (the key never reaches the browser)
 *  - sending the full message history
 *  - streaming the assistant response token-by-token to a callback
 *  - parsing NIM's Server-Sent-Events stream
 *  - stripping inline reasoning ("thinking") from the visible answer
 *  - classifying failures into actionable categories and logging them
 *
 * It deliberately knows nothing about Laravel HTTP responses, controllers or
 * the database — that keeps the streaming boundary clean and testable.
 */
class NvidiaNimService
{
    protected Client $client;

    /**
     * Cross-chunk state for stripping <think>...</think> reasoning that some
     * NIM models emit INLINE inside the "content" delta itself, rather than
     * on a separate "reasoning_content" channel. A <think> (or </think>) tag
     * can be split across two separate SSE chunks, so this buffer/flag has to
     * persist across calls to stripThinking() within a single streamOnce()
     * attempt — reset at the top of each streamOnce() call.
     */
    /**
     * NIM can answer HTTP 200 and then put a failure INSIDE the SSE stream, e.g.
     * data: {"error":{"message":"Service temporarily overloaded","code":503}}
     * followed by [DONE]. streamOnce() records it here so streamWithRetry() can tell
     * a real transient failure from a genuinely empty completion.
     *
     * @var array{code?: int, message?: string}|null
     */
    protected ?array $streamError = null;

    protected string $thinkBuffer = '';

    protected bool $inThinkBlock = false;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'timeout' => 300,
            'connect_timeout' => 15,
        ]);
    }

    /**
     * The configured model id (defaults to a working NVIDIA NIM free-tier model).
     */
    /** Which Astro is talking: 'student' (default tutor) or 'teacher' (teaching assistant). */
    protected string $persona = 'student';

    /** A copy of this service speaking as the given persona (never mutates the shared instance). */
    public function forPersona(string $persona): static
    {
        $clone = clone $this;
        $clone->persona = $persona;

        return $clone;
    }

    public function model(): string
    {
        return (string) config('nvidia_nim.model', '');
    }

    /**
     * Ensure the API key and model are configured before issuing a request.
     *
     * With the model now env-driven (no hardcoded default), a missing
     * NVIDIA_NIM_MODEL must fail loudly and safely rather than producing a
     * confusing upstream 400. The API key is never included in any message.
     *
     * @throws NvidiaNimException CONFIGURATION_ERROR with a safe message.
     */
    protected function assertConfigured(): void
    {
        if (empty(config('nvidia_nim.api_key'))) {
            throw new NvidiaNimException(
                "Astro couldn't respond because the NVIDIA NIM API key is missing from the server configuration.",
                'CONFIGURATION_ERROR'
            );
        }

        if (empty($this->model())) {
            throw new NvidiaNimException(
                "Astro couldn't respond because the NVIDIA NIM model isn't configured. Set NVIDIA_NIM_MODEL in your .env file.",
                'CONFIGURATION_ERROR'
            );
        }
    }

    /**
     * The TechLab tutor system prompt.
     *
     * Accepts optional context so Astro can adapt to the student:
     *  - 'level'  : auto|beginner|intermediate|advanced
     *  - 'context': a short free-form topic/course hint (e.g. "HTML5 · semantic tags")
     */
    public function systemPrompt(array $options = []): string
    {
        $base = $this->persona === 'teacher'
            ? config('nvidia_nim.teacher_system_prompt', config('nvidia_nim.system_prompt', ''))
            : config('nvidia_nim.system_prompt', '');

        $extras = [];

        $level = strtolower(trim($options['level'] ?? 'auto'));
        if (in_array($level, ['beginner', 'intermediate', 'advanced'], true)) {
            $extras[] = "Student level: explain at the **{$level}** level.";
        }

        $context = trim($options['context'] ?? '');
        if ($context !== '') {
            $extras[] = "Course context: {$context}. Relate your explanation to what the student is currently learning, but only bring in what is relevant.";
        }

        if ($extras === []) {
            return $base;
        }

        return $base."\n\n---\nContext for this conversation:\n".implode("\n", $extras);
    }

    /**
     * The system prompt used to extract a structured analogy from an explanation.
     */
    public function systemPromptForAnalogy(): string
    {
        return config('nvidia_nim.analogy_prompt', '');
    }

    /**
     * Extra request fields needed when reasoning ("thinking") mode is enabled.
     *
     * Returns an empty array when thinking is disabled. When enabled, the
     * model is REQUESTED to keep private reasoning separate from the final
     * "content" field via reasoning_content — but in practice some models
     * still emit <think>...</think> inline inside "content" instead. The
     * student-facing stream defends against this in streamOnce() /
     * stripThinking() regardless of which channel the model actually uses.
     *
     * @return array<string, mixed>
     */
    protected function reasoningFields(): array
    {
        $thinkingEnabled = (bool) config('nvidia_nim.thinking_enabled', false);

        $fields = [
            'chat_template_kwargs' => [
                'enable_thinking' => $thinkingEnabled,
            ],
        ];

        if ($thinkingEnabled) {
            $fields['chat_template_kwargs']['medium_effort'] = (bool) config('nvidia_nim.medium_effort', false);
            $fields['reasoning_budget'] = (int) config('nvidia_nim.reasoning_budget', 16384);
        }

        return $fields;
    }

    /**
     * Perform a single (non-streaming) chat completion and return the text.
     *
     * Used by background tasks such as analogy extraction — never by the live
     * chat stream, so it does not compete with streaming responses.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options  e.g. ['temperature' => 0.3, 'max_tokens' => 600]
     */
    public function complete(array $messages, array $options = []): string
    {
        $this->assertConfigured();

        // Thinking is opt-in for non-stream calls (e.g. analogy extraction keeps
        // it off for speed). Chat uses stream() which enables it by default.
        $enableThinking = ! empty($options['enable_thinking']);
        unset($options['enable_thinking']);

        // Client-side limits (not part of the API body): callers that must fail fast pass a short timeout / one retry.
        $retryTimeouts = isset($options['timeout']);   // a caller that set its own short timeout wants a retry on a slow attempt
        $timeout = (int) ($options['timeout'] ?? 300);
        $attempts = (int) ($options['attempts'] ?? 3);
        unset($options['timeout'], $options['attempts']);

        $payload = array_merge([
            'model' => $this->model(),
            'messages' => $messages,
            'stream' => false,
            'max_tokens' => (int) config('nvidia_nim.max_tokens', 4096),
            'temperature' => (float) config('nvidia_nim.temperature', 1),
            'top_p' => (float) config('nvidia_nim.top_p', 0.95),
        ], $options);

        if ($enableThinking) {
            $payload = array_merge($payload, $this->reasoningFields());
        }

        $response = $this->sendRequest($payload, false, $timeout, $attempts, $retryTimeouts);

        $body = json_decode((string) $response->getBody(), true);

        if (! is_array($body)) {
            throw new NvidiaNimException(
                "NVIDIA NIM responded, but Astro couldn't understand the response format.",
                'NIM_RESPONSE_ERROR'
            );
        }

        $content = $body['choices'][0]['message']['content'] ?? '';

        // Defensive: strip any inline <think> block even on non-stream calls,
        // in case a caller passes enable_thinking=true for this path later.
        $content = is_string($content) ? $content : '';
        if ($content !== '' && str_contains($content, '<think>')) {
            $content = preg_replace('/<think>.*?<\/think>/s', '', $content) ?? $content;
            $content = trim($content);
        }

        return $content;
    }

    /**
     * Like complete(), but parses the response as JSON.
     *
     * Strips a leading/trailing ```json markdown fence if present and extracts
     * the first balanced {...} block if the model adds commentary.
     *
     * @return array|null decoded JSON, or null if it could not be parsed
     */
    public function completeJson(array $messages, array $options = []): ?array
    {
        $raw = $this->complete($messages, $options);

        $clean = trim($raw);
        if (str_starts_with($clean, '```')) {
            $clean = preg_replace('/^```[a-zA-Z]*\s*/', '', $clean);
            $clean = preg_replace('/\s*```$/', '', $clean);
            $clean = trim($clean);
        }

        $decoded = json_decode($clean, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $clean, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Stream a chat completion from NVIDIA NIM.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  callable(string): void  $onDelta  invoked with each VISIBLE content chunk
     *                                           (reasoning/<think> blocks already stripped)
     * @return string the full VISIBLE assistant response (for persistence)
     *
     * @throws NvidiaNimException with a descriptive category on any failure.
     */
    public function stream(array $messages, callable $onDelta, string $sourceContext = '', array $mode = [], ?string $modelOverride = null): string
    {
        $this->assertConfigured();

        // Prepend the system prompt to ensure the model follows Astro's guidelines.
        // $sourceContext carries any connected Planet lessons (see LessonSourceService).
        $systemContent = $this->systemPrompt([]);
        if ($sourceContext !== '') {
            $systemContent .= "\n\n---\n".$sourceContext;
        }
        $systemMessage = ['role' => 'system', 'content' => $systemContent];
        $messagesWithSystem = array_merge([$systemMessage], $messages);

        $payload = [
            'model' => $modelOverride ?? $this->model(),
            'messages' => $messagesWithSystem,
            'stream' => true,
            'max_tokens' => (int) ($mode['max_tokens'] ?? config('nvidia_nim.max_tokens', 4096)),
            'temperature' => (float) config('nvidia_nim.temperature', 1),
            'top_p' => (float) config('nvidia_nim.top_p', 0.95),
        ];

        // A model override (the vision model) doesn't share the chat model's thinking
        // knobs, so it gets the plain payload.
        if ($modelOverride !== null) {
            return $this->streamWithRetry($payload, $onDelta);
        }

        // $mode is a Fast/Deep config from AstroRouter; without one, the .env defaults apply.
        if ($mode !== []) {
            return $this->streamWithRetry(array_merge($payload, $this->modeFields($mode)), $onDelta);
        }

        // Thinking is ON for chat by default. The model's private reasoning
        // trace is intentionally dropped by the stream loop below — whether
        // it arrives on a separate "reasoning_content" channel or inline
        // inside "content" wrapped in <think>...</think> — so only the final
        // visible answer is ever forwarded to the student.
        //
        // NOTE ON TRUNCATION: reasoning tokens (on either channel) are billed
        // against the SAME max_tokens budget as the visible answer. If
        // answers are getting cut off, the fix is here: raise
        // NVIDIA_NIM_MAX_TOKENS in .env (or lower NVIDIA_NIM_REASONING_BUDGET
        // if you're on a model that honors it as a separate cap), not just
        // stripping the tags below — stripping only fixes what's DISPLAYED,
        // not how much budget the answer gets to work with.
        $payload = array_merge($payload, $this->reasoningFields());

        return $this->streamWithRetry($payload, $onDelta);
    }

    /**
     * Request fields for an AstroRouter mode: thinking on/off and, when on, the
     * reasoning cap under the field name NVIDIA's endpoint accepts (see config).
     *
     * @param  array{thinking?: bool, max_tokens?: int, thinking_token_budget?: int}  $mode
     * @return array<string, mixed>
     */
    protected function modeFields(array $mode): array
    {
        $thinking = (bool) ($mode['thinking'] ?? false);
        $fields = ['chat_template_kwargs' => ['enable_thinking' => $thinking]];

        if ($thinking) {
            $fields['chat_template_kwargs']['medium_effort'] = (bool) config('nvidia_nim.medium_effort', false);

            if (isset($mode['thinking_token_budget'])) {
                $fields[(string) config('nvidia_nim.thinking_budget_param', 'reasoning_budget')] = (int) $mode['thinking_token_budget'];
            }
        }

        return $fields;
    }

    /**
     * Stream, retrying ONLY a genuine transient failure that hasn't shown the student anything.
     *
     * Retries when NIM reports an in-band 5xx (e.g. "Service temporarily overloaded") before any
     * visible text was sent, up to 3 attempts in total with a short backoff. It never retries:
     *  - a stream that already produced text (a retry would duplicate it — the error is raised
     *    instead and the partial answer is kept by the caller),
     *  - a non-transient error (auth, bad request, rate limit),
     *  - a stream that simply ended with no text and no error (nothing to indicate a retry helps).
     */
    protected function streamWithRetry(array $payload, callable $onDelta): string
    {
        $attempts = 3;
        $backoffMicros = [300_000, 600_000];

        for ($attempt = 1; ; $attempt++) {
            $emitted = false;
            $full = $this->streamOnce($payload, function (string $chunk) use ($onDelta, &$emitted) {
                $emitted = true;
                $onDelta($chunk);
            });

            $error = $this->streamError;

            if ($error === null) {
                if (trim($full) === '') {
                    throw new NvidiaNimException(
                        "Astro couldn't form a response just now. Please try again in a moment.",
                        'NIM_EMPTY_RESPONSE'
                    );
                }

                return $full;
            }

            $code = (int) ($error['code'] ?? 0);
            $transient = in_array($code, [500, 502, 503, 504], true);

            if ($transient && ! $emitted && $attempt < $attempts) {
                Log::warning('Astro NIM in-band error, retrying', [
                    'code' => $code,
                    'message' => (string) ($error['message'] ?? ''),
                    'attempt' => $attempt,
                ]);
                usleep($backoffMicros[$attempt - 1]);

                continue;
            }

            Log::warning('Astro NIM in-band error, giving up', [
                'code' => $code,
                'message' => (string) ($error['message'] ?? ''),
                'attempt' => $attempt,
                'text_already_sent' => $emitted,
            ]);

            [$message, $category] = $this->classify($code, new \RuntimeException((string) ($error['message'] ?? 'NIM stream error')));

            throw new NvidiaNimException($message, $category, $code !== 0 ? $code : null);
        }
    }

    /**
     * Open a single streaming request and forward every VISIBLE content delta.
     *
     * Reasoning / "thinking" tokens are intentionally never forwarded to the
     * student. This covers both cases: a model that puts reasoning on a
     * separate "reasoning_content" field (never read here), AND a model that
     * emits it inline inside "content" wrapped in <think>...</think> (caught
     * and stripped by stripThinking() below, stateful across chunks).
     *
     * @return string the full VISIBLE assistant content for this attempt
     */
    protected function streamOnce(array $payload, callable $onDelta): string
    {
        $full = '';
        $buffer = '';

        // Reset think-block state for this attempt. Important: stream()
        // may call streamOnce() twice (initial + retry) on the same service
        // instance, and stale state from a failed first attempt must not
        // leak into the retry.
        $this->thinkBuffer = '';
        $this->inThinkBlock = false;
        $this->streamError = null;

        $response = $this->sendRequest($payload, true);

        $body = $response->getBody();

        while (! $body->eof()) {
            $chunk = $body->read(4096);
            if ($chunk === '') {
                continue;
            }

            $buffer .= $chunk;

            // Process every complete SSE line we currently have buffered.
            while (($newline = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $newline));
                $buffer = substr($buffer, $newline + 1);

                if ($line === '') {
                    continue;
                }

                if (! str_starts_with($line, 'data:')) {
                    continue;
                }

                $data = trim(substr($line, 5));

                if ($data === '[DONE]') {
                    break 2;
                }

                $decoded = json_decode($data, true);

                if (! is_array($decoded)) {
                    continue;
                }

                // In-band failure (HTTP 200 + an error event): remember it, keep draining to [DONE].
                if (isset($decoded['error']) && ! isset($decoded['choices'])) {
                    $this->streamError = is_array($decoded['error']) ? $decoded['error'] : ['message' => (string) $decoded['error']];

                    continue;
                }

                // Only surface VISIBLE assistant content. Some NIM models put
                // reasoning inline inside this same "content" field wrapped
                // in <think>...</think> rather than on a separate channel —
                // stripThinking() filters that out before anything reaches
                // the student or gets persisted to the database.
                $delta = $decoded['choices'][0]['delta']['content'] ?? '';

                if (is_string($delta) && $delta !== '') {
                    $visible = $this->stripThinking($delta);
                    if ($visible !== '') {
                        $full .= $visible;
                        $onDelta($visible);
                    }
                }
            }
        }

        return $full;
    }

    /**
     * Strip <think>...</think> reasoning blocks from a streamed delta,
     * tracking open/close state across chunks via $this->thinkBuffer and
     * $this->inThinkBlock since a single <think> or </think> tag can be
     * split across two separate SSE chunks.
     *
     * Content inside an open-but-not-yet-closed think block is discarded
     * immediately rather than held indefinitely, so a stream that never
     * sends a closing </think> tag (e.g. it gets cut off) doesn't silently
     * swallow the rest of the real answer along with it — only the reasoning
     * text itself is lost, which is the desired behavior anyway.
     */
    protected function stripThinking(string $delta): string
    {
        $this->thinkBuffer .= $delta;
        $out = '';

        while (true) {
            if (! $this->inThinkBlock) {
                $pos = strpos($this->thinkBuffer, '<think>');
                if ($pos === false) {
                    // No opening tag (complete or partial) pending. But guard
                    // against a '<think>' tag split across chunks by holding
                    // back a short tail that could be the start of one.
                    $tailKeep = min(strlen($this->thinkBuffer), 6);
                    $safeLen = strlen($this->thinkBuffer) - $tailKeep;
                    if ($safeLen > 0 && str_contains(substr($this->thinkBuffer, -$tailKeep), '<')) {
                        $out .= substr($this->thinkBuffer, 0, $safeLen);
                        $this->thinkBuffer = substr($this->thinkBuffer, $safeLen);
                    } else {
                        $out .= $this->thinkBuffer;
                        $this->thinkBuffer = '';
                    }
                    break;
                }
                $out .= substr($this->thinkBuffer, 0, $pos);
                $this->thinkBuffer = substr($this->thinkBuffer, $pos + 7); // strlen('<think>')
                $this->inThinkBlock = true;
            } else {
                $pos = strpos($this->thinkBuffer, '</think>');
                if ($pos === false) {
                    // Still inside the think block — discard what we have so
                    // far, wait for more chunks to find the closing tag.
                    $this->thinkBuffer = '';
                    break;
                }
                $this->thinkBuffer = substr($this->thinkBuffer, $pos + 8); // strlen('</think>')
                $this->inThinkBlock = false;
            }
        }

        return $out;
    }

    /**
     * Perform the HTTP request to NVIDIA NIM with retry for transient failures.
     *
     * Network-level errors (including transient blips) and 5xx are retried a
     * couple of times with a short backoff; fatal errors (missing key, 400,
     * 401/403, 404, 429) surface immediately as a classified NvidiaNimException.
     *
     * @param  array  $payload  the full request body
     * @param  bool  $stream  whether to request a streaming response
     *
     * @throws NvidiaNimException after classifying the failure (always).
     */
    protected function sendRequest(array $payload, bool $stream, int $timeout = 300, int $attempts = 3, bool $retryTimeouts = false): ResponseInterface
    {
        $requestId = bin2hex(random_bytes(6));
        $attempts = max(1, $attempts);
        $last = null;

        Log::info('Astro NIM request started', [
            'request_id' => $requestId,
            'model' => $payload['model'] ?? $this->model(),
            'stream' => $stream,
        ]);

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $response = $this->client->request(
                    'POST',
                    rtrim(config('nvidia_nim.base_url'), '/').'/chat/completions',
                    [
                        'headers' => [
                            'Authorization' => 'Bearer '.config('nvidia_nim.api_key'),
                            'Content-Type' => 'application/json',
                        ],
                        'json' => $payload,
                        'stream' => $stream,
                        'timeout' => $timeout,
                        'connect_timeout' => 15,
                    ]
                );

                Log::info('Astro NIM response received', [
                    'request_id' => $requestId,
                    'http_status' => $response->getStatusCode(),
                ]);

                return $response;
            } catch (RequestException $e) {
                $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
                $transient = ($status !== 0) && in_array($status, [500, 502, 503, 504], true);

                if ($transient && $attempt < $attempts) {
                    Log::warning('Astro NIM transient HTTP error, retrying', [
                        'request_id' => $requestId,
                        'status' => $status,
                        'attempt' => $attempt,
                    ]);
                    usleep(400_000 * $attempt);

                    continue;
                }

                [$message, $category] = $this->classify($status, $e);

                Log::warning('Astro NIM request failed', [
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
                if ($retryTimeouts && $attempt < $attempts) {
                    Log::warning('Astro NIM slow/failed attempt, retrying', ['request_id' => $requestId, 'attempt' => $attempt, 'timeout_s' => $timeout]);

                    continue;
                }

                [$message, $category] = $this->classify(0, $e);

                Log::warning('Astro NIM request failed', [
                    'request_id' => $requestId,
                    'category' => $category,
                    'error' => $message,
                ]);

                throw new NvidiaNimException($message, $category, null, $requestId, $e);
            }
        }

        // Unreachable, but keeps static analysis happy.
        throw new NvidiaNimException(
            "Astro couldn't respond because of an unexpected error.",
            'UNKNOWN_ERROR',
            null,
            $requestId
        );
    }

    /**
     * Map an HTTP status (or a connection/timeout failure with no response) to a
     * safe, user-facing message and an error category. Never includes the API
     * key or raw provider text.
     *
     * @return array{0: string, 1: string} [safe message, category]
     */
    protected function classify(int $status, \Throwable $e): array
    {
        if ($status === 401 || $status === 403) {
            return [
                "Astro couldn't connect because NVIDIA NIM rejected the server credentials.",
                'AUTHENTICATION_ERROR',
            ];
        }

        if ($status === 404) {
            return [
                "Astro couldn't respond because the configured NVIDIA model is unavailable.",
                'NIM_MODEL_ERROR',
            ];
        }

        if ($status === 429) {
            return [
                'Astro is currently busy because NVIDIA NIM is rate-limiting requests. Please try again in a moment.',
                'NIM_RATE_LIMIT',
            ];
        }

        if ($status === 400) {
            return [
                "Astro couldn't process the request because the AI request sent by the server was invalid.",
                'NIM_REQUEST_ERROR',
            ];
        }

        if ($status >= 500) {
            return [
                'NVIDIA NIM encountered a server error. Please try again shortly.',
                'SERVER_ERROR',
            ];
        }

        // No HTTP response: connection refused, DNS failure, or timeout.
        // curl errno 28 == operation timed out; anything else is unreachable.
        $errno = method_exists($e, 'getHandlerContext')
            ? ($e->getHandlerContext()['errno'] ?? null)
            : null;

        if ($errno === 28) {
            return [
                "Astro couldn't respond because the NVIDIA NIM request timed out.",
                'NIM_TIMEOUT',
            ];
        }

        return [
            "Astro couldn't connect to NVIDIA NIM because the AI service is unreachable.",
            'NIM_CONNECTION_ERROR',
        ];
    }
}
