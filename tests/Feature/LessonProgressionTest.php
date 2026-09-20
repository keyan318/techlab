<?php

namespace Tests\Feature;

use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressionTest extends TestCase
{
    use RefreshDatabase;

    private function lessonUrl(string $module, string $lesson, string $slug = 'programming'): string
    {
        return route('student.planet.module.lesson', compact('slug', 'module', 'lesson'));
    }

    private function fragmentUrl(string $module, string $lesson): string
    {
        return route('student.planet.module.lesson.fragment', ['slug' => 'programming', 'module' => $module, 'lesson' => $lesson]);
    }

    private function completeUrl(string $module, string $lesson): string
    {
        return route('student.planet.lesson.complete', ['slug' => 'programming', 'module' => $module, 'lesson' => $lesson]);
    }

    /** Mark every lesson before (and not including) the given position complete. */
    private function completeThrough(User $user, string $module, string $lesson): void
    {
        $stop = CourseProgressService::indexOf($module, $lesson);

        foreach (array_slice(CourseProgressService::order(), 0, $stop + 1) as $item) {
            CourseProgressService::markComplete($user, $item['module'], $item['lesson']);
        }
    }

    // ── Blueprint / drift guards ────────────────────────────────────────────

    public function test_course_blueprint_has_all_35_lessons_in_order(): void
    {
        $order = CourseProgressService::order();

        $this->assertCount(35, $order);
        $this->assertSame(['module' => 'm1', 'lesson' => 'lesson01'], array_intersect_key($order[0], array_flip(['module', 'lesson'])));
        $this->assertSame('m7', $order[34]['module']);
    }

    public function test_sidebar_lesson_order_matches_the_server_blueprint(): void
    {
        $html = file_get_contents(resource_path('views/components/programming-sidebar.blade.php'));
        preg_match_all('/data-module="(m\d)" data-lesson="(lesson\d+)"/', $html, $m, PREG_SET_ORDER);

        $sidebar = array_map(fn ($x) => $x[1].'/'.$x[2], $m);
        $server  = array_map(fn ($x) => $x['module'].'/'.$x['lesson'], CourseProgressService::order());

        $this->assertSame($server, $sidebar, 'Sidebar DOM order drifted from config/course-structure.php');
    }

    public function test_m1_lessons_one_through_six_have_answer_keys(): void
    {
        foreach (['lesson01', 'lesson02', 'lesson03', 'lesson04', 'lesson05', 'lesson06'] as $lesson) {
            $this->assertNotNull(CourseProgressService::expectedFor('m1', $lesson), "m1/{$lesson} has no answer key");
        }
    }

    // ── Direct-URL locking ──────────────────────────────────────────────────

    public function test_fresh_user_can_open_first_lesson(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->lessonUrl('m1', 'lesson01'))
            ->assertOk();
    }

    public function test_second_lesson_redirects_back_when_first_is_incomplete(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->lessonUrl('m1', 'lesson02'))
            ->assertRedirect($this->lessonUrl('m1', 'lesson01'));
    }

    public function test_deep_lesson_url_redirects_for_a_new_user(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->lessonUrl('m4', 'lesson03'))
            ->assertRedirect($this->lessonUrl('m1', 'lesson01'));
    }

    public function test_alternate_url_spellings_cannot_bypass_the_lock(): void
    {
        $user = User::factory()->create();

        foreach (['m1/lesson-02', 'M1/lesson02', 'M1/lesson-02'] as $path) {
            [$module, $lesson] = explode('/', $path);

            $this->actingAs($user)
                ->get("/student/planet/programming/{$module}/{$lesson}")
                ->assertRedirect($this->lessonUrl('m1', 'lesson01'));

            $this->actingAs($user)
                ->get("/student/planet/programming/{$module}/{$lesson}/fragment")
                ->assertForbidden();
        }
    }

    public function test_locked_fragment_is_forbidden_and_unlocked_fragment_is_served(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get($this->fragmentUrl('m1', 'lesson02'))->assertForbidden();
        $this->actingAs($user)->get($this->fragmentUrl('m1', 'lesson01'))->assertOk();
    }

    public function test_lesson_outside_the_blueprint_is_404(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->lessonUrl('m1', 'lesson07'))
            ->assertNotFound();
    }

    public function test_guest_is_redirected_to_login_and_fragment_is_401(): void
    {
        $this->get($this->lessonUrl('m1', 'lesson01'))->assertRedirect(route('login'));
        $this->get($this->fragmentUrl('m1', 'lesson01'))->assertUnauthorized();
    }

    public function test_other_planets_are_not_gated(): void
    {
        $user = User::factory()->create();

        // The middleware must step aside for planets without a lesson blueprint: the request
        // reaches the controller (its own "Lesson not found" 404, not our 403/redirect).
        $this->actingAs($user)
            ->get(route('student.planet.module.lesson.fragment', ['slug' => 'cybersecurity', 'module' => 'm2', 'lesson' => 'lesson04']))
            ->assertNotFound()
            ->assertSee('Lesson not found', false);

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    // ── Planet entry points ─────────────────────────────────────────────────

    public function test_programming_overview_renders_the_first_lesson_not_a_missing_view(): void
    {
        // Regression: show() passed no lessonView, so the shell guessed
        // "…python_course.M1.lesson01" while the file is lesson-01.blade.php,
        // and the page rendered the "Lesson not found" placeholder instead.
        $this->actingAs(User::factory()->create())
            ->get(route('student.planet.play', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()
            ->assertSee('Lesson 1.1: First Signal')
            ->assertDontSee('The selected lesson file does not exist yet.');
    }

    public function test_planet_shows_a_course_picker_before_the_course(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('student.planet', ['slug' => 'programming']))
            ->assertOk()
            ->assertSee('Python')
            ->assertSee('0% Complete')
            ->assertSee(route('student.planet.play', ['slug' => 'programming', 'course' => 'python']), false);

        foreach (['networking', 'cybersecurity'] as $slug) {
            $this->actingAs($user)->get(route('student.planet', ['slug' => $slug]))->assertOk()->assertSee('Course');
        }

        $this->actingAs($user)
            ->get('/student/planet/programming/course/java')
            ->assertNotFound();
    }

    public function test_lesson_route_passes_course_to_every_planet_shell(): void
    {
        // Regression: viewModuleLesson() never passed $course, so the
        // networking/cybersecurity shells died with "Undefined variable $course".
        // They still fail on a separate missing component view, so assert on the
        // variable itself rather than the status code.
        $view = $this->actingAs(User::factory()->create())
            ->get($this->lessonUrl('m1', 'lesson01'))
            ->assertOk()
            ->original;

        $this->assertArrayHasKey('course', $view->getData());
        $this->assertSame('Programming City', $view->getData()['course']['title']);
    }

    // ── Completing a challenge ──────────────────────────────────────────────

    public function test_wrong_output_is_rejected_and_records_nothing(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson($this->completeUrl('m1', 'lesson01'), ['output' => 'nope'])
            ->assertStatus(422)
            ->assertJson(['ok' => false]);

        $this->assertDatabaseCount('lesson_progress', 0);
        $this->actingAs($user)->get($this->lessonUrl('m1', 'lesson02'))->assertRedirect();
    }

    public function test_correct_output_records_completion_and_unlocks_the_next_lesson(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson($this->completeUrl('m1', 'lesson01'), ['output' => "Astro to Codexia: comms online.\n"])
            ->assertOk()
            ->assertJson(['ok' => true, 'next' => $this->lessonUrl('m1', 'lesson02')]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id, 'course' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01',
        ]);

        $this->actingAs($user)->get($this->lessonUrl('m1', 'lesson02'))->assertOk();
        $this->actingAs($user)->get($this->fragmentUrl('m1', 'lesson02'))->assertOk();
        // ...but only the *next* one — lesson 3 is still locked.
        $this->actingAs($user)->get($this->lessonUrl('m1', 'lesson03'))->assertRedirect($this->lessonUrl('m1', 'lesson02'));
    }

    public function test_cannot_post_a_completion_for_a_locked_lesson(): void
    {
        $user = User::factory()->create();

        // Correct answer for lesson 2 ("42"), but lesson 1 was never completed.
        $this->actingAs($user)
            ->postJson($this->completeUrl('m1', 'lesson02'), ['output' => '42'])
            ->assertForbidden();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_lesson_without_a_challenge_rejects_submissions(): void
    {
        $user = User::factory()->create();
        $this->completeThrough($user, 'm1', 'lesson06');

        $this->actingAs($user)
            ->postJson($this->completeUrl('m2', 'lesson01'), ['output' => 'anything'])
            ->assertStatus(422);
    }

    public function test_guest_and_unknown_lessons_are_rejected_by_complete(): void
    {
        $this->postJson($this->completeUrl('m1', 'lesson01'), ['output' => 'x'])->assertUnauthorized();

        $this->actingAs(User::factory()->create())
            ->postJson($this->completeUrl('m9', 'lesson01'), ['output' => 'x'])
            ->assertNotFound();
    }

    public function test_completing_twice_does_not_duplicate(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 2) as $_) {
            $this->actingAs($user)
                ->postJson($this->completeUrl('m1', 'lesson01'), ['output' => 'Astro to Codexia: comms online.'])
                ->assertOk();
        }

        $this->assertSame(1, LessonProgress::where('user_id', $user->id)->count());
    }

    public function test_every_gated_lesson_accepts_its_own_key_in_order(): void
    {
        $user = User::factory()->create();

        foreach (['lesson01', 'lesson02', 'lesson03', 'lesson04', 'lesson05', 'lesson06'] as $lesson) {
            $this->actingAs($user)
                ->postJson($this->completeUrl('m1', $lesson), ['output' => CourseProgressService::expectedFor('m1', $lesson)])
                ->assertOk();
        }

        $this->assertSame(6, LessonProgress::where('user_id', $user->id)->count());
    }

    // ── Module boundary + challenge-less lessons ────────────────────────────

    public function test_module_boundary_unlocks_after_last_lesson_of_previous_module(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get($this->lessonUrl('m2', 'lesson01'))
            ->assertRedirect($this->lessonUrl('m1', 'lesson01'));

        $this->completeThrough($user, 'm1', 'lesson06');

        $this->actingAs($user)->get($this->lessonUrl('m2', 'lesson01'))->assertOk();
    }

    public function test_opening_a_challengeless_lesson_completes_it_so_the_course_does_not_dead_end(): void
    {
        $user = User::factory()->create();
        $this->completeThrough($user, 'm1', 'lesson06');

        $this->actingAs($user)->get($this->fragmentUrl('m2', 'lesson01'))->assertOk();

        $this->assertTrue(CourseProgressService::isCompleted($user, 'm2', 'lesson01'));
        $this->assertTrue(CourseProgressService::isUnlocked($user, 'm2', 'lesson02'));
        $this->assertFalse(CourseProgressService::isUnlocked($user, 'm2', 'lesson03'));
    }

    // ── Persistence across browsers / sessions ──────────────────────────────

    public function test_progress_follows_the_account_into_a_brand_new_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson($this->completeUrl('m1', 'lesson01'), ['output' => 'Astro to Codexia: comms online.'])
            ->assertOk();

        // "Another browser": no session, no cookies, no localStorage — just the account.
        $this->flushSession();
        $this->app['auth']->forgetGuards();

        $this->actingAs(User::find($user->id))
            ->get($this->lessonUrl('m1', 'lesson02'))
            ->assertOk();
    }

    public function test_progress_is_per_user(): void
    {
        $alice = User::factory()->create();
        $bob   = User::factory()->create();

        CourseProgressService::markComplete($alice, 'm1', 'lesson01');

        $this->actingAs($bob)->get($this->lessonUrl('m1', 'lesson02'))->assertRedirect();
        $this->actingAs($alice)->get($this->lessonUrl('m1', 'lesson02'))->assertOk();
    }

    public function test_sidebar_is_seeded_from_the_database(): void
    {
        $user = User::factory()->create();
        CourseProgressService::markComplete($user, 'm1', 'lesson01');

        $this->actingAs($user)
            ->get($this->lessonUrl('m1', 'lesson02'))
            ->assertOk()
            ->assertSee('const SERVER_COMPLETED = ["m1\/lesson01"]', false);
    }

    // ── Editor hand-off ─────────────────────────────────────────────────────

    public function test_launching_the_editor_for_a_gated_lesson_provides_a_server_verified_endpoint_only(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('student.planet.editor.launch', ['slug' => 'programming']), [
                'title'     => 'First Signal',
                'return_to' => $this->lessonUrl('m1', 'lesson01'),
            ])
            ->assertRedirect(route('student.planet.editor', ['slug' => 'programming']))
            ->assertSessionHas('editor_complete_url', $this->completeUrl('m1', 'lesson01'))
            ->assertSessionMissing('editor_expected')
            ->assertSessionMissing('editor_next_lesson');
    }

    public function test_editor_page_offers_submit_for_a_gated_lesson_and_never_ships_the_answer_key(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession([
                'editor_title'        => 'Data Types',
                'editor_return_to'    => $this->lessonUrl('m1', 'lesson03'),
                'editor_complete_url' => $this->completeUrl('m1', 'lesson03'),
            ])
            ->get(route('student.planet.editor', ['slug' => 'programming']))
            ->assertOk()
            ->assertSee('id="submit-btn"', false)
            ->assertSee('const COMPLETE_URL = '.json_encode($this->completeUrl('m1', 'lesson03')), false)
            ->assertDontSee('expectedOutput', false)
            ->assertDontSee('localStorage', false);
    }

    public function test_launching_the_editor_for_an_unknown_lesson_provides_no_endpoint(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('student.planet.editor.launch', ['slug' => 'programming']), [
                'return_to' => $this->lessonUrl('m1', 'lesson07'),
            ])
            ->assertSessionMissing('editor_complete_url');
    }
}
