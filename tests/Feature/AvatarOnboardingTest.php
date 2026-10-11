<?php

namespace Tests\Feature;

use App\Models\Crew;
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
            'animal' => 'dog', 'coat' => 'husky', 'suit' => 'commander', 'suit_color' => 'violet',
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

        // Animals, not humans: these five, each with its own coats and codename ideas.
        $this->assertSame(['cat', 'dog', 'cow', 'bird', 'pig'], array_keys($c['animals']));
        foreach ($c['animals'] as $key => $animal) {
            $this->assertNotEmpty($animal['coats'], $key);
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
            'name' => 'Nova Reyes', 'email' => 'nova@example.com', 'password' => 'password123',
            'password_confirmation' => 'password123', 'planets' => ['networking'],
        ])->assertRedirect(route('student.welcome'));

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

        $this->actingAs($user)->post(route('student.avatar.save'), $this->look(['coat' => 'ginger']))
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
        $me = User::factory()->create(['role' => 'student', 'crew_id' => $crew->id, 'name' => 'Keyan Rima']);
        User::factory()->create(['role' => 'student', 'crew_id' => $crew->id, 'name' => 'Sasa Dela Cruz', 'codename' => 'CosmicPaws42']);

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
}
