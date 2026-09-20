<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonHintTest extends TestCase
{
    use RefreshDatabase;

    private function route(string $name, string $lesson = 'lesson03'): string
    {
        return route($name, ['slug' => 'programming', 'module' => 'm1', 'lesson' => $lesson]);
    }

    /** Lessons 1 and 2 done (200 XP), so lesson 3 is open. */
    private function student(): User
    {
        $u = User::factory()->create(['role' => 'student']);
        CourseProgressService::markComplete($u, 'm1', 'lesson01', 'programming');
        CourseProgressService::markComplete($u, 'm1', 'lesson02', 'programming');

        return $u;
    }

    public function test_the_hint_text_is_not_in_the_lesson_page(): void
    {
        $html = $this->actingAs($this->student())->get(route('student.planet.module.lesson.fragment', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson03']))
            ->assertOk()->getContent();

        $this->assertStringContainsString('Code it yourself', $html);
        $this->assertStringNotContainsString('name="hint_body"', $html);
        $this->assertStringNotContainsString('name="hint_code"', $html);
        $this->assertStringNotContainsString('int(value) + 8', $html);
    }

    public function test_buying_the_hint_deducts_xp_once_and_reveals_it(): void
    {
        $u = $this->student();
        $cost = StudentDashboardService::XP_HINT_COST;

        $this->actingAs($u)->getJson($this->route('student.planet.hint.status'))->assertOk()
            ->assertJson(['cost' => $cost, 'balance' => 200, 'canAfford' => true, 'bought' => false, 'hint' => null]);

        $this->actingAs($u)->postJson($this->route('student.planet.hint.buy'))->assertOk()
            ->assertJson(['ok' => true, 'spent' => $cost, 'balance' => 200 - $cost, 'bought' => true])
            ->assertJsonPath('hint.code', fn ($c) => str_contains($c, 'int(value) + 8'));

        // Flipping back later is free.
        $this->actingAs($u)->postJson($this->route('student.planet.hint.buy'))->assertOk()->assertJson(['spent' => 0, 'balance' => 200 - $cost]);
        $this->actingAs($u)->getJson($this->route('student.planet.hint.status'))->assertJson(['bought' => true])->assertJsonPath('hint.title', fn ($t) => $t !== '');

        $this->assertDatabaseCount('xp_spends', 1);
        $this->assertSame(200 - $cost, StudentDashboardService::build($u)['xp']);
    }

    public function test_a_student_without_enough_xp_cannot_buy_it(): void
    {
        $u = User::factory()->create(['role' => 'student']);   // 0 XP, on lesson 1

        $this->actingAs($u)->getJson($this->route('student.planet.hint.status', 'lesson01'))->assertOk()->assertJson(['canAfford' => false, 'balance' => 0]);
        $this->actingAs($u)->postJson($this->route('student.planet.hint.buy', 'lesson01'))->assertStatus(402)
            ->assertJson(['error' => 'Not enough XP.', 'bought' => false, 'hint' => null]);

        $this->assertDatabaseCount('xp_spends', 0);
    }

    public function test_locked_lessons_and_guests_are_refused(): void
    {
        $this->postJson($this->route('student.planet.hint.buy'))->assertUnauthorized();
        $this->actingAs(User::factory()->create(['role' => 'student']))->postJson($this->route('student.planet.hint.buy'))->assertForbidden();
    }

    public function test_spent_xp_never_makes_the_total_negative_and_counts_on_the_leaderboard(): void
    {
        $this->assertSame(0, StudentDashboardService::xpFor(0, 0, 50));
        $this->assertSame(130, StudentDashboardService::xpFor(1, 50, 20));
    }
}
