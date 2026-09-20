<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\LessonSourceService;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanetSourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_lists_all_planets_and_reads_real_lessons(): void
    {
        $catalog = collect(app(LessonSourceService::class)->catalog())->keyBy('slug');

        $this->assertSame(['programming', 'networking', 'cybersecurity'], $catalog->keys()->all());
        $this->assertNotEmpty($catalog['programming']['modules']);
        $this->assertNotEmpty($catalog['networking']['modules']);
        $this->assertSame([], $catalog['cybersecurity']['modules']);
    }

    public function test_resolve_returns_lesson_text_and_drops_invalid_refs(): void
    {
        $sources = app(LessonSourceService::class)->resolve([
            ['planet' => 'programming', 'module' => 'M1', 'lesson' => 'lesson-02'],
            ['planet' => 'networking', 'module' => 'M1', 'lesson' => 'lesson-01'],
            ['planet' => 'programming', 'module' => '../../..', 'lesson' => 'lesson-01'],
            ['planet' => 'cybersecurity', 'module' => 'M1', 'lesson' => 'lesson-01'],
        ]);

        $this->assertCount(2, $sources);
        $this->assertStringContainsString('Variables', $sources[0]['label']);
        $this->assertStringContainsString('variable', $sources[0]['text']);
        $this->assertStringContainsString('Networking', $sources[1]['label']);
    }

    public function test_connected_lessons_reach_astro_and_are_returned_as_sources(): void
    {
        $captured = null;
        $this->mock(NvidiaNimService::class)
            ->shouldReceive('stream')
            ->once()
            ->andReturnUsing(function ($history, $cb, $context = '') use (&$captured) {
                $captured = $context;

                return 'Variables are labeled boxes.';
            });

        $response = $this->actingAs(User::factory()->create())->postJson('/chat/message', [
            'message' => 'Explain variables.',
            'sources' => [['planet' => 'programming', 'module' => 'M1', 'lesson' => 'lesson-02']],
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertStringContainsString('Variables & Memory', $response->json('sources.0'));
        $this->assertStringContainsString('Source 1', $captured);
        $this->assertStringContainsString('labeled box', $captured);
    }

    public function test_chat_without_sources_sends_no_context(): void
    {
        $captured = 'unset';
        $this->mock(NvidiaNimService::class)
            ->shouldReceive('stream')
            ->once()
            ->andReturnUsing(function ($history, $cb, $context = '') use (&$captured) {
                $captured = $context;

                return 'Hi!';
            });

        $response = $this->actingAs(User::factory()->create())->postJson('/chat/message', ['message' => 'Hello']);

        $response->assertOk()->assertJsonPath('sources', []);
        $this->assertSame('', $captured);
    }

    public function test_chat_page_renders_picker_and_deep_link_preselects(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/chat?source=programming/M1/lesson-02')
            ->assertOk()
            ->assertSee('Add from Planets')
            ->assertSee('"lesson":"lesson-02"', false);

        // A bogus deep link is ignored, not an error.
        $this->actingAs($user)->get('/chat?source=nope/x/y')->assertOk();
    }
}
