<?php

namespace Tests\Feature;

use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_dashboard_shows_stats_and_recent_activity(): void
    {
        $admin = $this->admin();
        $joy = User::factory()->create(['role' => 'faculty', 'name' => 'Joy Santos']);

        $this->actingAs($admin)->post(route('admin.courses.assign'), [
            'planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $joy->id,
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()
            ->assertSee('1') // one course assigned
            ->assertSee('Networking 1')
            ->assertSee('Joy Santos');
    }

    public function test_only_admins_reach_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'faculty']))
            ->get(route('admin.dashboard'))->assertForbidden();

        auth()->logout();
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_sidebar_shows_dashboard_and_course_assignment_but_nothing_else(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()
            ->assertSee('aria-label="Dashboard"', false)
            ->assertSee('aria-label="Course Assignment"', false)
            ->assertDontSee('aria-label="Chat"', false)
            ->assertDontSee('aria-label="Planets"', false)
            ->assertDontSee('aria-label="Crew"', false)
            ->assertSee('Admin');
    }

    public function test_clicking_a_captain_shows_the_crew_roster(): void
    {
        $admin = $this->admin();
        $faculty = User::factory()->create(['role' => 'faculty']);
        $assignment = CourseAssignment::create(['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $faculty->id]);
        $this->actingAs($faculty)->post(route('faculty.assignments.generate-code', $assignment));
        $crew = Crew::where('planet', 'networking')->where('course_slug', 'networking-fundamentals')->firstOrFail();

        $student = User::factory()->create(['role' => 'student']);
        $crew->roster()->attach($student->id, ['role' => 'student']);

        $this->actingAs($admin)->getJson(route('admin.courses.roster', $crew))->assertOk()
            ->assertJsonPath('title', 'Networking 1')
            ->assertJsonPath('code', $crew->code)
            ->assertJsonFragment(['name' => $student->name, 'role' => 'student']);

        $this->actingAs(User::factory()->create(['role' => 'student']))
            ->getJson(route('admin.courses.roster', $crew))->assertForbidden();
    }
}
