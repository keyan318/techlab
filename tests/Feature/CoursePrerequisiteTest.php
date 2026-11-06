<?php

namespace Tests\Feature;

use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Season 2 waits for Season 1 when taken Self-paced: Computer Programming 2, Networking 2 and IAS 2
 * can't be started until the student has finished (every lesson done, final exam passed) the course
 * before it. Joining a class skips that: the faculty member decides who belongs.
 */
class CoursePrerequisiteTest extends TestCase
{
    use RefreshDatabase;

    private function student(string $planet): User
    {
        return User::factory()->create(['role' => 'student', 'planets' => [$planet]]);
    }

    /** Every lesson of the course done, and its final exam passed. */
    private function finish(User $user, string $planet, string $course, bool $passFinal = true): void
    {
        $this->enroll($user, $planet, $course);
        $key = CourseProgressService::keyFor($planet, $course);
        foreach (CourseProgressService::order($key) as $item) {
            LessonProgress::create(['user_id' => $user->id, 'course' => $key, 'module' => $item['module'], 'lesson' => $item['lesson'], 'completed_at' => now()]);
        }

        $exam = $this->crewFor($planet, $course)->quizzes()->create(['title' => 'Final', 'published_at' => now(), 'is_final' => true, 'pass_percent' => 50]);
        CrewQuizAttempt::create([
            'crew_quiz_id' => $exam->id, 'user_id' => $user->id, 'score' => $passFinal ? 2 : 0, 'total' => 2, 'answers' => [],
            'status' => $passFinal ? CrewQuizAttempt::PASSED : CrewQuizAttempt::FAILED, 'started_at' => now(), 'finished_at' => now(),
        ]);
    }

    public static function seasons(): array
    {
        return [
            'programming' => ['programming', 'computer-programming-1', 'computer-programming-2', 'Computer Programming 1'],
            'networking' => ['networking', 'networking-fundamentals', 'networking-2', 'Networking 1'],
            'cybersecurity' => ['cybersecurity', 'information-security-1', 'information-assurance-security-2', 'Information Assurance and Security 1'],
        ];
    }

    #[DataProvider('seasons')]
    public function test_season_two_cannot_be_started_self_paced_before_season_one_is_finished(string $planet, string $first, string $second, string $firstTitle): void
    {
        $student = $this->student($planet);

        $this->actingAs($student)->postJson(route('student.course.self-paced', ['planet' => $planet, 'course' => $second]))
            ->assertStatus(422)->assertJsonPath('requires', $first);
        $this->assertNull(Crew::forStudent($student, $planet, $second));

        $this->actingAs($student)->get(route('student.planet', $planet))
            ->assertSee("Finish {$firstTitle} first");
    }

    #[DataProvider('seasons')]
    public function test_finishing_season_one_opens_self_paced(string $planet, string $first, string $second, string $firstTitle): void
    {
        $student = $this->student($planet);
        $this->finish($student, $planet, $first);

        $this->actingAs($student)->postJson(route('student.course.self-paced', ['planet' => $planet, 'course' => $second]))
            ->assertOk()->assertJsonPath('redirect', route('student.course.overview', ['slug' => $planet, 'course' => $second]));
        $this->assertTrue(Crew::forStudent($student, $planet, $second)->isSelfPaced());

        $this->actingAs($student)->get(route('student.planet', $planet))
            ->assertDontSee("Finish {$firstTitle} first");
    }

    public function test_lessons_done_but_final_exam_failed_is_not_finished(): void
    {
        $student = $this->student('networking');
        $this->finish($student, 'networking', 'networking-fundamentals', passFinal: false);

        $this->actingAs($student)->postJson(route('student.course.self-paced', ['planet' => 'networking', 'course' => 'networking-2']))->assertStatus(422);
    }

    /** Join Class skips the prerequisite: whether a student belongs in the class is the faculty member's call. */
    #[DataProvider('seasons')]
    public function test_a_class_can_be_asked_for_and_accepted_without_season_one(string $planet, string $first, string $second, string $firstTitle): void
    {
        $crew = $this->crewFor($planet, $second);
        $student = $this->student($planet);

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))
            ->assertOk()->assertJson(['status' => 'pending']);
        $request = $crew->joinRequests()->where('user_id', $student->id)->firstOrFail();

        $this->actingAs($crew->faculty)->post(route('faculty.join-requests.accept', $request))->assertRedirect(route('faculty.dashboard'));
        $this->assertTrue($student->fresh()->belongsToCrew($crew->id));

        // Once a track is picked, the course card stops advertising the prerequisite it skipped.
        $this->actingAs($student->fresh())->get(route('student.planet', $planet))
            ->assertDontSee("Pre-requisite: {$firstTitle}");
    }

    public function test_the_course_code_works_once_accepted_without_season_one(): void
    {
        $crew = $this->crewFor('cybersecurity', 'information-assurance-security-2');
        $student = $this->student('cybersecurity');
        $crew->joinRequests()->create(['user_id' => $student->id, 'status' => CourseJoinRequest::ACCEPTED]);

        $this->actingAs($student)->postJson(route('student.crew.join'), ['code' => $crew->code])->assertOk();
        $this->assertTrue($student->fresh()->belongsToCrew($crew->id));
    }

    #[DataProvider('seasons')]
    public function test_every_season_two_card_names_season_one_even_without_a_captain(string $planet, string $first, string $second, string $firstTitle): void
    {
        $student = $this->student($planet);

        $this->actingAs($student)->get(route('student.planet', $planet))
            ->assertSee("Pre-requisite: {$firstTitle}")
            ->assertDontSee("Pre-requisite: {$firstTitle} (done)");

        $this->finish($student, $planet, $first);

        $this->actingAs($student)->get(route('student.planet', $planet))
            ->assertSee("Pre-requisite: {$firstTitle} (done)");
    }

    public function test_season_one_has_no_prerequisite(): void
    {
        $student = $this->student('programming');

        $this->actingAs($student)->postJson(route('student.course.self-paced', ['planet' => 'programming', 'course' => 'computer-programming-1']))
            ->assertOk();
    }
}
