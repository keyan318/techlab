<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Networking 2 (ITP831) on the Networking Nebula: "Light the Grid", Season 2 of Networking 1's
 * "Call Earth". Second course on the planet, same lesson machinery as Networking 1. Its labs run
 * either in NetSim (with Cisco IOS routers and switches) or in the Relay Console (router-console
 * labs for what a packet simulator can't show).
 */
class Networking2CourseTest extends TestCase
{
    use RefreshDatabase;

    protected bool $skipQuizGate = true;   // about finishing lessons, not the quiz lock

    private const KEY = 'networking-2';

    private function student(): User
    {
        return $this->studentIn('networking', self::KEY, ['role' => 'student', 'planets' => ['networking']]);
    }

    private function labUrl(string $module, string $lesson): string
    {
        return route('student.planet.lab.complete', ['slug' => self::KEY, 'module' => $module, 'lesson' => $lesson]);
    }

    public function test_the_nebula_lists_networking_2_after_networking_1(): void
    {
        $this->assertSame(['networking-fundamentals', 'networking-2'], array_keys(config('course-catalog.networking.courses')));
        $this->assertSame('ITP831', config('course-catalog.networking.courses.networking-2.code'));

        $this->actingAs($this->student())->get(route('student.planet', 'networking'))
            ->assertOk()
            ->assertSee('Networking 2')
            ->assertSee(route('student.course.overview', ['slug' => 'networking', 'course' => self::KEY]), false);
    }

    public function test_the_course_resolves_to_its_own_blueprint_on_the_networking_planet(): void
    {
        $this->assertSame(self::KEY, CourseProgressService::keyFor('networking', self::KEY));
        $this->assertSame('networking', CourseProgressService::planetFor(self::KEY));
        $this->assertSame(self::KEY, CourseProgressService::catalogFor(self::KEY));
        $this->assertSame('student.planets.networking.net2_course', CourseProgressService::viewBase(self::KEY));

        $this->actingAs($this->student())
            ->get(route('student.planet.play', ['slug' => 'networking', 'course' => self::KEY]))
            ->assertOk()
            ->assertSee('Switches, Routers &amp; VLANs Revisited', false);
    }

    public function test_blueprint_follows_the_syllabus_units(): void
    {
        $order = CourseProgressService::order(self::KEY);
        $this->assertSame(['m1' => 3, 'm2' => 6, 'm3' => 5, 'm4' => 6, 'm5' => 4, 'm6' => 8], collect($order)->countBy('module')->all());

        foreach ($order as $item) {
            $this->assertNull($item['expected'], "{$item['module']}/{$item['lesson']} must not have a coding answer key");
            $this->assertMatchesRegularExpression('/^g\d+-l\d+$/', (string) $item['lab']);
            $this->assertContains($item['sim'], ['netsim', 'console']);
        }
    }

    public function test_the_course_hub_lists_its_own_lessons_and_the_syllabus(): void
    {
        $response = $this->actingAs($this->student())
            ->get(route('student.course.overview', ['slug' => 'networking', 'course' => self::KEY]))
            ->assertOk()
            ->assertSee('Link-State Routing with OSPF');

        $response->assertSee(route('student.planet.module.lesson', ['slug' => self::KEY, 'module' => 'm1', 'lesson' => 'lesson01']), false);
        $response->assertDontSee(route('student.planet.module.lesson', ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson01']), false);
        $response->assertSee('Networking 2 (ITP831) OBE Course Syllabus')
            ->assertSee('networking-2-syllabus-ITP831.pdf', false);
        $this->assertFileExists(public_path('resources/networking-2-syllabus-ITP831.pdf'));
    }

    public function test_passing_the_lab_completes_the_lesson_and_earns_xp_without_touching_networking_1(): void
    {
        $user = $this->student();
        $before = StudentDashboardService::balance($user);

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'g1-l1'])
            ->assertOk()
            ->assertJson(['ok' => true, 'next' => route('student.planet.module.lesson', ['slug' => self::KEY, 'module' => 'm1', 'lesson' => 'lesson02'])]);

        $this->assertTrue(CourseProgressService::isCompleted($user, 'm1', 'lesson01', self::KEY));
        $this->assertTrue(CourseProgressService::isUnlocked($user, 'm1', 'lesson02', self::KEY));
        $this->assertSame($before + StudentDashboardService::XP_PER_LESSON, StudentDashboardService::balance($user->fresh()));
        $this->assertFalse(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'networking'), 'must not leak into Networking 1');
    }

    public function test_a_pass_for_the_wrong_lab_or_out_of_order_is_rejected(): void
    {
        $user = $this->student();
        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'm1-l1'])->assertStatus(422);
        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson03'), ['lab' => 'g1-l3'])->assertForbidden();
        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_opening_the_lesson_does_not_complete_it(): void
    {
        $this->actingAs($this->student())
            ->get(route('student.planet.module.lesson.fragment', ['slug' => self::KEY, 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_the_lab_page_runs_the_right_simulator(): void
    {
        $user = $this->student();
        $this->actingAs($user)->get(route('student.planet.lab', ['slug' => self::KEY, 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk()
            ->assertSee('netsim-app/app.html?lab=g1-l1', false);

        CourseProgressService::markComplete($user, 'm1', 'lesson01', self::KEY);
        $this->actingAs($user)->get(route('student.planet.lab', ['slug' => self::KEY, 'module' => 'm1', 'lesson' => 'lesson02']))
            ->assertOk()
            ->assertSee('relay-console/index.html?lab=g1-l2', false)
            ->assertSee('Relay Console');
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function writtenLessons(): array
    {
        $cases = [];
        foreach (glob(dirname(__DIR__, 2).'/resources/views/student/planets/networking/net2_course/M*/lesson-*.blade.php') ?: [] as $file) {
            if (preg_match('#/(M\d+)/lesson-(\d+)\.blade\.php$#', $file, $m)) {
                $cases[strtolower($m[1]).'/lesson'.$m[2]] = [strtolower($m[1]), 'lesson'.$m[2]];
            }
        }

        return $cases;
    }

    public function test_every_blueprint_lesson_has_a_file(): void
    {
        $blueprint = array_map(fn ($i) => $i['module'].'/'.$i['lesson'], CourseProgressService::order(self::KEY));

        $this->assertEqualsCanonicalizing($blueprint, array_keys(self::writtenLessons()));
    }

    #[DataProvider('writtenLessons')]
    public function test_each_lesson_follows_the_lesson_contract(string $module, string $lesson): void
    {
        $html = LessonQuizService::source($module, $lesson, self::KEY);
        $this->assertNotNull($html);

        $key = LessonQuizService::key($html);
        $this->assertCount(5, $key, 'expected 5 quiz questions');
        foreach ($key as $q => $row) {
            $this->assertMatchesRegularExpression('/^[A-D]$/', $row['correct']);
            $this->assertNotSame('', $row['explanation'], "Q{$q} needs an explanation");
        }

        foreach (['Simple Explanation', 'Field Diagram', 'Quiz', 'Relay Lab'] as $heading) {
            $this->assertStringContainsString('>'.$heading.'</h2>', $html);
        }
        $this->assertMatchesRegularExpression('/<h2 class="section-heading">(Rivet\'s |Volt\'s |Astro\'s |Crew )[A-Za-z]+<\/h2>/', $html);
        // Exactly the five stages: any other h2 would become a stray stage in the player.
        $this->assertSame(5, substr_count($html, '<h2 class="section-heading">'));

        $lab = CourseProgressService::labFor($module, $lesson, self::KEY);
        $sim = CourseProgressService::simFor($module, $lesson, self::KEY);
        $this->assertStringContainsString('data-lab="'.$lab.'"', $html);
        $this->assertFileExists($sim === 'netsim' ? public_path("netsim-app/labs/{$lab}.json") : public_path("relay-console/labs/{$lab}.json"));

        $this->assertStringContainsString('Configure it yourself', $html);
        $this->assertStringContainsString('/student/planet/networking-2/lab/'.$module.'/'.$lesson, $html);
        $this->assertStringNotContainsString('<iframe', $html);

        $served = LessonQuizService::strip($html);
        $this->assertStringNotContainsString('quiz-correct', $served);
        $this->assertStringNotContainsString('✓', $served);
        $this->assertStringNotContainsString('quiz-explanation', $served);
    }

    public function test_every_lab_file_matches_its_blueprint_id(): void
    {
        foreach (CourseProgressService::order(self::KEY) as $item) {
            if ($item['sim'] === 'netsim') {
                $file = public_path("netsim-app/labs/{$item['lab']}.json");
                $this->assertFileExists($file);
                $this->assertSame($item['lab'], json_decode(file_get_contents($file), true)['lab']['id']);
            } else {
                $file = public_path("relay-console/labs/{$item['lab']}.json");
                $this->assertFileExists($file);
                $data = json_decode(file_get_contents($file), true);
                $this->assertSame($item['lab'], $data['id']);
                $this->assertSame('relay-console', $data['app']);
            }
        }
    }

    public function test_networking_1_is_unaffected(): void
    {
        $order = CourseProgressService::order('networking');
        $this->assertCount(24, $order);
        foreach ($order as $item) {
            $this->assertMatchesRegularExpression('/^m\d+-l\d+$/', (string) $item['lab']);
        }
    }
}
