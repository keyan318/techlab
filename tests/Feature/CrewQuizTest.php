<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrewQuizTest extends TestCase
{
    use RefreshDatabase;

    private function setUpCrew(): array
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $crew = Crew::create(['name' => 'Alpha', 'code' => 'ABC123', 'teacher_id' => $faculty->id]);
        $faculty->update(['crew_id' => $crew->id]);
        $crew->roster()->attach($faculty->id, ['role' => 'faculty']);
        $student = User::factory()->create(['role' => 'student', 'crew_id' => $crew->id]);
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

        $this->actingAs($student)->get(route('student.crew'))->assertOk()->assertSee('Basics')->assertSee('No graded work yet');

        $show = $this->actingAs($student)->getJson(route('student.quiz.show', $quiz))->assertOk();
        $this->assertStringNotContainsString('correct', json_encode($show->json('questions')));

        $ids = $quiz->questions->pluck('id');
        $this->actingAs($student)->postJson(route('student.quiz.submit', $quiz), ['answers' => [$ids[0] => 1, $ids[1] => 2]])
            ->assertOk()->assertJsonPath('result.score', 1)->assertJsonPath('result.percent', 50);

        // A second submit cannot change the grade.
        $this->actingAs($student)->postJson(route('student.quiz.submit', $quiz), ['answers' => [$ids[0] => 1, $ids[1] => 0]])
            ->assertJsonPath('result.score', 1);

        $this->actingAs($student)->get(route('student.crew'))->assertSee('50%');
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
}
