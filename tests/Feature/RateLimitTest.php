<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rate limits (AppServiceProvider): Astro's AI routes allow 12 calls a minute per account, and
 * every web page has a 300-a-minute flood guard. Being blocked answers 429 with a message the
 * chat screen can show, and one person's limit never blocks another.
 */
class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_routes_stop_after_twelve_calls_a_minute_with_a_readable_error(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        for ($i = 0; $i < 12; $i++) {
            $this->assertNotSame(429, $this->actingAs($student)->postJson('/chat/flashcards', [])->status());
        }

        $this->actingAs($student)->postJson('/chat/flashcards', [])
            ->assertStatus(429)
            ->assertJson(['ok' => false, 'kind' => 'rate_limit'])
            ->assertJsonPath('error', 'Astro needs a short breather. Wait a minute, then ask again.');

        // Chat shares the same AI budget.
        $this->actingAs($student)->postJson('/chat/message', ['message' => 'hi'])->assertStatus(429);

        // Someone else is not affected.
        $other = User::factory()->create(['role' => 'student']);
        $this->assertNotSame(429, $this->actingAs($other)->postJson('/chat/flashcards', [])->status());
    }

    public function test_every_page_has_a_flood_guard(): void
    {
        for ($i = 0; $i < 300; $i++) {
            $this->get('/login');
        }

        $this->get('/login')->assertStatus(429);
        $this->getJson('/login')->assertStatus(429)->assertJson(['kind' => 'rate_limit']);
    }
}
