<?php

namespace Tests\Feature;

use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonCheckpointService;
use App\Services\LessonContentService;
use App\Services\LessonQuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FacultyCourseEditingTest extends TestCase
{
    use RefreshDatabase;

    private const COURSE = 'computer-programming-1';

    /** The faculty member assigned to Computer Programming 1, who also runs its crew. */
    private function faculty(): User
    {
        $crew = $this->crewFor('programming', self::COURSE);
        CourseAssignment::firstOrCreate(['planet' => 'programming', 'course_slug' => self::COURSE], ['faculty_id' => $crew->teacher_id]);

        return $crew->faculty;
    }

    private function url(string $name, array $params = []): string
    {
        return route("faculty.courses.{$name}", ['course' => self::COURSE] + $params);
    }

    private function lesson(string $lesson = 'lesson01', string $module = 'm1'): array
    {
        return ['module' => $module, 'lesson' => $lesson];
    }

    private function fragment(User $user, string $lesson = 'lesson01', string $module = 'm1'): string
    {
        return $this->actingAs($user)
            ->get(route('student.planet.module.lesson.fragment', ['slug' => self::COURSE, 'module' => $module, 'lesson' => $lesson]))
            ->assertOk()->getContent();
    }

    /** The lesson's editable page, with its first section's text replaced. */
    private function editedDoc(User $faculty, string $html): array
    {
        $doc = $this->actingAs($faculty)->getJson($this->url('content', $this->lesson()))->assertOk()->json();
        $doc['sections'][0]['html'] = $html.' '.$doc['sections'][0]['html'];

        return ['title' => $doc['title'], 'objective' => $doc['objective'], 'sections' => $doc['sections']];
    }

    public function test_an_edited_stage_is_what_students_see(): void
    {
        $faculty = $this->faculty();
        $this->actingAs($faculty)->putJson($this->url('content.update', $this->lesson()), $this->editedDoc($faculty, '<p>Hello from your captain.</p>'))->assertOk();

        $html = $this->fragment($this->studentIn('programming', self::COURSE));
        $this->assertStringContainsString('Hello from your captain.', $html);
        $this->assertStringContainsString('<p class="body-text">Hello from your captain.</p>', $html);
    }

    public function test_only_the_assigned_faculty_member_can_edit(): void
    {
        $this->faculty();
        $other = User::factory()->create(['role' => 'faculty']);
        $student = $this->studentIn('programming', self::COURSE);

        foreach ([$other, $student] as $user) {
            $this->actingAs($user)->getJson($this->url('content', $this->lesson()))->assertForbidden();
            $this->actingAs($user)->putJson($this->url('content.update', $this->lesson()), ['title' => 'x', 'sections' => []])->assertForbidden();
            $this->actingAs($user)->putJson($this->url('outline.update'), ['outline' => [['key' => 'M1', 'title' => 'x', 'lessons' => []]]])->assertForbidden();
        }
        $this->assertDatabaseCount('lesson_contents', 0);
        $this->assertDatabaseCount('course_customizations', 0);
    }

    public function test_scripts_are_stripped_and_the_lesson_still_works(): void
    {
        $faculty = $this->faculty();
        $default = LessonQuizService::defaultSource('m1', 'lesson01', self::COURSE);

        $this->actingAs($faculty)->putJson($this->url('content.update', $this->lesson()),
            $this->editedDoc($faculty, '<p onclick="steal()">Hi <script>alert(1)</script><a href="javascript:alert(1)">x</a><img src="x" onerror="alert(1)"></p>'))->assertOk();

        $source = LessonQuizService::source('m1', 'lesson01', self::COURSE);
        $this->assertStringNotContainsString('<script>alert', $source);
        $this->assertStringNotContainsString('onclick', $source);
        $this->assertStringNotContainsString('onerror', $source);
        $this->assertStringNotContainsString('javascript:', $source);

        // The interactive parts come from the lesson file, untouched: the quiz key, the hint, the checkpoints and the form.
        $this->assertSame(LessonQuizService::key($default), LessonQuizService::key($source));
        $this->assertSame(LessonQuizService::hint($default), LessonQuizService::hint($source));
        $this->assertSame(substr_count($default, 'checkpoint-block'), substr_count($source, 'checkpoint-block'));
        $this->assertStringContainsString('<form method="POST"', $source);

        // And the student still never gets the answers.
        $html = $this->fragment($this->studentIn('programming', self::COURSE));
        $this->assertStringNotContainsString('quiz-correct', $html);
        $this->assertStringNotContainsString('name="hint_code"', $html);
    }

    public function test_a_required_block_dropped_from_the_page_comes_back(): void
    {
        $faculty = $this->faculty();
        $doc = $this->actingAs($faculty)->getJson($this->url('content', $this->lesson()))->assertOk()->json();
        $sections = array_map(fn ($s) => ['heading' => $s['heading'], 'html' => preg_replace('/<div data-keep="\d+"><\/div>/', '', $s['html'])], $doc['sections']);

        $this->actingAs($faculty)->putJson($this->url('content.update', $this->lesson()), ['title' => 'Edited', 'objective' => '', 'sections' => $sections])->assertOk();

        $source = LessonQuizService::source('m1', 'lesson01', self::COURSE);
        $this->assertStringContainsString('<form method="POST"', $source);   // the code exercise is required
        $this->assertSame([], LessonQuizService::key($source));              // the quiz is not
    }

    public function test_checkpoints_can_be_edited_added_and_removed(): void
    {
        $faculty = $this->faculty();
        $doc = $this->actingAs($faculty)->getJson($this->url('content', $this->lesson()))->assertOk()->json();

        // Every checkpoint of the file comes as an editable question, found by its number on the page.
        $this->assertSame('cp1', $doc['checkpointsByNumber']['1']);
        $this->assertSame('Precise', $doc['checkpoints']['cp1']['options'][0]);
        $this->assertSame('A', $doc['checkpoints']['cp1']['correct']);
        $count = count($doc['checkpoints']);

        // Edit the first, remove the last, add a new one at the end of the first section.
        $doc['checkpoints']['cp1'] = ['prompt' => 'Which `step` is precise?', 'options' => ['Add 5 g of salt', 'Add some salt'], 'correct' => 'A', 'explanation' => 'Five grams is exact.'];
        $last = array_key_last($doc['checkpoints']);
        unset($doc['checkpoints'][$last]);
        foreach ($doc['sections'] as &$section) {
            $section['html'] = str_replace('<div data-checkpoint="'.$last.'"></div>', '', $section['html']);
        }
        unset($section);
        $doc['checkpoints']['new1'] = ['prompt' => 'Brand new?', 'options' => ['No', 'Yes', 'Maybe'], 'correct' => 'B', 'explanation' => ''];
        $doc['sections'][0]['html'] .= '<div data-checkpoint="new1"></div>';

        $this->actingAs($faculty)->putJson($this->url('content.update', $this->lesson()), [
            'title' => $doc['title'], 'objective' => $doc['objective'], 'sections' => $doc['sections'], 'checkpoints' => $doc['checkpoints'],
        ])->assertOk();

        $source = (string) LessonQuizService::source('m1', 'lesson01', self::COURSE);
        $key = LessonCheckpointService::key($source);
        $this->assertCount($count, $key);                         // one removed, one added
        $this->assertSame(range(1, $count), array_keys($key));    // numbered in page order
        $this->assertSame('A', $key[1]['correct']);
        $this->assertSame('B', $key[2]['correct']);              // the new one closes the first section
        $this->assertStringContainsString('Which <code>step</code> is precise?', $source);

        // Students get the question without its answer.
        $html = $this->fragment($this->studentIn('programming', self::COURSE));
        $this->assertStringContainsString('Brand new?', $html);
        $this->assertStringNotContainsString('quiz-correct', $html);

        // A checkpoint with no right answer among its choices is refused.
        $doc['checkpoints']['new1']['correct'] = 'D';
        $this->actingAs($faculty)->putJson($this->url('content.update', $this->lesson()), [
            'title' => $doc['title'], 'sections' => $doc['sections'], 'checkpoints' => $doc['checkpoints'],
        ])->assertStatus(422);
    }

    public function test_resetting_brings_the_original_lesson_back(): void
    {
        $faculty = $this->faculty();
        $this->actingAs($faculty)->putJson($this->url('content.update', $this->lesson()), $this->editedDoc($faculty, '<p>Temporary.</p>'))->assertOk();
        $this->actingAs($faculty)->deleteJson($this->url('content.reset', $this->lesson()))->assertOk();

        $this->assertStringNotContainsString('Temporary.', $this->fragment($this->studentIn('programming', self::COURSE)));
        $this->assertDatabaseCount('lesson_contents', 0);
    }

    public function test_every_lesson_survives_a_round_trip_through_the_editor(): void
    {
        foreach (CourseProgressService::order(self::COURSE) as $item) {
            $default = (string) LessonQuizService::defaultSource($item['module'], $item['lesson'], self::COURSE);
            $split = LessonContentService::split($default);
            $doc = ['title' => $split['title'], 'objective' => $split['objective'], 'sections' => array_map(
                fn ($s) => ['heading' => $s['heading'], 'html' => LessonContentService::sanitize($s['html'])], $split['sections']),
                'checkpoints' => $split['checkpoints']];
            $rebuilt = LessonContentService::build($doc, $split, self::COURSE, $item['module'], $item['lesson']);

            $this->assertSame(LessonQuizService::key($default), LessonQuizService::key($rebuilt), $item['module'].'/'.$item['lesson']);
            $this->assertSame(LessonQuizService::hint($default), LessonQuizService::hint($rebuilt), $item['module'].'/'.$item['lesson']);
            $this->assertSame(substr_count($default, 'class="section-heading"'), substr_count($rebuilt, 'class="section-heading"'));
            $this->assertSame(array_column(LessonCheckpointService::key($default), 'correct'), array_column(LessonCheckpointService::key($rebuilt), 'correct'), $item['module'].'/'.$item['lesson']);
        }
    }

    public function test_hiding_a_lesson_takes_it_out_of_the_course(): void
    {
        $faculty = $this->faculty();
        $outline = $this->actingAs($faculty)->getJson($this->url('outline'))->assertOk()->json('outline');
        $outline[0]['lessons'][1]['hidden'] = true;   // M1 lesson 2

        $this->actingAs($faculty)->putJson($this->url('outline.update'), ['outline' => $outline])->assertOk();

        $order = array_map(fn ($i) => $i['module'].'/'.$i['lesson'], CourseProgressService::order(self::COURSE));
        $this->assertNotContains('m1/lesson02', $order);
        $this->assertSame('m1/lesson03', $order[1]);

        $student = $this->studentIn('programming', self::COURSE);
        CourseProgressService::markComplete($student, 'm1', 'lesson01', self::COURSE);
        $this->assertTrue(CourseProgressService::isUnlocked($student, 'm1', 'lesson03', self::COURSE));
        $this->actingAs($student)->get(route('student.planet.module.lesson.fragment', ['slug' => self::COURSE, 'module' => 'm1', 'lesson' => 'lesson02']))->assertNotFound();
    }

    public function test_an_added_lesson_has_a_page_and_its_own_quiz(): void
    {
        $faculty = $this->faculty();
        $outline = $this->actingAs($faculty)->getJson($this->url('outline'))->json('outline');
        $outline[0]['lessons'][] = ['key' => null, 'title' => 'Bonus Round', 'hidden' => false];
        $outline[0]['title'] = 'M1 — Renamed';

        $saved = $this->actingAs($faculty)->putJson($this->url('outline.update'), ['outline' => $outline])->assertOk()->json('outline');
        $this->assertSame('lesson-07', end($saved[0]['lessons'])['key']);
        $this->assertTrue(end($saved[0]['lessons'])['custom']);

        $order = array_map(fn ($i) => $i['module'].'/'.$i['lesson'], CourseProgressService::order(self::COURSE));
        $this->assertSame('m1/lesson07', $order[6]);

        $html = $this->fragment($faculty, 'lesson07');
        $this->assertStringContainsString('Lesson 1.7: Bonus Round', $html);
        $this->assertStringContainsString('Simple Explanation', $html);

        // The new lesson's Quiz stage takes the faculty's questions.
        $crew = Crew::where('course_slug', self::COURSE)->first();
        $quizUrl = route('faculty.lesson-quizzes.update', ['crew' => $crew, 'module' => 'm1', 'lesson' => 'lesson07']);
        $this->actingAs($faculty)->putJson($quizUrl, ['questions' => [
            ['prompt' => 'Is this new?', 'options' => ['Yes', 'No'], 'correct' => 'A', 'explanation' => 'It is.'],
        ]])->assertOk();
        $this->assertSame([1 => ['correct' => 'A', 'explanation' => '<em>It is.</em>']], LessonQuizService::key((string) LessonQuizService::source('m1', 'lesson07', self::COURSE)));

        // The sidebar shows the renamed module.
        $this->actingAs($faculty)->get(route('student.planet.module.lesson', ['slug' => self::COURSE, 'module' => 'm1', 'lesson' => 'lesson07']))
            ->assertOk()->assertSee('M1 — Renamed')->assertSee('Bonus Round');
    }

    public function test_the_course_needs_at_least_one_lesson(): void
    {
        $faculty = $this->faculty();
        $outline = $this->actingAs($faculty)->getJson($this->url('outline'))->json('outline');
        foreach ($outline as &$m) {
            $m['hidden'] = true;
        }

        $this->actingAs($faculty)->putJson($this->url('outline.update'), ['outline' => $outline])->assertStatus(422);
    }

    public function test_images_are_uploaded_to_the_public_disk(): void
    {
        Storage::fake('public');
        $faculty = $this->faculty();

        $url = $this->actingAs($faculty)->post($this->url('images'), ['image' => UploadedFile::fake()->image('diagram.png', 600, 300)], ['Accept' => 'application/json'])
            ->assertOk()->json('url');
        $this->assertStringStartsWith('/storage/lesson-images/'.self::COURSE.'/', $url);
        Storage::disk('public')->assertExists(substr($url, strlen('/storage/')));

        $this->actingAs($faculty)->post($this->url('images'), ['image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')], ['Accept' => 'application/json'])
            ->assertStatus(422);

        // An uploaded image is kept when the page is saved.
        $this->actingAs($faculty)->putJson($this->url('content.update', $this->lesson()),
            $this->editedDoc($faculty, '<figure class="net-diagram"><img src="'.$url.'" alt="Diagram"></figure>'))->assertOk();
        $this->assertStringContainsString('<img src="'.$url.'"', (string) LessonQuizService::source('m1', 'lesson01', self::COURSE));
    }

    public function test_the_edited_card_is_what_students_pick_from(): void
    {
        Storage::fake('public');
        $faculty = $this->faculty();

        $this->actingAs($faculty)->post($this->url('card'), [
            'title' => 'C for Captains', 'blurb' => 'A brand new subtitle', 'level' => 'Intermediate',
            'art' => UploadedFile::fake()->image('cover.png', 1250, 500),
        ], ['Accept' => 'application/json'])->assertOk();

        $this->actingAs($this->studentIn('programming', self::COURSE))->get(route('student.planet', 'programming'))
            ->assertOk()->assertSee('C for Captains')->assertSee('A brand new subtitle')->assertSee('storage/course-art/', false);

        $this->actingAs($faculty)->get(route('faculty.courses.index'))->assertOk()->assertSee('C for Captains')->assertSee('Manage');
    }

    public function test_faculty_see_the_editor_and_no_xp_in_the_course_player(): void
    {
        $faculty = $this->faculty();
        $page = route('student.planet.module.lesson', ['slug' => self::COURSE, 'module' => 'm2', 'lesson' => 'lesson03']);

        // Any lesson opens for faculty (no locks), with the editing tools and without the XP chips.
        $this->actingAs($faculty)->get($page)->assertOk()->assertSee('ce-outline-btn', false)->assertDontSee('data-stat=', false);

        // Another faculty member can read the course but not edit it.
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->get($page)->assertOk()->assertDontSee('ce-outline-btn', false);

        // Students keep their XP and never get the editor.
        $student = $this->studentIn('programming', self::COURSE);
        $this->actingAs($student)->get(route('student.planet.module.lesson', ['slug' => self::COURSE, 'module' => 'm1', 'lesson' => 'lesson01']))
            ->assertOk()->assertDontSee('ce-outline-btn', false);
    }
}
