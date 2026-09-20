<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NvidiaNimService;
use App\Services\WebSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebSourcesTest extends TestCase
{
    use RefreshDatabase;

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
        config(['web_search.api_key' => 'test-key']);
        Http::fake(['api.tavily.com/*' => Http::response(['results' => [
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

    public function test_simple_question_never_calls_the_web(): void
    {
        config(['web_search.api_key' => 'test-key']);
        Http::fake();
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()->andReturn('A language.');

        $this->actingAs(User::factory()->create())
            ->postJson('/chat/message', ['message' => 'What is Python?'])
            ->assertOk()->assertJsonPath('web', []);

        Http::assertNothingSent();
    }
}
