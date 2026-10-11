<?php

namespace Tests\Feature;

use App\Mail\FinalExamViolation;
use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * The final exam ("Final Boss"): the faculty writes it, students take it full screen once every
 * lesson is done, leaving it fails them and emails the faculty, and passing it earns the certificate.
 */
class FinalExamTest extends TestCase
{
    use RefreshDatabase;

    private const HUB = ['slug' => 'cybersecurity', 'course' => 'information-security-1'];

    /** @return array{0: User, 1: User, 2: Crew} faculty, student, crew */
    private function setUpCourse(): array
    {
        $faculty = User::factory()->create(['role' => 'faculty', 'name' => 'Prof Reyes']);
        $crew = Crew::create(['name' => 'IAS1', 'code' => 'FIN123', 'teacher_id' => $faculty->id, 'planet' => 'cybersecurity', 'course_slug' => 'information-security-1']);
        $crew->roster()->attach($faculty->id, ['role' => 'faculty']);
        $student = $this->studentIn('cybersecurity', 'information-security-1', ['role' => 'student', 'planets' => ['cybersecurity'], 'name' => 'Rosa Mendez']);

        return [$faculty, $student, $crew];
    }

    private function finishLessons(User $user): void
    {
        foreach (CourseProgressService::order('cybersecurity') as $item) {
            LessonProgress::create(['user_id' => $user->id, 'course' => 'cybersecurity', 'module' => $item['module'], 'lesson' => $item['lesson'], 'completed_at' => now()]);
        }
    }

    private function payload(array $extra = []): array
    {
        return $extra + ['title' => 'Final Exam', 'minutes' => 30, 'is_final' => true, 'pass_percent' => 50, 'questions' => [
            ['prompt' => '2+2?', 'options' => ['3', '4'], 'correct' => 1],
            ['prompt' => 'Sky?', 'options' => ['Blue', 'Red'], 'correct' => 0],
        ]];
    }

    private function publishFinal(User $faculty, Crew $crew): CrewQuiz
    {
        $this->actingAs($faculty)->postJson(route('faculty.quizzes.store', $crew), $this->payload())->assertOk();

        return $crew->finalExam()->firstOrFail();
    }

    public function test_faculty_are_told_to_create_the_final_until_one_is_live(): void
    {
        [$faculty, , $crew] = $this->setUpCourse();
        CourseAssignment::create(['faculty_id' => $faculty->id, 'planet' => 'cybersecurity', 'course_slug' => 'information-security-1']);

        $this->actingAs($faculty)->get(route('faculty.dashboard'))->assertOk()->assertSee('Create the final exam so students can pass');
        $this->actingAs($faculty)->get(route('faculty.course', $crew))->assertOk()->assertSee('Create the final exam for this course');

        $this->actingAs($faculty)->postJson(route('faculty.quizzes.store', $crew), $this->payload(['draft' => true]))->assertOk();
        $this->actingAs($faculty)->get(route('faculty.dashboard'))->assertSee('Final exam is a draft, publish it');

        $final = $crew->finalExam()->firstOrFail();
        $this->actingAs($faculty)->putJson(route('faculty.quizzes.update', $final), $this->payload())->assertOk();
        $this->assertTrue($final->fresh()->isPublished());
        $this->assertSame(50, $final->fresh()->passPercent());

        $this->actingAs($faculty)->get(route('faculty.dashboard'))->assertDontSee('Create the final exam so students can pass')->assertDontSee('Final exam is a draft');
        $this->actingAs($faculty)->get(route('faculty.course', $crew))->assertDontSee('Create the final exam for this course')->assertSee('pass mark 50%');

        // One final per course; the builder sends them to the existing one.
        $this->actingAs($faculty)->postJson(route('faculty.quizzes.store', $crew), $this->payload())->assertStatus(422);
        $this->actingAs($faculty)->get(route('faculty.quizzes.build', ['crew' => $crew, 'final' => 1]))->assertRedirect(route('faculty.quizzes.edit', $final));
    }

