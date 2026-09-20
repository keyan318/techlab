<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonQuizTest extends TestCase
{
    use RefreshDatabase;

    /** A student who has cleared M1 lessons 1 and 2, so lesson 3 (which has a 3-question quiz, answers C/C/C) is open. */
    private function student(): User
    {
        $u = User::factory()->create(['role' => 'student']);
        CourseProgressService::markComplete($u, 'm1', 'lesson01', 'programming');
        CourseProgressService::markComplete($u, 'm1', 'lesson02', 'programming');

        return $u;
    }

    private function url(string $lesson = 'lesson03'): string
    {
        return route('student.planet.quiz.answer', ['slug' => 'programming', 'module' => 'm1', 'lesson' => $lesson]);
    }

    public function test_the_lesson_html_never_carries_the_answers(): void
    {
        $html = $this->actingAs($this->student())->get(route('student.planet.module.lesson.fragment', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson03']))
            ->assertOk()->getContent();

        $this->assertStringContainsString('quiz-block', $html);
        $this->assertStringNotContainsString('quiz-correct', $html);
        $this->assertStringNotContainsString('✓', $html);
        $this->assertStringNotContainsString('quiz-explanation', $html);
        $this->assertStringNotContainsString('Boss Challenge', $html);
        $this->assertDoesNotMatchRegularExpression('/section-heading">\s*Challenge/', $html);
    }

    public function test_a_correct_first_answer_earns_xp_and_a_wrong_one_earns_none(): void
    {
        $u = $this->student();

        $this->actingAs($u)->postJson($this->url(), ['q' => 1, 'choice' => 'C'])
            ->assertOk()->assertJson(['ok' => true, 'correct' => true, 'correctChoice' => 'C', 'xp' => 10]);

        $this->actingAs($u)->postJson($this->url(), ['q' => 2, 'choice' => 'A'])
            ->assertOk()->assertJson(['correct' => false, 'correctChoice' => 'C', 'xp' => 0])
            ->assertJsonPath('explanation', fn ($e) => $e !== '');

        $this->assertDatabaseCount('quiz_answers', 2);
    }

    public function test_re_answering_never_pays_xp_twice_or_changes_the_result(): void
    {
        $u = $this->student();

        $this->actingAs($u)->postJson($this->url(), ['q' => 1, 'choice' => 'A'])->assertJson(['correct' => false, 'xp' => 0]);
        // Trying again with the right letter must not flip a wrong first answer into XP.
        $this->actingAs($u)->postJson($this->url(), ['q' => 1, 'choice' => 'C'])->assertJson(['first' => false, 'correct' => false, 'choice' => 'A', 'xp' => 0]);

        $this->actingAs($u)->postJson($this->url(), ['q' => 3, 'choice' => 'C'])->assertJson(['xp' => 10]);
        $this->actingAs($u)->postJson($this->url(), ['q' => 3, 'choice' => 'C'])->assertJson(['first' => false, 'xp' => 0]);

        $this->assertSame(10, (int) $u->quizAnswers()->sum('xp'));
    }

    public function test_status_restores_answers_after_a_reload(): void
    {
        $u = $this->student();
        $this->actingAs($u)->postJson($this->url(), ['q' => 1, 'choice' => 'C']);

        $this->actingAs($u)->getJson($this->url())->assertOk()
            ->assertJsonPath('total', 3)->assertJsonPath('perQuestion', 10)
            ->assertJsonPath('answered.1.correct', true)->assertJsonPath('answered.1.correctChoice', 'C')
            ->assertJsonMissingPath('answered.2');
    }

    public function test_locked_lessons_and_bad_input_are_refused(): void
    {
        $fresh = User::factory()->create(['role' => 'student']);
        $this->actingAs($fresh)->postJson($this->url(), ['q' => 1, 'choice' => 'C'])->assertForbidden();

        $u = $this->student();
        $this->actingAs($u)->postJson($this->url(), ['q' => 1, 'choice' => 'Z'])->assertStatus(422);
        $this->actingAs($u)->postJson($this->url(), ['q' => 9, 'choice' => 'A'])->assertNotFound();
        $this->postJson($this->url('lesson07'), ['q' => 1, 'choice' => 'A']);   // a lesson with no quiz (and still locked) never awards anything
        $this->assertDatabaseCount('quiz_answers', 0);
    }

    public function test_quiz_xp_counts_toward_total_xp_and_the_leaderboard(): void
    {
        $u = $this->student();   // 2 lessons done = 200 XP
        $this->actingAs($u)->postJson($this->url(), ['q' => 1, 'choice' => 'C']);
        $this->actingAs($u)->postJson($this->url(), ['q' => 2, 'choice' => 'C']);

        $this->assertSame(2 * StudentDashboardService::XP_PER_LESSON + 20, StudentDashboardService::build($u)['xp']);
    }
}
