<?php

namespace Tests\Feature;

use App\Mail\JoinRequestAccepted;
use App\Mail\JoinRequestDeclined;
use App\Models\CourseAssignment;
use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Ask to join → faculty accepts (the code is emailed) or declines (the student can
 * ask again). See CourseGatingTest for what happens once the code is redeemed.
 */
class CourseJoinRequestTest extends TestCase
{
    use RefreshDatabase;

    private function student(array $attrs = []): User
    {
        return User::factory()->create($attrs + ['role' => 'student', 'planets' => ['networking']]);
    }

    private function captainedCrew(string $planet = 'networking', string $courseSlug = 'networking-fundamentals', string $facultyName = 'Joy Santos'): Crew
    {
        $faculty = User::factory()->create(['role' => 'faculty', 'name' => $facultyName]);
        $assignment = CourseAssignment::create(['planet' => $planet, 'course_slug' => $courseSlug, 'faculty_id' => $faculty->id]);
        $this->actingAs($faculty)->post(route('faculty.assignments.generate-code', $assignment));

        return Crew::where('planet', $planet)->where('course_slug', $courseSlug)->firstOrFail();
    }

    public function test_asking_to_join_creates_a_pending_request_and_the_card_shows_it(): void
    {
        $crew = $this->captainedCrew();
        $student = $this->student();

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))
            ->assertOk()->assertJson(['status' => 'pending']);

        $this->assertDatabaseHas('course_join_requests', [
            'crew_id' => $crew->id, 'user_id' => $student->id, 'status' => 'pending',
        ]);

        $this->actingAs($student)->get(route('student.planet', 'networking'))
            ->assertSee('Waiting for Joy Santos to accept you');
    }

    public function test_the_code_is_refused_before_acceptance(): void
    {
        $crew = $this->captainedCrew();
        $student = $this->student();

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))->assertOk();

        $this->actingAs($student)->postJson(route('student.crew.join'), ['code' => $crew->code])
            ->assertStatus(422)->assertJsonFragment(['message' => "Ask to join {$crew->name} first. Your code works once Joy Santos accepts you."]);

        $this->assertFalse($student->fresh()->belongsToCrew($crew->id));
    }

    public function test_accepting_emails_the_code_and_lets_the_student_redeem_it(): void
    {
        Mail::fake();
        $crew = $this->captainedCrew();
        $student = $this->student();
        $faculty = $crew->faculty;

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))->assertOk();
        $joinRequest = CourseJoinRequest::where('crew_id', $crew->id)->where('user_id', $student->id)->firstOrFail();

        $this->actingAs($faculty)->post(route('faculty.join-requests.accept', $joinRequest))
            ->assertRedirect(route('faculty.dashboard'));

        $this->assertSame('accepted', $joinRequest->fresh()->status);
        Mail::assertQueued(JoinRequestAccepted::class, function (JoinRequestAccepted $mail) use ($student, $crew) {
            return $mail->hasTo($student->email) && str_contains($mail->render(), $crew->code);
        });

        $this->actingAs($student)->postJson(route('student.crew.join'), ['code' => $crew->code])->assertOk();
        $this->assertTrue($student->fresh()->belongsToCrew($crew->id));
    }

    public function test_declining_emails_the_student_and_they_can_ask_again(): void
    {
        Mail::fake();
        $crew = $this->captainedCrew();
        $student = $this->student();
        $faculty = $crew->faculty;

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))->assertOk();
        $joinRequest = CourseJoinRequest::where('crew_id', $crew->id)->where('user_id', $student->id)->firstOrFail();

        $this->actingAs($faculty)->post(route('faculty.join-requests.decline', $joinRequest))
            ->assertRedirect(route('faculty.dashboard'));

        $this->assertSame('declined', $joinRequest->fresh()->status);
        Mail::assertQueued(JoinRequestDeclined::class, fn (JoinRequestDeclined $mail) => $mail->hasTo($student->email));

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))
            ->assertOk()->assertJson(['status' => 'pending']);
        $this->assertSame('pending', $joinRequest->fresh()->status);
    }

    public function test_a_different_faculty_member_cannot_accept_or_decline(): void
    {
        $crew = $this->captainedCrew();
        $student = $this->student();
        $outsider = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))->assertOk();
        $joinRequest = CourseJoinRequest::where('crew_id', $crew->id)->firstOrFail();

        $this->actingAs($outsider)->post(route('faculty.join-requests.accept', $joinRequest))->assertForbidden();
        $this->actingAs($outsider)->post(route('faculty.join-requests.decline', $joinRequest))->assertForbidden();
    }

    public function test_a_student_not_enrolled_in_the_planet_cannot_ask(): void
    {
        $crew = $this->captainedCrew();
        $student = $this->student(['planets' => ['programming']]);

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))->assertForbidden();
    }

    public function test_lesson_urls_and_the_course_overview_stay_locked_until_accepted(): void
    {
        $crew = $this->captainedCrew();
        $student = $this->student();

        $lessonUrl = route('student.planet.module.lesson', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson01']);
        $overviewUrl = route('student.course.overview', ['slug' => 'networking', 'course' => 'networking-fundamentals']);

        $this->actingAs($student)->get($lessonUrl)->assertRedirect(route('student.planet', 'networking'));
        $this->actingAs($student)->get($overviewUrl)->assertRedirect(route('student.planet', 'networking'));

        $this->actingAs($student)->postJson(route('student.join-request.store', $crew))->assertOk();
        $joinRequest = CourseJoinRequest::where('crew_id', $crew->id)->firstOrFail();
        $this->actingAs($crew->faculty)->post(route('faculty.join-requests.accept', $joinRequest));
        $this->actingAs($student)->postJson(route('student.crew.join'), ['code' => $crew->code])->assertOk();

        $this->actingAs($student)->get($lessonUrl)->assertOk();
        $this->actingAs($student)->get($overviewUrl)->assertOk();
    }
}
