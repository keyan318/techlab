<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AstroRouter;
use App\Services\NvidiaNimService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AstroRouterTest extends TestCase
{
    use RefreshDatabase;

    private const SUPER = 'nvidia/nemotron-3-super-120b-a12b';

    public static function fastMessages(): array
    {
        return [
            'variable' => ['What is a variable?'],
            'python' => ['What is Python?'],
            'loops' => ['Explain loops simply.'],
            'planet word is not "plan"' => ['Tell me about the Networking planet'],
            'TechLab is not "lab"' => ['Is TechLab free?'],
            'prefix is not "fix"' => ['What does the prefix mean?'],
            'exactly 400 chars' => [str_repeat('a', 400)],
        ];
    }

    #[DataProvider('fastMessages')]
    public function test_simple_messages_route_fast(string $message): void
    {
        $route = app(AstroRouter::class)->route($message);

        $this->assertSame('fast', $route['mode'], $message);
        $this->assertNull($route['reason']);
        $this->assertFalse($route['thinking']);
        $this->assertSame(800, $route['max_tokens']);
        $this->assertArrayNotHasKey('thinking_token_budget', $route);
    }

    public static function deepMessages(): array
    {
        return [
            'long message' => [str_repeat('a', 401), 'long_message'],
            'code fence' => ["what does this do?\n```python\nx = 1\n```", 'code_block'],
            'python traceback' => ["Traceback (most recent call last):\n  File \"a.py\", line 3, in <module>\nNameError: name 'x' is not defined", 'traceback'],
            'php stack trace' => ['Got a stack trace on my page', 'traceback'],
            'exception type' => ['I get ValueError: invalid literal', 'traceback'],
            'debug' => ['Can you debug my loop?', 'debugging'],
            'error word' => ['I keep getting an error', 'debugging'],
            'not working' => ['my code is not working', 'debugging'],
            'why doesnt' => ["Why doesn't my function return anything?", 'debugging'],
            'fix' => ['Please fix this', 'debugging'],
            'broken' => ['my script is broken', 'debugging'],
            'design' => ['Help me design a small app', 'engineering'],
            'architecture' => ['What architecture should a chat app use?', 'engineering'],
            'project planning' => ['I need to plan my project', 'engineering'],
            'lab' => ['Stuck on the networking lab', 'engineering'],
            'step by step' => ['Walk me through DNS step by step', 'engineering'],
            'compare' => ['Compare TCP and UDP', 'engineering'],
            'think carefully' => ['Think carefully about what a pointer is', 'asks_for_depth'],
            'in depth' => ['Explain recursion in depth', 'asks_for_depth'],
        ];
    }

    #[DataProvider('deepMessages')]
    public function test_complex_messages_route_deep(string $message, string $reason): void
    {
        $route = app(AstroRouter::class)->route($message);

        $this->assertSame('deep', $route['mode'], $message);
        $this->assertSame($reason, $route['reason']);
        $this->assertTrue($route['thinking']);
        $this->assertSame(4096, $route['max_tokens']);
        $this->assertSame(2048, $route['thinking_token_budget']);
    }

    public function test_two_or_more_lesson_sources_route_deep_but_one_does_not(): void
    {
        $router = app(AstroRouter::class);

        $this->assertSame('fast', $router->route('What is a variable?', 1)['mode']);
        $this->assertSame('deep', $router->route('What is a variable?', 2)['mode']);
        $this->assertSame('multiple_sources', $router->route('What is a variable?', 3)['reason']);
    }

    /** Capture the exact JSON body sent to NVIDIA for a mode, with no network. */
    private function sentPayload(array $mode): array
    {
        config([
            'nvidia_nim.api_key' => 'test-key',
            'nvidia_nim.model' => self::SUPER,
            'nvidia_nim.medium_effort' => true,
        ]);

        $sse = 'data: '.json_encode(['choices' => [['delta' => ['content' => 'Hi']]]])."\n\ndata: [DONE]\n\n";
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], $sse)]));
        $stack->push(Middleware::history($history));

        (new NvidiaNimService(new Client(['handler' => $stack])))
            ->stream([['role' => 'user', 'content' => 'hi']], fn () => null, '', $mode);

        return json_decode((string) $history[0]['request']->getBody(), true);
    }

    public function test_fast_request_uses_super_with_thinking_off_and_800_tokens(): void
    {
        $payload = $this->sentPayload(app(AstroRouter::class)->route('What is a variable?'));

        $this->assertSame(self::SUPER, $payload['model']);
        $this->assertTrue($payload['stream']);
        $this->assertSame(800, $payload['max_tokens']);
        $this->assertSame(['enable_thinking' => false], $payload['chat_template_kwargs']);
        $this->assertArrayNotHasKey('reasoning_budget', $payload);
        $this->assertArrayNotHasKey('thinking_token_budget', $payload);
    }

    public function test_deep_request_uses_super_with_thinking_on_4096_tokens_and_2048_budget(): void
    {
        $payload = $this->sentPayload(app(AstroRouter::class)->route('Compare TCP and UDP'));

        $this->assertSame(self::SUPER, $payload['model']);
        $this->assertTrue($payload['stream']);
        $this->assertSame(4096, $payload['max_tokens']);
        $this->assertTrue($payload['chat_template_kwargs']['enable_thinking']);
        // Super's hosted endpoint rejects `thinking_token_budget` (HTTP 400), so the cap is
        // sent under the configured wire name, `reasoning_budget`.
        $this->assertSame(2048, $payload['reasoning_budget']);
        $this->assertArrayNotHasKey('thinking_token_budget', $payload);
    }

    public function test_budget_wire_name_is_configurable_for_models_that_use_thinking_token_budget(): void
    {
        config(['nvidia_nim.thinking_budget_param' => 'thinking_token_budget']);

        $payload = $this->sentPayload(app(AstroRouter::class)->route('Compare TCP and UDP'));

        $this->assertSame(2048, $payload['thinking_token_budget']);
        $this->assertArrayNotHasKey('reasoning_budget', $payload);
    }

    public function test_neither_mode_ever_targets_lightning(): void
    {
        foreach (['What is Python?', 'Compare TCP and UDP'] as $message) {
            $payload = $this->sentPayload(app(AstroRouter::class)->route($message));
            $this->assertStringNotContainsStringIgnoringCase('lightning', $payload['model']);
        }
        $this->assertSame(self::SUPER, config('nvidia_nim.model'));
    }

    public function test_chat_streams_via_sse_in_both_modes_and_passes_the_route_to_nim(): void
    {
        $seen = [];
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->twice()
            ->andReturnUsing(function ($history, $cb, $ctx, $mode) use (&$seen) {
                $seen[] = $mode;
                $cb('ok');

                return 'ok';
            });

        $user = User::factory()->create();
        foreach (['What is Python?' => 'fast', 'Compare TCP and UDP' => 'deep'] as $message => $expected) {
            $response = $this->actingAs($user)->withHeaders(['Accept' => 'application/json, text/event-stream'])
                ->post('/chat/message', ['message' => $message]);

            $response->assertOk();
            $this->assertStringContainsString('text/event-stream', $response->headers->get('Content-Type'));
            $this->assertSame($expected, $response->headers->get('X-Astro-Mode'));
            $body = $response->streamedContent();
            $this->assertStringContainsString("event: delta\n", $body);
            $this->assertStringContainsString("event: done\n", $body);
        }

        $this->assertSame(['fast', 'deep'], array_column($seen, 'mode'));
        $this->assertSame([800, 4096], array_column($seen, 'max_tokens'));
    }

    public function test_two_attached_lessons_make_the_chat_deep(): void
    {
        $mode = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb, $ctx, $m) use (&$mode) {
                $mode = $m;
                $cb('ok');

                return 'ok';
            });

        $this->actingAs(User::factory()->create())->withHeaders(['Accept' => 'application/json, text/event-stream'])
            ->post('/chat/message', [
                'message' => 'What is a variable?',
                'sources' => [
                    ['planet' => 'programming', 'module' => 'M1', 'lesson' => 'lesson-02'],
                    ['planet' => 'networking', 'module' => 'M1', 'lesson' => 'lesson-01'],
                ],
            ])->streamedContent();

        $this->assertSame('deep', $mode['mode']);
        $this->assertSame('multiple_sources', $mode['reason']);
    }

    public function test_plain_json_fallback_is_routed_too(): void
    {
        $mode = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb, $ctx, $m) use (&$mode) {
                $mode = $m;

                return 'ok';
            });

        $this->actingAs(User::factory()->create())->postJson('/chat/message', ['message' => 'Explain loops simply.'])
            ->assertOk();

        $this->assertSame('fast', $mode['mode']);
    }
}
