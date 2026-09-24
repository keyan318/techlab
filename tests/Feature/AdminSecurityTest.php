<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_repeated_wrong_passwords_for_the_same_admin_email_are_throttled_even_across_different_ips(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'correct-password-123']);

        // Each attempt comes from a fresh IP, so the per-IP throttle (5/min) never trips —
        // only the per-email limiter (10/hour) can be the one that blocks the 11th attempt.
        for ($i = 0; $i < 10; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.$i"])
                ->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong'])
                ->assertStatus(302);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.99'])
            ->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong'])
            ->assertStatus(429);
    }

    public function test_a_failed_admin_login_is_logged_without_the_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'correct-password-123']);
        Log::spy();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong-one']);

        Log::shouldHaveReceived('warning')->once()->withArgs(function ($message, $context) use ($admin) {
            return $message === 'Failed admin login attempt'
                && $context['email'] === $admin->email
                && ! str_contains(json_encode($context), 'wrong-one');
        });
    }

    public function test_a_successful_admin_login_is_not_logged_as_a_failure(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'correct-password-123']);
        Log::spy();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'correct-password-123'])
            ->assertRedirect(route('admin.dashboard'));

        Log::shouldNotHaveReceived('warning');
    }
}
