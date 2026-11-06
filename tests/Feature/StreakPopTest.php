<?php

namespace Tests\Feature;

use App\Models\StudyActivity;
use App\Models\User;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreakPopTest extends TestCase
{
    use RefreshDatabase;

    private function studied(User $user, int ...$daysAgo): void
    {
        foreach ($daysAgo as $d) {
            StudyActivity::create(['user_id' => $user->id, 'date' => now()->subDays($d)->toDateString(), 'minutes' => 5]);
        }
    }

    public function test_the_flame_evolves_every_ten_days_and_tops_out_blue(): void
    {
        $this->assertSame(0, StudentDashboardService::streakTier(1));
        $this->assertSame(0, StudentDashboardService::streakTier(9));
        $this->assertSame(1, StudentDashboardService::streakTier(10));
        $this->assertSame(4, StudentDashboardService::streakTier(45));
        $this->assertSame('Nova', StudentDashboardService::STREAK_TIERS[StudentDashboardService::streakTier(500)]['name']);
    }

    public function test_card_counts_the_streak_the_longest_run_and_an_evolution_day(): void
    {
        $user = User::factory()->create();
        $this->studied($user, 30, 29, 28, 27, 26);          // an old 5-day run
        $this->studied($user, ...range(0, 9));             // 10 days ending today

        $card = StudentDashboardService::streakCard($user);

        $this->assertSame(10, $card['count']);
        $this->assertSame(10, $card['longest']);
        $this->assertTrue($card['todayDone']);
        $this->assertSame(1, $card['tier']);
        $this->assertTrue($card['evolved']);
        $this->assertSame('Blaze', $card['nextName']);
        $this->assertSame(10, $card['nextIn']);
        $this->assertCount(7, $card['week']);
    }

    public function test_pop_up_shows_once_a_day_after_the_home_tour(): void
    {
        $user = User::factory()->create(['tour_seen' => ['home']]);
        $this->studied($user, 1, 2);

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('id="streak-pop"', false)->assertSee('Day streak');
        $this->assertTrue($user->fresh()->streak_seen_on->isToday());

        $this->actingAs($user->fresh())->get('/dashboard')->assertOk()->assertDontSee('id="streak-pop"', false);
    }

    public function test_pop_up_waits_for_the_tour_and_needs_a_streak(): void
    {
        $touring = User::factory()->create();
        $this->studied($touring, 0);
        $this->actingAs($touring)->withSession(['tour_fresh' => true])->get('/dashboard')->assertOk()->assertDontSee('id="streak-pop"', false);
        $this->assertNull($touring->fresh()->streak_seen_on);

        $fresh = User::factory()->create(['tour_seen' => ['home']]);
        $this->actingAs($fresh)->get('/dashboard')->assertOk()->assertDontSee('id="streak-pop"', false);
    }

    public function test_a_returning_user_who_never_took_the_tour_still_gets_the_pop_up(): void
    {
        $user = User::factory()->create();
        $this->studied($user, 0);

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('id="streak-pop"', false);
    }

    public function test_the_first_visit_of_each_day_pays_the_daily_login_xp_once(): void
    {
        $user = User::factory()->create(['tour_seen' => ['home']]);
        $this->studied($user, 1);

        $this->actingAs($user)->get('/dashboard')->assertOk()->assertSee('Daily login bonus');
        $this->actingAs($user->fresh())->get('/dashboard')->assertOk();
        $this->assertSame(StudentDashboardService::DAILY_LOGIN_XP, StudentDashboardService::earnedXp($user));
        $this->assertSame(StudentDashboardService::DAILY_LOGIN_XP, StudentDashboardService::balance($user));

        $this->travel(1)->days();
        $this->actingAs($user->fresh())->get('/dashboard')->assertOk();
        $this->assertSame(2 * StudentDashboardService::DAILY_LOGIN_XP, StudentDashboardService::earnedXp($user));
    }
}
