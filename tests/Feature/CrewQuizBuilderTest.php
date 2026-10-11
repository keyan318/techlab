<?php

namespace Tests\Feature;

use App\Models\MaterialChunk;
use App\Models\User;
use App\Services\Quiz\QuizNimService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The two-panel quiz builder: writing questions by hand, and having Astro draft them
 * from a material. A draft is never published behind the faculty member's back.
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

    /** Swap the quiz generator's transport so no real model is called. */
    private function fakeNim(array $questions): void
    {
        $body = json_encode(['choices' => [['message' => ['content' => json_encode([
            'title' => 'Cryptography Check',
            'questions' => $questions,
        ])]]]]);

        $queue = array_fill(0, 4, new Response(200, ['Content-Type' => 'application/json'], $body));
        $stack = HandlerStack::create(new MockHandler($queue));
        $this->app->instance(QuizNimService::class, new QuizNimService(new Client(['handler' => $stack])));
    }

    private function question(int $n): array
    {
        return [
            'type' => 'multiple_choice',
            'question' => "What does goal {$n} of cryptography protect?",
            'choices' => [
                ['key' => 'A', 'text' => "Answer A{$n}"], ['key' => 'B', 'text' => "Answer B{$n}"],
                ['key' => 'C', 'text' => "Answer C{$n}"], ['key' => 'D', 'text' => "Answer D{$n}"],
            ],
            'correct_key' => 'B',
            'explanation' => 'Because the handout says so.',
            'difficulty' => 'easy',
        ];
    }

    public function test_the_builder_shows_the_captains_material_beside_the_editor(): void
    {
        [$crew] = $this->crewWithMaterial();

        $this->actingAs($crew->faculty)->get(route('faculty.quizzes.build', $crew))
            ->assertOk()
            ->assertSee('Your material')
            ->assertSee('Cryptography Basics.pdf')
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
        $this->actingAs($student)->postJson(route('faculty.quizzes.draft', $crew), ['material_ids' => [1]])->assertForbidden();
    }

    public function test_saving_a_draft_keeps_it_away_from_students(): void
    {
        [$crew] = $this->crewWithMaterial();
        $student = $this->studentIn('programming', 'python');

        $this->actingAs($crew->faculty)->postJson(route('faculty.quizzes.store', $crew), [
            'title' => 'Week 3 Check', 'draft' => true,
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
            'title' => 'Week 3 Check', 'draft' => true,
            'questions' => [['prompt' => '2+2?', 'type' => 'multiple_choice', 'options' => ['3', '4'], 'correct' => 1]],
        ])->json('id');

        $this->actingAs($crew->faculty)->putJson(route('faculty.quizzes.update', $id), [
            'title' => 'Week 3 Check', 'draft' => false, 'due_at' => now()->addWeek()->toDateTimeString(),
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
            'title' => 'Live (fixed typo)', 'draft' => true,
            'questions' => [['prompt' => 'q', 'type' => 'multiple_choice', 'options' => ['a', 'b'], 'correct' => 0]],
        ])->assertOk()->assertJsonPath('published', true);

        $this->assertTrue($quiz->fresh()->isPublished());
    }

    public function test_astro_drafts_editable_questions_from_a_chosen_material(): void
    {
        [$crew, $material] = $this->crewWithMaterial();
        $this->fakeNim([$this->question(1), $this->question(2), $this->question(3)]);

        $body = $this->actingAs($crew->faculty)->postJson(route('faculty.quizzes.draft', $crew), [
            'material_ids' => [$material->id], 'count' => 3, 'prompt' => 'focus on confidentiality',
        ])->assertOk()->json();

        $this->assertSame('Cryptography Check', $body['title']);
        $this->assertNotEmpty($body['questions']);
        $first = $body['questions'][0];
        $this->assertSame('multiple_choice', $first['type']);
        $this->assertCount(4, $first['options']);
        // Choice B was marked correct, so the builder's index must be 1.
        $this->assertSame(1, $first['correct']);

        // Drafting alone must never create a quiz — it lands in the editor first.
        $this->assertSame(0, $crew->quizzes()->count());
    }

    public function test_drafting_from_a_material_astro_cannot_read_says_so_plainly(): void
    {
        [$crew] = $this->crewWithMaterial();
        $module = $crew->modules()->first();
        $image = $module->materials()->create([
            'name' => 'slide.png', 'ext' => 'png', 'path' => 'course-materials/x/slide.png',
            'size' => 10, 'indexed_at' => now(), 'index_error' => 'not readable',
        ]);

        $this->actingAs($crew->faculty)->postJson(route('faculty.quizzes.draft', $crew), [
            'material_ids' => [$image->id],
        ])->assertStatus(422)->assertJsonPath('message', "Astro can't read the material you picked. It needs a PDF, Word, PowerPoint or text file with selectable text.");
    }

    public function test_drafting_needs_a_material_to_work_from(): void
    {
        [$crew] = $this->crewWithMaterial();

        $this->actingAs($crew->faculty)->postJson(route('faculty.quizzes.draft', $crew), ['material_ids' => []])
            ->assertStatus(422)->assertJsonValidationErrors('material_ids');
    }

    public function test_a_captain_cannot_draft_from_another_courses_material(): void
    {
        [$crew] = $this->crewWithMaterial();
        $otherCrew = $this->crewFor('networking', 'networking-fundamentals');
        $otherModule = $otherCrew->modules()->create(['title' => 'Theirs']);
        $theirs = $otherModule->materials()->create(['name' => 'secret.pdf', 'ext' => 'pdf', 'path' => 'p', 'size' => 1]);
        MaterialChunk::create(['module_material_id' => $theirs->id, 'crew_id' => $otherCrew->id, 'position' => 0, 'text' => str_repeat('secret ', 40)]);

        // Scoped by crew, so their material yields nothing rather than leaking.
        $this->actingAs($crew->faculty)->postJson(route('faculty.quizzes.draft', $crew), [
            'material_ids' => [$theirs->id],
        ])->assertStatus(422);
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
}
