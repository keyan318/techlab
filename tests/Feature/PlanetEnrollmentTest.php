<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanetEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private function signUp(array $extra = [])
    {
        return $this->post('/register', $extra + [
            'name' => 'Sam Student',
            'email' => 'sam@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
    }

    public function test_sign_up_page_shows_the_three_planet_checkboxes(): void
    {
        $this->get('/register')->assertOk()
            ->assertSee('value="programming"', false)
            ->assertSee('value="networking"', false)
            ->assertSee('value="cybersecurity"', false);
    }

    public function test_sign_up_needs_at_least_one_planet(): void
    {
        $this->signUp()->assertSessionHasErrors('planets');
        $this->signUp(['planets' => ['pluto']])->assertSessionHasErrors('planets.0');
        $this->assertGuest();
    }

    public function test_student_only_gets_the_planets_they_picked(): void
    {
        $this->signUp(['planets' => ['programming']])->assertRedirect(route('student.dashboard'));

        $user = User::firstWhere('email', 'sam@example.com');
        $this->assertSame(['programming'], $user->planets);

        $this->get(route('student.planet', 'programming'))->assertOk();
        $this->get(route('student.planet', 'networking'))->assertRedirect(route('student.planets'));
        $this->get('/student/planet/cybersecurity/M1/01')->assertRedirect(route('student.planets'));
        $this->get(route('student.planets'))->assertOk()->assertDontSee('Networking Nebula');
    }

    public function test_sign_up_only_accepts_a_single_starting_planet(): void
    {
        $this->signUp(['planets' => ['programming', 'networking', 'cybersecurity']])->assertSessionHasErrors('planets');
        $this->assertGuest();
    }

    public function test_earning_a_gem_unlocks_the_next_planet_in_order(): void
    {
        $this->signUp(['planets' => ['programming']])->assertRedirect(route('student.dashboard'));
        $user = User::firstWhere('email', 'sam@example.com');

        $this->get(route('student.planet', 'networking'))->assertRedirect(route('student.planets'));

        // Complete every lesson in m1 — the first gem, one per finished module.
        foreach (['lesson01', 'lesson02', 'lesson03', 'lesson04', 'lesson05', 'lesson06'] as $lesson) {
            $this->actingAs($user)->postJson(
                route('student.planet.lesson.complete', ['slug' => 'programming', 'module' => 'm1', 'lesson' => $lesson]),
                ['output' => CourseProgressService::expectedFor('m1', $lesson)]
            )->assertOk();
        }

        $this->assertTrue($user->fresh()->isEnrolledIn('networking'));
        $this->assertFalse($user->fresh()->isEnrolledIn('cybersecurity'));
        $this->actingAs($user)->get(route('student.planet', 'networking'))->assertOk();
    }

    public function test_sign_up_cannot_create_a_faculty(): void
    {
        $this->signUp(['planets' => ['programming'], 'role' => 'faculty']);

        $this->assertSame('student', User::firstWhere('email', 'sam@example.com')->role);
    }

    public function test_accounts_from_before_enrollment_keep_every_planet(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->get(route('student.planet', 'networking'))->assertOk();
    }
}
