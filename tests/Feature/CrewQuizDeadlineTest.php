<?php

namespace Tests\Feature;

use App\Http\Controllers\CourseHubController;
use App\Models\CrewQuiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Deadlines, the four question types, and the red "new quiz" badge.
 */
class CrewQuizDeadlineTest extends TestCase
{
    use RefreshDatabase;

    private function setUp2(): array
    {
        $student = $this->studentIn('programming', 'python');
        $crew = $this->crewFor('programming', 'python');

        return [$student, $crew];
    }

    private function quiz($crew, array $attrs = []): CrewQuiz
    {
        $quiz = $crew->quizzes()->create($attrs + ['title' => 'Week 3 Check', 'published_at' => now()]);
        $quiz->questions()->create([
            'prompt' => '2+2?', 'type' => 'multiple_choice', 'options' => ['3', '4'],
            'correct_index' => 1, 'position' => 0,
        ]);

        return $quiz;
    }

    /** Press Start, then hand in: the only way a quiz is taken now. */
    private function take(User $student, CrewQuiz $quiz, array $body)
    {
        $this->actingAs($student)->postJson(route('student.quiz.start', $quiz));

        return $this->actingAs($student)->postJson(route('student.quiz.submit', $quiz), $body);
    }

    public function test_a_quiz_can_be_taken_before_its_deadline(): void
    {
        [$student, $crew] = $this->setUp2();
        $quiz = $this->quiz($crew, ['due_at' => now()->addDay()]);

        $this->actingAs($student)->getJson(route('student.quiz.show', $quiz))->assertOk();
        $this->take($student, $quiz, [
            'answers' => [$quiz->questions->first()->id => 1],
        ])->assertOk()->assertJsonPath('result.score', 1);
    }

    public function test_a_quiz_locks_out_once_the_deadline_passes(): void
    {
        [$student, $crew] = $this->setUp2();
        $quiz = $this->quiz($crew, ['due_at' => now()->subMinute()]);

        $this->actingAs($student)->getJson(route('student.quiz.show', $quiz))->assertForbidden();
        $this->take($student, $quiz, ['answers' => []])->assertForbidden();
    }

    public function test_someone_who_already_submitted_keeps_their_result_after_the_deadline(): void
    {
        [$student, $crew] = $this->setUp2();
        $quiz = $this->quiz($crew, ['due_at' => now()->addDay()]);
        $this->take($student, $quiz, [
            'answers' => [$quiz->questions->first()->id => 1],
        ])->assertOk();

        $quiz->update(['due_at' => now()->subMinute()]);

        $this->actingAs($student)->getJson(route('student.quiz.show', $quiz))
            ->assertOk()->assertJsonPath('result.score', 1);
    }

