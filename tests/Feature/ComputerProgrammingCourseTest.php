<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Computer Programming 1 (CC102) — "The Dead Console": a second course on the
 * `programming` planet, alongside Python. It is keyed by its own catalog id
 * ("computer-programming-1"), not by the planet slug, so it never shares
 * lessons or progress with Python even though they share a planet.
 */
class ComputerProgrammingCourseTest extends TestCase
{
    use RefreshDatabase;

    protected bool $skipQuizGate = true;   // about finishing lessons, not the quiz lock

    private function student(): User
    {
        return $this->studentIn('programming', 'computer-programming-1', ['role' => 'student']);
    }

    private function completeUrl(string $module, string $lesson): string
    {
        return route('student.planet.lesson.complete', ['slug' => 'computer-programming-1', 'module' => $module, 'lesson' => $lesson]);
    }

    public function test_programming_city_offers_python_and_computer_programming_1(): void
    {
        $this->assertSame(
            ['python', 'computer-programming-1', 'computer-programming-2'],
            array_keys(config('course-catalog.programming.courses'))
        );

        $this->actingAs($this->student())->get(route('student.planet', 'programming'))
            ->assertOk()
            ->assertSee('Computer Programming 1')
            ->assertSee(route('student.course.overview', ['slug' => 'programming', 'course' => 'computer-programming-1']), false);
    }

    public function test_the_course_card_opens_the_shared_lesson_player_on_lesson_one(): void
    {
        $this->assertSame('computer-programming-1', CourseProgressService::keyFor('programming', 'computer-programming-1'));
        $this->assertSame('programming', CourseProgressService::planetFor('computer-programming-1'));
        $this->assertSame('computer-programming-1', CourseProgressService::catalogFor('computer-programming-1'));

        $this->actingAs($this->student())
            ->get(route('student.planet.play', ['slug' => 'programming', 'course' => 'computer-programming-1']))
            ->assertOk()
            ->assertSee('Orders Astro Can Follow');
    }