    public function test_the_final_is_not_an_ordinary_quiz(): void
    {
        [$faculty, $student, $crew] = $this->setUpCourse();
        $final = $this->publishFinal($faculty, $crew);

        $this->actingAs($student)->get(route('student.course.quiz', self::HUB))->assertOk()->assertDontSee('Final Exam');
        $this->actingAs($student)->getJson(route('student.quiz.show', $final))->assertNotFound();
        $this->actingAs($student)->postJson(route('student.quiz.submit', $final), ['answers' => []])->assertNotFound();
    }

    public function test_the_boss_stays_locked_until_every_lesson_is_done_and_waits_for_the_faculty(): void
    {
        [$faculty, $student, $crew] = $this->setUpCourse();

        $this->actingAs($student)->get(route('student.course.overview', self::HUB))->assertOk()->assertSee('Clear every chapter to face the Final Boss');

        $this->finishLessons($student);
        $this->actingAs($student)->get(route('student.course.overview', self::HUB))->assertSee("hasn't set the final exam yet", false);

        $final = $this->publishFinal($faculty, $crew);
        $this->actingAs($student)->get(route('student.course.overview', self::HUB))->assertSee('Face the boss')->assertSee('score 50% or more to win');
        $this->actingAs($student)->get(route('student.course.final-exam', self::HUB))->assertOk()->assertSee('Rules of the arena');

        // Someone with lessons left cannot start it by calling the endpoint directly.
        $other = $this->studentIn('cybersecurity', 'information-security-1', ['role' => 'student', 'planets' => ['cybersecurity']]);
        $this->actingAs($other)->postJson(route('student.final-exam.start', $final))->assertForbidden();
    }

    public function test_questions_arrive_only_on_start_and_without_answers(): void
    {
        [$faculty, $student, $crew] = $this->setUpCourse();
        $final = $this->publishFinal($faculty, $crew);
        $this->finishLessons($student);

        $this->actingAs($student)->get(route('student.course.final-exam', self::HUB))->assertDontSee('2+2?');

        $start = $this->actingAs($student)->postJson(route('student.final-exam.start', $final))->assertOk()
            ->assertJsonPath('seconds_left', 1800)->assertJsonCount(2, 'questions');
        $this->assertStringNotContainsString('correct', json_encode($start->json('questions')));
        $this->assertSame(CrewQuizAttempt::IN_PROGRESS, $final->attempts()->first()->status);
    }

