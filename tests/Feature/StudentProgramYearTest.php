<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentProgramYearTest extends TestCase
{
    use RefreshDatabase;

    private function signUp(array $extra = [])
    {
        return $this->post('/register', $extra + [
            'first_name' => 'Nova', 'last_name' => 'Reyes', 'email' => 'nova@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'planets' => ['networking'],
        ]);
    }

    public function test_sign_up_asks_for_program_and_year_level(): void
    {
        $page = $this->get('/register')->assertOk()->assertSee('name="program"', false)->assertSee('name="year_level"', false);
        foreach (array_keys(User::PROGRAMS) as $code) {
            $page->assertSee('value="'.$code.'"', false);
        }
    }

    public function test_sign_up_saves_program_and_year_level(): void
    {
        $this->signUp(['program' => 'EMC', 'year_level' => '2'])->assertRedirect(route('student.welcome'));

        $user = User::where('email', 'nova@example.com')->first();
        $this->assertSame(['EMC', 2], [$user->program, $user->year_level]);
        $this->assertSame(['Nova', 'Reyes', 'Nova Reyes'], [$user->first_name, $user->last_name, $user->name]);
    }

    public function test_sign_up_asks_for_first_and_last_name(): void
    {
        $this->get('/register')->assertSee('name="first_name"', false)->assertSee('name="last_name"', false)
            ->assertDontSee('Your full name')->assertDontSee('Build things with code');
        $this->signUp(['first_name' => '', 'program' => 'IT', 'year_level' => 1])->assertSessionHasErrors('first_name');
    }

    public function test_sign_up_needs_a_real_program_and_year_level(): void
    {
        $this->signUp()->assertSessionHasErrors(['program', 'year_level']);
        $this->signUp(['program' => 'MED', 'year_level' => 5])->assertSessionHasErrors(['program', 'year_level']);
        $this->assertGuest();
    }

    public function test_admin_dashboard_counts_students_by_program_and_year(): void
    {
        User::factory()->count(2)->create(['role' => 'student', 'program' => 'IT', 'year_level' => 1]);
        User::factory()->create(['role' => 'student', 'program' => 'CS', 'year_level' => 3]);
        User::factory()->create(['role' => 'student']); // an older account that never picked
        User::factory()->create(['role' => 'faculty', 'program' => null]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('studentCount', 4)
            ->assertViewHas('grid', fn (array $grid) => $grid['IT'][1] === 2 && $grid['CS'][3] === 1 && $grid[''][0] === 1)
            ->assertSee('Students by program and year')
            ->assertSee('Library and Information Science')
            ->assertSee('Not set');
    }
}
