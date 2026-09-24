<?php

namespace Tests\Feature;

use App\Models\CourseAssignment;
use App\Models\CourseJoinRequest;
use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A course only locks once a faculty captain has actually generated a code for it —
 * a course nobody has claimed yet behaves exactly as it did before this feature
 * existed (see PlanetController::courses()). This suite covers the claimed case.
 */
class CourseGatingTest extends TestCase
{
    use RefreshDatabase;

    private function student(): User
    {
        return User::factory()->create(['role' => 'student', 'planets' => ['networking']]);
    }

    private function captainedCrew(string $planet = 'networking', string $courseSlug = 'networking-fundamentals', string $facultyName = 'Joy Santos'): Crew
    {
        $faculty = User::factory()->create(['role' => 'faculty', 'name' => $facultyName]);
        $assignment = CourseAssignment::create(['planet' => $planet, 'course_slug' => $courseSlug, 'faculty_id' => $faculty->id]);
        $this->actingAs($faculty)->post(route('faculty.assignments.generate-code', $assignment));

        return Crew::where('planet', $planet)->where('course_slug', $courseSlug)->firstOrFail();
    }

    /** The faculty member has accepted this student (see CourseJoinRequestTest for the real flow). */
    private function accepted(Crew $crew, User $student): void
    {
        CourseJoinRequest::create(['crew_id' => $crew->id, 'user_id' => $student->id, 'status' => CourseJoinRequest::ACCEPTED]);
    }

    public function test_a_course_nobody_has_claimed_stays_open(): void
    {
        $this->actingAs($this->student())
            ->get(route('student.planet.play', ['slug' => 'networking', 'course' => 'networking-fundamentals']))
            ->assertOk();
    }

    public function test_a_captained_course_shows_who_to_ask_and_stays_locked_until_the_code_is_redeemed(): void
    {
        $crew = $this->captainedCrew();
        $student = $this->student();

        $this->actingAs($student)->get(route('student.planet', 'networking'))
            ->assertOk()->assertSee('Joy Santos decides who joins')->assertSee('Ask to join');

        $this->actingAs($student)
            ->get(route('student.planet.play', ['slug' => 'networking', 'course' => 'networking-fundamentals']))
            ->assertRedirect(route('student.planet', 'networking'));

        $this->accepted($crew, $student);
        $this->actingAs($student)->postJson(route('student.crew.join'), ['code' => $crew->code])->assertOk();

        $this->actingAs($student)
            ->get(route('student.planet.play', ['slug' => 'networking', 'course' => 'networking-fundamentals']))
            ->assertOk();
    }

    public function test_one_courses_code_does_not_unlock_a_different_course(): void
    {
        $networking = $this->captainedCrew('networking', 'networking-fundamentals', 'Joy');
        $cyberFaculty = User::factory()->create(['role' => 'faculty', 'name' => 'Abegail']);
        $cyberAssignment = CourseAssignment::create(['planet' => 'cybersecurity', 'course_slug' => 'information-security-1', 'faculty_id' => $cyberFaculty->id]);
        $this->actingAs($cyberFaculty)->post(route('faculty.assignments.generate-code', $cyberAssignment));

        $student = User::factory()->create(['role' => 'student', 'planets' => ['networking', 'cybersecurity']]);
        $this->accepted($networking, $student);
        $this->actingAs($student)->postJson(route('student.crew.join'), ['code' => $networking->code])->assertOk();

        // Networking is unlocked, but Information Security 1 — a different captain's course — is not.
        $this->actingAs($student)
            ->get(route('student.planet.play', ['slug' => 'networking', 'course' => 'networking-fundamentals']))
            ->assertOk();
        $this->actingAs($student)
            ->get(route('student.planet.play', ['slug' => 'cybersecurity', 'course' => 'information-security-1']))
            ->assertRedirect(route('student.planet', 'cybersecurity'));
    }
}
