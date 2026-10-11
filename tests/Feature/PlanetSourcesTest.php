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
        // No `planets` set (the factory default) means every planet counts as enrolled.
        $user = User::factory()->create();
        $catalog = collect(app(LessonSourceService::class)->catalog($user))->keyBy('slug');

        $this->assertSame(['programming', 'networking', 'cybersecurity', 'information-assurance-security-2', 'computer-programming-1'], $catalog->keys()->all());
        $this->assertNotEmpty($catalog['programming']['modules']);
        $this->assertNotEmpty($catalog['networking']['modules']);
        $this->assertNotEmpty($catalog['cybersecurity']['modules']);
        $this->assertNotEmpty($catalog['information-assurance-security-2']['modules']);
        $this->assertNotEmpty($catalog['computer-programming-1']['modules']);
    }

    public function test_catalog_only_lists_the_students_enrolled_planet(): void
    {
        $user = User::factory()->create(['planets' => ['cybersecurity']]);
        $catalog = collect(app(LessonSourceService::class)->catalog($user))->keyBy('slug');

        $this->assertSame(['cybersecurity', 'information-assurance-security-2'], $catalog->keys()->all());
    }

    public function test_catalog_grows_once_another_planet_is_unlocked(): void
    {
        $user = User::factory()->create(['planets' => ['cybersecurity']]);
        $user->update(['planets' => ['cybersecurity', 'networking']]);

        $catalog = collect(app(LessonSourceService::class)->catalog($user))->keyBy('slug');

        $this->assertSame(['networking', 'cybersecurity', 'information-assurance-security-2'], $catalog->keys()->all());
    }

    public function test_faculty_see_every_planet_regardless_of_the_planets_column(): void
    {
        $user = User::factory()->create(['role' => 'faculty', 'planets' => ['cybersecurity']]);
        $catalog = collect(app(LessonSourceService::class)->catalog($user))->keyBy('slug');

        $this->assertSame(['programming', 'networking', 'cybersecurity', 'information-assurance-security-2', 'computer-programming-1'], $catalog->keys()->all());
    }

    public function test_resolve_returns_lesson_text_and_drops_invalid_refs(): void
    {
        $sources = app(LessonSourceService::class)->resolve([
            ['planet' => 'programming', 'module' => 'M1', 'lesson' => 'lesson-02'],
            ['planet' => 'networking', 'module' => 'M1', 'lesson' => 'lesson-01'],
            ['planet' => 'programming', 'module' => '../../..', 'lesson' => 'lesson-01'],
            ['planet' => 'cybersecurity', 'module' => 'M1', 'lesson' => 'lesson-01'],
        ]);

        $this->assertCount(3, $sources);
        $this->assertStringContainsString('Variables', $sources[0]['label']);
        $this->assertStringContainsString('variable', $sources[0]['text']);
        $this->assertStringContainsString('Networking', $sources[1]['label']);
        $this->assertStringContainsString('Citadel', $sources[2]['label']);
        $this->assertStringContainsString('asset', $sources[2]['text']);
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

    public function test_chat_page_renders_picker_without_auto_selecting_a_lesson(): void
    {
        $user = User::factory()->create();

        // Lessons are only added as sources when the student picks them via "Add from Planets".
        $this->actingAs($user)->get('/chat')
            ->assertOk()
            ->assertSee('Add from Planets')
            ->assertDontSee('"lesson":"lesson-02"', false);
    }
}
