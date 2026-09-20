<?php

namespace Tests\Feature;

use App\Exceptions\NvidiaNimException;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * /chat/message as Server-Sent Events: chunks are forwarded as NIM produces them and
 * nothing is written to the database until the stream has finished.
 */
class AstroStreamingTest extends TestCase
{
    use RefreshDatabase;

    private const ACCEPT = 'application/json, text/event-stream';

    /** @return array<int, array{0:string,1:array}> */
    private function events(string $body): array
    {
        $out = [];
        foreach (array_filter(explode("\n\n", $body)) as $block) {
            $event = 'message';
            $data = '';
            foreach (explode("\n", $block) as $line) {
                if (str_starts_with($line, 'event:')) {
                    $event = trim(substr($line, 6));
                } elseif (str_starts_with($line, 'data:')) {
                    $data .= ltrim(substr($line, 5), ' ');
                }
            }
            $out[] = [$event, json_decode($data, true) ?? []];
        }

        return $out;
    }

    private function stream(User $user, array $payload = []): TestResponse
    {
        return $this->actingAs($user)
            ->withHeaders(['Accept' => self::ACCEPT])
            ->post('/chat/message', $payload + ['message' => 'Explain variables.']);
    }

    public function test_responds_with_event_stream_and_ordered_events(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb) {
                foreach (['Vari', 'ables ', 'are boxes.'] as $c) {
                    $cb($c);
                }

                return 'Variables are boxes.';
            });

        $response = $this->stream(User::factory()->create());

        $response->assertOk();
        $this->assertStringContainsString('text/event-stream', $response->headers->get('Content-Type'));
        $this->assertSame('no', $response->headers->get('X-Accel-Buffering'));

        $events = $this->events($response->streamedContent());
        $this->assertSame(['start', 'delta', 'delta', 'delta', 'end', 'done'], array_column($events, 0));
        $this->assertSame(['Vari', 'ables ', 'are boxes.'], array_map(fn ($e) => $e[1]['t'], array_slice($events, 1, 3)));
    }

    public function test_no_database_write_happens_before_or_between_chunks(): void
    {
        $seen = [];
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb) use (&$seen) {
                $cb('first');
                $seen[] = [Conversation::count(), Message::count()];
                $cb('second');
                $seen[] = [Conversation::count(), Message::count()];

                return 'firstsecond';
            });

        $this->stream(User::factory()->create())->streamedContent();

        $this->assertSame([[0, 0], [0, 0]], $seen, 'persistence must wait until the stream finishes');
        $this->assertSame(2, Message::count());
    }

    public function test_full_answer_is_persisted_after_stream_and_ids_are_returned(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb) {
                $cb('Hello ');
                $cb('there');

                return 'Hello there';
            });

        $events = $this->events($this->stream(User::factory()->create())->streamedContent());
        $done = end($events);

        $this->assertSame('done', $done[0]);
        $this->assertTrue($done[1]['saved']);
        $conversation = Conversation::findOrFail($done[1]['conversation_id']);
        $this->assertSame(['user', 'assistant'], $conversation->messages()->orderBy('id')->pluck('role')->all());
        $this->assertSame('Hello there', $conversation->messages()->where('role', 'assistant')->value('content'));
    }

    public function test_existing_conversation_history_is_sent_once_with_the_new_message(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create(['title' => 'x']);
        $conversation->messages()->create(['role' => 'user', 'content' => 'earlier q']);
        $conversation->messages()->create(['role' => 'assistant', 'content' => 'earlier a']);

        $sent = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb) use (&$sent) {
                $sent = $history;
                $cb('ok');

                return 'ok';
            });

        $this->stream($user, ['conversation_id' => $conversation->id, 'message' => 'follow up'])->streamedContent();

        $this->assertSame(['earlier q', 'earlier a', 'follow up'], array_column($sent, 'content'));
        $this->assertSame(4, $conversation->messages()->count());
    }

    public function test_nim_failure_emits_error_and_saves_only_the_user_message(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andThrow(new NvidiaNimException('NIM is down', 'NIM_UNAVAILABLE'));

        $events = $this->events($this->stream(User::factory()->create())->streamedContent());

        $names = array_column($events, 0);
        $this->assertContains('error', $names);
        $this->assertNotContains('end', $names);
        $this->assertSame('NIM is down', $events[array_search('error', $names)][1]['error']);
        $this->assertSame(['user'], Message::orderBy('id')->pluck('role')->all());
    }

    public function test_mid_stream_failure_keeps_the_partial_answer_without_duplicating(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb) {
                $cb('Half an ans');
                throw new NvidiaNimException('connection reset', 'NIM_NETWORK_ERROR');
            });

        $events = $this->events($this->stream(User::factory()->create())->streamedContent());

        $this->assertSame(['start', 'delta', 'error', 'done'], array_column($events, 0));
        $this->assertSame(1, Message::where('role', 'assistant')->count());
    }

    public function test_validation_and_ownership_errors_are_json_not_streams(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create()->conversations()->create(['title' => 'not yours']);

        $this->actingAs($user)->withHeaders(['Accept' => self::ACCEPT])
            ->post('/chat/message', [])->assertStatus(422);

        $this->actingAs($user)->withHeaders(['Accept' => self::ACCEPT])
            ->post('/chat/message', ['message' => 'hi', 'conversation_id' => $other->id])->assertNotFound();
    }

    public function test_plain_json_clients_still_get_the_buffered_response(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb) {
                $cb('Hi');

                return 'Hi';
            });

        $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', ['message' => 'hello'])
            ->assertOk()
            ->assertJsonPath('response', 'Hi')
            ->assertJsonPath('success', true);
    }
}
