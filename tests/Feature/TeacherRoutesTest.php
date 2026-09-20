<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Regression: commit 807f262 deleted the teacher routes while AuthController,
 * ChatController, StudentController and TeacherController still redirected to
 * route('teacher.dashboard'). Registering as a teacher inserted the user row and then
 * died with RouteNotFoundException; a placeholder route pointing at showLogin() turned
 * that into an infinite redirect loop.
 */
class TeacherRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(): User
    {
        return User::factory()->create(['role' => 'teacher']);
    }

    public function test_registering_as_a_teacher_lands_on_a_working_dashboard(): void
    {
        $this->post(route('register'), [
            'role' => 'teacher',
            'name' => 'sasa',
            'email' => 'sasa@example.com',
            'password' => 'sasa1234',
            'password_confirmation' => 'sasa1234',
        ])->assertRedirect(route('teacher.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('teacher.dashboard'))->assertOk();
    }

    public function test_the_teacher_dashboard_renders_instead_of_redirecting_to_itself(): void
    {
        // The placeholder route (showLogin) answered 302 -> /teacher/dashboard forever.
        $this->actingAs($this->teacher())
            ->get(route('teacher.dashboard'))
            ->assertOk();
    }

    public function test_an_existing_teacher_logging_in_lands_on_the_dashboard(): void
    {
        $teacher = $this->teacher();

        $this->post(route('login'), ['email' => $teacher->email, 'password' => 'password'])
            ->assertRedirect(route('teacher.dashboard'));
    }

    public function test_students_and_guests_cannot_use_the_teacher_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))
            ->get(route('teacher.dashboard'))
            ->assertRedirect(route('student.dashboard'));

        auth()->logout();

        $this->get(route('teacher.dashboard'))->assertRedirect(route('login'));
    }

    public function test_a_teacher_can_create_exactly_one_crew(): void
    {
        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->post(route('teacher.crew.create'), ['name' => 'Crew Alpha'])
            ->assertRedirect(route('teacher.dashboard'));

        $crew = Crew::where('teacher_id', $teacher->id)->firstOrFail();
        $this->assertSame('Crew Alpha', $crew->name);
        $this->assertSame(6, strlen($crew->code));
        $this->assertSame($crew->id, $teacher->fresh()->crew_id);

        // A second attempt is refused, not duplicated.
        $this->actingAs($teacher)->post(route('teacher.crew.create'), ['name' => 'Crew Beta']);
        $this->assertSame(1, Crew::where('teacher_id', $teacher->id)->count());
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
