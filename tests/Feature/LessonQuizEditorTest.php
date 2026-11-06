<?php

namespace Tests\Feature;

use App\Models\LessonQuiz;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
use App\Services\QuizGateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonQuizEditorTest extends TestCase
{
    use RefreshDatabase;

    private function faculty(): User
    {
        return $this->crewFor('programming', 'python')->faculty;
    }

    private function url(string $action = 'update', string $lesson = 'lesson03'): string
    {
        return route("faculty.lesson-quizzes.{$action}", ['crew' => $this->crewFor('programming', 'python'), 'module' => 'm1', 'lesson' => $lesson]);
    }

    private function questions(): array
    {
        return [
            ['prompt' => 'What does `print(1)` show?', 'options' => ['1', 'one', 'nothing'], 'correct' => 'A', 'explanation' => 'It prints the number.'],
            ['prompt' => 'Is Python fun?', 'options' => ['No', 'Yes'], 'correct' => 'B', 'explanation' => ''],
        ];
    }

    /** A student with lesson 3 of the Python course open. */
    private function student(): User
    {
        $u = $this->studentIn();
        CourseProgressService::markComplete($u, 'm1', 'lesson01', 'programming');
        CourseProgressService::markComplete($u, 'm1', 'lesson02', 'programming');

        return $u;
    }

    public function test_the_faculty_sees_every_lesson_quiz_starting_as_the_default(): void
    {
        $this->actingAs($this->faculty())->get(route('faculty.lesson-quizzes.index', $this->crewFor('programming', 'python')))
            ->assertOk()->assertSee('Default')->assertDontSee('Edited by you');

        $this->actingAs($this->faculty())->get($this->url('edit'))->assertOk()->assertSee('This is the default quiz.');
    }

    public function test_only_the_course_faculty_can_edit_its_quizzes(): void
    {
        $other = User::factory()->create(['role' => 'faculty']);
        $this->actingAs($other)->get($this->url('edit'))->assertForbidden();
        $this->actingAs($other)->putJson($this->url(), ['questions' => $this->questions()])->assertForbidden();
        $this->actingAs($this->studentIn())->putJson($this->url(), ['questions' => $this->questions()])->assertForbidden();

        $this->assertDatabaseCount('lesson_quizzes', 0);
    }

    public function test_an_edited_quiz_replaces_the_default_for_students(): void
    {
        $this->actingAs($this->faculty())->putJson($this->url(), ['questions' => $this->questions()])->assertOk()->assertJson(['count' => 2]);

        $u = $this->student();
        $html = $this->actingAs($u)->get(route('student.planet.module.lesson.fragment', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson03']))
            ->assertOk()->getContent();
        $this->assertStringContainsString('Q1. What does <code>print(1)</code> show?', $html);
        $this->assertStringContainsString('B. Yes', $html);
        $this->assertStringNotContainsString('quiz-correct', $html);
        $this->assertSame(2, substr_count($html, 'class="quiz-block"'));

        $answer = route('student.planet.quiz.answer', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson03']);
        $this->actingAs($u)->postJson($answer, ['q' => 1, 'choice' => 'A'])->assertOk()
            ->assertJson(['correct' => true, 'correctChoice' => 'A'])->assertJsonPath('explanation', '<em>It prints the number.</em>');
        $this->actingAs($u)->postJson($answer, ['q' => 2, 'choice' => 'B'])->assertOk()->assertJson(['correct' => true]);
        $this->actingAs($u)->postJson($answer, ['q' => 3, 'choice' => 'A'])->assertNotFound();

        $this->actingAs($u)->getJson($answer)->assertJsonPath('total', 2)->assertJsonPath('gate.allowed', true);
    }

    public function test_resetting_brings_the_default_quiz_back(): void
    {
        $default = LessonQuizService::key((string) LessonQuizService::source('m1', 'lesson03', 'programming'));
        $this->actingAs($this->faculty())->putJson($this->url(), ['questions' => $this->questions()])->assertOk();
        $this->assertCount(2, LessonQuizService::key((string) LessonQuizService::source('m1', 'lesson03', 'programming')));

        $this->actingAs($this->faculty())->delete($this->url('destroy'))->assertRedirect();

        $this->assertDatabaseCount('lesson_quizzes', 0);
        $this->assertSame($default, LessonQuizService::key((string) LessonQuizService::source('m1', 'lesson03', 'programming')));
    }

    public function test_the_default_quiz_survives_a_round_trip_through_the_editor(): void
    {
        $html = (string) LessonQuizService::defaultSource('m1', 'lesson03', 'programming');
        $parsed = LessonQuizService::parse($html);
        $this->assertNotEmpty($parsed);

        LessonQuiz::create(['crew_id' => $this->crewFor('programming', 'python')->id, 'course' => 'programming', 'module' => 'm1', 'lesson' => 'lesson03', 'questions' => $parsed]);

        $edited = (string) LessonQuizService::source('m1', 'lesson03', 'programming');
        $this->assertSame(array_column(LessonQuizService::key($html), 'correct'), array_column(LessonQuizService::key($edited), 'correct'));
        $this->assertSame($parsed, LessonQuizService::parse($edited));
    }

    public function test_answers_to_removed_questions_no_longer_count(): void
    {
        $u = $this->student();
        $this->answerQuiz($u, 'programming', 'm1', 'lesson03', mistakes: 3);   // failed the 5-question default

        $this->actingAs($this->faculty())->putJson($this->url(), ['questions' => array_slice($this->questions(), 0, 1)])->assertOk();

        $state = app(QuizGateService::class)->state($u, 'programming', 'm1', 'lesson03');
        $this->assertSame(1, $state['total']);
        $this->assertSame(1, $state['answered']);
    }

    public function test_a_question_needs_text_choices_and_a_valid_right_answer(): void
    {
        $this->actingAs($this->faculty())->putJson($this->url(), ['questions' => []])->assertUnprocessable();
        $this->actingAs($this->faculty())->putJson($this->url(), ['questions' => [['prompt' => '', 'options' => ['a', 'b'], 'correct' => 'A']]])->assertUnprocessable();
        $this->actingAs($this->faculty())->putJson($this->url(), ['questions' => [['prompt' => 'Q', 'options' => ['a', 'b'], 'correct' => 'C']]])->assertUnprocessable();
        $this->actingAs($this->faculty())->putJson($this->url(), ['questions' => [['prompt' => '<script>x</script>', 'options' => ['a', 'b'], 'correct' => 'A']]])->assertOk();

        $this->assertStringContainsString('&lt;script&gt;', (string) LessonQuizService::source('m1', 'lesson03', 'programming'));
    }
}
