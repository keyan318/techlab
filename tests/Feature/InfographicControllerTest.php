<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Infographic\InfographicImageService;
use App\Services\Infographic\InfographicNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Infographic endpoint: source validation, model orchestration (mocked), schema
 * enforcement, and the image fallback path — all without touching the live model.
 */
class InfographicControllerTest extends TestCase
{
    use RefreshDatabase;

    private function cannedPoster(): array
    {
        return [
            'title' => 'Laravel MVC',
            'subtitle' => 'Mission control for web applications',
            'sections' => [
                ['label' => 'Model', 'heading' => 'Data lives here', 'detail' => 'Models wrap tables.', 'points' => ['Eloquent'], 'image_prompt' => 'database cylinder'],
                ['label' => 'View', 'heading' => 'What users see', 'detail' => 'Blade renders HTML.', 'points' => [], 'image_prompt' => 'browser window'],
                ['label' => 'Controller', 'heading' => 'Coordinates it all', 'detail' => 'Handles requests.', 'points' => [], 'image_prompt' => 'traffic controller'],
            ],
            'takeaways' => [
                ['heading' => 'Separation', 'detail' => 'Each part has one job.'],
            ],
        ];
    }

    private function mockNim(?array $response): void
    {
        $this->mock(InfographicNimService::class)
            ->shouldReceive('completeJson')
            ->andReturn($response);
    }

    public function test_generates_poster_from_transcript(): void
    {
        $user = User::factory()->create();
        $this->mockNim($this->cannedPoster());
        $this->mock(InfographicImageService::class)
            ->shouldReceive('generatePosterImages')
            ->andReturn(['/storage/a.png', null, '/storage/c.png']);

        $response = $this->actingAs($user)->postJson('/chat/infographic', [
            'source_text' => 'Laravel routes map URLs to controllers and separate Model, View, Controller.',
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $inf = $response->json('infographic');
        $this->assertSame('Laravel MVC', $inf['title']);
        $this->assertCount(3, $inf['sections']);
        $this->assertSame('/storage/a.png', $inf['sections'][0]['image']);
        $this->assertNull($inf['sections'][1]['image']);
        $this->assertArrayNotHasKey('image_prompt', $inf['sections'][0]);
    }

    public function test_empty_source_returns_422_with_friendly_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/chat/infographic', [
            'source_text' => '   ',
        ]);

        $response->assertStatus(422);
        $this->assertSame('empty_source', $response->json('kind'));
        $this->assertStringContainsString('Chat with Astro first', $response->json('error'));
    }

    public function test_model_is_not_called_when_source_is_empty(): void
    {
        $user = User::factory()->create();

        $this->mock(InfographicNimService::class)
            ->shouldNotReceive('completeJson');

        $this->actingAs($user)->postJson('/chat/infographic', ['source_text' => '']);
    }

    public function test_malformed_model_json_returns_502(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        // No `sections` -> schema returns null -> generation failure.
        $this->mockNim(['title' => 'broken']);

        $response = $this->actingAs($user)->postJson('/chat/infographic', [
            'source_text' => 'some material',
        ]);

        $response->assertStatus(502);
        $this->assertSame('generation', $response->json('kind'));
    }

    public function test_requires_authentication(): void
    {
        $response = $this->postJson('/chat/infographic', ['source_text' => 'x']);
        $response->assertStatus(401);
    }
}
