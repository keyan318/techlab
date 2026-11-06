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
 * Computer Programming 2 (CC103) — "Switch the City On": Season 2 of the Wanderer's story and
 * the third course on the `programming` planet. Keyed by its own catalog id, so it never shares
 * lessons or progress with Programming 1. Its editor runs PicoC rather than JSCPP, because the
 * course needs structs, malloc/free and FILE*.
 */
class ComputerProgramming2CourseTest extends TestCase
{
    use RefreshDatabase;

    protected bool $skipQuizGate = true;   // about finishing lessons, not the quiz lock

    private function student(): User
    {
        return $this->studentIn('programming', 'computer-programming-2', ['role' => 'student']);
    }

    private function completeUrl(string $module, string $lesson): string
    {
        return route('student.planet.lesson.complete', ['slug' => 'computer-programming-2', 'module' => $module, 'lesson' => $lesson]);
    }

    public function test_programming_city_lists_computer_programming_2_after_1(): void
    {
        $this->assertSame(
            ['python', 'computer-programming-1', 'computer-programming-2'],
            array_keys(config('course-catalog.programming.courses'))
        );
        $this->assertSame('CC103', config('course-catalog.programming.courses.computer-programming-2.code'));

        $this->actingAs($this->student())->get(route('student.planet', 'programming'))
            ->assertOk()
            ->assertSee('Computer Programming 2')
            ->assertSee(route('student.course.overview', ['slug' => 'programming', 'course' => 'computer-programming-2']), false);
    }

    public function test_the_course_card_opens_the_shared_lesson_player_on_lesson_one(): void
    {
        $this->assertSame('computer-programming-2', CourseProgressService::keyFor('programming', 'computer-programming-2'));
        $this->assertSame('programming', CourseProgressService::planetFor('computer-programming-2'));
        $this->assertSame('computer-programming-2', CourseProgressService::catalogFor('computer-programming-2'));

        $this->actingAs($this->student())
            ->get(route('student.planet.play', ['slug' => 'programming', 'course' => 'computer-programming-2']))
            ->assertOk()
            ->assertSee('The First Street');
    }

