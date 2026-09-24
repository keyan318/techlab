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
 * Information Security 1 on the Cybersecurity Citadel: "Siege of the Citadel".
 * Same lesson machinery as Networking 1, but labs run in the Citadel Sim ("Defend it yourself").
 */
class CybersecurityCourseTest extends TestCase
{
    use RefreshDatabase;

    private function student(): User
    {
        return User::factory()->create(['role' => 'student', 'planets' => ['cybersecurity']]);
    }

    private function labUrl(string $module, string $lesson): string
    {
        return route('student.planet.lab.complete', ['slug' => 'cybersecurity', 'module' => $module, 'lesson' => $lesson]);
    }

    public function test_the_citadel_offers_only_information_security_1(): void
    {
        $this->assertSame(['information-security-1'], array_keys(config('course-catalog.cybersecurity.courses')));

        $this->actingAs($this->student())->get(route('student.planet', 'cybersecurity'))
            ->assertOk()
            ->assertSee('Information Security 1')
            ->assertSee(route('student.planet.play', ['slug' => 'cybersecurity', 'course' => 'information-security-1']), false);
    }

    public function test_the_course_card_opens_the_shared_lesson_player_on_lesson_one(): void
    {
        $this->assertSame('cybersecurity', CourseProgressService::keyFor('cybersecurity', 'information-security-1'));

        $this->actingAs($this->student())
            ->get(route('student.planet.play', ['slug' => 'cybersecurity', 'course' => 'information-security-1']))
            ->assertOk()
            ->assertSee('Know Your Citadel');
    }

    public function test_every_lesson_is_a_citadel_or_netsim_lab_with_no_coding_challenge(): void
    {
        $order = CourseProgressService::order('cybersecurity');
        $this->assertNotEmpty($order);

        foreach ($order as $item) {
            $this->assertNull($item['expected']);
            $this->assertMatchesRegularExpression('/^c\d+-l\d+$/', (string) $item['lab']);
            $this->assertContains($item['sim'], ['citadel', 'netsim']);
        }
        $this->assertSame('citadel', CourseProgressService::simFor('m1', 'lesson01', 'cybersecurity'));
        $this->assertSame('netsim', CourseProgressService::simFor('m1', 'lesson01', 'networking'));
    }

    public function test_clearing_the_defense_lab_completes_the_lesson_and_earns_100_xp(): void
    {
        $user = $this->student();
        $before = StudentDashboardService::balance($user);

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'c1-l1'])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'cybersecurity'));
        $this->assertSame($before + StudentDashboardService::XP_PER_LESSON, StudentDashboardService::balance($user->fresh()));
        $this->assertFalse(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'networking'), 'must not leak into networking');
    }

    public function test_a_pass_for_the_wrong_lab_is_rejected(): void
    {
        $this->actingAs($this->student())->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'm1-l1'])->assertStatus(422);

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_opening_the_lesson_does_not_complete_it(): void
    {
        $this->actingAs($this->student())
            ->get(route('student.planet.module.lesson.fragment', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function writtenLessons(): array
    {
        $cases = [];
        foreach (glob(dirname(__DIR__, 2).'/resources/views/student/planets/cybersecurity/infosec_course/M*/lesson-*.blade.php') ?: [] as $file) {
            if (preg_match('#/(M\d+)/lesson-(\d+)\.blade\.php$#', $file, $m)) {
                $cases[strtolower($m[1]).'/lesson'.$m[2]] = [strtolower($m[1]), 'lesson'.$m[2]];
            }
        }

        return $cases;
    }

    public function test_every_blueprint_lesson_has_a_file(): void
    {
        $blueprint = array_map(fn ($i) => $i['module'].'/'.$i['lesson'], CourseProgressService::order('cybersecurity'));

        $this->assertEqualsCanonicalizing($blueprint, array_keys(self::writtenLessons()));
    }

    #[DataProvider('writtenLessons')]
    public function test_each_lesson_follows_the_lesson_contract(string $module, string $lesson): void
    {
        $html = LessonQuizService::source($module, $lesson, 'cybersecurity');
        $this->assertNotNull($html);

        // Five questions, 10 XP each on the first correct try.
        $key = LessonQuizService::key($html);
        $this->assertCount(5, $key, 'expected 5 quiz questions');
        foreach ($key as $q => $row) {
            $this->assertMatchesRegularExpression('/^[A-D]$/', $row['correct']);
            $this->assertNotSame('', $row['explanation'], "Q{$q} needs an explanation");
        }

        foreach (['Simple Explanation', 'Field Diagram', 'Quiz', 'Defense Lab'] as $heading) {
            $this->assertStringContainsString('>'.$heading.'</h2>', $html);
        }
        $this->assertMatchesRegularExpression('/<h2 class="section-heading">(Astro|Rivet|Volt)(\'s)? [A-Za-z]+<\/h2>/', $html);

        $lab = CourseProgressService::labFor($module, $lesson, 'cybersecurity');
        $sim = CourseProgressService::simFor($module, $lesson, 'cybersecurity');
        $this->assertStringContainsString('data-lab="'.$lab.'"', $html);
        $this->assertFileExists(public_path(($sim === 'citadel' ? 'citadel-sim' : 'netsim-app')."/labs/{$lab}.json"));

        $this->assertStringContainsString('Defend it yourself', $html);
        $this->assertStringContainsString("/student/planet/cybersecurity/lab/{$module}/{$lesson}", $html);
        $this->assertStringNotContainsString('<iframe', $html);

        $served = LessonQuizService::strip($html);
        $this->assertStringNotContainsString('quiz-correct', $served);
        $this->assertStringNotContainsString('✓', $served);
        $this->assertStringNotContainsString('quiz-explanation', $served);
    }

    public function test_every_citadel_lab_file_matches_its_blueprint_id_and_tells_the_story(): void
    {
        foreach (CourseProgressService::order('cybersecurity') as $item) {
            if ($item['sim'] !== 'citadel') {
                continue;
            }
            $lab = json_decode(file_get_contents(public_path("citadel-sim/labs/{$item['lab']}.json")), true);
            $this->assertSame($item['lab'], $lab['id']);
            $this->assertNotEmpty($lab['story']['alert']);
            $this->assertNotEmpty($lab['story']['victory']);
            foreach ($lab['steps'] as $step) {
                $this->assertContains($step['by'], ['astro', 'rivet', 'volt']);
                $this->assertNotEmpty($step['objectives']);
            }
        }
    }
}
