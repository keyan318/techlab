<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_lands_on_dashboard_after_login(): void
    {
        $user = User::factory()->create(['role' => 'student', 'password' => bcrypt('secret-pass')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass'])
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_teacher_lands_on_teacher_dashboard_after_login(): void
    {
        $user = User::factory()->create(['role' => 'teacher', 'password' => bcrypt('secret-pass')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass'])
            ->assertRedirect(route('teacher.dashboard'));
    }

    public function test_signed_in_student_visiting_login_is_sent_to_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->get('/login')->assertRedirect(route('student.dashboard'));
    }
}
