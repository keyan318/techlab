<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards the wiring of Astro's responding indicator (the animated TechLab logo +
 * status lines). The status logic itself is covered in Node:
 *   node tests/js/astro-status.test.cjs
 */
class AstroRespondingIndicatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_page_ships_the_indicator_and_the_status_engine(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('student.chat'))
            ->assertOk()
            ->assertSee('js/astro-status.js', false)   // deterministic status engine
            ->assertSee('astro-resp', false)            // the indicator
            ->assertSee('apple-touch-icon.png', false)  // the existing TechLab logo, reused
            ->assertSee('data-state', false);
    }

    public function test_the_old_generic_thinking_dots_and_copy_are_gone(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('student.chat'))
            ->assertOk()
            ->assertDontSee('Astro is thinking', false);
    }

    public function test_the_reused_logo_and_the_engine_exist_on_disk(): void
    {
        $this->assertFileExists(public_path('apple-touch-icon.png'), 'the existing TechLab logo must not be removed');
        $this->assertFileExists(public_path('js/astro-status.js'));
    }

    public function test_the_indicator_never_asks_the_model_for_its_words(): void
    {
        // The status lines are chosen client-side; the send() payload must stay exactly as it was.
        $chat = file_get_contents(resource_path('views/student/chat.blade.php'));

        $this->assertStringContainsString("fetch('/chat/message'", $chat);
        $this->assertStringNotContainsString('/chat/status', $chat);
        $this->assertSame(1, substr_count($chat, "fetch('/chat/message'"), 'no extra request was added for status text');
    }
}
