<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrewQuizTest extends TestCase
{
    use RefreshDatabase;

    private function setUpCrew(): array
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        // Bound to a real catalog course so the student's course hub is reachable.
        $crew = Crew::create(['name' => 'Alpha', 'code' => 'ABC123', 'teacher_id' => $faculty->id, 'planet' => 'programming', 'course_slug' => 'python']);
        $faculty->update(['crew_id' => $crew->id]);
        $crew->roster()->attach($faculty->id, ['role' => 'faculty']);
        $student = User::factory()->create(['role' => 'student', 'crew_id' => $crew->id, 'planets' => ['programming']]);
        $crew->roster()->attach($student->id, ['role' => 'student']);

        return [$faculty, $student, $crew];
    }

    private function payload(): array
    {
        return ['title' => 'Basics', 'minutes' => 10, 'questions' => [
            ['prompt' => '2+2?', 'options' => ['3', '4'], 'correct' => 1],
            ['prompt' => 'Sky?', 'options' => ['Blue', 'Green', 'Red'], 'correct' => 0],
        ]];
    }

    public function test_faculty_creates_quiz_and_student_sees_no_answers_then_is_graded(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();

        $this->actingAs($faculty)->post(route('faculty.quizzes.store', $crew), $this->payload())->assertRedirect();
        $quiz = $crew->quizzes()->firstOrFail();

        $this->actingAs($student)->get(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']))->assertOk()->assertSee('Basics')->assertSee('2 questions');

        // Before Start there are no questions to read; Start hands them over without answers.
        $this->actingAs($student)->getJson(route('student.quiz.show', $quiz))->assertOk()
            ->assertJsonMissingPath('questions')->assertJsonPath('question_count', 2)->assertJsonPath('result', null);
        $start = $this->actingAs($student)->postJson(route('student.quiz.start', $quiz))->assertOk();
        $this->assertCount(2, $start->json('questions'));
        $this->assertStringNotContainsString('correct', json_encode($start->json('questions')));

        $ids = $quiz->questions->pluck('id');
        $this->actingAs($student)->postJson(route('student.quiz.submit', $quiz), ['answers' => [$ids[0] => 1, $ids[1] => 2]])
            ->assertOk()->assertJsonPath('result.score', 1)->assertJsonPath('result.percent', 50);

        // A second submit cannot change the grade.
        $this->actingAs($student)->postJson(route('student.quiz.submit', $quiz), ['answers' => [$ids[0] => 1, $ids[1] => 0]])
            ->assertJsonPath('result.score', 1);

        $this->actingAs($student)->get(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']))->assertSee('50%');
    }

    public function test_other_crews_students_and_students_cannot_touch_quizzes(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();
        $this->actingAs($faculty)->post(route('faculty.quizzes.store', $crew), $this->payload());
        $quiz = $crew->quizzes()->firstOrFail();

        $outsider = User::factory()->create(['role' => 'student', 'crew_id' => null]);
        $this->actingAs($outsider)->getJson(route('student.quiz.show', $quiz))->assertForbidden();
        $this->actingAs($student)->post(route('faculty.quizzes.store', $crew), $this->payload())->assertForbidden();
        $this->actingAs($student)->delete(route('faculty.quizzes.destroy', $quiz))->assertForbidden();
    }

    public function test_quiz_needs_a_valid_correct_answer(): void
    {
        [$faculty, , $crew] = $this->setUpCrew();
        $bad = $this->payload();
        $bad['questions'][0]['correct'] = 5;
        $this->actingAs($faculty)->post(route('faculty.quizzes.store', $crew), $bad)->assertSessionHasErrors('questions');
    }

    private function publishedQuiz(Crew $crew, User $faculty, array $overrides = []): CrewQuiz
    {
        $this->actingAs($faculty)->post(route('faculty.quizzes.store', $crew), array_merge($this->payload(), $overrides))->assertRedirect();

        return $crew->quizzes()->firstOrFail();
    }

    public function test_a_quiz_needs_a_time_limit(): void
    {
        [$faculty, , $crew] = $this->setUpCrew();
        $payload = $this->payload();
        unset($payload['minutes']);

        $this->actingAs($faculty)->postJson(route('faculty.quizzes.store', $crew), $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('minutes');
    }

    public function test_a_quiz_cannot_be_handed_in_without_pressing_start(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();
        $quiz = $this->publishedQuiz($crew, $faculty);

        $this->actingAs($student)->postJson(route('student.quiz.submit', $quiz), ['answers' => []])->assertUnprocessable();
        $this->assertSame(0, $quiz->attempts()->count());
    }

    public function test_the_clock_keeps_running_when_the_page_is_closed(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();
        $quiz = $this->publishedQuiz($crew, $faculty);
        $this->freezeTime();

        $this->actingAs($student)->postJson(route('student.quiz.start', $quiz))->assertJsonPath('seconds_left', 600);

        // Four minutes later they open it again: same attempt, the clock was not reset.
        $this->travel(4)->minutes();
        $this->actingAs($student)->getJson(route('student.quiz.show', $quiz))->assertJsonPath('started', true)->assertJsonPath('seconds_left', 360);
        $this->actingAs($student)->postJson(route('student.quiz.start', $quiz))->assertJsonPath('seconds_left', 360);
        $this->assertSame(1, $quiz->attempts()->count());
    }

    public function test_once_time_is_up_the_saved_answers_are_what_count(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();
        $quiz = $this->publishedQuiz($crew, $faculty);
        $ids = $quiz->questions->pluck('id');

        $this->actingAs($student)->postJson(route('student.quiz.start', $quiz));
        $this->actingAs($student)->putJson(route('student.quiz.answers', $quiz), ['answers' => [$ids[0] => 1]])->assertOk();

        // Back long after the clock ran out, trying to hand in a perfect paper.
        $this->travel(30)->minutes();
        $this->actingAs($student)->postJson(route('student.quiz.submit', $quiz), ['answers' => [$ids[0] => 1, $ids[1] => 0]])
            ->assertOk()->assertJsonPath('result.score', 1)->assertJsonPath('result.total', 2);

        // And no second try.
        $this->actingAs($student)->postJson(route('student.quiz.start', $quiz))->assertJsonPath('result.score', 1);
    }

    public function test_an_abandoned_quiz_is_graded_once_its_time_is_up(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();
        $quiz = $this->publishedQuiz($crew, $faculty);
        $ids = $quiz->questions->pluck('id');

        $this->actingAs($student)->postJson(route('student.quiz.start', $quiz));
        $this->actingAs($student)->putJson(route('student.quiz.answers', $quiz), ['answers' => [$ids[0] => 1, $ids[1] => 0]]);
        $this->travel(30)->minutes();

        $quiz->closeExpiredAttempts();
        $attempt = $quiz->attempts()->first();
        $this->assertNotSame(\App\Models\CrewQuizAttempt::IN_PROGRESS, $attempt->status);
        $this->assertSame(2, (int) $attempt->score);
    }

    public function test_the_review_after_handing_in_shows_every_answer(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();
        $quiz = $this->publishedQuiz($crew, $faculty);
        $ids = $quiz->questions->pluck('id');

        $this->actingAs($student)->postJson(route('student.quiz.start', $quiz));
        $review = $this->actingAs($student)->postJson(route('student.quiz.submit', $quiz), ['answers' => [$ids[0] => 0, $ids[1] => 0]])
            ->json('result.review');

        $this->assertSame('2+2?', $review[0]['prompt']);
        $this->assertSame(['3', '4'], $review[0]['options']);
        $this->assertSame(0, $review[0]['chosen']);
        $this->assertSame(1, $review[0]['correct']);
        $this->assertFalse($review[0]['right']);
        $this->assertTrue($review[1]['right']);
    }

    public function test_a_questions_source_is_kept_for_the_faculty_and_never_sent_to_students(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();
        $payload = $this->payload();
        $payload['questions'][0]['source'] = 'Lesson 2.2 · TCP';
        $quiz = $this->publishedQuiz($crew, $faculty, $payload);

        $this->assertSame('Lesson 2.2 · TCP', $quiz->questions->first()->source);
        $this->actingAs($faculty)->get(route('faculty.quizzes.edit', $quiz))->assertSee('Lesson 2.2', false);

        $start = $this->actingAs($student)->postJson(route('student.quiz.start', $quiz));
        $this->assertStringNotContainsString('Lesson 2.2', $start->getContent());
    }
}
