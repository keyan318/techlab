<?php

namespace Tests\Feature;

use App\Models\AiUsage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\AstroSmallTalk;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * "hi", "are you working?", "thanks!" get an instant built-in reply: no NVIDIA call, no XP.
 * Anything with a real question in it still goes to the AI.
 */
class AstroSmallTalkTest extends TestCase
{
    use RefreshDatabase;

    private const ACCEPT = 'application/json, text/event-stream';

    public static function smallTalk(): array
    {
        return [
            ['hi are you working?', 'check_in'],
            ['Hi Astro!! 👋', 'greeting'],
            ['hello', 'greeting'],
            ['heyyy', 'greeting'],
            ['Good morning!', 'greeting'],
            ['are you there?', 'check_in'],
            ['is this working', 'check_in'],
            ['how are you?', 'check_in'],
            ["what's up", 'greeting'],
            ['thanks!', 'thanks'],
            ['Thank you so much Astro', 'thanks'],
            ['ok cool thanks', 'thanks'],
            ['ok', 'ack'],
            ['got it', 'ack'],
            ['bye!', 'bye'],
            ['thanks, see you later', 'bye'],
        ];
    }

    #[DataProvider('smallTalk')]
    public function test_pure_small_talk_is_recognised(string $message, string $kind): void
    {
        $this->assertSame($kind, app(AstroSmallTalk::class)->kind($message), $message);
    }

    public static function realQuestions(): array
    {
        return [
            ['hi, what is a loop?'],
            ['hi can you help me with python'],
            ['thanks, but why does it crash?'],
            ['what is phishing?'],
            ['how are you supposed to declare a variable'],
            ['is it working if I use a for loop?'],
            ['ok so how do I fix this'],
            ['hello world in C'],
            ['hi '.str_repeat('there ', 12)],
            [''],
        ];
    }

    #[DataProvider('realQuestions')]
    public function test_messages_with_a_real_question_go_to_the_ai(string $message): void
    {
        $this->assertNull(app(AstroSmallTalk::class)->reply($message), $message);
    }

    public function test_a_bare_ok_mid_conversation_goes_to_the_ai_because_it_may_answer_astros_offer(): void
    {
        $smallTalk = app(AstroSmallTalk::class);

        $this->assertNotNull($smallTalk->reply('ok'));
        $this->assertNull($smallTalk->reply('ok', 'astronaut', midConversation: true));
        $this->assertNull($smallTalk->reply('sure', 'astronaut', midConversation: true));
        $this->assertNotNull($smallTalk->reply('thanks!', 'astronaut', midConversation: true));
    }

    public function test_hi_streams_an_instant_reply_without_calling_nvidia_or_charging_xp(): void
    {
        $this->mock(NvidiaNimService::class)->shouldNotReceive('stream');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->withHeaders(['Accept' => self::ACCEPT])
            ->post('/chat/message', ['message' => 'hi are you working?']);

        $response->assertOk()->assertHeader('X-Astro-Mode', 'instant');
        $body = $response->streamedContent();

        $this->assertMatchesRegularExpression('/event: start\n.*event: delta\n.*event: end\n.*event: done\n/s', $body);
        preg_match('/event: delta\ndata: (.+)\n/', $body, $delta);
        $this->assertStringContainsString('astronaut', json_decode($delta[1], true)['t']);

        preg_match('/event: done\ndata: (.+)\n/', $body, $done);
        $done = json_decode($done[1], true);
        $this->assertTrue($done['saved']);
        $this->assertSame(['unchanged' => true], $done['xp']);

        $this->assertSame(['user', 'assistant'], Message::orderBy('id')->pluck('role')->all());
        $this->assertSame($user->id, Conversation::find($done['conversation_id'])->user_id);
        $this->assertSame(0, AiUsage::count(), 'small talk is free');
    }

    public function test_plain_json_clients_get_the_instant_reply_too(): void
    {
        $this->mock(NvidiaNimService::class)->shouldNotReceive('stream');

        $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', ['message' => 'thanks!'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('xp.unchanged', true);
    }

    public function test_faculty_are_greeted_as_captain(): void
    {
        $this->mock(NvidiaNimService::class)->shouldNotReceive('stream');

        $reply = $this->actingAs(User::factory()->create(['role' => 'faculty']))
            ->postJson('/chat/message', ['message' => 'hello'])
            ->json('response');

        $this->assertStringContainsString('Captain', $reply);
        $this->assertStringNotContainsString('astronaut', $reply);
    }

    public function test_small_talk_in_someone_elses_conversation_is_refused(): void
    {
        $this->mock(NvidiaNimService::class)->shouldNotReceive('stream');
        $foreign = Conversation::create(['user_id' => User::factory()->create()->id, 'title' => 'x']);

        $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', ['message' => 'thanks!', 'conversation_id' => $foreign->id])
            ->assertNotFound();
        $this->assertSame(0, Message::count());
    }

    public function test_a_real_question_still_reaches_nvidia(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()->andReturn('A loop repeats code.');

        $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', ['message' => 'hi, what is a loop?'])
            ->assertOk()
            ->assertJsonPath('response', 'A loop repeats code.');
    }

    public function test_hi_with_a_file_attached_goes_to_the_ai(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()->andReturn('I see your code.');

        $this->actingAs(User::factory()->create())
            ->post('/chat/message', ['message' => 'hi', 'files' => [UploadedFile::fake()->createWithContent('a.py', "print('x')\n")]], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('response', 'I see your code.');
    }
}
