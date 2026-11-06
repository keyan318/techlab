<?php

namespace Tests\Feature;

use App\Mail\TemporaryPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function requestTemporary(User $user, string $audience = 'student'): string
    {
        $sent = null;
        Mail::fake();
        $this->post('/forgot-password', ['email' => $user->email, 'audience' => $audience])->assertSessionHas('status');
        Mail::assertSent(TemporaryPassword::class, function (TemporaryPassword $mail) use ($user, &$sent) {
            $sent = $mail->temporary;

            return $mail->hasTo($user->email);
        });

        return $sent;
    }

    public function test_login_pages_link_to_forgot_password(): void
    {
        $this->get('/login')->assertSee(route('password.request'), false);
        $this->get('/faculty/login')->assertSee(route('password.request', ['for' => 'faculty']), false);
        $this->get('/forgot-password')->assertOk()->assertSee('Email me a temporary password');
    }

    public function test_student_gets_a_temporary_password_signs_in_and_chooses_a_new_one(): void
    {
        $student = User::factory()->create(['role' => 'student', 'password' => 'old-password']);
        $temporary = $this->requestTemporary($student);

        $this->post('/login', ['email' => $student->email, 'password' => $temporary])->assertRedirect(route('password.new'));
        $this->assertAuthenticatedAs($student);

        $this->post('/password/new', ['password' => 'brand-new-pass', 'password_confirmation' => 'brand-new-pass'])
            ->assertRedirect(route('student.dashboard'));
        $this->assertTrue(Hash::check('brand-new-pass', $student->fresh()->password));
    }

    public function test_faculty_can_use_a_temporary_password_on_the_faculty_login(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $temporary = $this->requestTemporary($faculty, 'faculty');

        $this->post('/faculty/login', ['email' => $faculty->email, 'password' => $temporary])->assertRedirect(route('password.new'));
        $this->assertAuthenticatedAs($faculty);
    }

    public function test_old_password_still_works_until_the_temporary_one_is_used(): void
    {
        $student = User::factory()->create(['role' => 'student', 'password' => 'old-password']);
        $this->requestTemporary($student);

        $this->post('/login', ['email' => $student->email, 'password' => 'old-password'])->assertRedirect(route('student.dashboard'));
    }

    public function test_temporary_password_works_only_once_and_expires(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $temporary = $this->requestTemporary($student);

        $this->post('/login', ['email' => $student->email, 'password' => $temporary])->assertRedirect(route('password.new'));
        $this->post('/logout');
        $this->post('/login', ['email' => $student->email, 'password' => $temporary])->assertSessionHasErrors('email');

        $temporary = $this->requestTemporary($student);
        $this->travel(61)->minutes();
        $this->post('/login', ['email' => $student->email, 'password' => $temporary])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_asking_twice_keeps_the_first_emails_password_working(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $first = $this->requestTemporary($student);
        $this->requestTemporary($student);

        $this->post('/login', ['email' => $student->email, 'password' => $first])->assertRedirect(route('password.new'));
    }

    public function test_spaces_copied_around_the_temporary_password_are_ignored(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $temporary = $this->requestTemporary($student);

        $this->post('/login', ['email' => $student->email, 'password' => ' '.$temporary.' '])->assertRedirect(route('password.new'));
    }

    public function test_unknown_emails_and_admins_get_the_same_answer_but_no_email(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->post('/forgot-password', ['email' => 'nobody@example.com'])->assertSessionHas('status');
        $this->post('/forgot-password', ['email' => $admin->email])->assertSessionHas('status');

        Mail::assertNothingSent();
    }
}
