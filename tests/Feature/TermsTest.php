<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TermsTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_read_the_terms(): void
    {
        $this->get(route('terms'))->assertOk()
            ->assertSee('TechLab Terms')
            ->assertSee('Astro is an AI')
            ->assertDontSee('I have read and agree');
    }

    public function test_a_new_student_agrees_before_onboarding(): void
    {
        $this->post('/register', [
            'first_name' => 'Nova', 'last_name' => 'Reyes', 'email' => 'nova@example.com', 'password' => 'password123',
            'password_confirmation' => 'password123', 'planets' => ['networking'], 'program' => 'IT', 'year_level' => 1,
        ])->assertRedirect(route('student.welcome'));

        $this->get(route('student.welcome'))->assertRedirect(route('terms'));
        $this->get(route('terms'))->assertOk()->assertSee('Before we launch, Nova!')->assertSee('I have read and agree');

        // Not ticking the box doesn't count.
        $this->post(route('terms.accept'))->assertSessionHasErrors('agree');
        $this->assertNull(User::first()->terms_accepted_at);

        $this->post(route('terms.accept'), ['agree' => 1])->assertRedirect(route('student.welcome'));
        $user = User::first();
        $this->assertNotNull($user->terms_accepted_at);
        $this->assertSame(config('terms.version'), $user->terms_version);
        $this->get(route('student.welcome'))->assertOk();
    }

    public function test_a_new_captain_agrees_before_onboarding(): void
    {
        $domain = config('faculty.email_domain') ?: 'example.com';
        $this->post('/faculty/register', [
            'name' => 'New Captain', 'email' => "captain@{$domain}",
            'password' => 'secret-pass', 'password_confirmation' => 'secret-pass',
        ])->assertRedirect(route('faculty.welcome'));

        $this->get(route('faculty.welcome'))->assertRedirect(route('terms'));
        $this->get(route('terms'))->assertSee('Captain rules');
        $this->post(route('terms.accept'), ['agree' => 1])->assertRedirect(route('faculty.welcome'));
    }

    public function test_json_requests_are_refused_until_agreed_and_logout_still_works(): void
    {
        $user = User::factory()->withoutTerms()->create(['role' => 'student']);

        $this->actingAs($user)->getJson(route('student.dashboard'))->assertForbidden()->assertJsonPath('terms', route('terms'));
        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('home'));
    }

    public function test_a_new_terms_version_asks_everyone_again_but_not_admins(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->withoutTerms()->create(['role' => 'admin']);

        config(['terms.version' => '2099-01-01']);

        $this->actingAs($student)->get(route('student.dashboard'))->assertRedirect(route('terms'));
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }
}
