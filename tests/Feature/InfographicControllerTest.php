<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\NvidiaNimService;
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

    private function cannedPlan(): array
    {
        return [
            'title' => 'Laravel MVC',
            'subtitle' => 'Mission Control for Web Applications',
            'theme' => 'space_mission_control',
            'source_summary' => 'Laravel separates concerns into Model, View, Controller.',
            'slides' => [
                ['type' => 'cover', 'title' => 'Laravel MVC', 'subtitle' => 'Mission Control for Web Applications'],
                ['type' => 'concept', 'title' => 'What is MVC?', 'summary' => 'Separation of concerns.', 'keyPoints' => ['Model manages data', 'View presents', 'Controller coordinates']],
                ['type' => 'architecture', 'title' => 'The MVC System', 'elements' => [['name' => 'Model', 'detail' => 'data'], ['name' => 'View', 'detail' => 'ui'], ['name' => 'Controller', 'detail' => 'logic']]],
                ['type' => 'analogy', 'title' => 'MVC as Mission Control', 'analogy' => 'The controller is mission control.', 'visual' => ['required' => true, 'type' => 'illustration', 'prompt' => 'mission control station']],
                ['type' => 'summary', 'title' => 'Key Takeaways', 'keyPoints' => ['MVC separates concerns', 'Easier to maintain']],
            ],
        ];
    }

    public function test_generates_infographic_from_pasted_text(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->mock(NvidiaNimService::class)
            ->shouldReceive('completeJson')
            ->andReturn($this->cannedPlan());

        $response = $this->actingAs($user)->postJson('/chat/infographic', [
            'source_text' => 'Laravel routes map URLs to controllers and separate Model, View, Controller.',
        ]);

        $response->assertOk();
        $response->assertJson(['ok' => true]);

        $inf = $response->json('infographic');
        $this->assertSame('Laravel MVC', $inf['title']);
        $this->assertSame('cover', $inf['slides'][0]['type']);
        $this->assertGreaterThanOrEqual(5, $inf['slide_count']);

        // The required analogy slide should have received generated artwork
        // (offline SVG fallback) so the deck never renders an empty image.
        $analogy = collect($inf['slides'])->firstWhere('type', 'analogy');
        $this->assertNotNull($analogy);
        $this->assertNotEmpty($analogy['image']);
        $this->assertSame('generated', $analogy['image_status']);
    }

    public function test_empty_source_returns_422_with_friendly_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/chat/infographic', [
            'source_text' => '   ',
        ]);

        $response->assertStatus(422);
        $this->assertSame('empty_source', $response->json('kind'));
        $this->assertStringContainsString('paste some content', $response->json('error'));
    }

    public function test_model_is_not_called_when_source_is_empty(): void
    {
        $user = User::factory()->create();

        $this->mock(NvidiaNimService::class)
            ->shouldNotReceive('completeJson');

        $this->actingAs($user)->postJson('/chat/infographic', ['source_text' => '']);
    }

    public function test_malformed_model_json_returns_502(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        // No `slides` -> schema returns null -> generation failure.
        $this->mock(NvidiaNimService::class)
            ->shouldReceive('completeJson')
            ->andReturn(['title' => 'broken', 'theme' => 'x']);

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