    public function test_the_course_hub_lists_its_own_lessons_urls_and_syllabus(): void
    {
        $moduleCount = count(config('course-structure.computer-programming-2.modules'));
        $lessonCount = count(CourseProgressService::order('computer-programming-2'));

        $response = $this->actingAs($this->student())
            ->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'computer-programming-2']))
            ->assertOk()
            ->assertSee('Down Into the City')
            ->assertSee('The First Street')
            ->assertSee($moduleCount.' '.Str::plural('chapter', $moduleCount))
            ->assertSee($lessonCount.' '.Str::plural('lesson', $lessonCount));

        $response->assertSee(route('student.planet.module.lesson', ['slug' => 'computer-programming-2', 'module' => 'm1', 'lesson' => 'lesson01']), false);
        $response->assertDontSee(route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm1', 'lesson' => 'lesson01']), false);

        $response->assertSee('CC103 — Computer Programming 2 OBE Course Syllabus')
            ->assertSee('computer-programming-2-syllabus-CC103.pdf', false);
        $this->assertFileExists(public_path('resources/computer-programming-2-syllabus-CC103.pdf'));
    }

    public function test_the_syllabus_topics_are_all_covered_in_order(): void
    {
        $titles = array_column(config('course-structure.computer-programming-2.modules'), 'title');

        $this->assertSame([
            'M1 — Down Into the City',     // functions
            'M2 — Street Addresses',       // pointers
            'M3 — Lamp Street',            // arrays and strings
            'M4 — The Machine Registry',   // structures
            'M5 — The Tram Line',          // dynamic variables and linked lists
            'M6 — The City Archive',       // files
        ], $titles);
    }

    public function test_every_lesson_is_a_coding_challenge_with_no_lab(): void
    {
        $order = CourseProgressService::order('computer-programming-2');
        $this->assertCount(31, $order);

        foreach ($order as $item) {
            $this->assertNotNull($item['expected']);
            $this->assertNull($item['lab']);
        }
    }

    public function test_its_editor_plays_music_and_astro_cheers_a_pass(): void
    {
        $this->actingAs($this->student())->get(route('student.planet.editor', ['slug' => 'computer-programming-2']))
            ->assertOk()
            ->assertSee('id="music-btn"', false)
            ->assertSee('citadel-sim\/js\/music.js', false)
            ->assertSee('id="astro-cheer"', false)
            ->assertSee("sfx('lose')", false);
    }

    public function test_its_editor_runs_picoc_while_programming_1_keeps_jscpp(): void
    {
        $user = $this->student();

        $this->actingAs($user)->get(route('student.planet.editor', ['slug' => 'computer-programming-2']))
            ->assertOk()
            ->assertSee('picoc-worker.js', false)
            ->assertDontSee('jscpp-worker.js', false);

        $cp1 = $this->studentIn('programming', 'computer-programming-1', ['role' => 'student']);
        $this->actingAs($cp1)->get(route('student.planet.editor', ['slug' => 'computer-programming-1']))
            ->assertOk()
            ->assertSee('jscpp-worker.js', false)
            ->assertDontSee('picoc-worker.js', false);

        $this->assertFileExists(public_path('js/picoc.js'));
        $this->assertFileExists(public_path('js/picoc-worker.js'));
    }

    public function test_completing_a_challenge_earns_xp_and_does_not_touch_programming_1(): void
    {
        $user = $this->student();
        $before = StudentDashboardService::balance($user);

        $this->actingAs($user)->postJson($this->completeUrl('m1', 'lesson01'), ['output' => "Lamp 1: on\nLamp 2: on\nLamp 3: on\nStreet lit.\n"])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'computer-programming-2'));
        $this->assertSame($before + StudentDashboardService::XP_PER_LESSON, StudentDashboardService::balance($user->fresh()));
        $this->assertFalse(CourseProgressService::isCompleted($user, 'm1', 'lesson01', 'computer-programming-1'), 'must not leak into Programming 1');

        $this->assertDatabaseHas('lesson_progress', ['user_id' => $user->id, 'course' => 'computer-programming-2', 'module' => 'm1', 'lesson' => 'lesson01']);
    }

    public function test_programming_1_answers_do_not_pass_programming_2(): void
    {
        $this->actingAs($this->student())
            ->postJson($this->completeUrl('m1', 'lesson01'), ['output' => "Console online.\n"])
            ->assertStatus(422);

        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_programming_1_progress_does_not_unlock_programming_2(): void
    {
        $user = $this->student();
        CourseProgressService::markComplete($user, 'm1', 'lesson01', 'computer-programming-1');

        $this->assertFalse(CourseProgressService::isUnlocked($user, 'm1', 'lesson02', 'computer-programming-2'));
    }

    public function test_a_student_not_in_the_course_is_refused_the_lesson(): void
    {
        $outsider = User::factory()->create(['role' => 'student']);

        $this->actingAs($outsider)
            ->get(route('student.planet.module.lesson.fragment', ['slug' => 'computer-programming-2', 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertForbidden();
    }

    public function test_the_course_rolls_into_the_programming_planets_percentage(): void
    {
        $user = $this->student();

        CourseProgressService::markComplete($user, 'm1', 'lesson01', 'computer-programming-2');

        $planets = StudentDashboardService::planets($user->fresh());
        $this->assertArrayNotHasKey('computer-programming-2', $planets, 'the course is not its own dashboard card');
        $this->assertSame(1, $planets['programming']['completed']);
    }

    public function test_season_2_keeps_the_programming_story_rules(): void
    {
        $all = '';
        foreach (glob(resource_path('views/student/planets/programming/c2_course/M*/lesson-*.blade.php')) as $file) {
            $all .= file_get_contents($file);
        }

        // Codexia, the Citadel and Planet Doom belong to other worlds' stories, never Programming City's.
        $this->assertDoesNotMatchRegularExpression('/codexia|citadel|planet doom/i', $all);
        // It picks up exactly where Season 1 stopped, and ends with the console remembering the crew.
        $this->assertStringContainsString('HELLO. WHO ARE YOU?', $all);
        $this->assertStringContainsString('HELLO, ASTRO.', $all);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function writtenLessons(): array
    {
        $cases = [];
        foreach (glob(dirname(__DIR__, 2).'/resources/views/student/planets/programming/c2_course/M*/lesson-*.blade.php') ?: [] as $file) {
            if (preg_match('#/(M\d+)/lesson-(\d+)\.blade\.php$#', $file, $m)) {
                $cases[strtolower($m[1]).'/lesson'.$m[2]] = [strtolower($m[1]), 'lesson'.$m[2]];
            }
        }

        return $cases;
    }

    public function test_every_blueprint_lesson_has_a_file(): void
    {
        $blueprint = array_map(fn ($i) => $i['module'].'/'.$i['lesson'], CourseProgressService::order('computer-programming-2'));

        $this->assertEqualsCanonicalizing($blueprint, array_keys(self::writtenLessons()));
    }

    #[DataProvider('writtenLessons')]
    public function test_each_lesson_follows_the_lesson_contract(string $module, string $lesson): void
    {
        $html = LessonQuizService::source($module, $lesson, 'computer-programming-2');
        $this->assertNotNull($html);

        $key = LessonQuizService::key($html);
        $this->assertCount(5, $key, 'expected 5 quiz questions');
        foreach ($key as $q => $row) {
            $this->assertMatchesRegularExpression('/^[A-D]$/', $row['correct']);
            $this->assertNotSame('', $row['explanation'], "Q{$q} needs an explanation");
        }

        foreach (['Simple Explanation', 'Field Diagram', 'Code Example', 'Quiz'] as $heading) {
            $this->assertStringContainsString('>'.$heading.'</h2>', $html);
        }
        $this->assertMatchesRegularExpression('/<h2 class="section-heading">(Astro|Rivet|Volt)(\'s)? [A-Za-z]+<\/h2>/', $html);

        $this->assertStringContainsString('Code it yourself', $html);
        $this->assertStringContainsString(
            route('student.planet.editor.launch', ['slug' => 'computer-programming-2']),
            $html
        );
        $this->assertStringNotContainsString('<iframe', $html);

        // The hint parses (a stray " inside hint_body would silently kill it).
        $hint = LessonQuizService::hint($html);
        $this->assertNotNull($hint);
        $this->assertNotSame('', trim($hint['body']));
        $this->assertStringContainsString('int main', $hint['code']);

        $served = LessonQuizService::strip($html);
        $this->assertStringNotContainsString('quiz-correct', $served);
        $this->assertStringNotContainsString('✓', $served);
        $this->assertStringNotContainsString('quiz-explanation', $served);
        $this->assertStringNotContainsString('name="hint_body"', $served);
        $this->assertStringNotContainsString('name="hint_code"', $served);
    }
}
