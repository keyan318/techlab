<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioOutputsTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_outputs_follow_the_user_and_stay_private(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $item = ['id' => 'qz-1', 'type' => 'quiz', 'title' => 'Quiz', 'subtitle' => '5 questions', 'quiz' => ['questions' => []]];

        $this->actingAs($a)->postJson('/chat/studio-outputs', ['item' => $item])->assertOk();
        $this->actingAs($a)->postJson('/chat/studio-outputs', ['item' => $item])->assertOk(); // upsert, no duplicate

        $this->actingAs($a)->getJson('/chat/studio-outputs')->assertOk()->assertJsonCount(1, 'items')->assertJsonPath('items.0.id', 'qz-1');
        $this->actingAs($b)->getJson('/chat/studio-outputs')->assertJsonCount(0, 'items');

        $this->actingAs($b)->deleteJson('/chat/studio-outputs/qz-1')->assertOk();
        $this->actingAs($a)->getJson('/chat/studio-outputs')->assertJsonCount(1, 'items');
        $this->actingAs($a)->deleteJson('/chat/studio-outputs/qz-1')->assertOk();
        $this->actingAs($a)->getJson('/chat/studio-outputs')->assertJsonCount(0, 'items');
    }
}