    public function test_leaving_the_exam_fails_it_and_emails_the_faculty_who_can_allow_a_retake(): void
    {
        Mail::fake();
        [$faculty, $student, $crew] = $this->setUpCourse();
        $final = $this->publishFinal($faculty, $crew);
        $this->finishLessons($student);
        CourseAssignment::create(['faculty_id' => $faculty->id, 'planet' => 'cybersecurity', 'course_slug' => 'information-security-1']);

        $this->actingAs($student)->postJson(route('student.final-exam.start', $final))->assertOk();
        $this->actingAs($student)->post(route('student.final-exam.violation', $final), ['reason' => 'left_fullscreen'], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonPath('result.status', 'failed')->assertJsonPath('result.violation', 'Left full screen');

        Mail::assertQueued(FinalExamViolation::class, fn ($m) => $m->hasTo($faculty->email));
        Mail::assertQueuedCount(1);

        // Answers sent after being caught change nothing; a second report sends no second email.
        $ids = $final->questions->pluck('id');
        $this->actingAs($student)->postJson(route('student.final-exam.submit', $final), ['answers' => [$ids[0] => 1, $ids[1] => 0]])
            ->assertJsonPath('result.status', 'failed');
        $this->actingAs($student)->post(route('student.final-exam.violation', $final), ['reason' => 'switched_away'], ['Accept' => 'application/json'])->assertOk();
        Mail::assertQueuedCount(1);

        $this->actingAs($student)->get(route('student.course.certificate', self::HUB))->assertRedirect(route('student.course.overview', self::HUB));
        $this->actingAs($student)->get(route('student.course.overview', self::HUB))->assertSee('The exam ended early: left full screen');

        // The faculty dashboard flags it until they open the course page.
        $this->actingAs($faculty)->get(route('faculty.dashboard'))->assertSee('Final exam alerts')->assertSee('Rosa Mendez');
        $this->actingAs($faculty)->get(route('faculty.course', $crew))->assertSee('Caught: Left full screen')->assertSee('Allow retake');
        $this->actingAs($faculty)->get(route('faculty.dashboard'))->assertDontSee('Final exam alerts');

        // Only the course's own faculty member can allow a retake.
        $attempt = $final->attempts()->firstOrFail();
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->post(route('faculty.final-exam.retake', $attempt))->assertForbidden();
        $this->actingAs($faculty)->post(route('faculty.final-exam.retake', $attempt))->assertRedirect();
        $this->assertSame(0, $final->attempts()->count());

        // A rewrite is blocked while anyone has a result, and allowed again after the retake cleared it.
        $this->actingAs($student)->postJson(route('student.final-exam.start', $final))->assertOk();
        $this->actingAs($faculty)->putJson(route('faculty.quizzes.update', $final), $this->payload())->assertStatus(422);
    }

    public function test_reloading_mid_exam_counts_as_leaving(): void
    {
        Mail::fake();
        [$faculty, $student, $crew] = $this->setUpCourse();
        $final = $this->publishFinal($faculty, $crew);
        $this->finishLessons($student);

        $this->actingAs($student)->postJson(route('student.final-exam.start', $final))->assertOk();
        // The page came back (reload / reopened tab) instead of reporting anything.
        $this->actingAs($student)->get(route('student.course.final-exam', self::HUB))->assertOk()->assertSee('left or reloaded the exam page');

        $this->assertSame('left_page', $final->attempts()->first()->violation);
        $this->actingAs($student)->postJson(route('student.final-exam.start', $final))->assertStatus(409);
        Mail::assertQueuedCount(1);
    }

    public function test_passing_earns_the_certificate_and_failing_does_not(): void
    {
        [$faculty, $student, $crew] = $this->setUpCourse();
        $final = $this->publishFinal($faculty, $crew);
        $this->finishLessons($student);
        $ids = $final->questions->pluck('id');

        // 1 of 2 = 50%, exactly the pass mark.
        $this->actingAs($student)->postJson(route('student.final-exam.start', $final))->assertOk();
        $this->actingAs($student)->postJson(route('student.final-exam.submit', $final), ['answers' => [$ids[0] => 1, $ids[1] => 1]])
            ->assertOk()->assertJsonPath('result.status', 'passed')->assertJsonPath('result.percent', 50)
            ->assertJsonMissingPath('result.review');

        $this->actingAs($student)->get(route('student.course.certificate', self::HUB))->assertOk()->assertSee('Rosa Mendez')->assertSee('Prof Reyes');
        $this->actingAs($faculty)->get(route('faculty.course', $crew))->assertSee('Passed · 50%');

        $other = $this->studentIn('cybersecurity', 'information-security-1', ['role' => 'student', 'planets' => ['cybersecurity']]);
        $this->finishLessons($other);
        $this->actingAs($other)->postJson(route('student.final-exam.start', $final))->assertOk();
        $this->actingAs($other)->postJson(route('student.final-exam.submit', $final), ['answers' => [$ids[0] => 0]])
            ->assertJsonPath('result.status', 'failed')->assertJsonPath('result.percent', 0);
        $this->actingAs($other)->get(route('student.course.certificate', self::HUB))->assertRedirect(route('student.course.overview', self::HUB));
    }

    public function test_answers_after_the_clock_ran_out_fail(): void
    {
        Mail::fake();
        [$faculty, $student, $crew] = $this->setUpCourse();
        $final = $this->publishFinal($faculty, $crew);
        $this->finishLessons($student);
        $ids = $final->questions->pluck('id');

        $this->actingAs($student)->postJson(route('student.final-exam.start', $final))->assertOk();
        $this->travel(32)->minutes();

        $this->actingAs($student)->postJson(route('student.final-exam.submit', $final), ['answers' => [$ids[0] => 1, $ids[1] => 0]])
            ->assertJsonPath('result.status', 'failed')->assertJsonPath('result.violation', 'Submitted after the time ran out');
    }
}
