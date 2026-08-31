<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_get_401_json(): void
    {
        $this->post(route('student.planet.plan', ['slug' => 'programming']))
            ->assertStatus(401)
            ->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_unknown_slug_404s(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('student.planet.plan', ['slug' => 'underwater']))
            ->assertStatus(404)
            ->assertJson(['error' => 'Planet not found.']);
    }

    public function test_returns_ordered_placeholder_plan_for_every_planet(): void
    {
        foreach (['programming', 'networking', 'cybersecurity'] as $slug) {
            $user = User::factory()->create();

            $this->actingAs($user)
                ->post(route('student.planet.plan', ['slug' => $slug]))
                ->assertOk()
                ->assertJsonPath('ok', true)
                ->assertJsonPath("plan.slug", $slug);
        }
    }

    public function test_programming_nodes_are_ordered_with_first_unlocked(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('student.planet.plan', ['slug' => 'programming']))
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'plan' => [
                    'slug' => 'programming',
                    'title' => 'Programming City',
                    'nodes' => [
                        ['label' => 'Thinking in Code', 'locked' => false],
                        ['label' => 'Programming with Variables', 'locked' => true],
                        ['label' => 'Programming with Functions', 'locked' => true],
                        ['label' => 'Algorithmic Thinking', 'locked' => true],
                        ['label' => 'Build Your First Project', 'locked' => true],
                    ],
                ],
            ]);
    }
}
