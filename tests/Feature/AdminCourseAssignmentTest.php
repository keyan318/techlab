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

    public function test_a_second_faculty_member_gets_their_own_class_and_the_first_keeps_theirs(): void
    {
        $original = User::factory()->create(['role' => 'faculty']);
        $assignment = CourseAssignment::create(['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $original->id]);
        $this->actingAs($original)->post(route('faculty.assignments.generate-code', $assignment));
        $crew = Crew::where('planet', 'networking')->where('course_slug', 'networking-fundamentals')->firstOrFail();

        $second = User::factory()->create(['role' => 'faculty']);
        $this->actingAs($this->admin())->post(route('admin.courses.assign'), [
            'planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $second->id,
        ]);
        $this->assertSame(2, CourseAssignment::where('course_slug', 'networking-fundamentals')->count());

        $this->actingAs($second)->post(route('faculty.assignments.generate-code', CourseAssignment::where('faculty_id', $second->id)->firstOrFail()));
        $theirs = Crew::where('teacher_id', $second->id)->firstOrFail();

        $this->assertNotSame($crew->id, $theirs->id);
        $this->assertSame($original->id, $crew->refresh()->teacher_id);
        $this->actingAs($original)->get(route('faculty.course', $crew))->assertOk();
        $this->actingAs($second)->get(route('faculty.course', $crew))->assertForbidden();
    }

    public function test_a_faculty_member_with_students_cannot_be_removed_but_one_without_can(): void
    {
        $joy = User::factory()->create(['role' => 'faculty']);
        $assignment = CourseAssignment::create(['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $joy->id]);
        $crew = $assignment->openClass();
        $crew->admit(User::factory()->create(['role' => 'student']));

        $this->actingAs($this->admin())->delete(route('admin.courses.unassign', $assignment))->assertRedirect(route('admin.home'));
        $this->assertModelExists($assignment);

        $crew->roster()->wherePivot('role', 'student')->detach();
        $this->actingAs($this->admin())->delete(route('admin.courses.unassign', $assignment));
        $this->assertModelMissing($assignment);
        $this->assertModelMissing($crew);
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

        // Adding a second faculty member writes a second row; removing one writes another.
        $this->actingAs($admin)->post(route('admin.courses.assign'), [
            'planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $kent->id,
        ]);
        $this->assertSame(2, AdminActionLog::where('action', 'course.assign')->count());

        $this->actingAs($admin)->delete(route('admin.courses.unassign', CourseAssignment::where('faculty_id', $kent->id)->firstOrFail()));
        $row = AdminActionLog::where('action', 'course.unassign')->firstOrFail();
        $this->assertSame($kent->id, $row->details['to_faculty_id']);
    }

    public function test_classes_page_groups_every_faculty_members_class_under_one_course_card(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        foreach (['Ana Cruz', 'Ben Reyes', 'Carla Lim'] as $i => $name) {
            $faculty = User::factory()->create(['role' => 'faculty', 'name' => $name]);
            $crew = Crew::create(['name' => $name, 'code' => 'CODE0'.$i, 'teacher_id' => $faculty->id,
                'planet' => 'networking', 'course_slug' => 'networking-fundamentals']);
            if ($i === 0) {
                $crew->roster()->attach($student->id, ['role' => 'student']);
            }
        }

        $response = $this->actingAs($this->admin())->get(route('admin.classes'))->assertOk()
            ->assertSee('Ana Cruz')->assertSee('Ben Reyes')->assertSee('Carla Lim')
            ->assertSee('3 faculty members')->assertSee('1 astronaut in all')
            ->assertSee('CODE00')->assertSee('CODE02');

        $this->assertSame(1, substr_count($response->getContent(), 'class="class-card"'));
    }
}
