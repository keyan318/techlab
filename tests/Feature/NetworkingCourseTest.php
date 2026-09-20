<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NetworkingCourseTest extends TestCase
{
    use RefreshDatabase;

    private function labUrl(string $module, string $lesson): string
    {
        return route('student.planet.lab.complete', ['slug' => 'networking', 'module' => $module, 'lesson' => $lesson]);
    }

    private function fragmentUrl(string $module, string $lesson): string
    {
        return route('student.planet.module.lesson.fragment', ['slug' => 'networking', 'module' => $module, 'lesson' => $lesson]);
    }

    public function test_blueprint_follows_the_syllabus_units(): void
    {
        $order = CourseProgressService::order('networking');
        $perModule = collect($order)->countBy('module')->all();

        $this->assertSame(['m1' => 6, 'm2' => 7, 'm3' => 5, 'm4' => 6], $perModule);
        $this->assertSame('m1', $order[0]['module']);
        $this->assertSame('lesson01', $order[0]['lesson']);
    }

    public function test_every_networking_lesson_has_a_lab_and_no_coding_challenge(): void
    {
        foreach (CourseProgressService::order('networking') as $item) {
            $this->assertNull($item['expected'], "{$item['module']}/{$item['lesson']} must not have a coding answer key");
            $this->assertMatchesRegularExpression('/^m\d+-l\d+$/', (string) $item['lab']);
        }
    }

    public function test_networking_lessons_live_under_their_own_view_folder(): void
    {
        $this->assertSame('student.planets.networking.net_course', CourseProgressService::viewBase('networking'));
        $this->assertSame('student.planets.programming.python_course', CourseProgressService::viewBase('programming'));
        $this->assertNull(CourseProgressService::viewBase('cybersecurity'));
    }

    public function test_a_locked_networking_lesson_is_refused(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get($this->fragmentUrl('m1', 'lesson02'))->assertForbidden();
    }

    public function test_opening_a_lab_lesson_does_not_complete_it(): void
    {
        $user = User::factory()->create();

        // Lesson 1 is unlocked; opening it must not count as done (unlike a Python lesson with no challenge).
        $this->actingAs($user)->get($this->fragmentUrl('m1', 'lesson01'));

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_passing_the_lab_completes_the_lesson_and_unlocks_the_next(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'm1-l1'])
            ->assertOk()
            ->assertJson(['ok' => true, 'next' => route('student.planet.module.lesson', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson02'])]);

        $this->assertTrue(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'networking'));
        $this->assertTrue(CourseProgressService::isUnlocked($user, 'm1', 'lesson02', 'networking'));
        $this->assertFalse(CourseProgressService::isCompleted($user, 'm1', 'lesson01'), 'must not leak into the programming course');
    }

    public function test_a_lab_pass_for_the_wrong_lab_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'm4-l6'])->assertStatus(422);

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_a_lab_pass_cannot_skip_ahead(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson03'), ['lab' => 'm1-l3'])->assertForbidden();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_lab_endpoint_requires_login_and_a_real_planet(): void
    {
        $this->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'm1-l1'])->assertUnauthorized();

        $this->actingAs(User::factory()->create())
            ->postJson(route('student.planet.lab.complete', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson01']), ['lab' => 'm1-l1'])
            ->assertNotFound();
    }

    public function test_a_python_lesson_without_a_lab_rejects_the_lab_endpoint(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('student.planet.lab.complete', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01']), ['lab' => 'm1-l1'])
            ->assertStatus(422);
    }

    public function test_dashboard_continues_in_the_planet_the_student_last_touched(): void
    {
        $user = User::factory()->create();
        CourseProgressService::markComplete($user, 'm1', 'lesson01', 'networking');

        $current = StudentDashboardService::currentLesson($user);

        $this->assertSame('Ship\'s Wiring', $current['title']);
        $this->assertStringContainsString('/student/planet/networking/m1/lesson02', $current['url']);
    }

    public function test_dashboard_defaults_to_programming_for_a_new_student(): void
    {
        $current = StudentDashboardService::currentLesson(User::factory()->create());

        $this->assertStringContainsString('/student/planet/programming/m1/lesson01', $current['url']);
    }
}
