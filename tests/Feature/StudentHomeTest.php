<?php

namespace Tests\Feature;

use App\Models\LessonProgress;
use App\Models\StudyActivity;
use App\Models\User;
use App\Services\CourseOverviewService;
use App\Services\CourseProgressService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentHomeTest extends TestCase
{
    use RefreshDatabase;

    private function complete(User $u, int $count): void
    {
        foreach (array_slice(CourseProgressService::order(), 0, $count) as $item) {
            CourseProgressService::markComplete($u, $item['module'], $item['lesson']);
        }
    }

    public function test_new_student_is_invited_to_start_the_first_course(): void
    {
        $user = User::factory()->create(['name' => 'Keyan Rima']);

        $home = StudentDashboardService::home($user);

        $this->assertSame(0, $home['xp']);
        $this->assertSame(1, $home['level']);
        $this->assertNull($home['rank']);
        $this->assertSame(0, $home['streak']);
        $this->assertSame('Python', $home['hero']['title']);
        $this->assertFalse($home['hero']['started']);

        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertSee('Welcome aboard, Keyan')
            ->assertSee('Start Learning')
            ->assertSee(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']), false)
            ->assertDontSee('Monthly Challenge');
    }

    public function test_hero_continues_the_course_in_progress_and_links_to_its_overview(): void
    {
        $user = User::factory()->create(['name' => 'Keyan Rima']);
        $this->complete($user, 2);

        $home = StudentDashboardService::home($user);
        $hero = $home['hero'];

        $this->assertTrue($hero['started']);
        $this->assertSame(200, $home['xp']);
        $this->assertStringContainsString('/student/planet/programming/', $hero['continueUrl']);
        $this->assertSame(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']), $hero['overviewUrl']);

        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertSee('Welcome back, Keyan')
            ->assertSee('Jump back in')
            ->assertSee('Continue Learning')
            ->assertSee('View course')
            ->assertSee($hero['continueUrl'], false)
            ->assertSee(route('student.progress'), false);
    }

    public function test_hero_is_gone_when_every_tracked_lesson_is_done(): void
    {
        $user = User::factory()->create();
        foreach (['programming', 'networking'] as $planet) {
            foreach (CourseProgressService::order($planet) as $item) {
                CourseProgressService::markComplete($user, $item['module'], $item['lesson'], $planet);
            }
        }

        $this->assertNull(StudentDashboardService::home($user)['hero']);
        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('All caught up');
    }

    public function test_level_rises_with_earned_xp_and_streak_counts_consecutive_days(): void
    {
        $user = User::factory()->create();
        $this->complete($user, 3);   // 300 XP -> level 2

        $this->assertSame(2, StudentDashboardService::home($user)['level']);

        // Today has a completion; add the two days before it.
        StudyActivity::create(['user_id' => $user->id, 'date' => now()->subDay()->toDateString(), 'minutes' => 5]);
        StudyActivity::create(['user_id' => $user->id, 'date' => now()->subDays(2)->toDateString(), 'minutes' => 5]);
        $this->assertSame(3, StudentDashboardService::streak($user));

        // A gap ends the run, and today not being over yet doesn't break yesterday's streak.
        LessonProgress::where('user_id', $user->id)->update(['completed_at' => now()->subDays(5)]);
        $this->assertSame(2, StudentDashboardService::streak($user));
    }

    public function test_overview_lists_chapters_with_done_current_and_locked_lessons(): void
    {
        $user = User::factory()->create();
        $this->complete($user, 2);

        $data = CourseOverviewService::build($user, 'programming', 'python');
        $states = collect($data['chapters'])->pluck('lessons')->flatten(1)->pluck('state');

        $this->assertSame(['done', 'done', 'current'], $states->take(3)->all());
        $this->assertSame(1, $states->filter(fn ($s) => $s === 'current')->count());
        $this->assertSame(2, $data['lessonsDone']);
        $this->assertSame($data['lessonsTotal'] * 100, $data['xpTotal']);
        $this->assertSame(200, $data['xpEarned']);
        $this->assertSame('Resume Learning', $data['resume']['label']);
        $this->assertSame(0, $data['badgesEarned']);

        $this->actingAs($user)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()
            ->assertSee('Course Progress')
            ->assertSee('Course Badges')
            ->assertSee('Done!')
            ->assertSee('+100 XP')
            ->assertSee('Locked');
    }

    public function test_finishing_a_chapter_earns_its_badge(): void
    {
        $user = User::factory()->create();
        $m1 = collect(CourseProgressService::order())->where('module', 'm1')->count();
        $this->complete($user, $m1);

        $data = CourseOverviewService::build($user, 'programming', 'python');

        $this->assertTrue($data['chapters'][0]['complete']);
        $this->assertSame(1, $data['badgesEarned']);
    }

    public function test_overview_guards_and_unknown_courses(): void
    {
        $url = route('student.course.overview', ['slug' => 'programming', 'course' => 'python']);

        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->get($url)->assertRedirect(route('faculty.dashboard'));

        $student = User::factory()->create();
        $this->actingAs($student)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'nope']))->assertNotFound();
        $this->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'next-language']))->assertNotFound();
    }
}
