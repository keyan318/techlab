<?php

namespace Tests\Feature;

use App\Models\LessonProgress;
use App\Models\ShopPurchase;
use App\Models\User;
use App\Services\AvatarService;
use App\Services\CourseProgressService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The Shop: frames, animals and suits bought with XP (never lowering the level), gems traded for XP, the rank-up. */
class ShopTest extends TestCase
{
    use RefreshDatabase;

    private const COURSE = 'computer-programming-1';

    /** A student who finished their first $lessons lessons of Computer Programming 1 (100 XP each). */
    private function student(int $lessons = 0, array $attrs = []): User
    {
        $user = User::factory()->create(['role' => 'student'] + $attrs);
        foreach (array_slice(CourseProgressService::order(self::COURSE), 0, $lessons) as $item) {
            LessonProgress::create(['user_id' => $user->id, 'course' => self::COURSE, 'module' => $item['module'], 'lesson' => $item['lesson'], 'completed_at' => now()]);
        }

        return $user;
    }

    /** Lessons in Computer Programming 1's first module: finishing them all earns GEMS_PER_MODULE gems. */
    private function firstModule(): int
    {
        return collect(CourseProgressService::order(self::COURSE))->where('module', 'm1')->count();
    }

    private function buy(User $user, string $kind, string $key)
    {
        return $this->actingAs($user)->postJson(route('student.shop.buy', ['kind' => $kind, 'key' => $key]));
    }