    public function test_the_course_hub_lists_c_lessons_and_urls_not_pythons(): void
    {
        $moduleCount = count(config('course-structure.computer-programming-1.modules'));
        $lessonCount = count(CourseProgressService::order('computer-programming-1'));

        $response = $this->actingAs($this->student())
            ->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'computer-programming-1']))
            ->assertOk()
            ->assertSee('The Dead Console')
            ->assertSee('Orders Astro Can Follow')
            ->assertSee($moduleCount.' '.Str::plural('chapter', $moduleCount))
            ->assertSee($lessonCount.' '.Str::plural('lesson', $lessonCount));

        // The chapter's lesson links must point at the C course's own lesson URLs, not Python's.
        $response->assertSee(route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson01']), false);
        $response->assertDontSee(route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01']), false);

        // The syllabus preview shows on the hub before Start Learning is even clicked
        // (a click-to-preview button wired to the shared filePreview store, not a bare link).
        $response->assertSee('CC102 — Computer Programming 1 OBE Course Syllabus')
            ->assertSee('Tap to preview')
            ->assertSee('computer-programming-1-syllabus-CC102.pdf', false);
    }

    public function test_every_lesson_is_a_coding_challenge_with_no_lab(): void
    {
        $order = CourseProgressService::order('computer-programming-1');
        $this->assertNotEmpty($order);

        foreach ($order as $item) {
            $this->assertNotNull($item['expected']);
            $this->assertNull($item['lab']);
        }
    }

    public function test_completing_a_challenge_earns_xp_and_does_not_touch_pythons_progress(): void
    {
        $user = $this->student();
        $before = StudentDashboardService::balance($user);

        $this->actingAs($user)->postJson($this->completeUrl('m1', 'lesson01'), ['output' => "Console online.\n"])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'computer-programming-1'));
        $this->assertSame($before + StudentDashboardService::XP_PER_LESSON, StudentDashboardService::balance($user->fresh()));
        $this->assertFalse(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'programming'), 'must not leak into the Python course');

        $this->assertDatabaseHas('lesson_progress', ['user_id' => $user->id, 'course' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson01']);
    }

    public function test_wrong_output_is_rejected_and_records_no_progress(): void
    {
        $this->actingAs($this->student())
            ->postJson($this->completeUrl('m1', 'lesson01'), ['output' => 'not even close'])
            ->assertStatus(422);

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_opening_the_lesson_does_not_complete_it(): void
    {
        $this->actingAs($this->student())
            ->get(route('student.planet.module.lesson.fragment', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk();

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_a_student_not_in_the_c_crew_is_refused_the_lesson(): void
    {
        $outsider = User::factory()->create(['role' => 'student']);

        $this->actingAs($outsider)
            ->get(route('student.planet.module.lesson.fragment', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertForbidden();
    }

    public function test_python_progress_does_not_unlock_c_lessons(): void
    {
        $user = $this->student();
        CourseProgressService::markComplete($user, 'm1', 'lesson01', 'programming');

        $this->assertFalse(CourseProgressService::isUnlocked($user, 'm1', 'lesson02', 'computer-programming-1'));
    }

    public function test_the_c_course_rolls_into_the_programming_planets_percentage(): void
    {
        $user = $this->student();

        CourseProgressService::markComplete($user, 'm1', 'lesson01', 'computer-programming-1');

        $planets = StudentDashboardService::planets($user->fresh());
        $this->assertArrayNotHasKey('computer-programming-1', $planets, 'the C course is not its own dashboard card');
        $this->assertSame(1, $planets['programming']['completed']);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function writtenLessons(): array
    {
        $cases = [];
        foreach (glob(dirname(__DIR__, 2).'/resources/views/student/planets/programming/c_course/M*/lesson-*.blade.php') ?: [] as $file) {
            if (preg_match('#/(M\d+)/lesson-(\d+)\.blade\.php$#', $file, $m)) {
                $cases[strtolower($m[1]).'/lesson'.$m[2]] = [strtolower($m[1]), 'lesson'.$m[2]];
            }
        }

        return $cases;
    }

    public function test_every_blueprint_lesson_has_a_file(): void
    {
        $blueprint = array_map(fn ($i) => $i['module'].'/'.$i['lesson'], CourseProgressService::order('computer-programming-1'));

        $this->assertEqualsCanonicalizing($blueprint, array_keys(self::writtenLessons()));
    }

    #[DataProvider('writtenLessons')]
    public function test_each_lesson_follows_the_lesson_contract(string $module, string $lesson): void
    {
        $html = LessonQuizService::source($module, $lesson, 'computer-programming-1');
        $this->assertNotNull($html);

        // Five questions, 10 XP each on the first correct try — same contract every course uses.
        $key = LessonQuizService::key($html);
        $this->assertCount(5, $key, 'expected 5 quiz questions');
        foreach ($key as $q => $row) {
            $this->assertMatchesRegularExpression('/^[A-D]$/', $row['correct']);
            $this->assertNotSame('', $row['explanation'], "Q{$q} needs an explanation");
        }

        // Stage 2 is an everyday analogy with a picture, not a crew scene.
        foreach (['Simple Explanation', 'Picture It', 'Field Diagram', 'Code Example', 'Quiz'] as $heading) {
            $this->assertStringContainsString('>'.$heading.'</h2>', $html);
        }
        $this->assertDoesNotMatchRegularExpression('/<h2 class="section-heading">(Astro|Rivet|Volt)(\'s)? [A-Za-z]+<\/h2>/', $html);

        $this->assertStringContainsString('Code it yourself', $html);
        $this->assertStringContainsString(
            route('student.planet.editor.launch', ['slug' => 'computer-programming-1']),
            $html
        );
        $this->assertStringNotContainsString('<iframe', $html);

        $served = LessonQuizService::strip($html);
        $this->assertStringNotContainsString('quiz-correct', $served);
        $this->assertStringNotContainsString('✓', $served);
        $this->assertStringNotContainsString('quiz-explanation', $served);
        // The answer key never ships to the browser, same guarantee LessonHintTest makes for Python.
        $this->assertStringNotContainsString('name="hint_body"', $served);
        $this->assertStringNotContainsString('name="hint_code"', $served);
    }
}
