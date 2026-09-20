<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTitleTest extends TestCase
{
    use RefreshDatabase;

    private function conversationFor(User $user)
    {
        $c = $user->conversations()->create(['title' => 'hey can you explain']);
        $c->messages()->create(['role' => 'user', 'content' => 'hey can you explain how SQL joins work']);
        $c->messages()->create(['role' => 'assistant', 'content' => 'A join combines rows from two tables...']);

        return $c;
    }

    public function test_astro_names_the_conversation_from_its_content_and_cleans_the_output(): void
    {
        $user = User::factory()->create();
        $c = $this->conversationFor($user);
        $this->mock(NvidiaNimService::class)->shouldReceive('complete')->once()->andReturn("\"Understanding SQL Joins.\"\nextra");

        $this->actingAs($user)->postJson("/chat/conversations/{$c->id}/title")
            ->assertOk()->assertJsonPath('conversation.title', 'Understanding SQL Joins');

        $this->assertSame('Understanding SQL Joins', $c->fresh()->title);
    }

    public function test_naming_does_not_reorder_recents(): void
    {
        $user = User::factory()->create();
        $c = $this->conversationFor($user);
        $before = $c->fresh()->updated_at->toISOString();
        $this->mock(NvidiaNimService::class)->shouldReceive('complete')->once()->andReturn('SQL Joins');

        $this->travel(1)->hours();
        $this->actingAs($user)->postJson("/chat/conversations/{$c->id}/title")->assertOk();

        $this->assertSame($before, $c->fresh()->updated_at->toISOString());
    }

    public function test_a_model_failure_keeps_the_existing_title(): void
    {
        $user = User::factory()->create();
        $c = $this->conversationFor($user);
        $this->mock(NvidiaNimService::class)->shouldReceive('complete')->once()->andThrow(new \RuntimeException('down'));

        $this->actingAs($user)->postJson("/chat/conversations/{$c->id}/title")
            ->assertOk()->assertJsonPath('conversation.title', 'hey can you explain');
    }

    public function test_someone_elses_conversation_404s(): void
    {
        $c = $this->conversationFor(User::factory()->create());

        $this->actingAs(User::factory()->create())->postJson("/chat/conversations/{$c->id}/title")->assertNotFound();
    }

    public function test_chat_page_renders_recents_in_the_sidebar(): void
    {
        $user = User::factory()->create();
        $user->conversations()->create(['title' => 'Understanding SQL Joins']);

        $this->actingAs($user)->get('/chat')->assertOk()
            ->assertSee('Understanding SQL Joins', false)
            ->assertSee('Recents', false);
    }

    public function test_owner_can_pin_unpin_and_delete_but_others_cannot(): void
    {
        $user = User::factory()->create();
        $c = $this->conversationFor($user);
        $other = User::factory()->create();

        $this->actingAs($other)->postJson("/chat/conversations/{$c->id}/pin")->assertNotFound();
        $this->actingAs($other)->deleteJson("/chat/conversations/{$c->id}")->assertNotFound();

        $this->actingAs($user)->postJson("/chat/conversations/{$c->id}/pin")->assertOk()->assertJsonPath('conversation.pinned', true);
        $this->actingAs($user)->postJson("/chat/conversations/{$c->id}/pin")->assertOk()->assertJsonPath('conversation.pinned', false);

        $this->actingAs($user)->deleteJson("/chat/conversations/{$c->id}")->assertOk();
        $this->assertDatabaseMissing('conversations', ['id' => $c->id]);
        $this->assertDatabaseMissing('messages', ['conversation_id' => $c->id]);
    }
}
