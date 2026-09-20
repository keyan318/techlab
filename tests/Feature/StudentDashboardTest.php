<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\LessonProgress;
use App\Models\StudyActivity;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function complete(User $u, int $count): void
    {
        foreach (array_slice(CourseProgressService::order(), 0, $count) as $item) {
            CourseProgressService::markComplete($u, $item['module'], $item['lesson']);
        }
    }

    private function crewWithTeacher(): Crew
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        return Crew::create(['name' => 'Sasa Crew', 'code' => 'SASA01', 'teacher_id' => $teacher->id]);
    }

    public function test_guests_go_to_login_and_teachers_to_their_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'teacher']))->get('/dashboard')->assertRedirect(route('teacher.dashboard'));
    }

    public function test_dashboard_shows_real_xp_lessons_and_modules(): void
    {
        $user = User::factory()->create(['name' => 'Keyan Rima']);
        $order = CourseProgressService::order();
        $m1 = collect($order)->where('module', 'm1')->count();
        $this->complete($user, $m1 + 2);

        $data = StudentDashboardService::build($user);

        $this->assertSame(($m1 + 2) * 100, $data['xp']);
        $this->assertSame($m1 + 2, $data['lessonsDone']);
        $this->assertSame(1, $data['modulesDone']);
        $this->assertSame($m1 + 2, $data['planets']['programming']['completed']);
        $this->assertTrue($data['planets']['networking']['tracked']);
        $this->assertSame(0, $data['planets']['networking']['completed']);
        $this->assertFalse($data['planets']['cybersecurity']['tracked']);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('Hello,', false)
            ->assertSee(number_format(($m1 + 2) * 100))
            ->assertSee(route('student.chat'), false)
            ->assertSee('href="'.route('student.dashboard').'"', false);
    }

    public function test_leaderboard_ranks_crewmates_by_xp_and_excludes_other_crews(): void
    {
        $crew = $this->crewWithTeacher();
        $other = Crew::create(['name' => 'Other', 'code' => 'OTHER1', 'teacher_id' => $crew->teacher_id]);

        $keyan = User::factory()->create(['name' => 'Keyan', 'crew_id' => $crew->id]);
        $sasa = User::factory()->create(['name' => 'Sasa', 'crew_id' => $crew->id]);
        $zed = User::factory()->create(['name' => 'Zed', 'crew_id' => $crew->id]);
        $out = User::factory()->create(['name' => 'Outsider', 'crew_id' => $other->id]);

        $this->complete($keyan, 5);
        $this->complete($sasa, 2);
        $this->complete($zed, 2);
        $this->complete($out, 9);

        $board = StudentDashboardService::leaderboard($sasa);

        $this->assertSame(['Keyan', 'Sasa', 'Zed'], array_column($board, 'name'));
        $this->assertSame([1, 2, 2], array_column($board, 'rank'));
        $this->assertSame([500, 200, 200], array_column($board, 'xp'));
        $this->assertTrue($board[1]['me']);

        $this->actingAs($sasa)->get('/dashboard')->assertOk()->assertSee('Sasa Crew leaderboard')->assertDontSee('Outsider');
    }

    public function test_no_crew_renders_a_join_prompt(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertOk()->assertSee("haven't joined a crew", false);
    }

    public function test_activity_ping_accumulates_minutes_and_is_rate_limited(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('student.activity'))->assertNoContent();
        $this->post(route('student.activity'))->assertNoContent();   // too soon: not counted
        $this->assertSame(1, StudyActivity::where('user_id', $user->id)->value('minutes'));

        StudyActivity::where('user_id', $user->id)->update(['last_ping_at' => now()->subMinutes(2)]);
        $this->post(route('student.activity'))->assertNoContent();
        $this->assertSame(2, StudyActivity::where('user_id', $user->id)->value('minutes'));

        $weekly = StudentDashboardService::weeklyActivity($user);
        $this->assertSame(2, $weekly['totalMinutes']);
    }

    public function test_performance_compares_this_week_with_last(): void
    {
        $user = User::factory()->create();
        $this->complete($user, 3);
        LessonProgress::where('user_id', $user->id)->limit(1)->update(['completed_at' => now()->subWeek()]);

        $perf = StudentDashboardService::performance($user);

        $this->assertSame(200, $perf['thisWeekXp']);
        $this->assertSame(100, $perf['lastWeekXp']);
        $this->assertSame(100, $perf['changePct']);
        $this->assertTrue($perf['hasData']);
    }

    public function test_sidebar_links_dashboard_chat_and_planets_separately(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/chat')->assertOk()
            ->assertSee('aria-label="Dashboard"', false)
            ->assertSee('href="'.route('student.dashboard').'"', false)
            ->assertSee('href="'.route('student.chat').'"', false);
        $this->get('/planets')->assertOk();
        $this->assertSame('/dashboard', parse_url(route('student.dashboard'), PHP_URL_PATH));
        $this->assertSame('/chat', parse_url(route('student.chat'), PHP_URL_PATH));
    }
}
