<?php

namespace Tests\Feature;

use App\Http\Controllers\PlanetController;
use App\Models\CourseAssignment;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonLabService;
use App\Services\LessonQuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacultyLessonLabTest extends TestCase
{
    use RefreshDatabase;

    private const COURSE = 'computer-programming-1';

    private function faculty(): User
    {
        $crew = $this->crewFor('programming', self::COURSE);
        CourseAssignment::firstOrCreate(['planet' => 'programming', 'course_slug' => self::COURSE], ['faculty_id' => $crew->teacher_id]);

        return $crew->faculty;
    }

    private function url(string $name, string $lesson = 'lesson01', string $course = self::COURSE): string
    {
        return route("faculty.courses.{$name}", ['course' => $course, 'module' => 'm1', 'lesson' => $lesson]);
    }

    private function lab(array $overrides = []): array
    {
        return $overrides + [
            'title' => 'Say "hi" to the crew',
            'difficulty' => 'Medium',
            'instructions' => "Print two lines:\nHello, crew!\nReady.",
            'starter_code' => "#include <stdio.h>\n\nint main() {\n    return 0;\n}",
            'expected_output' => "Hello, crew!\nReady.",
            'hint_body' => 'Use printf twice.',
            'hint_code' => "#include <stdio.h>\nint main() { printf(\"Hello, crew!\\nReady.\\n\"); return 0; }",
        ];
    }

    public function test_the_editor_starts_from_the_lessons_own_lab_and_answer_key(): void
    {
        $doc = $this->actingAs($this->faculty())->getJson($this->url('lab'))->assertOk()->json();

        $this->assertTrue($doc['exists']);
        $this->assertFalse($doc['edited']);
        $this->assertSame('Orders Astro Can Follow', $doc['lab']['title']);
        $this->assertStringContainsString('Console online.', $doc['lab']['instructions']);
        $this->assertSame(CourseProgressService::order(self::COURSE)[0]['expected'], $doc['lab']['expected_output']);
        $this->assertSame('C', $doc['language']);
        // The lab editor runs the answer code in the course's own C runtime (Programming 1: JSCPP).
        $this->assertSame('c', $doc['lang']);
        $this->assertStringContainsString('js/jscpp-worker.js?v=', $doc['worker']);
    }

    public function test_only_the_original_python_course_is_python_and_faculty_built_courses_are_c(): void
    {
        $this->assertSame('python', PlanetController::editorLang('programming'));
        $this->assertSame('c', PlanetController::editorLang('computer-programming-2'));
        $this->assertSame('c', PlanetController::editorLang('intro-to-c-by-a-faculty-member'));
    }

    public function test_a_saved_lab_is_what_students_get_and_what_their_output_is_checked_against(): void
    {
        $faculty = $this->faculty();
        $this->actingAs($faculty)->putJson($this->url('lab.update'), $this->lab())->assertOk();

        // The lesson's "Code it yourself" form carries the new lab; the hint stays server-side.
        $html = $this->actingAs($faculty)
            ->get(route('student.planet.module.lesson.fragment', ['slug' => self::COURSE, 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk()->getContent();
        $this->assertStringContainsString('value="Say &quot;hi&quot; to the crew"', $html);
        $this->assertStringContainsString('value="Medium"', $html);
        $this->assertStringNotContainsString('Use printf twice.', $html);
        $this->assertStringContainsString('Hello, crew!', LessonLabService::fromHtml($html)['instructions']);

        // The bought hint is the faculty's.
        $hint = LessonQuizService::hint(LessonQuizService::source('m1', 'lesson01', self::COURSE));
        $this->assertSame('Use printf twice.', $hint['body']);
        $this->assertStringContainsString('Hello, crew!', $hint['code']);

        // And the answer key is the faculty's: the old output no longer passes, the new one does.
        $this->assertSame("Hello, crew!\nReady.", CourseProgressService::expectedFor('m1', 'lesson01', self::COURSE));
        $student = $this->studentIn('programming', self::COURSE);
        $complete = route('student.planet.lesson.complete', ['slug' => self::COURSE, 'module' => 'm1', 'lesson' => 'lesson01']);
        $this->actingAs($student)->postJson($complete, ['output' => 'Console online.'])->assertStatus(422);
        // Right output: past the output check, stopped only by the Knowledge Check gate.
        $this->actingAs($student)->postJson($complete, ['output' => "Hello, crew!\r\nReady.\n"])
            ->assertStatus(403)->assertJsonFragment(['error' => 'Pass the Knowledge Check first (at most 2 mistakes).']);
    }

    public function test_resetting_brings_the_original_lab_back(): void
    {
        $faculty = $this->faculty();
        $original = CourseProgressService::expectedFor('m1', 'lesson01', self::COURSE);
        $this->actingAs($faculty)->putJson($this->url('lab.update'), $this->lab())->assertOk();
        $this->assertTrue($this->actingAs($faculty)->getJson($this->url('lab'))->json('edited'));

        $this->actingAs($faculty)->deleteJson($this->url('lab.reset'))->assertOk();

        $this->assertSame($original, CourseProgressService::expectedFor('m1', 'lesson01', self::COURSE));
        $this->assertSame('Orders Astro Can Follow', LessonLabService::fromHtml(LessonQuizService::source('m1', 'lesson01', self::COURSE))['title']);
    }

    public function test_a_lesson_the_faculty_added_can_get_a_lab(): void
    {
        $faculty = $this->faculty();
        $outlineUrl = route('faculty.courses.outline', ['course' => self::COURSE]);
        $outline = $this->actingAs($faculty)->getJson($outlineUrl)->json('outline');
        $outline[0]['lessons'][] = ['key' => null, 'title' => 'Bonus Round', 'hidden' => false];
        $this->actingAs($faculty)->putJson(route('faculty.courses.outline.update', ['course' => self::COURSE]), ['outline' => $outline])->assertOk();

        $doc = $this->actingAs($faculty)->getJson($this->url('lab', 'lesson07'))->assertOk()->json();
        $this->assertFalse($doc['exists']);
        $this->assertNull(CourseProgressService::expectedFor('m1', 'lesson07', self::COURSE));

        $this->actingAs($faculty)->putJson($this->url('lab.update', 'lesson07'), $this->lab())->assertOk();

        $source = LessonQuizService::source('m1', 'lesson07', self::COURSE);
        $fields = LessonLabService::fromHtml($source);
        $this->assertSame('Say "hi" to the crew', $fields['title']);
        $this->assertSame('main.c', $fields['filename']);
        $this->assertStringContainsString(route('student.planet.editor.launch', ['slug' => self::COURSE]), $source);
        // It sits in the lesson before the Quiz stage.
        $this->assertLessThan(strpos($source, '>Quiz</h2>'), strpos($source, 'Code it yourself'));
        $this->assertSame("Hello, crew!\nReady.", CourseProgressService::expectedFor('m1', 'lesson07', self::COURSE));
    }

    public function test_a_lab_needs_instructions_and_an_expected_output(): void
    {
        $faculty = $this->faculty();
        $this->actingAs($faculty)->putJson($this->url('lab.update'), $this->lab(['expected_output' => '']))->assertJsonValidationErrors('expected_output');
        $this->actingAs($faculty)->putJson($this->url('lab.update'), $this->lab(['instructions' => '']))->assertJsonValidationErrors('instructions');
        $this->actingAs($faculty)->putJson($this->url('lab.update'), $this->lab(['difficulty' => 'Impossible']))->assertJsonValidationErrors('difficulty');
    }

    public function test_only_the_courses_faculty_can_edit_labs_and_only_on_programming_courses(): void
    {
        $this->faculty();
        $other = User::factory()->create(['role' => 'faculty']);
        $student = $this->studentIn('programming', self::COURSE);

        foreach ([$other, $student] as $user) {
            $this->actingAs($user)->getJson($this->url('lab'))->assertForbidden();
            $this->actingAs($user)->putJson($this->url('lab.update'), $this->lab())->assertForbidden();
        }

        // Networking and cybersecurity labs are simulators, not the code editor: not editable here yet.
        $net = $this->crewFor('networking', 'networking-fundamentals');
        CourseAssignment::firstOrCreate(['planet' => 'networking', 'course_slug' => 'networking-fundamentals'], ['faculty_id' => $net->teacher_id]);
        $this->actingAs($net->faculty)->getJson($this->url('lab', 'lesson01', 'networking'))->assertNotFound();
    }
}
