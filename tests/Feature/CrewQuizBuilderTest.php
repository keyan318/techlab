<?php

namespace Tests\Feature;

use App\Models\MaterialChunk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Tests\TestCase;

/**
 * The quiz builder: a Google Forms-style editor with the course summary PDF and the captain's
 * own material open beside it. A draft is never published behind the faculty member's back.
 */
class CrewQuizBuilderTest extends TestCase
{
    use RefreshDatabase;

    /** A crew whose captain has one readable, already-indexed material. */
    private function crewWithMaterial(): array
    {
        Storage::fake('local');
        $crew = $this->crewFor('programming', 'python');
        $module = $crew->modules()->create(['title' => 'Week 3']);
        Storage::disk('local')->put('course-materials/x/notes.pdf', 'raw');
        $material = $module->materials()->create([
            'name' => 'Cryptography Basics.pdf', 'ext' => 'pdf',
            'path' => 'course-materials/x/notes.pdf', 'size' => 3, 'indexed_at' => now(),
        ]);
        MaterialChunk::create([
            'module_material_id' => $material->id, 'crew_id' => $crew->id, 'position' => 0,
            'text' => str_repeat('Cryptography keeps data confidential and proves who sent it. ', 8),
        ]);

        return [$crew, $material];
    }

    public function test_the_builder_shows_the_captains_material_beside_the_editor(): void
    {
        [$crew] = $this->crewWithMaterial();

        $this->actingAs($crew->faculty)->get(route('faculty.quizzes.build', $crew))
            ->assertOk()
            ->assertSee('Course summary')
            ->assertSee('My materials')
            ->assertSee('Cryptography Basics.pdf')
            ->assertDontSee('Create with Astro')
            ->assertSee('Multiple choice')
            ->assertSee('True / False')
            ->assertSee('Checkboxes')
            ->assertSee('Drop-down');
    }

    public function test_only_the_courses_own_captain_can_open_the_builder(): void
    {
        [$crew] = $this->crewWithMaterial();
        $other = User::factory()->create(['role' => 'faculty']);
        $student = $this->studentIn('programming', 'python');

        $this->actingAs($other)->get(route('faculty.quizzes.build', $crew))->assertForbidden();
        $this->actingAs($student)->get(route('faculty.quizzes.build', $crew))->assertForbidden();
    }

    public function test_saving_a_draft_keeps_it_away_from_students(): void
    {
        [$crew] = $this->crewWithMaterial();
        $student = $this->studentIn('programming', 'python');

        $this->actingAs($crew->faculty)->postJson(route('faculty.quizzes.store', $crew), [
            'title' => 'Week 3 Check', 'minutes' => 15, 'draft' => true,
            'questions' => [['prompt' => '2+2?', 'type' => 'multiple_choice', 'options' => ['3', '4'], 'correct' => 1]],
        ])->assertOk()->assertJsonPath('published', false);

        $this->actingAs($student)->get(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertDontSee('Week 3 Check');
    }

    public function test_publishing_from_the_builder_puts_it_in_front_of_students(): void
    {
        [$crew] = $this->crewWithMaterial();
        $student = $this->studentIn('programming', 'python');

        $id = $this->actingAs($crew->faculty)->postJson(route('faculty.quizzes.store', $crew), [
            'title' => 'Week 3 Check', 'minutes' => 15, 'draft' => true,
            'questions' => [['prompt' => '2+2?', 'type' => 'multiple_choice', 'options' => ['3', '4'], 'correct' => 1]],
        ])->json('id');

        $this->actingAs($crew->faculty)->putJson(route('faculty.quizzes.update', $id), [
            'title' => 'Week 3 Check', 'minutes' => 15, 'draft' => false, 'due_at' => now()->addWeek()->toDateTimeString(),
            'questions' => [
                ['prompt' => '2+2?', 'type' => 'multiple_choice', 'options' => ['3', '4'], 'correct' => 1],
                ['prompt' => 'Pick both', 'type' => 'checkboxes', 'options' => ['a', 'b', 'c'], 'correct_many' => [0, 1]],
            ],
        ])->assertOk()->assertJsonPath('published', true);

        $this->actingAs($student)->get(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertSee('Week 3 Check')->assertSee('2 questions');
    }

    public function test_editing_a_published_quiz_does_not_silently_unpublish_it(): void
    {
        [$crew] = $this->crewWithMaterial();
        $quiz = $crew->quizzes()->create(['title' => 'Live', 'published_at' => now()]);
        $quiz->questions()->create(['prompt' => 'q', 'options' => ['a', 'b'], 'correct_index' => 0, 'position' => 0]);

        $this->actingAs($crew->faculty)->putJson(route('faculty.quizzes.update', $quiz), [
            'title' => 'Live (fixed typo)', 'minutes' => 15, 'draft' => true,
            'questions' => [['prompt' => 'q', 'type' => 'multiple_choice', 'options' => ['a', 'b'], 'correct' => 0]],
        ])->assertOk()->assertJsonPath('published', true);

        $this->assertTrue($quiz->fresh()->isPublished());
    }

    public function test_the_edit_screen_loads_an_existing_quiz_back_into_the_builder(): void
    {
        [$crew] = $this->crewWithMaterial();
        $quiz = $crew->quizzes()->create(['title' => 'Week 3 Check', 'published_at' => now()]);
        $quiz->questions()->create([
            'prompt' => 'Pick the private ranges', 'type' => 'checkboxes',
            'options' => ['10.0.0.0/8', '8.8.8.8'], 'correct_index' => 0, 'correct_indexes' => [0], 'position' => 0,
        ]);

        $this->actingAs($crew->faculty)->get(route('faculty.quizzes.edit', $quiz))
            ->assertOk()
            ->assertSee('Week 3 Check')
            ->assertSee('Pick the private ranges')
            ->assertSee('checkboxes');
    }

    public function test_the_course_summary_is_a_pdf_of_every_modules_lessons(): void
    {
        Storage::fake('local');
        $crew = $this->crewFor('networking', 'networking-fundamentals');
        $url = route('faculty.course.summary-pdf', $crew);

        $this->actingAs($crew->faculty)->get(route('faculty.quizzes.build', $crew))
            ->assertOk()->assertSee($url, false);

        $response = $this->actingAs($crew->faculty)->get($url)->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('inline;', $response->headers->get('Content-Disposition'));

        // Module 2, lesson 2.2's key terms come straight from the lesson file.
        $text = (new Parser)->parseFile($response->getFile()->getPathname())->getText();
        $this->assertStringContainsString('Protocols', $text);
        $this->assertStringContainsString('well-known ports', $text);
        $this->assertStringContainsString('handshake', $text);
    }

    public function test_only_the_courses_own_captain_can_read_its_summary_pdf(): void
    {
        $crew = $this->crewFor('networking', 'networking-fundamentals');
        $other = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($other)->get(route('faculty.course.summary-pdf', $crew))->assertForbidden();
    }

    public function test_the_astro_draft_endpoint_is_gone(): void
    {
        [$crew] = $this->crewWithMaterial();

        $this->actingAs($crew->faculty)->postJson('/faculty/courses/'.$crew->id.'/quizzes/draft', ['material_ids' => [1]])
            ->assertNotFound();
    }
}
