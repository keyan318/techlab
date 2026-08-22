<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\ImageGenerationService;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Covers the "Draw Analogy" feature: ownership enforcement, the analogy payload,
 * and the guarantee that a NORMAL chat message never triggers image generation.
 */
class AstroAnalogyTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_draw_analogy_for_their_own_message(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $conversation = $user->conversations()->create(['title' => 'APIs']);
        $message = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => 'An API is like a waiter in a restaurant. The customer (client) asks the waiter (API) to bring food (data) from the kitchen (server).',
        ]);

        $this->mock(NvidiaNimService::class)
            ->shouldReceive('systemPromptForAnalogy')->andReturn('extract')
            ->shouldReceive('completeJson')->andReturn([
                'title' => 'An API is like a waiter',
                'elements' => [
                    ['name' => 'Customer', 'role' => 'asks for food'],
                    ['name' => 'Waiter', 'role' => 'carries the order'],
                    ['name' => 'Kitchen', 'role' => 'cooks the food'],
                ],
                'flow' => ['orders', 'delivers order', 'serves food'],
                'caption' => 'The waiter (API) carries requests between the customer and the kitchen.',
            ]);

        $response = $this->actingAs($user)->postJson('/chat/analogy', [
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
        ]);

        $response->assertOk();
        $response->assertJson(['ok' => true]);
        $response->assertJsonStructure(['image_url', 'title', 'caption', 'elements']);

        // The illustration should be a stored asset under analogies/.
        $path = Str::after(parse_url($response->json('image_url'), PHP_URL_PATH), '/storage/');
        Storage::disk('public')->assertExists($path);
        $this->assertStringContainsString('analogies/', $response->json('image_url'));
    }

    public function test_cannot_draw_analogy_for_another_students_conversation(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $conversation = $owner->conversations()->create(['title' => 'secret']);
        $message = $conversation->messages()->create(['role' => 'assistant', 'content' => 'secret analogy']);

        $response = $this->actingAs($intruder)->postJson('/chat/analogy', [
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
        ]);

        $response->assertStatus(404);
    }

    public function test_cannot_draw_analogy_for_a_non_assistant_message(): void
    {
        $user = User::factory()->create();
        $conversation = $user->conversations()->create(['title' => 't']);
        $message = $conversation->messages()->create(['role' => 'user', 'content' => 'hello']);

        $response = $this->actingAs($user)->postJson('/chat/analogy', [
            'conversation_id' => $conversation->id,
            'message_id' => $message->id,
        ]);

        $response->assertStatus(404);
    }

    public function test_draw_analogy_requires_authentication(): void
    {
        $response = $this->postJson('/chat/analogy', [
            'conversation_id' => 1,
            'message_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_normal_chat_message_does_not_generate_an_image(): void
    {
        $user = User::factory()->create();

        // The image pipeline must never be touched by a normal chat message.
        $this->mock(ImageGenerationService::class)
            ->shouldNotReceive('drawFromMessage');

        $this->mock(NvidiaNimService::class)
            ->shouldReceive('systemPrompt')->andReturn('sys')
            ->shouldReceive('stream')->andReturnUsing(function ($messages, $onDelta) {
                $onDelta('An API lets two systems talk to each other.');

                return 'An API lets two systems talk to each other.';
            });

        $response = $this->actingAs($user)->post('/chat/message', [
            'message' => 'What is an API?',
        ]);

        // streamedContent() actually sends the StreamedResponse, which runs the
        // streaming closure (and the DB write inside it).
        $content = $response->streamedContent();
        $response->assertOk();
        $this->assertStringContainsString('__END__', $content);
        $this->assertStringContainsString('two systems talk', $content);

        // The assistant reply was persisted with the streamed text…
        $assistant = Conversation::where('user_id', $user->id)
            ->first()
            ->messages()
            ->where('role', 'assistant')
            ->first();

        $this->assertNotNull($assistant);
        $this->assertStringContainsString('two systems talk', $assistant->content);
        // …and it contains no image payload.
        $this->assertStringNotContainsString('image_url', $assistant->content);
    }

    public function test_chat_message_builds_a_course_aware_system_prompt(): void
    {
        $user = User::factory()->create();

        $captured = [];
        $this->mock(NvidiaNimService::class)
            ->shouldReceive('systemPrompt')->andReturnUsing(function ($options) use (&$captured) {
                $captured = $options;

                return 'sys';
            })
            ->shouldReceive('stream')->andReturnUsing(function ($messages, $onDelta) {
                $onDelta('ok');

                return 'ok';
            });

        $this->actingAs($user)->post('/chat/message', [
            'message' => 'What is semantic HTML?',
            'level' => 'beginner',
            'context' => 'Frontend Development · HTML5',
        ]);

        $this->assertSame('beginner', $captured['level'] ?? null);
        $this->assertStringContainsString('HTML5', $captured['context'] ?? '');
    }
}
