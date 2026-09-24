<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NetworkingCourseTest extends TestCase
{
    use RefreshDatabase;

    private function labUrl(string $module, string $lesson): string
    {
        return route('student.planet.lab.complete', ['slug' => 'networking', 'module' => $module, 'lesson' => $lesson]);
    }

    private function fragmentUrl(string $module, string $lesson): string
    {
        return route('student.planet.module.lesson.fragment', ['slug' => 'networking', 'module' => $module, 'lesson' => $lesson]);
    }

    public function test_blueprint_follows_the_syllabus_units(): void
    {
        $order = CourseProgressService::order('networking');
        $perModule = collect($order)->countBy('module')->all();

        $this->assertSame(['m1' => 6, 'm2' => 7, 'm3' => 5, 'm4' => 6], $perModule);
        $this->assertSame('m1', $order[0]['module']);
        $this->assertSame('lesson01', $order[0]['lesson']);
    }

    public function test_every_networking_lesson_has_a_lab_and_no_coding_challenge(): void
    {
        foreach (CourseProgressService::order('networking') as $item) {
            $this->assertNull($item['expected'], "{$item['module']}/{$item['lesson']} must not have a coding answer key");
            $this->assertMatchesRegularExpression('/^m\d+-l\d+$/', (string) $item['lab']);
        }
    }

    public function test_networking_lessons_live_under_their_own_view_folder(): void
    {
        $this->assertSame('student.planets.networking.net_course', CourseProgressService::viewBase('networking'));
        $this->assertSame('student.planets.programming.python_course', CourseProgressService::viewBase('programming'));
        $this->assertSame('student.planets.cybersecurity.infosec_course', CourseProgressService::viewBase('cybersecurity'));
        $this->assertNull(CourseProgressService::viewBase('deep-space'));
    }

    public function test_a_locked_networking_lesson_is_refused(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get($this->fragmentUrl('m1', 'lesson02'))->assertForbidden();
    }

    public function test_opening_a_lab_lesson_does_not_complete_it(): void
    {
        $user = User::factory()->create();

        // Lesson 1 is unlocked; opening it must not count as done (unlike a Python lesson with no challenge).
        $this->actingAs($user)->get($this->fragmentUrl('m1', 'lesson01'));

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_passing_the_lab_completes_the_lesson_and_unlocks_the_next(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'm1-l1'])
            ->assertOk()
            ->assertJson(['ok' => true, 'next' => route('student.planet.module.lesson', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson02'])]);

        $this->assertTrue(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'networking'));
        $this->assertTrue(CourseProgressService::isUnlocked($user, 'm1', 'lesson02', 'networking'));
        $this->assertFalse(CourseProgressService::isCompleted($user, 'm1', 'lesson01'), 'must not leak into the programming course');
    }

    public function test_a_lab_pass_for_the_wrong_lab_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'm4-l6'])->assertStatus(422);

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_a_lab_pass_cannot_skip_ahead(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson03'), ['lab' => 'm1-l3'])->assertForbidden();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_lab_endpoint_requires_login_and_a_real_planet(): void
    {
        $this->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'm1-l1'])->assertUnauthorized();

        $this->actingAs(User::factory()->create())
            ->postJson(route('student.planet.lab.complete', ['slug' => 'deep-space', 'module' => 'm1', 'lesson' => 'lesson01']), ['lab' => 'm1-l1'])
            ->assertNotFound();
    }

    public function test_a_python_lesson_without_a_lab_rejects_the_lab_endpoint(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('student.planet.lab.complete', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01']), ['lab' => 'm1-l1'])
            ->assertStatus(422);
    }

    public function test_dashboard_continues_in_the_planet_the_student_last_touched(): void
    {
        $user = User::factory()->create();
        CourseProgressService::markComplete($user, 'm1', 'lesson01', 'networking');

        $current = StudentDashboardService::currentLesson($user);

        $this->assertSame('Ship\'s Wiring', $current['title']);
        $this->assertStringContainsString('/student/planet/networking/m1/lesson02', $current['url']);
    }

    public function test_dashboard_defaults_to_programming_for_a_new_student(): void
    {
        $current = StudentDashboardService::currentLesson(User::factory()->create());

        $this->assertStringContainsString('/student/planet/programming/m1/lesson01', $current['url']);
    }

    // ── Lesson files: every lesson that exists must follow the lesson contract ─────────────

    /**
     * Found on disk, not from config: data providers run before Laravel boots.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function writtenLessons(): array
    {
        $cases = [];
        $files = glob(dirname(__DIR__, 2).'/resources/views/student/planets/networking/net_course/M*/lesson-*.blade.php') ?: [];

        foreach ($files as $file) {
            if (preg_match('#/(M\d+)/lesson-(\d+)\.blade\.php$#', $file, $m)) {
                $module = strtolower($m[1]);
                $lesson = 'lesson'.$m[2];
                $cases["{$module}/{$lesson}"] = [$module, $lesson];
            }
        }

        return $cases;
    }

    public function test_module_one_is_fully_written(): void
    {
        $written = array_keys(self::writtenLessons());

        foreach (['m1/lesson01', 'm1/lesson02', 'm1/lesson03', 'm1/lesson04', 'm1/lesson05', 'm1/lesson06'] as $expected) {
            $this->assertContains($expected, $written, "{$expected} has no lesson file");
        }
    }

    public function test_module_two_is_fully_written(): void
    {
        $written = array_keys(self::writtenLessons());

        foreach (['m2/lesson01', 'm2/lesson02', 'm2/lesson03', 'm2/lesson04', 'm2/lesson05', 'm2/lesson06', 'm2/lesson07'] as $expected) {
            $this->assertContains($expected, $written, "{$expected} has no lesson file");
        }
    }

    public function test_module_three_is_fully_written(): void
    {
        $written = array_keys(self::writtenLessons());

        foreach (['m3/lesson01', 'm3/lesson02', 'm3/lesson03', 'm3/lesson04', 'm3/lesson05'] as $expected) {
            $this->assertContains($expected, $written, "{$expected} has no lesson file");
        }
    }

    public function test_module_four_is_fully_written_so_the_whole_course_exists(): void
    {
        $written = array_keys(self::writtenLessons());

        foreach (['m4/lesson01', 'm4/lesson02', 'm4/lesson03', 'm4/lesson04', 'm4/lesson05', 'm4/lesson06'] as $expected) {
            $this->assertContains($expected, $written, "{$expected} has no lesson file");
        }

        // Every lesson in the blueprint now has a file: no student can land on "Lesson not found".
        $blueprint = array_map(fn ($i) => $i['module'].'/'.$i['lesson'], CourseProgressService::order('networking'));
        $this->assertEqualsCanonicalizing($blueprint, $written);
    }

    #[DataProvider('writtenLessons')]
    public function test_each_written_lesson_follows_the_lesson_contract(string $module, string $lesson): void
    {
        $html = LessonQuizService::source($module, $lesson, 'networking');
        $this->assertNotNull($html);

        // Answer key: three questions, each with a correct letter and an explanation.
        $key = LessonQuizService::key($html);
        $this->assertCount(3, $key, 'expected 3 quiz questions');
        foreach ($key as $q => $row) {
            $this->assertMatchesRegularExpression('/^[A-D]$/', $row['correct']);
            $this->assertNotSame('', $row['explanation'], "Q{$q} needs an explanation");
        }

        // The five stages the player builds cards from, plus the lab wired to this lesson.
        foreach (['Simple Explanation', 'Field Diagram', 'Quiz', 'Relay Lab'] as $heading) {
            $this->assertStringContainsString('>'.$heading.'</h2>', $html);
        }
        $this->assertMatchesRegularExpression('/<h2 class="section-heading">(Rivet\'s |Volt\'s |Crew )[A-Za-z]+<\/h2>/', $html);
        $lab = CourseProgressService::labFor($module, $lesson, 'networking');
        $this->assertStringContainsString('data-lab="'.$lab.'"', $html);
        $this->assertFileExists(public_path("netsim-app/labs/{$lab}.json"));

        // The lesson only carries a button; the simulator opens full screen on its own page.
        $this->assertStringContainsString('Configure it yourself', $html);
        $this->assertStringContainsString("/student/planet/networking/lab/{$module}/{$lesson}", $html);
        $this->assertStringNotContainsString('<iframe', $html);

        // What the browser gets must not reveal the answers.
        $served = LessonQuizService::strip($html);
        $this->assertStringNotContainsString('quiz-correct', $served);
        $this->assertStringNotContainsString('✓', $served);
        $this->assertStringNotContainsString('quiz-explanation', $served);
    }

    public function test_every_lab_file_matches_its_blueprint_id(): void
    {
        foreach (CourseProgressService::order('networking') as $item) {
            $file = public_path("netsim-app/labs/{$item['lab']}.json");
            if (! is_file($file)) {
                continue;   // lesson not written yet
            }
            $this->assertSame($item['lab'], json_decode(file_get_contents($file), true)['lab']['id']);
        }
    }
}
