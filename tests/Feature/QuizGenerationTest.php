<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\Quiz\QuizNimService;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\Http\Message\RequestInterface;
use Tests\TestCase;

class QuizGenerationTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, array> decoded request bodies, in send order */
    private array $sent = [];

    private int $serial = 0;

    private function quizBody(int $n, string $wrap = '%s'): string
    {
        $qs = [];
        for ($i = 1; $i <= $n; $i++) {
            $id = ++$this->serial;
            $qs[] = [
                'type' => 'multiple_choice', 'question' => "Unique question number $id?",
                'choices' => [['key' => 'A', 'text' => 'a'], ['key' => 'B', 'text' => 'b'], ['key' => 'C', 'text' => 'c'], ['key' => 'D', 'text' => 'd']],
                'correct_key' => 'A', 'explanation' => 'Because.', 'difficulty' => 'easy', 'points' => 1,
            ];
        }
        $json = json_encode(['title' => 'T', 'description' => 'D', 'topic' => 'X', 'questions' => $qs]);

        return json_encode(['choices' => [['message' => ['content' => sprintf($wrap, $json)]]]]);
    }

    /** @param  callable(string $model, int $n): Response  $respond */
    private function fakeNim(callable $respond): void
    {
        $stack = HandlerStack::create(function (RequestInterface $req) use ($respond) {
            $body = json_decode((string) $req->getBody(), true);
            $this->sent[] = $body;
            preg_match('/(\d+)-question/', $body['messages'][1]['content'], $m);

            return Create::promiseFor($respond($body['model'], (int) ($m[1] ?? 5)));
        });
        $this->app->instance(QuizNimService::class, new QuizNimService(new Client(['handler' => $stack])));
    }

    private function chatWithAstro(): array
    {
        $user = User::factory()->create();
        $conv = Conversation::create(['user_id' => $user->id, 'title' => 'Laravel']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'user', 'content' => 'What is Laravel and how do I deploy it to production?']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'assistant', 'content' => 'Laravel is a PHP framework with routing, Eloquent and Blade. Deploy with Nginx and PHP-FPM, run composer install and php artisan migrate.']);

        return [$user, $conv];
    }

    public function test_ten_questions_run_as_two_parallel_batches_of_five(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['good/model']]);
        $this->fakeNim(fn ($model, $n) => new Response(200, [], $this->quizBody($n)));
        [$user, $conv] = $this->chatWithAstro();

        $res = $this->actingAs($user)->postJson('/chat/quiz', ['conversation_id' => $conv->id, 'count' => 10]);

        $res->assertOk()->assertJsonCount(10, 'quiz.questions');
        $this->assertCount(2, $this->sent);
        $this->assertStringContainsString('5-question', $this->sent[0]['messages'][1]['content']);
    }

    public function test_retired_model_410_falls_through_to_next_model_per_batch(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['old/retired', 'new/working']]);
        $this->fakeNim(fn ($model, $n) => $model === 'old/retired'
            ? new Response(410, [], '{"title":"Gone"}')
            : new Response(200, [], $this->quizBody($n)));
        [$user, $conv] = $this->chatWithAstro();

        $this->actingAs($user)->postJson('/chat/quiz', ['conversation_id' => $conv->id, 'count' => 5])
            ->assertOk()->assertJsonCount(5, 'quiz.questions');
        $this->assertSame(['old/retired', 'new/working'], array_column($this->sent, 'model'));
    }

    public function test_thinking_block_and_prose_around_json_still_parse(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        $this->fakeNim(fn ($model, $n) => new Response(200, [], $this->quizBody($n, '<think>plan {not json}</think> Here you go: %s Enjoy!')));
        [$user, $conv] = $this->chatWithAstro();

        $this->actingAs($user)->postJson('/chat/quiz', ['conversation_id' => $conv->id])
            ->assertOk()->assertJsonCount(5, 'quiz.questions');
    }

    public function test_unparseable_answer_falls_to_next_model(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['chatty', 'good']]);
        $this->fakeNim(fn ($model, $n) => $model === 'chatty'
            ? new Response(200, [], json_encode(['choices' => [['message' => ['content' => '**Quiz** | # | Question |']]]]))
            : new Response(200, [], $this->quizBody($n)));
        [$user, $conv] = $this->chatWithAstro();

        $this->actingAs($user)->postJson('/chat/quiz', ['conversation_id' => $conv->id])
            ->assertOk()->assertJsonCount(5, 'quiz.questions');
    }

    public function test_one_failed_batch_still_returns_a_partial_quiz(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        $calls = 0;
        $this->fakeNim(function ($model, $n) use (&$calls) {
            return ++$calls === 1 ? new Response(503, [], '{}') : new Response(200, [], $this->quizBody($n));
        });
        [$user, $conv] = $this->chatWithAstro();

        $this->actingAs($user)->postJson('/chat/quiz', ['conversation_id' => $conv->id, 'count' => 10])
            ->assertOk()->assertJsonCount(5, 'quiz.questions');
    }

    public function test_all_models_gone_reports_model_unavailable_not_unreachable(): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['old/retired']]);
        $this->fakeNim(fn () => new Response(410, [], '{}'));
        [$user, $conv] = $this->chatWithAstro();

        $res = $this->actingAs($user)->postJson('/chat/quiz', ['conversation_id' => $conv->id]);

        $res->assertStatus(503)->assertJsonPath('category', 'NIM_MODEL_ERROR');
        $this->assertStringNotContainsString('unreachable', $res->json('error'));
    }

    public function test_count_out_of_range_is_rejected(): void
    {
        [$user, $conv] = $this->chatWithAstro();

        $this->actingAs($user)->postJson('/chat/quiz', ['conversation_id' => $conv->id, 'count' => 99])->assertStatus(422);
    }
}
