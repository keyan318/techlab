<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Database work per chat message: as few round trips as possible, none of them
 * between NIM's first token and the browser, and every message stored exactly once.
 */
class ChatDatabaseWorkTest extends TestCase
{
    use RefreshDatabase;

    private const ACCEPT = 'application/json, text/event-stream';

    /** Mock NIM so it records how many queries had run when the first chunk was emitted. */
    private function fakeNim(?int &$atFirstChunk): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb) use (&$atFirstChunk) {
                $atFirstChunk = count(DB::getQueryLog());
                $cb('Hi');

                return 'Hi';
            });
    }

    private function send(User $user, array $payload): string
    {
        return $this->actingAs($user)->withHeaders(['Accept' => self::ACCEPT])
            ->post('/chat/message', $payload)->streamedContent();
    }

    public function test_new_conversation_costs_two_queries_and_none_before_the_first_chunk(): void
    {
        $user = User::factory()->create();
        $atFirst = null;
        $this->fakeNim($atFirst);

        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->send($user, ['message' => 'hello']);

        $this->assertSame(0, $atFirst, 'no query may run before the first chunk of a new chat');
        $this->assertCount(2, DB::getQueryLog(), 'conversation insert + one multi-row message insert');
    }

    public function test_existing_conversation_costs_three_queries_and_one_before_the_first_chunk(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create(['title' => 'x']);
        $conversation->messages()->create(['role' => 'user', 'content' => 'q']);
        $conversation->messages()->create(['role' => 'assistant', 'content' => 'a']);
        $atFirst = null;
        $this->fakeNim($atFirst);

        DB::enableQueryLog();
        DB::flushQueryLog();
        $this->send($user, ['message' => 'follow up', 'conversation_id' => $conversation->id]);

        $this->assertSame(1, $atFirst, 'ownership + history must be a single read');
        $this->assertCount(3, DB::getQueryLog(), 'history read + one message insert + one timestamp update');
    }

    public function test_each_message_is_stored_exactly_once_with_user_before_assistant(): void
    {
        $user = User::factory()->create();
        $atFirst = null;
        $this->fakeNim($atFirst);

        $events = $this->send($user, ['message' => 'hello']);

        $this->assertSame(1, Message::where('role', 'user')->count());
        $this->assertSame(1, Message::where('role', 'assistant')->count());
        $this->assertSame(['user', 'assistant'], Message::orderBy('id')->pluck('role')->all());

        // `done` reports the USER message's id, as before.
        preg_match('/event: done\ndata: (.+)\n/', $events, $m);
        $done = json_decode($m[1], true);
        $this->assertSame(Message::where('role', 'user')->value('id'), $done['message_id']);
        $this->assertSame(Conversation::value('id'), $done['conversation_id']);
    }

    public function test_history_order_survives_repeated_turns(): void
    {
        $user = User::factory()->create();
        $sent = [];
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->twice()
            ->andReturnUsing(function ($history, $cb) use (&$sent) {
                $sent[] = array_column($history, 'content');
                $cb('a');

                return 'reply';
            });

        $first = $this->send($user, ['message' => 'one']);
        preg_match('/event: done\ndata: (.+)\n/', $first, $m);
        $this->send($user, ['message' => 'two', 'conversation_id' => json_decode($m[1], true)['conversation_id']]);

        $this->assertSame([['one'], ['one', 'reply', 'two']], $sent);
    }

    public function test_existing_conversation_is_bumped_and_a_new_one_is_not_double_touched(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create(['title' => 'x']);
        $conversation->timestamps = false;
        $conversation->update(['updated_at' => now()->subDay()]);
        $conversation->messages()->create(['role' => 'user', 'content' => 'q']);
        $atFirst = null;
        $this->fakeNim($atFirst);

        $this->send($user, ['message' => 'again', 'conversation_id' => $conversation->id]);

        $this->assertTrue($conversation->fresh()->updated_at->isToday());
    }

    public function test_an_owned_conversation_with_no_messages_still_works_and_a_foreign_one_404s(): void
    {
        $user = User::factory()->create();
        $empty = $user->conversations()->create(['title' => 'empty']);
        $foreignEmpty = User::factory()->create()->conversations()->create(['title' => 'theirs']);
        $foreignBusy = User::factory()->create()->conversations()->create(['title' => 'theirs too']);
        $foreignBusy->messages()->create(['role' => 'user', 'content' => 'secret']);
        $atFirst = null;
        $this->fakeNim($atFirst);

        $this->send($user, ['message' => 'hi', 'conversation_id' => $empty->id]);
        $this->assertSame(2, $empty->messages()->count());

        foreach ([$foreignEmpty, $foreignBusy] as $foreign) {
            $this->actingAs($user)->withHeaders(['Accept' => self::ACCEPT])
                ->post('/chat/message', ['message' => 'hi', 'conversation_id' => $foreign->id])
                ->assertNotFound();
        }
        $this->assertSame(1, $foreignBusy->messages()->count(), 'a foreign conversation must never be written to');
    }
}
