<?php

namespace Tests\Feature;

use App\Models\AdminActionLog;
use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCourseAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_sees_every_faculty_member_to_choose_from(): void
    {
        $joy = User::factory()->create(['role' => 'faculty', 'name' => 'Joy Santos']);
        User::factory()->create(['role' => 'student', 'name' => 'Not A Captain']);

        $this->actingAs($this->admin())->get(route('admin.home'))->assertOk()
            ->assertSee('Joy Santos')->assertDontSee('Not A Captain');
    }

    public function test_admin_assigns_a_faculty_member_to_a_course(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($this->admin())->post(route('admin.courses.assign'), [
            'planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $faculty->id,
        ])->assertRedirect(route('admin.home'));

        $this->assertDatabaseHas('course_assignments', [
            'planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $faculty->id,
        ]);
    }

    public function test_reassigning_a_live_course_hands_the_existing_crew_to_the_new_captain(): void
    {
        $original = User::factory()->create(['role' => 'faculty']);
        $assignment = CourseAssignment::create(['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $original->id]);
        $this->actingAs($original)->post(route('faculty.assignments.generate-code', $assignment));
        $crew = Crew::where('planet', 'networking')->where('course_slug', 'networking-fundamentals')->firstOrFail();
        $code = $crew->code;

        $replacement = User::factory()->create(['role' => 'faculty']);
        $this->actingAs($this->admin())->post(route('admin.courses.assign'), [
            'planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $replacement->id,
        ]);

        $crew->refresh();
        $this->assertSame($replacement->id, $crew->teacher_id);
        $this->assertSame($code, $crew->code); // students who already have the code keep working access
        $this->actingAs($replacement)->get(route('faculty.course', $crew))->assertOk();
        $this->actingAs($original)->get(route('faculty.course', $crew))->assertForbidden();
    }

    public function test_only_admins_reach_the_assignment_panel(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'faculty']))
            ->post(route('admin.courses.assign'), ['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => 1])
            ->assertForbidden();
    }

    public function test_assigning_a_course_writes_an_audit_row(): void
    {
        $admin = $this->admin();
        $joy = User::factory()->create(['role' => 'faculty']);
        $kent = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($admin)->post(route('admin.courses.assign'), [
            'planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $joy->id,
        ]);
        $this->assertDatabaseHas('admin_action_logs', [
            'admin_id' => $admin->id, 'action' => 'course.assign', 'subject' => 'networking.networking-fundamentals',
        ]);

        // Reassigning writes a second row that remembers who it came from.
        $this->actingAs($admin)->post(route('admin.courses.assign'), [
            'planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $kent->id,
        ]);
        $row = AdminActionLog::where('action', 'course.reassign')->firstOrFail();
        $this->assertSame($joy->id, $row->details['from_faculty_id']);
        $this->assertSame($kent->id, $row->details['to_faculty_id']);
    }
}