    public function test_the_shop_shows_every_rank_animals_suits_and_the_gem_exchange_to_students_only(): void
    {
        $this->actingAs($this->student(1))->get(route('student.shop'))->assertOk()
            ->assertSee('Twinkle Wreath')->assertSee('Infinity Crown')->assertSee('Galaxy Legend')
            ->assertSee('Reach level 10')
            ->assertSee('Pig')->assertSee('Commander')
            ->assertSee('Trade gems for XP')
            ->assertSee('aria-label="Shop"', false);   // the sidebar link

        $this->actingAs(User::factory()->create(['role' => 'faculty']))->get(route('student.shop'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->get(route('faculty.dashboard'))->assertDontSee('aria-label="Shop"', false);
    }

    public function test_buying_an_animal_spends_xp_once_never_the_level_then_it_can_be_worn(): void
    {
        $user = $this->student(3, ['avatar' => AvatarService::normalize(['animal' => 'cat', 'coat' => 'grey'])]);   // 300 XP, level 6

        $this->buy($user, 'animal', 'dog')->assertOk()
            ->assertJson(['ok' => true, 'spent' => 200, 'xp' => 100, 'owned' => ['animal' => ['cat', 'dog']]]);
        $this->buy($user, 'animal', 'dog')->assertOk()->assertJson(['spent' => 0, 'xp' => 100]);
        $this->assertSame(1, ShopPurchase::where('user_id', $user->id)->count());
        $this->assertSame(6, StudentDashboardService::levelOf($user));

        // Wearing it: the coat keeps its place in the list (grey is the cat's 2nd, chocolate the dog's 2nd).
        $this->actingAs($user)->postJson(route('student.shop.equip'), ['kind' => 'animal', 'key' => 'dog'])
            ->assertOk()->assertJsonPath('worn.animal', 'dog');
        $this->assertSame(['dog', 'chocolate'], [$user->refresh()->avatar['animal'], $user->avatar['coat']]);
    }

    public function test_what_cannot_be_afforded_is_free_unknown_or_out_of_rank_is_refused(): void
    {
        $user = $this->student(1);   // 100 XP, level 2

        $this->buy($user, 'suit', 'pilot')->assertStatus(402)->assertJson(['ok' => false, 'xp' => 100])
            ->assertJsonPath('error', fn ($e) => str_contains($e, '200 more XP'));
        $this->buy($user, 'suit', 'explorer')->assertNotFound();          // free
        $this->buy($user, 'animal', 'lion')->assertNotFound();            // a captain's
        $this->buy($user, 'frame', 'stardust-default')->assertNotFound(); // comes with the rank
        $this->buy($user, 'frame', 'nope-a')->assertNotFound();
        $this->buy($user, 'frame', 'comet-a')->assertForbidden()->assertJsonPath('error', fn ($e) => str_contains($e, 'Reach level 10'));
        $this->actingAs($user)->postJson('/shop/hat/top/buy')->assertNotFound();
        $this->assertSame(0, ShopPurchase::count());

        $this->actingAs(User::factory()->create(['role' => 'faculty']))->postJson(route('student.shop.buy', ['kind' => 'suit', 'key' => 'pilot']))->assertForbidden();
    }

    public function test_a_bought_frame_is_worn_on_the_leaderboard_and_the_rank_default_follows_the_rank(): void
    {
        $user = $this->student(3);   // level 6: Stardust

        $this->buy($user, 'frame', 'stardust-a')->assertOk()->assertJson(['spent' => 200]);
        $this->actingAs($user)->postJson(route('student.shop.equip'), ['kind' => 'frame', 'key' => 'stardust-a'])
            ->assertOk()->assertJsonPath('worn.frame', 'stardust-a');
        $this->assertSame('stardust-a', $user->refresh()->frame);
        $this->assertSame('stardust-a', StudentDashboardService::build($user)['frame']);

        // Not owned, or a rank they haven't reached: refused.
        $this->actingAs($user)->postJson(route('student.shop.equip'), ['kind' => 'frame', 'key' => 'stardust-b'])->assertForbidden();
        $this->actingAs($user)->postJson(route('student.shop.equip'), ['kind' => 'frame', 'key' => 'comet-default'])->assertForbidden();
        $this->actingAs($user)->postJson(route('student.shop.equip'), ['kind' => 'hat', 'key' => 'x'])->assertUnprocessable();

        // Picking the rank's default means "always my rank's default".
        $this->actingAs($user)->postJson(route('student.shop.equip'), ['kind' => 'frame', 'key' => 'stardust-default'])->assertOk();
        $this->assertNull($user->refresh()->frame);
    }

    public function test_gems_trade_for_spendable_xp_that_never_raises_the_level(): void
    {
        $user = $this->student($this->firstModule());   // one module: 10 gems
        $xp = StudentDashboardService::balance($user);
        $level = StudentDashboardService::levelOf($user);
        $this->assertSame(StudentDashboardService::GEMS_PER_MODULE, StudentDashboardService::gems($user));

        $gain = 4 * config('ranks.xp_per_gem');
        $this->actingAs($user)->postJson(route('student.shop.exchange'), ['gems' => 4])
            ->assertOk()->assertJson(['ok' => true, 'traded' => 4, 'gained' => $gain, 'gems' => 6, 'xp' => $xp + $gain]);
        $this->assertSame($level, StudentDashboardService::levelOf($user));
        $this->assertSame(6, StudentDashboardService::home($user)['badges']);

        $this->actingAs($user)->postJson(route('student.shop.exchange'), ['gems' => 7])->assertStatus(422)->assertJson(['ok' => false, 'gems' => 6]);
        $this->actingAs($user)->postJson(route('student.shop.exchange'), ['gems' => 0])->assertUnprocessable();
    }

    public function test_reaching_a_new_rank_celebrates_once(): void
    {
        $user = $this->student(7);   // 700 XP: level 10, Comet

        $this->actingAs($user)->get(route('student.dashboard'))->assertOk()
            ->assertSee('Rank up!')->assertSee('Comet')->assertSee('data-frame="comet-default"', false);
        $this->assertSame(1, $user->refresh()->rank_seen);
        $this->actingAs($user)->get(route('student.dashboard'))->assertOk()->assertDontSee('Rank up!');

        $this->actingAs($this->student(1))->get(route('student.dashboard'))->assertOk()->assertDontSee('Rank up!');
    }

    public function test_students_start_over_as_cats_and_captains_keep_their_animals(): void
    {
        $dog = User::factory()->create(['role' => 'student', 'avatar' => AvatarService::normalize(['animal' => 'dog', 'coat' => 'spotty', 'suit' => 'pilot'])]);
        $cat = User::factory()->create(['role' => 'student', 'avatar' => AvatarService::normalize(['animal' => 'cat', 'coat' => 'cream'])]);
        $captain = User::factory()->create(['role' => 'faculty', 'avatar' => AvatarService::normalize(['animal' => 'eagle'], 'faculty')]);

        (require database_path('migrations/2026_10_17_000006_reset_student_animals_to_cat.php'))->up();

        $this->assertSame(['cat', 'cream', 'pilot'], [$dog->refresh()->avatar['animal'], $dog->avatar['coat'], $dog->avatar['suit']]);
        $this->assertSame(['cat', 'cream'], [$cat->refresh()->avatar['animal'], $cat->avatar['coat']]);
        $this->assertSame('eagle', $captain->refresh()->avatar['animal']);
    }
}
