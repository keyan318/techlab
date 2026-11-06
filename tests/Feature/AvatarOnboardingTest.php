<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\GemSpend;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Onboarding after sign-up: Astro's hello, then the student builds their pixel animal astronaut
 * (cat, dog, cow, bird or pig, each with its own coats) and picks a codename, saved on the user
 * and shown around the app instead of their full name.
 */
class AvatarOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function look(array $overrides = []): array
    {
        return $overrides + [
            'animal' => 'cat', 'coat' => 'grey', 'suit' => 'explorer', 'suit_color' => 'violet',
            'patch' => 'cybersecurity', 'helmet' => 1, 'visor' => 'gold', 'codename' => 'TurboWoof17',
        ];
    }

    public function test_every_sprite_row_is_24_wide_and_only_uses_known_colours(): void
    {
        $c = config('avatar');
        $known = array_merge(['.'], array_keys($c['ink']), ['f', 'F', 'k', 'K', 's', 'S', 'p', 'v', 'V']);
        $layers = [$c['helmet']];
        foreach ([...$c['animals'], ...$c['suits']] as $sprite) {
            $layers[] = $sprite['rows'];
        }

        foreach ($layers as $rows) {
            foreach ($rows as $y => $row) {
                $this->assertLessThan($c['height'], $y);
                $this->assertSame($c['width'], strlen($row), "row {$y}: {$row}");
                $this->assertSame([], array_diff(str_split($row), $known), "row {$y}: {$row}");
            }
        }

        // Animals, not humans: five for cadets, each with its own coats and codename ideas, and three for captains.
        $this->assertSame(['cat', 'dog', 'cow', 'bird', 'pig'], array_keys(AvatarService::choicesFor('student')['animals']));
        $this->assertSame(['lion', 'gorilla', 'eagle'], array_keys(AvatarService::choicesFor('faculty')['animals']));
        $this->assertSame(['explorer', 'pilot', 'engineer', 'commander'], array_keys(AvatarService::choicesFor('student')['suits']));
        $this->assertSame(['admiral', 'marshal', 'commodore'], array_keys(AvatarService::choicesFor('faculty')['suits']));
        foreach ($c['animals'] as $key => $animal) {
            $this->assertNotEmpty($animal['coats'], $key);
        }
        foreach (AvatarService::choicesFor('student')['animals'] as $key => $animal) {
            $this->assertNotEmpty($c['codenames']['nouns'][$key], $key);
        }
    }

    public function test_a_bad_or_mismatched_look_falls_back_to_valid_choices(): void
    {
        $look = AvatarService::normalize(['animal' => 'pig', 'coat' => 'ginger', 'suit' => 'tuxedo', 'helmet' => '0']);

        $this->assertSame('pig', $look['animal']);
        $this->assertSame('pink', $look['coat']);          // ginger is a cat coat → the pig's first
        $this->assertSame('explorer', $look['suit']);      // unknown → default
        $this->assertFalse($look['helmet']);
        $this->assertSame(config('avatar.default'), AvatarService::normalize(null));

        // A look saved back when astronauts were humans keeps its suit and becomes the default animal.
        $old = AvatarService::normalize(['body' => 'female', 'skin' => 'tan', 'hair' => 'bob', 'suit' => 'commander', 'suit_color' => 'red']);
        $this->assertSame(['cat', 'ginger', 'commander', 'red'], [$old['animal'], $old['coat'], $old['suit'], $old['suit_color']]);
    }

    public function test_the_helmet_hides_whatever_sticks_out_of_the_glass(): void
    {
        // A whisker long enough to poke out past the helmet glass.
        config(['avatar.animals.cat.rows.11' => 'WWWWooocfffnnfffcoooWWWW']);
        $whisker = config('avatar.ink.W');
        $outside = fn (array $px) => array_filter($px, fn ($p) => $p[1] === 11 && ($p[0] < 4 || $p[0] > 19) && $p[2] === $whisker);

        [$withoutHelmet] = AvatarService::pixels($this->look(['animal' => 'cat', 'coat' => 'ginger', 'helmet' => false]));
        [$withHelmet, $glass] = AvatarService::pixels($this->look(['animal' => 'cat', 'coat' => 'ginger', 'helmet' => true]));

        $this->assertNotEmpty($outside($withoutHelmet), 'the whisker sticks out past the head');
        $this->assertEmpty($outside($withHelmet), 'but not out of the helmet');
        $this->assertNotEmpty($glass);
        $this->assertStringContainsString('fill-opacity=', AvatarService::rects($this->look()));
    }

    public function test_signing_up_lands_on_the_welcome_onboarding(): void
    {
        $this->post('/register', [
            'first_name' => 'Nova', 'last_name' => 'Reyes', 'email' => 'nova@example.com', 'password' => 'password123',
            'password_confirmation' => 'password123', 'planets' => ['networking'], 'program' => 'IT', 'year_level' => 1,
        ])->assertRedirect(route('student.welcome'));

        // The Terms come first, then agreeing carries on to the onboarding.
        $this->get(route('student.welcome'))->assertRedirect(route('terms'));
        $this->post(route('terms.accept'), ['agree' => 1])->assertRedirect(route('student.welcome'));

        $this->get(route('student.welcome'))->assertOk()
            ->assertSee('Hey there, Nova!')
            ->assertSee('Pick your animal')
            ->assertSee('Every astronaut needs a codename')
            ->assertSee('Welcome to the crew');
    }

    public function test_saving_the_look_and_codename_stores_both_and_returns_to_the_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->post(route('student.avatar.save'), $this->look() + ['onboarding' => 1])
            ->assertRedirect(route('student.dashboard'))
            ->assertSessionHas('status', "You're all suited up!");

        $user->refresh();
        $this->assertSame(AvatarService::normalize($this->look()), $user->avatar);
        $this->assertSame('TurboWoof17', $user->codename);
        $this->assertSame('TurboWoof17', $user->display_name);
    }

    public function test_a_coat_from_another_animal_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->post(route('student.avatar.save'), $this->look(['coat' => 'husky']))
            ->assertSessionHasErrors('coat');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_codenames_are_unique_whatever_the_capitals_and_must_be_one_word(): void
    {
        User::factory()->create(['role' => 'student', 'codename' => 'CosmicPaws42']);
        $user = User::factory()->create(['role' => 'student']);

        foreach (['cosmicpaws42', 'Cosmic Paws', 'ab', 'Astro', str_repeat('x', 21)] as $bad) {
            $this->actingAs($user)->post(route('student.avatar.save'), $this->look(['codename' => $bad]))
                ->assertSessionHasErrors('codename');
        }
        $this->assertNull($user->fresh()->codename);

        // Keeping your own codename while changing your look is fine.
        $user->update(['codename' => 'Pixel_Pig']);
        $this->actingAs($user)->post(route('student.avatar.save'), $this->look(['codename' => 'Pixel_Pig']))
            ->assertSessionHasNoErrors();
    }

    public function test_the_live_check_says_whether_a_codename_is_free(): void
    {
        User::factory()->create(['role' => 'student', 'codename' => 'MegaMoo61']);
        $me = User::factory()->create(['role' => 'student', 'codename' => 'NovaChirp88']);

        $this->actingAs($me)->getJson(route('student.codename.check', ['codename' => 'megamoo61']))
            ->assertOk()->assertJson(['available' => false]);
        $this->actingAs($me)->getJson(route('student.codename.check', ['codename' => 'NovaChirp88']))
            ->assertJson(['available' => true]);
        $this->actingAs($me)->getJson(route('student.codename.check', ['codename' => 'LaserOink23']))
            ->assertJson(['available' => true]);

        $idea = AvatarService::suggestCodename('pig');
        $this->assertMatchesRegularExpression(AvatarService::CODENAME_PATTERN, $idea);
        $this->assertNull(AvatarService::codenameProblem($idea));
    }

    public function test_classmates_see_the_codename_on_the_leaderboard_not_the_full_name(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $crew = Crew::create(['name' => 'Alpha', 'code' => 'ABC123', 'teacher_id' => $faculty->id, 'planet' => 'programming', 'course_slug' => 'python']);
        $me = User::factory()->create(['role' => 'student', 'name' => 'Keyan Rima']);
        $crew->admit($me);
        $crew->admit(User::factory()->create(['role' => 'student', 'name' => 'Sasa Dela Cruz', 'codename' => 'CosmicPaws42']));

        $this->actingAs($me)->get(route('student.dashboard'))->assertOk()
            ->assertSee('CosmicPaws42')
            ->assertDontSee('Sasa Dela Cruz');
    }

    public function test_the_builder_is_for_students_and_reopens_without_the_intro(): void
    {
        $student = User::factory()->create(['role' => 'student', 'avatar' => AvatarService::normalize($this->look()), 'codename' => 'TurboWoof17']);

        $this->actingAs($student)->get(route('student.avatar'))->assertOk()
            ->assertSee('Save changes')
            ->assertSee('TurboWoof17')
            ->assertDontSee('Every astronaut needs a codename');

        $this->actingAs(User::factory()->create(['role' => 'faculty']))->get(route('student.welcome'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->getJson(route('student.codename.check', ['codename' => 'abc']))->assertForbidden();
        auth()->logout();
        $this->post(route('student.avatar.save'), $this->look())->assertRedirect(route('login'));
    }

    private function captainLook(array $overrides = []): array
    {
        return $overrides + [
            'animal' => 'eagle', 'coat' => 'bald', 'suit' => 'marshal', 'suit_color' => 'red',
            'patch' => 'networking', 'helmet' => 0, 'visor' => 'gold',
        ];
    }

    public function test_a_new_captain_meets_astro_and_builds_a_captain_without_a_codename(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty', 'name' => 'Jose Cruz']);

        $this->actingAs($faculty)->get(route('faculty.welcome'))->assertOk()
            ->assertSee('Welcome aboard, Captain Jose', false)
            ->assertSee('Lion')->assertSee('Admiral')
            ->assertDontSee('Every astronaut needs a codename');

        $this->actingAs($faculty)->post(route('faculty.avatar.save'), $this->captainLook(['onboarding' => 1]))
            ->assertRedirect(route('faculty.dashboard'));

        $faculty->refresh();
        $this->assertSame('eagle', $faculty->avatar['animal']);
        $this->assertSame('marshal', $faculty->avatar['suit']);
        $this->assertFalse($faculty->avatar['helmet']);
        $this->assertNull($faculty->codename);

        // Changing it later: the same builder without Astro's intro.
        $this->actingAs($faculty)->get(route('faculty.avatar'))->assertOk()->assertSee('<title>Your captain', false);
    }

    public function test_captains_and_cadets_can_only_pick_their_own_animals_and_suits(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($faculty)->post(route('faculty.avatar.save'), $this->captainLook(['animal' => 'cat', 'coat' => 'ginger']))->assertSessionHasErrors('animal');
        $this->actingAs($faculty)->post(route('faculty.avatar.save'), $this->captainLook(['suit' => 'explorer']))->assertSessionHasErrors('suit');
        $this->actingAs($student)->post(route('student.avatar.save'), $this->look(['animal' => 'lion', 'coat' => 'golden']))->assertSessionHasErrors('animal');
        $this->actingAs($student)->post(route('student.avatar.save'), $this->look(['suit' => 'admiral']))->assertSessionHasErrors('suit');

        // Random cadet looks never borrow a captain's animal or suit.
        foreach (range(1, 30) as $i) {
            $look = AvatarService::random();
            $this->assertArrayHasKey($look['animal'], AvatarService::choicesFor('student')['animals']);
            $this->assertArrayHasKey($look['suit'], AvatarService::choicesFor('student')['suits']);
        }

        // Students can't open the captain's builder, and a new captain starts as a lion.
        $this->actingAs($student)->get(route('faculty.welcome'))->assertForbidden();
        $this->assertSame('lion', AvatarService::normalize(null, 'faculty')['animal']);
    }

    public function test_the_cat_and_the_explorer_suit_are_free_and_the_rest_cost_xp(): void
    {
        $animals = array_map(fn ($a) => AvatarService::price('animal', $a), ['cat', 'dog', 'cow', 'bird', 'pig']);
        $suits = array_map(fn ($s) => AvatarService::price('suit', $s), ['explorer', 'pilot', 'engineer', 'commander']);

        $this->assertSame([0, 200, 400, 300, 400], $animals);
        $this->assertSame([0, 300, 600, 900], $suits);
        foreach (AvatarService::choicesFor('faculty') as $kind => $items) {
            foreach (array_keys($items) as $key) {
                $this->assertSame(0, AvatarService::price($kind === 'animals' ? 'animal' : 'suit', $key), $key);   // captains never pay
            }
        }
    }

    public function test_a_student_cannot_wear_an_animal_or_suit_they_have_not_bought(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $this->assertSame(['cat'], AvatarService::ownedAnimals($user));
        $this->actingAs($user)->post(route('student.avatar.save'), $this->look(['animal' => 'dog', 'coat' => 'husky']))->assertSessionHasErrors('animal');
        $this->actingAs($user)->post(route('student.avatar.save'), $this->look(['suit' => 'pilot']))->assertSessionHasErrors('suit');
        $this->assertNull($user->refresh()->avatar);

        // Wearing Engineer from before suits had prices doesn't make it theirs: only Explorer is free.
        $old = User::factory()->create(['role' => 'student', 'avatar' => AvatarService::normalize(['suit' => 'engineer'])]);
        $this->assertSame(['explorer'], AvatarService::ownedSuits($old));
        $this->actingAs($old)->post(route('student.avatar.save'), $this->look(['suit' => 'engineer']))->assertSessionHasErrors('suit');
    }

    public function test_a_suit_bought_with_gems_before_the_shop_is_still_owned(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        GemSpend::create(['user_id' => $user->id, 'item' => 'suit:engineer', 'cost' => 20]);

        $this->assertSame(['explorer', 'engineer'], AvatarService::ownedSuits($user));
        $this->actingAs($user)->post(route('student.avatar.save'), $this->look(['suit' => 'engineer']))->assertRedirect(route('student.dashboard'));
        $this->assertSame('engineer', $user->refresh()->avatar['suit']);
    }

    public function test_the_builder_shows_xp_prices_and_what_the_student_owns(): void
    {
        $user = User::factory()->create(['role' => 'student', 'codename' => 'PixelPig42']);
        LessonProgress::create(['user_id' => $user->id, 'course' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson01', 'completed_at' => now()]);

        $this->actingAs($user)->get(route('student.avatar'))->assertOk()
            ->assertViewHas('avatarConfig', fn (array $c) => $c['suits']['commander']['xp'] === 900 && $c['animals']['dog']['xp'] === 200)
            ->assertViewHas('shop', fn (array $shop) => $shop['xp'] === 100 && $shop['owned'] === ['animal' => ['cat'], 'suit' => ['explorer']])
            ->assertSee('Buy for')
            ->assertSee('Visit the Shop');
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->get(route('faculty.avatar'))->assertViewHas('shop', null);
    }

    public function test_the_crew_page_shows_the_captains_character(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty', 'avatar' => AvatarService::normalize(['animal' => 'gorilla'], 'faculty')]);

        $this->assertStringContainsString('<rect', AvatarService::rects($faculty->avatar));
        $this->actingAs($faculty)->get(route('faculty.dashboard'))->assertOk()->assertSee(route('settings'), false);
        $this->actingAs($faculty)->get(route('settings'))->assertOk()->assertSee('Change my captain');
    }
}
