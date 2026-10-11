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
 * Information Assurance and Security 2 on the Cybersecurity Citadel: the rebuild after the
 * siege — governance, secure design, and standing watch. Second course on the planet, same
 * lesson machinery as Information Assurance and Security 1 ("Siege of the Citadel"), except
 * every lab runs in the Citadel Sim only (no netsim lessons in this course).
 */
class InformationAssuranceSecurity2CourseTest extends TestCase
{
    use RefreshDatabase;

    private function student(): User
    {
        return $this->studentIn('cybersecurity', 'information-assurance-security-2', ['role' => 'student', 'planets' => ['cybersecurity']]);
    }

    private function labUrl(string $module, string $lesson): string
    {
        return route('student.planet.lab.complete', ['slug' => 'information-assurance-security-2', 'module' => $module, 'lesson' => $lesson]);
    }

    public function test_the_citadel_lists_information_assurance_security_2_alongside_1(): void
    {
        $this->assertSame(
            ['information-security-1', 'information-assurance-security-2'],
            array_keys(config('course-catalog.cybersecurity.courses'))
        );

        $this->actingAs($this->student())->get(route('student.planet', 'cybersecurity'))
            ->assertOk()
            ->assertSee('Information Assurance and Security 2')
            ->assertSee(route('student.course.overview', ['slug' => 'cybersecurity', 'course' => 'information-assurance-security-2']), false);
    }

    public function test_the_course_card_opens_the_shared_lesson_player_on_lesson_one(): void
    {
        $this->assertSame('information-assurance-security-2', CourseProgressService::keyFor('cybersecurity', 'information-assurance-security-2'));
        $this->assertSame('cybersecurity', CourseProgressService::planetFor('information-assurance-security-2'));
        $this->assertSame('information-assurance-security-2', CourseProgressService::catalogFor('information-assurance-security-2'));

        $this->actingAs($this->student())
            ->get(route('student.planet.play', ['slug' => 'cybersecurity', 'course' => 'information-assurance-security-2']))
            ->assertOk()
            ->assertSee('Why Information Protection Matters');
    }

    public function test_the_course_hub_lists_ias2_lessons_and_urls_not_ias1s(): void
    {
        $moduleCount = count(config('course-structure.information-assurance-security-2.modules'));
        $lessonCount = count(CourseProgressService::order('information-assurance-security-2'));
        $this->assertSame(8, $moduleCount);
        $this->assertSame(40, $lessonCount);

        $response = $this->actingAs($this->student())
            ->get(route('student.course.overview', ['slug' => 'cybersecurity', 'course' => 'information-assurance-security-2']))
            ->assertOk()
            ->assertSee('Why Information Protection Matters');

        // The chapter's lesson links must point at IAS 2's own lesson URLs, not IAS 1's.
        $response->assertSee(route('student.planet.module.lesson', ['slug' => 'information-assurance-security-2', 'module' => 'm1', 'lesson' => 'lesson01']), false);
        $response->assertDontSee(route('student.planet.module.lesson', ['slug' => 'cybersecurity', 'module' => 'm1', 'lesson' => 'lesson01']), false);

        // The syllabus preview shows on the hub before Start Learning is even clicked.
        $response->assertSee('Information Assurance and Security 2 (ITP1333) OBE Course Syllabus')
            ->assertSee('Tap to preview')
            ->assertSee('ias-2-syllabus-ITP1333.pdf', false);
    }

    public function test_every_lesson_is_a_citadel_lab_with_no_coding_challenge_and_no_netsim(): void
    {
        $order = CourseProgressService::order('information-assurance-security-2');
        $this->assertNotEmpty($order);

        foreach ($order as $item) {
            $this->assertNull($item['expected']);
            $this->assertMatchesRegularExpression('/^a\d+-l\d+$/', (string) $item['lab']);
            $this->assertSame('citadel', $item['sim']);
        }
        $this->assertSame('citadel', CourseProgressService::simFor('m1', 'lesson01', 'information-assurance-security-2'));
    }

    public function test_clearing_the_defense_lab_completes_the_lesson_and_earns_100_xp(): void
    {
        $user = $this->student();
        $before = StudentDashboardService::balance($user);

        $this->actingAs($user)->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'a1-l1'])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'information-assurance-security-2'));
        $this->assertSame($before + StudentDashboardService::XP_PER_LESSON, StudentDashboardService::balance($user->fresh()));
        $this->assertFalse(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'cybersecurity'), 'must not leak into information-security-1');
    }

    public function test_a_pass_for_the_wrong_lab_is_rejected(): void
    {
        $this->actingAs($this->student())->postJson($this->labUrl('m1', 'lesson01'), ['lab' => 'c1-l1'])->assertStatus(422);

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_opening_the_lesson_does_not_complete_it(): void
    {
        $this->actingAs($this->student())
            ->get(route('student.planet.module.lesson.fragment', ['slug' => 'information-assurance-security-2', 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function writtenLessons(): array
    {
        $cases = [];
        foreach (glob(dirname(__DIR__, 2).'/resources/views/student/planets/cybersecurity/ias2_course/M*/lesson-*.blade.php') ?: [] as $file) {
            if (preg_match('#/(M\d+)/lesson-(\d+)\.blade\.php$#', $file, $m)) {
                $cases[strtolower($m[1]).'/lesson'.$m[2]] = [strtolower($m[1]), 'lesson'.$m[2]];
            }
        }

        return $cases;
    }

    public function test_every_blueprint_lesson_has_a_file(): void
    {
        $blueprint = array_map(fn ($i) => $i['module'].'/'.$i['lesson'], CourseProgressService::order('information-assurance-security-2'));

        $this->assertEqualsCanonicalizing($blueprint, array_keys(self::writtenLessons()));
    }

    #[DataProvider('writtenLessons')]
    public function test_each_lesson_follows_the_lesson_contract(string $module, string $lesson): void
    {
        $html = LessonQuizService::source($module, $lesson, 'information-assurance-security-2');
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

        $lab = CourseProgressService::labFor($module, $lesson, 'information-assurance-security-2');
        $sim = CourseProgressService::simFor($module, $lesson, 'information-assurance-security-2');
        $this->assertSame('citadel', $sim);
        $this->assertStringContainsString('data-lab="'.$lab.'"', $html);
        $this->assertFileExists(public_path("citadel-sim/labs/{$lab}.json"));

        $this->assertStringContainsString('Defend it yourself', $html);
        $this->assertStringContainsString('/student/planet/information-assurance-security-2/lab/'.$module.'/'.$lesson, $html);
        $this->assertStringNotContainsString('<iframe', $html);

        $served = LessonQuizService::strip($html);
        $this->assertStringNotContainsString('quiz-correct', $served);
        $this->assertStringNotContainsString('✓', $served);
        $this->assertStringNotContainsString('quiz-explanation', $served);
    }

    public function test_every_citadel_lab_file_matches_its_blueprint_id_and_tells_the_story(): void
    {
        foreach (CourseProgressService::order('information-assurance-security-2') as $item) {
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

    public function test_ias1_progress_and_lessons_are_unaffected_by_ias2(): void
    {
        // IAS 1 keeps its own 24-lesson blueprint and its own lab id namespace (c#-l#).
        $order = CourseProgressService::order('cybersecurity');
        $this->assertCount(24, $order);
        foreach ($order as $item) {
            $this->assertMatchesRegularExpression('/^c\d+-l\d+$/', (string) $item['lab']);
        }
    }
}
