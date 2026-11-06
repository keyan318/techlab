<?php

namespace Tests\Feature;

use App\Exceptions\NvidiaNimException;
use App\Models\AiUsage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\QuizAnswer;
use App\Models\User;
use App\Services\NvidiaNimService;
use App\Services\Quiz\QuizNimService;
use App\Services\StudentDashboardService as Xp;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Astro's AI costs XP: a few free uses a day, then every chat message and Studio tool has a price
 * (StudentDashboardService::AI_COSTS). Not enough XP stops the AI call; a failed call is free.
 */
class AiXpTest extends TestCase
{
    use RefreshDatabase;

    private function student(int $xp = 0): User
    {
        $user = User::factory()->create(['role' => 'student']);
        if ($xp > 0) {
            QuizAnswer::create(['user_id' => $user->id, 'course' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01',
                'question' => 1, 'choice' => 'a', 'is_correct' => true, 'xp' => $xp]);
        }

        return $user;
    }

    /** Astro answers every message (or fails, when $fail). */
    private function fakeAstro(bool $fail = false): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')
            ->andReturnUsing(function ($history, $cb) use ($fail) {
                if ($fail) {
                    throw new NvidiaNimException('Astro is offline.');
                }
                $cb('Hi');

                return 'Hi';
            });
    }

    private function chat(User $user): TestResponse
    {
        return $this->actingAs($user)->postJson('/chat/message', ['message' => 'What is Python?']);
    }

    private function useFreeChats(User $user): void
    {
        AiUsage::insert(array_fill(0, Xp::AI_FREE_CHATS_PER_DAY,
            ['user_id' => $user->id, 'feature' => 'chat', 'cost' => 0, 'created_at' => now(), 'updated_at' => now()]));
    }

    /** NIM answers a flashcards request once per given response. */
    private function fakeStudioNim(array $responses): void
    {
        config(['quiz.nim.api_key' => 'k', 'quiz.nim.models' => ['m']]);
        $stack = HandlerStack::create(new MockHandler($responses));
        $this->app->instance(QuizNimService::class, new QuizNimService(new Client(['handler' => $stack])));
    }

    private function deck(): Response
    {
        $cards = [['front' => 'Python', 'back' => 'A language.'], ['front' => 'Variable', 'back' => 'A named box.'], ['front' => 'Loop', 'back' => 'Repeats code.']];

        return new Response(200, [], json_encode(['choices' => [['message' => ['content' => json_encode(['title' => 'Python', 'topic' => 'Python', 'cards' => $cards])]]]]));
    }

    private function flashcards(User $user): TestResponse
    {
        $conv = Conversation::create(['user_id' => $user->id, 'title' => 'Python']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'user', 'content' => 'What is Python and what are variables and loops?']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'assistant', 'content' => 'Python is a programming language. Variables are named boxes and loops repeat code.']);

        return $this->actingAs($user)->postJson('/chat/flashcards', ['conversation_id' => $conv->id, 'count' => 5]);
    }

    public function test_the_first_chats_of_the_day_are_free_then_each_costs_its_price(): void
    {
        $user = $this->student(xp: 100);
        $this->fakeAstro();

        for ($i = 1; $i <= Xp::AI_FREE_CHATS_PER_DAY; $i++) {
            $this->chat($user)->assertOk()->assertJsonPath('xp.cost', 0)->assertJsonPath('xp.freeLeft', Xp::AI_FREE_CHATS_PER_DAY - $i);
        }
        $this->assertSame(100, Xp::balance($user));

        $this->chat($user)->assertOk()->assertJsonPath('xp.cost', Xp::AI_COSTS['chat'])->assertJsonPath('xp.balance', 100 - Xp::AI_COSTS['chat']);
        $this->assertSame(100 - Xp::AI_COSTS['chat'], Xp::balance($user));
    }

    public function test_a_student_out_of_xp_gets_402_and_astro_is_never_called(): void
    {
        $user = $this->student(xp: Xp::AI_COSTS['chat'] - 1);
        $this->useFreeChats($user);
        $this->mock(NvidiaNimService::class)->shouldNotReceive('stream');

        $this->chat($user)->assertStatus(402)->assertJsonPath('kind', 'xp')->assertJsonPath('cost', Xp::AI_COSTS['chat']);
        $this->assertSame(0, Message::count());
        $this->assertSame(Xp::AI_FREE_CHATS_PER_DAY, AiUsage::count());
    }

    public function test_a_message_astro_fails_to_answer_costs_nothing(): void
    {
        $user = $this->student(xp: 100);
        $this->useFreeChats($user);
        $this->fakeAstro(fail: true);

        $this->chat($user)->assertStatus(500);

        $this->assertSame(100, Xp::balance($user));
        $this->assertSame(Xp::AI_FREE_CHATS_PER_DAY, AiUsage::count());
    }

    public function test_the_free_chats_come_back_the_next_day(): void
    {
        $user = $this->student();
        $this->useFreeChats($user);
        $this->fakeAstro();

        $this->chat($user)->assertStatus(402);

        Carbon::setTestNow(now()->addDay()->startOfDay()->addHour());
        $this->chat($user)->assertOk()->assertJsonPath('xp.cost', 0);
        Carbon::setTestNow();
    }

    public function test_the_first_studio_tool_is_free_and_the_next_costs_its_price(): void
    {
        $user = $this->student(xp: 100);
        $this->fakeStudioNim([$this->deck(), $this->deck()]);

        $this->flashcards($user)->assertOk()->assertHeader('X-Xp-Balance', '100')->assertHeader('X-Ai-Free-Left', '0');
        $this->flashcards($user)->assertOk()->assertHeader('X-Xp-Balance', (string) (100 - Xp::AI_COSTS['flashcards']));

        $this->assertSame(100 - Xp::AI_COSTS['flashcards'], Xp::balance($user));
    }

    public function test_a_studio_tool_without_enough_xp_is_refused_and_a_failed_one_is_refunded(): void
    {
        $user = $this->student(xp: 100);
        $this->fakeStudioNim([new Response(500, [], 'down'), new Response(500, [], 'down'), new Response(500, [], 'down')]);

        $this->flashcards($user)->assertStatus(500);
        $this->assertSame(0, AiUsage::count(), 'a failed tool gives the free use back');

        $broke = $this->student(xp: 5);
        AiUsage::create(['user_id' => $broke->id, 'feature' => 'quiz', 'cost' => 0]);
        $this->flashcards($broke)->assertStatus(402)->assertJsonPath('kind', 'xp');
    }

    public function test_faculty_are_never_charged(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $this->fakeStudioNim([$this->deck(), $this->deck()]);

        $this->flashcards($faculty)->assertOk()->assertHeaderMissing('X-Xp-Balance');
        $this->flashcards($faculty)->assertOk();

        $this->assertSame(0, AiUsage::count());
    }

    public function test_spending_on_astro_lowers_xp_but_never_the_level(): void
    {
        $user = $this->student(xp: 300);
        $level = Xp::levelFor(Xp::earnedXp($user));
        AiUsage::create(['user_id' => $user->id, 'feature' => 'report', 'cost' => Xp::AI_COSTS['report']]);

        $this->assertSame(300 - Xp::AI_COSTS['report'], Xp::balance($user));
        $this->assertSame($level, Xp::levelFor(Xp::earnedXp($user)));
    }

    public function test_the_status_endpoint_reports_xp_free_uses_and_prices(): void
    {
        $user = $this->student(xp: 60);
        AiUsage::create(['user_id' => $user->id, 'feature' => 'chat', 'cost' => 0]);

        $this->actingAs($user)->getJson('/chat/xp')->assertOk()->assertJson([
            'charged' => true, 'balance' => 60, 'freeChatsLeft' => Xp::AI_FREE_CHATS_PER_DAY - 1,
            'freeStudioLeft' => Xp::AI_FREE_STUDIO_PER_DAY, 'costs' => Xp::AI_COSTS,
        ]);
    }
}
