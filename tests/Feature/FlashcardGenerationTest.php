<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\Quiz\QuizNimService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashcardGenerationTest extends TestCase
{
    use RefreshDatabase;

    private function fakeNim(array $responses): void
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $this->app->instance(QuizNimService::class, new QuizNimService(new Client(['handler' => $stack])));
    }

    private function nimResponse(array $payload): Response
    {
        return new Response(200, [], json_encode(['choices' => [['message' => ['content' => json_encode($payload)]]]]));
    }

    private function chat(): array
    {
        $user = User::factory()->create();
        $conv = Conversation::create(['user_id' => $user->id, 'title' => 'Laravel']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'user', 'content' => 'What is Laravel and how do I deploy it to production?']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'assistant', 'content' => 'Laravel is a PHP framework with routing, Eloquent and Blade. Deploy with Nginx and PHP-FPM, run composer install and php artisan migrate.']);

        return [$user, $conv];
    }

    public function test_generates_a_clean_deck_and_drops_bad_or_duplicate_cards(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        [$user, $conv] = $this->chat();
        $this->fakeNim([$this->nimResponse([
            'title' => 'Laravel Basics', 'topic' => 'PHP',
            'cards' => [
                ['front' => 'Laravel', 'back' => 'A PHP framework.', 'hint' => 'Think PHP'],
                ['front' => 'laravel', 'back' => 'Duplicate front.'],
                ['front' => 'Eloquent', 'back' => 'The ORM.'],
                ['front' => '', 'back' => 'No front.'],
                ['front' => 'Blade', 'back' => 'The templating engine.'],
            ],
        ])]);

        $res = $this->actingAs($user)->postJson('/chat/flashcards', ['conversation_id' => $conv->id, 'count' => 5]);

        $res->assertOk()->assertJsonPath('deck.title', 'Laravel Basics')->assertJsonCount(3, 'deck.cards');
        $this->assertSame([1, 2, 3], array_column($res->json('deck.cards'), 'id'));
    }

    public function test_other_users_conversation_is_404(): void
    {
        [, $conv] = $this->chat();
        $this->actingAs(User::factory()->create())
            ->postJson('/chat/flashcards', ['conversation_id' => $conv->id])
            ->assertNotFound();
    }

    public function test_tiny_conversation_is_rejected_without_calling_the_model(): void
    {
        $user = User::factory()->create();
        $conv = Conversation::create(['user_id' => $user->id, 'title' => 'x']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'user', 'content' => 'hi']);

        $this->actingAs($user)->postJson('/chat/flashcards', ['conversation_id' => $conv->id])
            ->assertStatus(422)->assertJsonPath('kind', 'insufficient_content');
    }

    public function test_model_declared_insufficient_content_is_422(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        [$user, $conv] = $this->chat();
        $this->fakeNim([$this->nimResponse(['insufficient_content' => true, 'reason' => 'Too thin.'])]);

        $this->actingAs($user)->postJson('/chat/flashcards', ['conversation_id' => $conv->id])
            ->assertStatus(422)->assertJsonPath('error', 'Too thin.');
    }

    public function test_unusable_model_output_is_a_safe_502(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        [$user, $conv] = $this->chat();
        $this->fakeNim([$this->nimResponse(['title' => 'x', 'cards' => [['front' => 'a', 'back' => 'b']]])]);

        $this->actingAs($user)->postJson('/chat/flashcards', ['conversation_id' => $conv->id])
            ->assertStatus(502)->assertJsonPath('category', 'SCHEMA_ERROR');
    }
}
