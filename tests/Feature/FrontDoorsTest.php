<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontDoorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_faculty_landing_page_is_public_and_hidden_from_search_engines(): void
    {
        $this->get('/faculty')->assertOk()
            ->assertSee('Faculty sign in')
            ->assertSee('noindex', false);
    }

    public function test_faculty_login_links_to_faculty_sign_up_not_the_student_one(): void
    {
        $this->get('/faculty/login')->assertOk()
            ->assertSee('/faculty/register', false)
            ->assertDontSee('href="/register"', false);
    }

    private function facultySignUp(string $email)
    {
        return $this->post('/faculty/register', [
            'name' => 'Prof Cruz', 'email' => $email,
            'password' => 'password123', 'password_confirmation' => 'password123',
        ]);
    }

    public function test_faculty_can_sign_up_with_a_school_email(): void
    {
        $this->get('/faculty/register')->assertOk();

        $this->facultySignUp('jcruz@catsu.edu.ph')->assertRedirect(route('faculty.dashboard'));

        $this->assertSame('faculty', User::firstWhere('email', 'jcruz@catsu.edu.ph')->role);
        $this->assertAuthenticated();
    }

    public function test_faculty_sign_up_refuses_other_email_domains(): void
    {
        $this->facultySignUp('someone@gmail.com')->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'someone@gmail.com']);
    }

    public function test_the_school_domain_check_can_be_switched_off(): void
    {
        config(['faculty.email_domain' => '']);

        $this->facultySignUp('someone@gmail.com')->assertRedirect(route('faculty.dashboard'));
    }

    public function test_student_pages_no_longer_offer_a_faculty_option(): void
    {
        $this->get('/register')->assertOk()->assertDontSee('data-role', false);
        $this->get('/login')->assertOk()->assertDontSee('data-role', false);
    }

    public function test_faculty_signs_in_at_the_faculty_door(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty', 'password' => 'secret-pass']);

        $this->post('/faculty/login', ['email' => $faculty->email, 'password' => 'secret-pass'])
            ->assertRedirect(route('faculty.dashboard'));
        $this->assertAuthenticatedAs($faculty);
    }

    public function test_a_student_cannot_sign_in_at_the_faculty_or_admin_door(): void
    {
        $student = User::factory()->create(['role' => 'student', 'password' => 'secret-pass']);

        foreach (['/faculty/login', '/admin/login'] as $door) {
            $this->post($door, ['email' => $student->email, 'password' => 'secret-pass'])
                ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
            $this->assertGuest();
        }
    }

    public function test_only_admins_get_through_the_admin_door_and_panel(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'secret-pass']);
        $faculty = User::factory()->create(['role' => 'faculty']);

        $this->get('/admin/login')->assertOk()->assertSee('noindex', false);
        $this->get('/admin')->assertRedirect(route('login'));
        $this->actingAs($faculty)->get('/admin')->assertForbidden();
        auth()->logout();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'secret-pass'])
            ->assertRedirect(route('admin.dashboard'));
        $this->get('/admin')->assertOk();
    }

    public function test_signed_in_users_are_sent_home_from_the_faculty_page(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($faculty)->get('/faculty')->assertRedirect(route('faculty.dashboard'));
    }

    public function test_old_teacher_links_redirect_to_faculty(): void
    {
        $this->get('/teacher/dashboard')->assertRedirect('/faculty/dashboard')->assertStatus(301);
    }

    public function test_the_student_landing_links_to_the_faculty_page(): void
    {
        $this->get('/')->assertOk()->assertSee('href="'.url('/faculty').'"', false);
    }
}
