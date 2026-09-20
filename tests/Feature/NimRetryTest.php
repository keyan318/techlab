<?php

namespace Tests\Feature;

use App\Exceptions\NvidiaNimException;
use App\Models\Message;
use App\Models\User;
use App\Services\NvidiaNimService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Retries happen only for genuine transient failures that have not shown the student
 * any text yet. Everything else is exactly one NIM call.
 */
class NimRetryTest extends TestCase
{
    use RefreshDatabase;

    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();
        config(['nvidia_nim.api_key' => 'test-key', 'nvidia_nim.model' => 'nvidia/nemotron-3-super-120b-a12b']);
    }

    private function sse(string ...$events): Response
    {
        return new Response(200, [], implode('', array_map(fn ($e) => "data: {$e}\n\n", $events))."data: [DONE]\n\n");
    }

    private function text(string $t): string
    {
        return json_encode(['choices' => [['delta' => ['content' => $t]]]]);
    }

    private function overloaded(int $code = 503): string
    {
        return json_encode(['error' => ['message' => 'Service temporarily overloaded', 'type' => 'service_unavailable', 'code' => $code]]);
    }

    /** Build a real service whose transport replays $responses and counts every HTTP call. */
    private function service(array $responses): NvidiaNimService
    {
        $this->calls = [];
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->calls));

        return new NvidiaNimService(new Client(['handler' => $stack]));
    }

    private function streamIt(NvidiaNimService $svc, array $mode = []): array
    {
        $chunks = [];
        $full = $svc->stream([['role' => 'user', 'content' => 'hi']], function ($c) use (&$chunks) {
            $chunks[] = $c;
        }, '', $mode);

        return [$full, $chunks];
    }

    public function test_a_successful_response_makes_exactly_one_nim_call(): void
    {
        [$full, $chunks] = $this->streamIt($this->service([$this->sse($this->text('Hel'), $this->text('lo'))]));

        $this->assertSame('Hello', $full);
        $this->assertSame(['Hel', 'lo'], $chunks);
        $this->assertCount(1, $this->calls);
    }

    public function test_a_very_short_valid_answer_is_not_retried(): void
    {
        [$full] = $this->streamIt($this->service([$this->sse($this->text('4'))]));

        $this->assertSame('4', $full);
        $this->assertCount(1, $this->calls);
    }

    public function test_fast_and_deep_success_each_make_one_call(): void
    {
        foreach ([['thinking' => false, 'max_tokens' => 800], ['thinking' => true, 'max_tokens' => 4096, 'thinking_token_budget' => 2048]] as $mode) {
            [$full] = $this->streamIt($this->service([$this->sse($this->text('ok'))]), $mode);
            $this->assertSame('ok', $full);
            $this->assertCount(1, $this->calls);
        }
    }

    public function test_in_band_overload_then_success_retries_once_and_delivers_the_text_once(): void
    {
        [$full, $chunks] = $this->streamIt($this->service([
            $this->sse($this->overloaded()),
            $this->sse($this->text('Recovered')),
        ]));

        $this->assertSame('Recovered', $full);
        $this->assertSame(['Recovered'], $chunks, 'the student sees the answer exactly once');
        $this->assertCount(2, $this->calls);
    }

    public function test_two_overloads_then_success_uses_the_third_attempt(): void
    {
        [$full] = $this->streamIt($this->service([
            $this->sse($this->overloaded()), $this->sse($this->overloaded()), $this->sse($this->text('Third time')),
        ]));

        $this->assertSame('Third time', $full);
        $this->assertCount(3, $this->calls);
    }

    public function test_persistent_overload_stops_after_three_calls_with_a_clear_error(): void
    {
        try {
            $this->streamIt($this->service([$this->sse($this->overloaded()), $this->sse($this->overloaded()), $this->sse($this->overloaded()), $this->sse($this->text('never used'))]));
            $this->fail('expected an exception');
        } catch (NvidiaNimException $e) {
            $this->assertSame('SERVER_ERROR', $e->category);
        }

        $this->assertCount(3, $this->calls, 'never more than three attempts');
    }

    public function test_non_transient_in_band_errors_are_not_retried(): void
    {
        foreach ([[400, 'NIM_REQUEST_ERROR'], [429, 'NIM_RATE_LIMIT'], [401, 'AUTHENTICATION_ERROR']] as [$code, $category]) {
            try {
                $this->streamIt($this->service([$this->sse($this->overloaded($code)), $this->sse($this->text('unused'))]));
                $this->fail("expected {$category}");
            } catch (NvidiaNimException $e) {
                $this->assertSame($category, $e->category);
            }
            $this->assertCount(1, $this->calls, "HTTP {$code} must not be retried");
        }
    }

    public function test_an_error_after_text_was_already_sent_is_never_retried(): void
    {
        $chunks = [];
        $svc = $this->service([$this->sse($this->text('Half '), $this->overloaded()), $this->sse($this->text('duplicate!'))]);

        try {
            $svc->stream([['role' => 'user', 'content' => 'hi']], function ($c) use (&$chunks) {
                $chunks[] = $c;
            });
            $this->fail('expected an exception');
        } catch (NvidiaNimException) {
        }

        $this->assertSame(['Half '], $chunks, 'no duplicated text');
        $this->assertCount(1, $this->calls);
    }

    public function test_an_empty_stream_with_no_error_is_not_blindly_retried(): void
    {
        try {
            $this->streamIt($this->service([$this->sse(), $this->sse($this->text('unused'))]));
            $this->fail('expected an exception');
        } catch (NvidiaNimException $e) {
            $this->assertSame('NIM_EMPTY_RESPONSE', $e->category);
        }

        $this->assertCount(1, $this->calls);
    }

    public function test_a_real_http_503_is_still_retried_by_the_request_layer(): void
    {
        $fail = new RequestException('busy', new Request('POST', 'x'), new Response(503));

        [$full] = $this->streamIt($this->service([$fail, $this->sse($this->text('after http 503'))]));

        $this->assertSame('after http 503', $full);
        $this->assertCount(2, $this->calls);
    }

    // ---- through the controller: SSE + persistence ----

    private function chat(User $user, string $message = 'What is a variable?'): array
    {
        $body = $this->actingAs($user)->withHeaders(['Accept' => 'application/json, text/event-stream'])
            ->post('/chat/message', ['message' => $message])->streamedContent();

        preg_match_all('/^event: (\w+)$/m', $body, $m);

        return [$m[1], $body];
    }

    public function test_chat_recovers_from_an_overload_with_one_stream_and_no_duplicate_messages(): void
    {
        $this->app->instance(NvidiaNimService::class, $this->service([
            $this->sse($this->overloaded()),
            $this->sse($this->text('It is '), $this->text('a box.')),
        ]));

        [$events, $body] = $this->chat(User::factory()->create());

        $this->assertSame(['start', 'delta', 'delta', 'end', 'done'], $events);
        $this->assertStringNotContainsString('event: error', $body);
        $this->assertCount(2, $this->calls);
        $this->assertSame(['user', 'assistant'], Message::orderBy('id')->pluck('role')->all());
        $this->assertSame('It is a box.', Message::where('role', 'assistant')->value('content'));
    }

    public function test_chat_reports_failure_after_retries_and_saves_only_the_user_message(): void
    {
        $this->app->instance(NvidiaNimService::class, $this->service([
            $this->sse($this->overloaded()), $this->sse($this->overloaded()), $this->sse($this->overloaded()),
        ]));

        [$events, $body] = $this->chat(User::factory()->create());

        $this->assertSame(['start', 'error', 'done'], $events);
        $this->assertStringContainsString('NVIDIA NIM encountered a server error', $body);
        $this->assertCount(3, $this->calls);
        $this->assertSame(['user'], Message::orderBy('id')->pluck('role')->all());
    }

    public function test_chat_with_a_healthy_nim_makes_one_call_and_one_of_each_message(): void
    {
        $this->app->instance(NvidiaNimService::class, $this->service([$this->sse($this->text('Fine.'))]));

        [$events] = $this->chat(User::factory()->create());

        $this->assertSame(['start', 'delta', 'end', 'done'], $events);
        $this->assertCount(1, $this->calls);
        $this->assertSame(1, Message::where('role', 'user')->count());
        $this->assertSame(1, Message::where('role', 'assistant')->count());
    }
}
