<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Astro's tour (config/astro-tour.php + components/astro-tour): a step-by-step guide for students and captains,
 * one chapter per page. It plays by itself the first time, then only from the Tour button, which
 * is why each finished or skipped chapter is saved on the student.
 */
class AstroTourTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_chapter_is_written_and_points_at_real_pages(): void
    {
        $student = config('astro-tour.chapters');
        $captain = config('astro-tour.faculty_chapters');
        // One users.tour_seen list holds both, so the keys must never clash.
        $this->assertEmpty(array_intersect_key($student, $captain));

        foreach ($student + $captain as $key => $chapter) {
            $this->assertNotEmpty($chapter['label'], $key);
            $this->assertNotEmpty($chapter['steps'], $key);
            foreach ($chapter['routes'] as $route) {
                $this->assertTrue(Route::has($route), "{$key}: no route {$route}");
            }
            foreach ($chapter['steps'] as $i => $step) {
                $this->assertNotEmpty($step['title'], "{$key} step {$i}");
                $this->assertNotEmpty($step['text'], "{$key} step {$i}");
                $this->assertContains($step['missing'] ?? 'skip', ['skip', 'center'], "{$key} step {$i}");
                if (isset($step['next'])) {
                    $this->assertTrue(Route::has($step['next']['route']), "{$key}: no route {$step['next']['route']}");
                }
                // Students are astronauts, never cadets.
                $this->assertStringNotContainsStringIgnoringCase('cadet', $step['title'].$step['text']);
            }
        }
    }

    public function test_a_student_gets_the_chapter_for_the_page_they_are_on(): void
    {
        $student = $this->studentIn(attrs: ['role' => 'student']);

        $this->actingAs($student)->get(route('student.dashboard'))->assertOk()
            ->assertSee('id="astro-tour"', false)
            ->assertSee("Astro's tour · Home", false)
            ->assertSee('data-tour="nav-tour"', false)
            ->assertSee('data-tour="home-profile"', false);

        $this->actingAs($student)->get(route('student.chat'))->assertOk()
            ->assertSee("Astro's tour · Chat", false)
            ->assertSee('data-tour="chat-input"', false);

        $this->actingAs($student)->get(route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk()
            ->assertSee("Astro's tour · Lessons", false);
    }

    public function test_pages_without_a_chapter_and_admins_get_no_tour(): void
    {
        $this->actingAs($this->studentIn(attrs: ['role' => 'student']))->get(route('student.progress'))->assertOk()
            ->assertDontSee('id="astro-tour"', false)
            ->assertSee('data-tour="nav-tour"', false);   // the Tour button still works: it opens the Home chapter

        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('admin.dashboard'))
            ->assertDontSee('id="astro-tour"', false)
            ->assertDontSee('data-tour="nav-tour"', false);
    }

    public function test_a_captain_gets_the_captain_chapter_for_the_page_they_are_on(): void
    {
        $captain = User::factory()->create(['role' => 'faculty', 'name' => 'Ada Lovelace']);

        $this->actingAs($captain)->get(route('faculty.dashboard'))->assertOk()
            ->assertSee('id="astro-tour"', false)
            ->assertSee("Astro's tour · Dashboard", false)
            ->assertSee('Welcome, Captain {name}', false)
            ->assertSee('"Ada"', false)
            ->assertSee('faculty\/tour\/seen', false)
            ->assertSee('data-tour="nav-tour"', false)
            ->assertSee('data-tour="nav-captain"', false)
            ->assertSee('data-tour="fd-tiles"', false)
            ->assertDontSee("Astro's tour · Home", false);

        $this->actingAs($captain)->get(route('faculty.courses.index'))->assertOk()
            ->assertSee("Astro's tour · Courses", false);
    }

    public function test_a_finished_chapter_is_remembered_once(): void
    {
        $student = $this->studentIn(attrs: ['role' => 'student']);

        $this->actingAs($student)->postJson(route('student.tour.seen'), ['chapter' => 'home'])->assertOk();
        $this->actingAs($student)->postJson(route('student.tour.seen'), ['chapter' => 'home'])->assertOk();
        $this->actingAs($student)->postJson(route('student.tour.seen'), ['chapter' => 'chat'])->assertOk()
            ->assertJson(['seen' => ['home', 'chat']]);

        $this->assertSame(['home', 'chat'], $student->fresh()->tour_seen);
    }

    public function test_a_captain_remembers_only_captain_chapters(): void
    {
        $captain = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($captain)->postJson(route('faculty.tour.seen'), ['chapter' => 'captain-home'])->assertOk()
            ->assertJson(['seen' => ['captain-home']]);
        $this->actingAs($captain)->postJson(route('faculty.tour.seen'), ['chapter' => 'home'])->assertStatus(422);
        $this->actingAs($this->studentIn(attrs: ['role' => 'student']))->postJson(route('faculty.tour.seen'), ['chapter' => 'captain-home'])->assertForbidden();

        $this->assertSame(['captain-home'], $captain->fresh()->tour_seen);
    }

    public function test_only_real_chapters_and_only_students(): void
    {
        $this->postJson(route('student.tour.seen'), ['chapter' => 'home'])->assertRedirect(route('login'));
        $this->actingAs($this->studentIn(attrs: ['role' => 'student']))->postJson(route('student.tour.seen'), ['chapter' => 'nope'])->assertStatus(422);
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->postJson(route('student.tour.seen'), ['chapter' => 'home'])->assertForbidden();
    }

    public function test_only_a_freshly_created_account_starts_the_tour_by_itself(): void
    {
        $this->post('/register', [
            'first_name' => 'New', 'last_name' => 'Astronaut', 'email' => 'new@example.com',
            'password' => 'secret-pass', 'password_confirmation' => 'secret-pass',
            'planets' => [User::PLANETS[0]], 'program' => 'CS', 'year_level' => 2,
        ]);
        $this->assertAuthenticated();
        $this->assertTrue(session('tour_fresh'));

        $this->post('/logout');
        $this->assertNull(session('tour_fresh'));

        // Logging back in later never restarts it.
        $this->post('/login', ['email' => 'new@example.com', 'password' => 'secret-pass']);
        $this->assertAuthenticated();
        $this->assertNull(session('tour_fresh'));
    }

    public function test_an_older_account_logging_in_for_the_first_time_gets_no_tour(): void
    {
        $student = User::factory()->create(['role' => 'student', 'password' => 'secret-pass']);

        $this->post('/login', ['email' => $student->email, 'password' => 'secret-pass']);
        $this->assertAuthenticatedAs($student);
        $this->assertNull(session('tour_fresh'));
    }

    public function test_a_new_faculty_account_starts_the_captain_tour(): void
    {
        $domain = config('faculty.email_domain') ?: 'example.com';
        $this->post('/faculty/register', [
            'name' => 'New Captain', 'email' => "captain@{$domain}",
            'password' => 'secret-pass', 'password_confirmation' => 'secret-pass',
        ]);
        $this->assertAuthenticated();
        $this->assertTrue(session('tour_fresh'));
    }

    public function test_the_page_only_auto_plays_the_tour_in_a_fresh_session(): void
    {
        $student = $this->studentIn(attrs: ['role' => 'student']);

        $this->actingAs($student)->get(route('student.dashboard'))
            ->assertOk()->assertSee('var FRESH     = false', false);
        $this->actingAs($student)->withSession(['tour_fresh' => true])->get(route('student.dashboard'))
            ->assertOk()->assertSee('var FRESH     = true', false);
    }
}
