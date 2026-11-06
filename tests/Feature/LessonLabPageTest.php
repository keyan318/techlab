<?php

namespace Tests\Feature;

use App\Services\CourseProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The full-page simulator that a lesson's "Configure it yourself" button opens.
 */
class LessonLabPageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $skipQuizGate = true;   // about finishing lessons, not the quiz lock

    private function labPage(string $module, string $lesson, string $slug = 'networking'): string
    {
        return route('student.planet.lab', compact('slug', 'module', 'lesson'));
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get($this->labPage('m1', 'lesson01'))->assertRedirect(route('login'));
    }

    public function test_the_first_lesson_lab_opens_the_simulator_on_its_own_page(): void
    {
        $user = $this->studentIn('networking', 'networking-fundamentals');

        $this->actingAs($user)->get($this->labPage('m1', 'lesson01'))
            ->assertOk()
            ->assertSee('netsim-app/app.html?lab=m1-l1', false)
            ->assertSee('Back to lesson')
            ->assertSee('Lesson 1.1')
            ->assertSee('First Contact')
            // The lesson link is a plain href; the complete URL lives in the page's JSON config (slashes escaped).
            ->assertSee('href="'.route('student.planet.module.lesson', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson01']).'"', false)
            ->assertSee(str_replace('/', '\\/', route('student.planet.lab.complete', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson01'])), false);
    }

    public function test_a_locked_lesson_lab_is_not_reachable(): void
    {
        $user = $this->studentIn('networking', 'networking-fundamentals');

        // Lesson 1.3 is locked until 1.2 is done: the student is sent back to where they are.
        $this->actingAs($user)->get($this->labPage('m1', 'lesson03'))
            ->assertRedirect(route('student.planet.module.lesson', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson01']));
    }

    public function test_it_offers_the_next_lesson_once_the_lab_is_already_done(): void
    {
        $user = $this->studentIn('networking', 'networking-fundamentals');
        CourseProgressService::markComplete($user, 'm1', 'lesson01', 'networking');

        $this->actingAs($user)->get($this->labPage('m1', 'lesson01'))
            ->assertOk()
            ->assertSee('Lab complete')
            ->assertSee(route('student.planet.module.lesson', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson02']), false);
    }

    public function test_the_last_lesson_has_no_next_lesson_link(): void
    {
        $user = $this->studentIn('networking', 'networking-fundamentals');
        foreach (CourseProgressService::order('networking') as $item) {
            CourseProgressService::markComplete($user, $item['module'], $item['lesson'], 'networking');
        }

        $this->actingAs($user)->get($this->labPage('m4', 'lesson06'))
            ->assertOk()
            ->assertSee('Lab complete')
            ->assertDontSee('class="lab-next"', false);   // no link element (the script only builds one after a pass)
    }

    public function test_lessons_without_a_lab_and_unknown_planets_are_404(): void
    {
        $user = $this->studentIn();

        // Python lessons finish through the code editor, not a simulator lab.
        $this->actingAs($user)->get($this->labPage('m1', 'lesson01', 'programming'))->assertNotFound();
        $this->actingAs($user)->get($this->labPage('m1', 'lesson01', 'deep-space'))->assertNotFound();
    }

    public function test_a_security_lesson_opens_the_citadel_sim_instead_of_netsim(): void
    {
        $user = $this->studentIn('cybersecurity', 'information-security-1', ['planets' => ['cybersecurity']]);

        $html = $this->actingAs($user)->get($this->labPage('m1', 'lesson01', 'cybersecurity'))
            ->assertOk()
            ->assertSee('citadel-sim/index.html?lab=c1-l1&u='.$user->id, false)
            ->assertSee('Citadel Defense Lab')
            ->assertSee('Know Your Citadel')
            ->assertDontSee('netsim-app')
            ->getContent();

        // The page only trusts messages from the simulator it opened.
        $this->assertStringContainsString('"source":"citadel"', $html);
    }

    public function test_ias1_citadel_labs_turn_on_the_background_music(): void
    {
        $user = $this->studentIn('cybersecurity', 'information-security-1', ['planets' => ['cybersecurity']]);

        $this->actingAs($user)->get($this->labPage('m1', 'lesson01', 'cybersecurity'))
            ->assertOk()
            ->assertSee('citadel-sim/index.html?lab=c1-l1&u='.$user->id.'&music=1', false);
    }

    public function test_networking_labs_play_the_music_from_the_lab_page(): void
    {
        // NetSim has no music of its own, so the page around it plays it (the iframe gets no ?music=1).
        $this->actingAs($this->studentIn('networking', 'networking-fundamentals'))
            ->get($this->labPage('m1', 'lesson01'))
            ->assertOk()
            ->assertDontSee('music=1', false)
            ->assertSee('citadel-sim\/js\/music.js', false)
            ->assertSee('id="lab-music"', false);
    }

    public function test_ias2_labs_have_no_music_yet(): void
    {
        // IAS2 shares Citadel Sim but has not been given music yet.
        $ias2 = $this->studentIn('cybersecurity', 'information-assurance-security-2', ['role' => 'student', 'planets' => ['cybersecurity']]);
        $this->actingAs($ias2)
            ->get($this->labPage('m1', 'lesson01', 'information-assurance-security-2'))
            ->assertOk()
            ->assertSee('citadel-sim/index.html?lab=a1-l1', false)
            ->assertDontSee('music=1', false)
            ->assertDontSee('music.js', false);
    }

    public function test_every_lab_has_astros_cheer_pop_up(): void
    {
        $this->actingAs($this->studentIn('networking', 'networking-fundamentals'))
            ->get($this->labPage('m1', 'lesson01'))
            ->assertOk()
            ->assertSee('id="astro-cheer"', false)
            ->assertSee('astroCheer.show', false);
    }

    public function test_a_network_lab_still_listens_to_netsim(): void
    {
        $html = $this->actingAs($this->studentIn('networking', 'networking-fundamentals'))->get($this->labPage('m1', 'lesson01'))->assertOk()->getContent();

        $this->assertStringContainsString('"source":"netsim"', $html);
        $this->assertStringContainsString('Relay Lab', $html);
    }

    public function test_the_lesson_page_shows_the_button_instead_of_an_embedded_simulator(): void
    {
        $user = $this->studentIn('networking', 'networking-fundamentals');

        $html = $this->actingAs($user)
            ->get(route('student.planet.module.lesson.fragment', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Configure it yourself', $html);
        $this->assertStringContainsString($this->labPage('m1', 'lesson01'), $html);
        $this->assertStringNotContainsString('<iframe', $html);
    }

    public function test_the_lab_page_is_never_cached_so_its_csrf_token_stays_fresh(): void
    {
        $user = $this->studentIn('networking', 'networking-fundamentals');

        $response = $this->actingAs($user)->get($this->labPage('m1', 'lesson01'))->assertOk();

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }
}
