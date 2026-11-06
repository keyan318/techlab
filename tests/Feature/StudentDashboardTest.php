<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\LessonProgress;
use App\Models\ShopPurchase;
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

    /** A student taking a course on this planet (on its self-paced crew). */
    private function onPlanet(string $name, string $planet = 'programming', string $course = 'python'): User
    {
        $user = User::factory()->create(['name' => $name, 'role' => 'student']);
        Crew::selfPaced($planet, $course)->admit($user);

        return $user;
    }

    public function test_guests_go_to_login_and_facultys_to_their_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->get('/dashboard')->assertRedirect(route('faculty.dashboard'));
        $this->get('/progress')->assertRedirect(route('faculty.dashboard'));
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
        $this->assertTrue($data['planets']['cybersecurity']['tracked']);
        $this->assertSame(0, $data['planets']['cybersecurity']['completed']);

        $this->actingAs($user)->get('/progress')
            ->assertOk()
            ->assertSee('Hello,', false)
            ->assertSee(number_format(($m1 + 2) * 100))
            ->assertSee(route('student.chat'), false)
            ->assertSee('href="'.route('student.dashboard').'"', false);
    }

    public function test_header_stats_endpoint_returns_xp_gems_and_streak_for_students_only(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $m1 = collect(CourseProgressService::order())->where('module', 'm1')->count();
        $this->complete($user, $m1);

        $home = StudentDashboardService::home($user);
        $this->actingAs($user)->getJson('/me/stats')
            ->assertOk()
            ->assertExactJson(['xp' => $m1 * 100, 'gems' => $home['badges'], 'streak' => $home['streak']]);

        $this->actingAs(User::factory()->create(['role' => 'faculty']))->getJson('/me/stats')->assertForbidden();
    }

    public function test_leaderboard_ranks_everyone_on_the_planet_by_xp_and_excludes_other_planets(): void
    {
        $keyan = $this->onPlanet('Keyan');
        $sasa = $this->onPlanet('Sasa');
        $zed = $this->onPlanet('Zed');
        $out = $this->onPlanet('Outsider', 'networking', 'networking-fundamentals');

        $this->complete($keyan, 5);
        $this->complete($sasa, 2);
        $this->complete($zed, 2);
        $this->complete($out, 9);

        $board = StudentDashboardService::leaderboard($sasa, 'programming');

        $this->assertSame(['Keyan', 'Sasa', 'Zed'], array_column($board, 'name'));
        $this->assertSame([1, 2, 2], array_column($board, 'rank'));
        $this->assertSame([500, 200, 200], array_column($board, 'xp'));
        $this->assertTrue($board[1]['me']);

        $this->actingAs($sasa)->get('/progress')->assertOk()->assertSee('leaderboard')->assertDontSee('Outsider');
    }

    public function test_leaderboard_ranks_by_level_and_spending_xp_never_drops_anyone(): void
    {
        $ana = $this->onPlanet('Ana');
        $cy = $this->onPlanet('Cy');
        $bo = $this->onPlanet('Bo');

        $this->complete($ana, 3);   // 300 XP: level 6
        $this->complete($bo, 2);    // 200 XP: level 4
        $this->complete($cy, 2);
        // Ana spends most of her XP in the Shop: her balance drops, her level doesn't.
        ShopPurchase::create(['user_id' => $ana->id, 'item' => 'suit:pilot', 'cost' => 250]);

        $board = StudentDashboardService::leaderboard($ana, 'programming');

        $this->assertSame(['Ana', 'Bo', 'Cy'], array_column($board, 'name'));
        $this->assertSame([1, 2, 2], array_column($board, 'rank'));   // same level and XP earned: same place
        $this->assertSame([6, 4, 4], array_column($board, 'level'));
        $this->assertSame(['Stardust', 'Stardust', 'Stardust'], array_column(array_column($board, 'tier'), 'name'));
        $this->assertSame('stardust-default', $board[0]['frame']);
        $this->assertSame(50, StudentDashboardService::balance($ana));

        $this->actingAs($ana)->get('/dashboard')->assertOk()
            ->assertSee('leaderboard')
            ->assertSee('data-frame="stardust-default"', false)
            ->assertSeeInOrder(['By level', 'Ana', '(you)', 'Lv 6', 'Bo', 'Lv 4', 'Cy']);
    }

    public function test_no_course_renders_a_join_prompt(): void
    {
        $this->actingAs(User::factory()->create(['planets' => []]))->get('/progress')->assertOk()->assertSee('to compete with everyone on the planet', false);
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

    public function test_sidebar_links_home_chat_and_planets_separately(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/chat')->assertOk()
            ->assertSee('aria-label="Home"', false)
            ->assertSee('href="'.route('student.dashboard').'"', false)
            ->assertSee('href="'.route('student.chat').'"', false);
        $this->get('/planets')->assertOk();
        $this->assertSame('/dashboard', parse_url(route('student.dashboard'), PHP_URL_PATH));
        $this->assertSame('/chat', parse_url(route('student.chat'), PHP_URL_PATH));
    }
}
