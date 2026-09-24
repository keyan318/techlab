<?php

namespace Tests\Feature;

use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Regression: commit 807f262 deleted the faculty member routes while AuthController,
 * ChatController, StudentController and FacultyController still redirected to
 * route('faculty.dashboard'). Registering as a faculty member inserted the user row and then
 * died with RouteNotFoundException; a placeholder route pointing at showLogin() turned
 * that into an infinite redirect loop.
 */
class FacultyRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function faculty(): User
    {
        return User::factory()->create(['role' => 'faculty']);
    }

    public function test_registering_as_a_faculty_lands_on_a_working_dashboard(): void
    {
        $this->post(route('register'), [
            'role' => 'faculty',
            'name' => 'sasa',
            'email' => 'sasa@example.com',
            'password' => 'sasa1234',
            'password_confirmation' => 'sasa1234',
        ])->assertRedirect(route('faculty.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('faculty.dashboard'))->assertOk();
    }

    public function test_the_faculty_dashboard_renders_instead_of_redirecting_to_itself(): void
    {
        // The placeholder route (showLogin) answered 302 -> /faculty/dashboard forever.
        $this->actingAs($this->faculty())
            ->get(route('faculty.dashboard'))
            ->assertOk();
    }

    public function test_an_existing_faculty_logging_in_lands_on_the_dashboard(): void
    {
        $faculty = $this->faculty();

        $this->post(route('login'), ['email' => $faculty->email, 'password' => 'password'])
            ->assertRedirect(route('faculty.dashboard'));
    }

    public function test_students_and_guests_cannot_use_the_faculty_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))
            ->get(route('faculty.dashboard'))
            ->assertRedirect(route('student.dashboard'));

        auth()->logout();

        $this->get(route('faculty.dashboard'))->assertRedirect(route('login'));
    }

    public function test_a_faculty_with_no_assignment_sees_the_empty_state(): void
    {
        $this->actingAs($this->faculty())
            ->get(route('faculty.dashboard'))
            ->assertOk()
            ->assertSee('No courses assigned yet');
    }

    public function test_a_faculty_generates_a_code_for_a_course_admin_assigned_them(): void
    {
        $faculty = $this->faculty();
        $assignment = CourseAssignment::create(['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $faculty->id]);

        $this->actingAs($faculty)
            ->post(route('faculty.assignments.generate-code', $assignment))
            ->assertRedirect(route('faculty.dashboard'));

        $crew = Crew::where('planet', 'networking')->where('course_slug', 'networking-fundamentals')->firstOrFail();
        $this->assertSame($faculty->id, $crew->teacher_id);
        $this->assertSame(6, strlen($crew->code));
        $this->assertTrue($crew->roster()->where('user_id', $faculty->id)->exists());

        // Generating again for the same assignment doesn't spin up a second crew or code.
        $this->actingAs($faculty)->post(route('faculty.assignments.generate-code', $assignment));
        $this->assertSame(1, Crew::where('planet', 'networking')->where('course_slug', 'networking-fundamentals')->count());
    }

    public function test_a_faculty_cannot_generate_a_code_for_someone_elses_assignment(): void
    {
        $owner = $this->faculty();
        $assignment = CourseAssignment::create(['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $owner->id]);

        $this->actingAs($this->faculty())
            ->post(route('faculty.assignments.generate-code', $assignment))
            ->assertForbidden();
    }

    public function test_a_faculty_can_be_in_charge_of_more_than_one_course(): void
    {
        $faculty = $this->faculty();
        $networking = CourseAssignment::create(['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $faculty->id]);
        $cyber = CourseAssignment::create(['planet' => 'cybersecurity', 'course_slug' => 'information-security-1', 'faculty_id' => $faculty->id]);

        $this->actingAs($faculty)->post(route('faculty.assignments.generate-code', $networking));
        $this->actingAs($faculty)->post(route('faculty.assignments.generate-code', $cyber));

        $this->assertSame(2, Crew::where('teacher_id', $faculty->id)->count());
        $this->actingAs($faculty)->get(route('faculty.dashboard'))->assertOk()
            ->assertSee('Networking 1')->assertSee('Information Security 1');
    }

    public function test_every_named_route_used_by_the_code_actually_exists(): void
    {
        // Guards this whole bug class: a route is deleted but route('that.name') callers remain.
        // `(?<!->)` skips $request->route('slug'), which reads a URL parameter, not a route name.
        $used = [];
        foreach ([app_path(), resource_path('views')] as $dir) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)) as $file) {
                $path = $file->getPathname();
                if (! str_ends_with($path, '.php') || preg_match('/\.(backup|bak)/', $path)) {
                    continue;
                }
                preg_match_all('/(?<!->)\broute\(\s*[\'"]([\w.\-]+)[\'"]/', file_get_contents($path), $m);
                foreach ($m[1] as $name) {
                    $used[$name][] = str_replace(base_path().'/', '', $path);
                }
            }
        }

        $missing = [];
        foreach ($used as $name => $files) {
            if (! Route::has($name)) {
                $missing[] = $name.'  <-  '.implode(', ', array_unique($files));
            }
        }

        $this->assertSame([], $missing, "route() calls with no matching route:\n".implode("\n", $missing));
    }
}