    public function test_the_quiz_tab_says_missed_rather_than_offering_a_closed_quiz(): void
    {
        [$student, $crew] = $this->setUp2();
        $this->quiz($crew, ['due_at' => now()->subDay()]);

        $this->actingAs($student)->get(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertSee('Missed')->assertDontSee('Take quiz');
    }

    public function test_a_draft_quiz_is_invisible_to_students(): void
    {
        [$student, $crew] = $this->setUp2();
        $quiz = $this->quiz($crew, ['published_at' => null]);

        $this->actingAs($student)->get(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertDontSee('Week 3 Check');
        $this->actingAs($student)->getJson(route('student.quiz.show', $quiz))->assertNotFound();
    }

    public function test_new_quizzes_are_counted_but_no_badge_shows_now_the_quiz_tab_is_gone(): void
    {
        [$student, $crew] = $this->setUp2();
        $this->quiz($crew);
        $this->quiz($crew);

        $this->assertSame(2, CourseHubController::unseenQuizCount($crew, $student->id));

        // The hub has no Quiz tab any more, so neither the hub nor the course card shows a quiz badge.
        $this->actingAs($student)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertDontSee('2 new quizzes');
        $this->actingAs($student)->get(route('student.planet', 'programming'))->assertOk()->assertDontSee('2 new quizzes');

        // Opening the Quiz tab is what marks them seen.
        $this->actingAs($student)->get(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']))->assertOk();
        $this->assertSame(0, CourseHubController::unseenQuizCount($crew, $student->id));
    }

    public function test_a_quiz_already_taken_never_counts_towards_the_badge(): void
    {
        [$student, $crew] = $this->setUp2();
        $quiz = $this->quiz($crew);
        $this->take($student, $quiz, [
            'answers' => [$quiz->questions->first()->id => 1],
        ])->assertOk();

        $this->assertSame(0, CourseHubController::unseenQuizCount($crew, $student->id));
    }

    public function test_every_question_type_is_graded_correctly(): void
    {
        [$student, $crew] = $this->setUp2();
        $quiz = $crew->quizzes()->create(['title' => 'Mixed', 'published_at' => now()]);

        $mc = $quiz->questions()->create(['prompt' => 'Capital of Japan?', 'type' => 'multiple_choice', 'options' => ['Osaka', 'Tokyo'], 'correct_index' => 1, 'position' => 0]);
        $tf = $quiz->questions()->create(['prompt' => 'HTTP is stateless.', 'type' => 'true_false', 'options' => ['True', 'False'], 'correct_index' => 0, 'position' => 1]);
        $cb = $quiz->questions()->create(['prompt' => 'Pick the private ranges.', 'type' => 'checkboxes', 'options' => ['10.0.0.0/8', '8.8.8.8', '192.168.0.0/16'], 'correct_index' => 0, 'correct_indexes' => [0, 2], 'position' => 2]);
        $dd = $quiz->questions()->create(['prompt' => 'Default HTTPS port?', 'type' => 'dropdown', 'options' => ['80', '443', '22'], 'correct_index' => 1, 'position' => 3]);

        $this->take($student, $quiz, ['answers' => [
            $mc->id => 1, $tf->id => 0, $cb->id => [2, 0], $dd->id => 1,
        ]])->assertOk()->assertJsonPath('result.score', 4)->assertJsonPath('result.total', 4);
    }

    public function test_checkboxes_need_every_right_answer_and_no_wrong_ones(): void
    {
        [$student, $crew] = $this->setUp2();
        $quiz = $crew->quizzes()->create(['title' => 'Sets', 'published_at' => now()]);
        $cb = $quiz->questions()->create([
            'prompt' => 'Pick the private ranges.', 'type' => 'checkboxes',
            'options' => ['10.0.0.0/8', '8.8.8.8', '192.168.0.0/16'],
            'correct_index' => 0, 'correct_indexes' => [0, 2], 'position' => 0,
        ]);

        // Half right is still wrong — no partial credit.
        $this->take($student, $quiz, ['answers' => [$cb->id => [0]]])
            ->assertOk()->assertJsonPath('result.score', 0);

        $other = $this->studentIn('programming', 'python');
        $this->take($other, $quiz, ['answers' => [$cb->id => [0, 1, 2]]])
            ->assertOk()->assertJsonPath('result.score', 0);
    }

    public function test_the_questions_a_student_is_sent_carry_a_type_but_never_the_answers(): void
    {
        [$student, $crew] = $this->setUp2();
        $quiz = $this->quiz($crew);

        $body = $this->actingAs($student)->postJson(route('student.quiz.start', $quiz))->assertOk()->json();

        $this->assertSame('multiple_choice', $body['questions'][0]['type']);
        $this->assertStringNotContainsString('correct', json_encode($body['questions']));
    }

    public function test_faculty_can_set_a_deadline_and_a_checkbox_question(): void
    {
        $crew = $this->crewFor('programming', 'python');

        $this->actingAs($crew->faculty)->post(route('faculty.quizzes.store', $crew), [
            'title' => 'Week 4', 'minutes' => 20, 'due_at' => now()->addWeek()->toDateTimeString(),
            'questions' => [
                ['prompt' => 'Pick two', 'type' => 'checkboxes', 'options' => ['a', 'b', 'c'], 'correct_many' => [0, 2]],
            ],
        ])->assertSessionHasNoErrors();

        $quiz = $crew->quizzes()->firstOrFail();
        $this->assertNotNull($quiz->due_at);
        $this->assertTrue($quiz->isPublished());
        $this->assertSame([0, 2], $quiz->questions->first()->correct_indexes);
    }

    public function test_a_checkbox_question_with_no_right_answer_is_rejected(): void
    {
        $crew = $this->crewFor('programming', 'python');

        $this->actingAs($crew->faculty)->post(route('faculty.quizzes.store', $crew), [
            'title' => 'Broken', 'minutes' => 20,
            'questions' => [['prompt' => 'Pick two', 'type' => 'checkboxes', 'options' => ['a', 'b'], 'correct_many' => []]],
        ])->assertSessionHasErrors('questions');
    }

    public function test_an_outsider_still_cannot_reach_a_quiz(): void
    {
        [, $crew] = $this->setUp2();
        $quiz = $this->quiz($crew);
        $outsider = User::factory()->create(['role' => 'student']);

        $this->actingAs($outsider)->getJson(route('student.quiz.show', $quiz))->assertForbidden();
    }
}
