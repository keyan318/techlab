<?php

namespace Tests\Feature;

use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonSourceService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A course marked 'hidden' in config/course-catalog.php (Python, for now) stays on disk
 * but is never offered: not on the planet's course picker, not in the dashboard, and
 * its lessons don't count toward the planet.
 */
class HiddenCourseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['course-catalog.programming.courses.python.hidden' => true]);
    }

    public function test_python_is_hidden_out_of_the_box(): void
    {
        $catalog = require base_path('config/course-catalog.php');

        $this->assertTrue($catalog['programming']['courses']['python']['hidden']);
    }

    public function test_the_course_picker_leaves_a_hidden_course_out(): void
    {
        $this->crewFor('programming', 'computer-programming-1');

        $this->actingAs($this->studentIn())
            ->get(route('student.planet', ['slug' => 'programming']))
            ->assertOk()
            ->assertSee('Computer Programming 1')
            ->assertDontSee(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']), false)
            ->assertDontSee('Zero to Automation');
    }

    public function test_astros_add_from_planets_picker_leaves_a_hidden_course_out(): void
    {
        $sources = app(LessonSourceService::class);
        $slugs = array_column($sources->catalog(User::factory()->create()), 'slug');

        $this->assertNotContains('programming', $slugs);
        $this->assertContains('computer-programming-1', $slugs);
        $this->assertSame([], $sources->resolve([['planet' => 'programming', 'module' => 'M1', 'lesson' => 'lesson-01']]));
    }

    public function test_a_hidden_course_does_not_count_toward_its_planet(): void
    {
        $this->assertTrue(CourseProgressService::isHidden('programming'));
        $this->assertSame(['computer-programming-1', 'computer-programming-2'], CourseProgressService::keysForPlanet('programming'));
        $this->assertSame('computer-programming-1', StudentDashboardService::catalogCourse('programming')['id']);
    }

    public function test_the_dashboard_resumes_the_visible_course_not_the_hidden_one(): void
    {
        $user = $this->studentIn('programming', 'computer-programming-1');
        LessonProgress::create(['user_id' => $user->id, 'course' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01', 'completed_at' => now()]);

        $current = StudentDashboardService::currentLesson($user);

        $this->assertSame('programming', $current['planet']);
        $this->assertSame('computer-programming-1', $current['key']);
        $this->assertStringContainsString('/student/planet/computer-programming-1/', $current['url']);
    }
}
