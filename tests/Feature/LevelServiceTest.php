<?php

namespace Tests\Feature;

use App\Models\GemExchange;
use App\Models\User;
use App\Models\XpSpend;
use App\Services\CourseProgressService;
use App\Services\LevelService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Levels from earned XP (50 XP for the first, 5 more each level after), ranks every 10 levels, and frames. */
class LevelServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_curve_starts_fast_and_grows_by_five_xp_a_level(): void
    {
        $this->assertSame(0, LevelService::xpToReach(1));
        $this->assertSame(50, LevelService::xpToReach(2));
        $this->assertSame(105, LevelService::xpToReach(3));
        $this->assertSame(29205, LevelService::xpToReach(100));

        $this->assertSame(1, LevelService::levelFor(0));
        $this->assertSame(1, LevelService::levelFor(49));
        $this->assertSame(2, LevelService::levelFor(50));
        $this->assertSame(2, LevelService::levelFor(100));   // the first lesson levels you up
        $this->assertSame(1, LevelService::levelFor(-10));

        // Exact at every boundary, far past 100 too.
        foreach ([2, 9, 10, 57, 99, 100, 101, 250] as $level) {
            $this->assertSame($level, LevelService::levelFor(LevelService::xpToReach($level)), "start of {$level}");
            $this->assertSame($level - 1, LevelService::levelFor(LevelService::xpToReach($level) - 1), "just before {$level}");
        }
        $this->assertSame(['level' => 2, 'into' => 100, 'needed' => 105], LevelService::progress(100));
    }

    public function test_ranks_change_every_ten_levels_and_galaxy_legends_count_stars(): void
    {
        $this->assertSame('Stardust', LevelService::rank(1)['name']);
        $this->assertSame('Stardust', LevelService::rank(9)['name']);
        $this->assertSame('Comet', LevelService::rank(10)['name']);
        $this->assertSame('Supernova', LevelService::rank(99)['name']);

        $legend = LevelService::rank(100);
        $this->assertSame(['Galaxy Legend', 1, 'Galaxy Legend ★1'], [$legend['name'], $legend['stars'], $legend['label']]);
        $this->assertSame(12, LevelService::rank(111)['stars']);
        $this->assertSame(0, LevelService::rank(99)['stars']);
        $this->assertSame(1, LevelService::tierStart(0));
        $this->assertSame(50, LevelService::tierStart(5));
    }

    public function test_every_rank_has_a_free_frame_and_two_for_sale_that_unlock_with_the_rank(): void
    {
        $frames = LevelService::frames();
        $this->assertCount(33, $frames);
        $this->assertSame(0, $frames['stardust-default']['price']);
        $this->assertSame([200, 300], [$frames['stardust-a']['price'], $frames['stardust-b']['price']]);
        $this->assertSame([1200, 1800], [$frames['galaxy-a']['price'], $frames['galaxy-b']['price']]);

        $this->assertTrue(LevelService::frameUnlocked('comet-a', 10));
        $this->assertFalse(LevelService::frameUnlocked('comet-a', 9));
        $this->assertTrue(LevelService::frameUnlocked('stardust-b', 80));   // earlier ranks stay on sale
        $this->assertFalse(LevelService::frameUnlocked('nope-a', 80));

        // No pick, or one above your rank: your rank's default, so it upgrades by itself.
        $this->assertSame('comet-default', LevelService::wornFrame(null, 12));
        $this->assertSame('nova-default', LevelService::wornFrame(null, 50));
        $this->assertSame('stardust-b', LevelService::wornFrame('stardust-b', 50));
        $this->assertSame('stardust-default', LevelService::wornFrame('nova-b', 5));
    }

    public function test_spending_xp_or_trading_gems_never_changes_the_level(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        foreach (array_slice(CourseProgressService::order('computer-programming-1'), 0, 3) as $item) {
            CourseProgressService::markComplete($user, $item['module'], $item['lesson'], 'computer-programming-1');
        }
        $level = StudentDashboardService::levelOf($user);
        $this->assertSame(6, $level);   // 300 XP

        XpSpend::create(['user_id' => $user->id, 'course' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson01', 'item' => 'hint', 'cost' => 50]);
        GemExchange::create(['user_id' => $user->id, 'gems' => 50, 'xp' => 500]);

        $this->assertSame($level, StudentDashboardService::levelOf($user));
        $this->assertSame(300 - 50 + 500, StudentDashboardService::balance($user));
    }
}
