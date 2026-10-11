<?php

namespace Tests\Feature;

use App\Http\Controllers\CourseCertificateController;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * The certificate: earned by finishing every lesson of a course and then passing the faculty's
 * final exam, linked from the course hub.
 */
class CourseCertificateTest extends TestCase
{
    use RefreshDatabase;

    private const HUB = ['slug' => 'cybersecurity', 'course' => 'information-security-1'];

    private function student(array $attrs = []): User
    {
        return $this->studentIn('cybersecurity', 'information-security-1', $attrs + ['role' => 'student', 'planets' => ['cybersecurity']]);
    }

    /** Clears every Defense Lab in order, through the same endpoint the lab page uses. */
    private function finishCourse(User $user): void
    {
        foreach (CourseProgressService::order('cybersecurity') as $item) {
            $this->actingAs($user)->postJson(
                route('student.planet.lab.complete', ['slug' => 'cybersecurity', 'module' => $item['module'], 'lesson' => $item['lesson']]),
                ['lab' => $item['lab']],
            )->assertOk();
        }
    }

    /** The course's faculty member publishes a two-question final exam with a 50% pass mark. */
    private function publishFinal(): CrewQuiz
    {
        $crew = $this->crewFor('cybersecurity', 'information-security-1');
        if ($existing = $crew->finalExam()->first()) {
            return $existing;
        }

        $quiz = $crew->quizzes()->create(['title' => 'Final Exam', 'published_at' => now(), 'is_final' => true, 'pass_percent' => 50]);
        $quiz->questions()->create(['prompt' => '2+2?', 'type' => 'multiple_choice', 'options' => ['3', '4'], 'correct_index' => 1, 'position' => 0]);
        $quiz->questions()->create(['prompt' => 'Sky?', 'type' => 'multiple_choice', 'options' => ['Blue', 'Red'], 'correct_index' => 0, 'position' => 1]);

        return $quiz;
    }

    /** Sits the final exam through the same endpoints the exam page uses, and gets every answer right. */
    private function passFinal(User $user): void
    {
        $quiz = $this->publishFinal();
        $this->actingAs($user)->postJson(route('student.final-exam.start', $quiz))->assertOk();
        $ids = $quiz->questions->pluck('id');
        $this->actingAs($user)->postJson(route('student.final-exam.submit', $quiz), ['answers' => [$ids[0] => 1, $ids[1] => 0]])
            ->assertOk()->assertJsonPath('result.status', 'passed');
    }

    public function test_the_certificate_is_locked_until_every_lesson_is_done(): void
    {
        $user = $this->student();
        $this->actingAs($user)->postJson(
            route('student.planet.lab.complete', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson01']),
            ['lab' => 'c1-l1'],
        )->assertOk();

        $this->actingAs($user)->get(route('student.course.certificate', self::HUB))
            ->assertRedirect(route('student.course.overview', self::HUB));

        $total = count(CourseProgressService::order('cybersecurity'));
        $this->actingAs($user)->get(route('student.course.overview', self::HUB))
            ->assertOk()
            ->assertSee("Finish all {$total} lessons, then pass the final exam to earn it.")
            ->assertDontSee(route('student.course.certificate', self::HUB), false);
    }

    public function test_finishing_the_lessons_alone_does_not_earn_it(): void
    {
        $user = $this->student();
        $this->finishCourse($user);
        $this->publishFinal();

        $this->actingAs($user)->get(route('student.course.certificate', self::HUB))
            ->assertRedirect(route('student.course.overview', self::HUB));
        $this->actingAs($user)->get(route('student.course.overview', self::HUB))
            ->assertOk()
            ->assertSee('All lessons done! Pass the final exam to earn it.')
            ->assertSee('Face the Final Boss')
            ->assertDontSee(route('student.course.certificate', self::HUB), false);
    }

    public function test_finishing_the_course_and_passing_the_final_earns_the_certificate(): void
    {
        $user = $this->student(['name' => 'Rosa Mendez']);
        $this->finishCourse($user);
        $this->passFinal($user);

        $this->actingAs($user)->get(route('student.course.overview', self::HUB))
            ->assertOk()
            ->assertSee('View Certificate')
            ->assertSee(route('student.course.certificate', self::HUB), false);

        $this->actingAs($user)->get(route('student.course.certificate', self::HUB))
            ->assertOk()
            ->assertSee('CERTIFICATE')
            ->assertSee('Rosa Mendez')
            ->assertSee('Information Assurance and Security 1')
            ->assertSee('ITP1232')
            ->assertSee('Cybersecurity Citadel')
            ->assertSee(CourseCertificateController::certificateId($user, 'cybersecurity', 'ITP1232'));
    }

    public function test_the_certificate_keeps_its_issue_date_and_number(): void
    {
        $user = $this->student();
        $this->finishCourse($user);
        $this->passFinal($user);
        LessonProgress::where('user_id', $user->id)->update(['completed_at' => Carbon::parse('2026-03-01 09:00')]);
        CrewQuizAttempt::where('user_id', $user->id)->update(['finished_at' => Carbon::parse('2026-03-14 16:30')]);

        // Issued when the final exam was passed, not whenever the page is opened.
        $this->actingAs($user)->get(route('student.course.certificate', self::HUB))->assertSee('March 14, 2026');

        // One number per student and course, the same on every visit, different for anyone else.
        $id = CourseCertificateController::certificateId($user, 'cybersecurity', 'ITP1232');
        $this->assertMatchesRegularExpression('/^TL-ITP1232-[0-9A-F]{4}-[0-9A-F]{4}$/', $id);
        $this->assertSame($id, CourseCertificateController::certificateId($user->fresh(), 'cybersecurity', 'ITP1232'));
        $this->assertNotSame($id, CourseCertificateController::certificateId($this->student(), 'cybersecurity', 'ITP1232'));
    }

    public function test_another_courses_progress_does_not_count(): void
    {
        $user = $this->student();
        $this->finishCourse($user);
        $this->enroll($user, 'cybersecurity', 'information-assurance-security-2');

        $this->assertNull(CourseProgressService::finishedAt($user, 'information-assurance-security-2'));
        $this->actingAs($user)
            ->get(route('student.course.certificate', ['slug' => 'cybersecurity', 'course' => 'information-assurance-security-2']))
            ->assertRedirect(route('student.course.overview', ['slug' => 'cybersecurity', 'course' => 'information-assurance-security-2']));
    }

    public function test_a_student_outside_the_course_cannot_open_it(): void
    {
        $outsider = User::factory()->create(['role' => 'student', 'planets' => ['cybersecurity']]);

        $this->actingAs($outsider)->get(route('student.course.certificate', self::HUB))
            ->assertRedirect(route('student.planet', 'cybersecurity'));
    }
}
