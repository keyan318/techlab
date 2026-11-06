<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_saves_an_admin_who_can_then_sign_in(): void
    {
        $this->artisan('admin:create', ['email' => 'boss@school.edu'])
            ->expectsQuestion('Password (min 12 characters, hidden while you type)', 'a-long-password-1')
            ->expectsQuestion('Type the same password again', 'a-long-password-1')
            ->assertSuccessful();

        $this->post('/admin/login', ['email' => 'boss@school.edu', 'password' => 'a-long-password-1'])
            ->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_create_saves_nothing_when_the_two_passwords_differ(): void
    {
        $this->artisan('admin:create', ['email' => 'boss@school.edu'])
            ->expectsQuestion('Password (min 12 characters, hidden while you type)', 'a-long-password-1')
            ->expectsQuestion('Type the same password again', 'a-long-pasword-1')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'boss@school.edu']);
    }

    public function test_password_resets_an_admin_so_the_new_one_works_and_the_old_one_does_not(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'old-password-123']);

        $this->artisan('admin:password', ['email' => $admin->email])
            ->expectsQuestion('Password (min 12 characters, hidden while you type)', 'new-password-456')
            ->expectsQuestion('Type the same password again', 'new-password-456')
            ->assertSuccessful();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'old-password-123']);
        $this->assertGuest();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'new-password-456']);
        $this->assertAuthenticated();
    }

    public function test_password_will_not_touch_a_non_admin_account(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->artisan('admin:password', ['email' => $student->email])->assertFailed();
    }
}
