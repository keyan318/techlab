<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use App\Services\AstroRouter;
use App\Services\NvidiaNimService;
use App\Services\WebSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebSourcesTest extends TestCase
{
    use RefreshDatabase;

    private const SEARXNG = 'http://searxng.test/search';

    public function test_simple_questions_do_not_need_research(): void
    {
        $svc = new WebSearchService;

        $this->assertFalse($svc->needsResearch('What is Python?'));
        $this->assertFalse($svc->needsResearch('What is Laravel and why do people use it for websites?'));
        $this->assertTrue($svc->needsResearch('how to use laravel refer based on their docs'));
        $this->assertTrue($svc->needsResearch('Research the latest best practices for securing a Laravel API and cite sources'));
    }

    public function test_research_question_returns_real_web_sources_and_grounds_astro(): void
    {
        config(['web_search.endpoint' => self::SEARXNG, 'web_search.api_key' => 'test-key']);
        Http::fake(['searxng.test/*' => Http::response(['results' => [
            ['title' => 'OWASP API Security', 'url' => 'https://www.owasp.org/api-security', 'content' => 'Top API risks.'],
            ['title' => 'Bad', 'url' => 'javascript:alert(1)', 'content' => 'x'],
        ]])]);

        $captured = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($h, $cb, $context = '') use (&$captured) {
                $captured = $context;

                return 'Answer [Web 1]';
            });

        $response = $this->actingAs(User::factory()->create())->postJson('/chat/message', [
            'message' => 'Research the latest best practices for securing a Laravel API and cite sources',
        ]);

        $response->assertOk()
            ->assertJsonCount(1, 'web')
            ->assertJsonPath('web.0.domain', 'owasp.org')
            ->assertJsonMissingPath('web.0.snippet');
        $this->assertStringContainsString('[Web 1: OWASP API Security', $captured);
    }

    public function test_small_talk_never_calls_the_web(): void
    {
        config(['web_search.endpoint' => self::SEARXNG, 'web_search.api_key' => 'test-key']);
        Http::fake();
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()->andReturn('Hey!');

        $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', ['message' => 'hey astro thanks'])
            ->assertOk()->assertJsonPath('web', []);

        Http::assertNothingSent();
    }

    public function test_a_plain_question_falls_back_to_the_web_when_no_material_covers_it(): void
    {
        // The fallback the whole feature rests on: nothing uploaded covers "cryptography",
        // so Astro cites real pages rather than answering from memory.
        config(['web_search.endpoint' => self::SEARXNG, 'web_search.api_key' => 'test-key']);
        Http::fake(['*' => Http::response(['results' => [
            ['title' => 'What is cryptography?', 'url' => 'https://www.cloudflare.com/learning/ssl/what-is-cryptography/', 'content' => 'Cryptography is...'],
        ]])]);
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()->andReturn('Answer [Web 1]');

        $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', ['message' => 'What is cryptography?'])
            ->assertOk()
            ->assertJsonCount(1, 'web')
            ->assertJsonPath('web.0.domain', 'cloudflare.com');
    }

    public function test_a_deep_question_searches_the_web_even_when_a_material_already_matched(): void
    {
        config(['web_search.endpoint' => self::SEARXNG, 'web_search.api_key' => 'test-key']);
        $route = ['mode' => 'deep', 'reason' => 'engineering'];
        $this->assertTrue((new WebSearchService)->shouldSearch('Compare TCP and UDP in depth', $route, hasMaterials: true));
    }

    public function test_a_selected_lesson_never_hits_the_web(): void
    {
        // The student picked a lesson so Astro answers from it — no web search, even for a
        // question that would otherwise be researched.
        config(['web_search.endpoint' => self::SEARXNG, 'web_search.api_key' => 'test-key']);
        Http::fake();
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()->andReturn('From the lesson.');

        $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', [
                'message' => 'Research the latest best practices for Python variables and cite sources',
                'sources' => [['planet' => 'programming', 'module' => 'M1', 'lesson' => 'lesson-02']],
            ])
            ->assertOk()
            ->assertJsonPath('web', []);

        Http::assertNothingSent();

        $route = ['mode' => 'deep', 'reason' => 'engineering'];
        $this->assertFalse((new WebSearchService)->shouldSearch('Compare TCP and UDP in depth', $route, hasMaterials: false, hasLessons: true));
    }

    public function test_a_pasted_traceback_never_hits_the_web_even_though_it_is_deep(): void
    {
        config(['web_search.endpoint' => self::SEARXNG, 'web_search.api_key' => 'test-key']);
        Http::fake();
        $route = app(AstroRouter::class)->route("Traceback (most recent call last):\n  File \"a.py\", line 3, in <module>\nNameError: name 'x' is not defined");
        $this->assertSame('deep', $route['mode']);
        $this->assertSame('traceback', $route['reason']);

        $this->assertFalse((new WebSearchService)->shouldSearch('some traceback text', $route, hasMaterials: false));
        Http::assertNothingSent();
    }

    public function test_a_link_astro_was_not_given_is_stripped_before_it_is_stored(): void
    {
        config(['web_search.endpoint' => self::SEARXNG, 'web_search.api_key' => 'test-key']);
        Http::fake(['searxng.test/*' => Http::response(['results' => [
            ['title' => 'OWASP API Security', 'url' => 'https://www.owasp.org/api-security', 'content' => 'Top API risks.'],
        ]])]);

        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturn('See [Web 1] and also [a totally different site](https://evil.example.com/phishing) plus http://another-made-up.example.net for more.');

        $conversationId = $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', ['message' => 'Research the latest Laravel API security best practices and cite sources'])
            ->assertOk()->json('conversation_id');

        $stored = Message::where('conversation_id', $conversationId)->where('role', 'assistant')->firstOrFail();
        // Markdown link to an unlisted URL collapses to its plain text — no domain leaks at all.
        $this->assertStringNotContainsString('evil.example.com', $stored->content);
        $this->assertStringNotContainsString('](https://evil.example.com/phishing)', $stored->content);
        // Bare unlisted URL loses its scheme/path — it's no longer a clickable citation.
        $this->assertStringNotContainsString('http://another-made-up.example.net', $stored->content);
        $this->assertStringContainsString('[Web 1]', $stored->content);
    }
}
