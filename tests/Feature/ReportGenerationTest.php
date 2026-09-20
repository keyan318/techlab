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

class ReportGenerationTest extends TestCase
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

    public function test_generates_a_report_and_drops_empty_sections_and_raw_html(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        [$user, $conv] = $this->chat();
        $this->fakeNim([$this->nimResponse([
            'title' => 'Laravel Basics', 'topic' => 'PHP',
            'sections' => [
                ['heading' => 'Introduction', 'body' => 'Laravel is a **framework**.'],
                ['heading' => '', 'body' => 'No heading.'],
                ['heading' => 'Routing', 'body' => "- one\n- two<script>alert(1)</script>"],
                ['heading' => 'Empty', 'body' => '   '],
                ['heading' => 'Summary', 'body' => '- Done'],
            ],
        ])]);

        $res = $this->actingAs($user)->postJson('/chat/report', ['conversation_id' => $conv->id]);

        $res->assertOk()->assertJsonPath('report.title', 'Laravel Basics')->assertJsonCount(3, 'report.sections');
        $this->assertSame([1, 2, 3], array_column($res->json('report.sections'), 'id'));
        $this->assertStringNotContainsString('<script>', $res->json('report.sections.1.body'));
    }

    public function test_other_users_conversation_is_404(): void
    {
        [, $conv] = $this->chat();
        $this->actingAs(User::factory()->create())
            ->postJson('/chat/report', ['conversation_id' => $conv->id])
            ->assertNotFound();
    }

    public function test_tiny_conversation_is_rejected_without_calling_the_model(): void
    {
        $user = User::factory()->create();
        $conv = Conversation::create(['user_id' => $user->id, 'title' => 'x']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'user', 'content' => 'hi']);

        $this->actingAs($user)->postJson('/chat/report', ['conversation_id' => $conv->id])
            ->assertStatus(422)->assertJsonPath('kind', 'insufficient_content');
    }

    public function test_model_declared_insufficient_content_is_422(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        [$user, $conv] = $this->chat();
        $this->fakeNim([$this->nimResponse(['insufficient_content' => true, 'reason' => 'Too thin.'])]);

        $this->actingAs($user)->postJson('/chat/report', ['conversation_id' => $conv->id])
            ->assertStatus(422)->assertJsonPath('error', 'Too thin.');
    }

    public function test_unusable_model_output_is_a_safe_502(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        [$user, $conv] = $this->chat();
        $this->fakeNim([$this->nimResponse(['title' => 'x', 'sections' => []])]);

        $this->actingAs($user)->postJson('/chat/report', ['conversation_id' => $conv->id])
            ->assertStatus(502)->assertJsonPath('category', 'SCHEMA_ERROR');
    }
}
