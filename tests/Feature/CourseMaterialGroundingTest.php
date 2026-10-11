<?php

namespace Tests\Feature;

use App\Jobs\ExtractMaterialText;
use App\Models\Crew;
use App\Models\MaterialChunk;
use App\Models\Message;
use App\Services\CourseMaterialSourceService;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Astro only answers from the faculty's own uploads when the student attaches one by hand
 * — the "Add from Materials" button, exactly like a Planet lesson. It used to search every
 * upload on every message, which meant an irrelevant handout got cited just because a
 * keyword happened to overlap.
 */
class CourseMaterialGroundingTest extends TestCase
{
    use RefreshDatabase;

    private const CRYPTO_TEXT = <<<'TXT'
    Cryptography relies on mathematical algorithms to achieve four security goals.

    Confidentiality keeps data private and hidden from unauthorized users. Integrity
    ensures data has not been altered in transit. Authentication confirms the identity
    of the user, and non-repudiation prevents a party denying a message they sent.
    TXT;

    /** A crew with one readable material already indexed, and a student on its roster. */
    private function crewWithMaterial(string $text = self::CRYPTO_TEXT, string $name = 'Cryptography Basics.pdf'): array
    {
        Storage::fake('local');
        $student = $this->studentIn('programming', 'python');
        $crew = $this->crewFor('programming', 'python');
        $module = $crew->modules()->create(['title' => 'Week 3']);

        Storage::disk('local')->put('course-materials/x/notes.pdf', 'raw');
        $material = $module->materials()->create([
            'name' => $name, 'ext' => 'pdf', 'path' => 'course-materials/x/notes.pdf', 'size' => 3,
        ]);

        foreach (ExtractMaterialText::chunk($text) as $i => $chunk) {
            MaterialChunk::create([
                'module_material_id' => $material->id, 'crew_id' => $crew->id,
                'position' => $i, 'text' => $chunk,
            ]);
        }

        return [$student, $crew, $material];
    }

    /** Capture the system context Astro is given, and return a canned answer. */
    private function captureContext(?string &$captured, string $answer = 'Here you go.'): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb, $context = '') use (&$captured, $answer) {
                $captured = $context;

                return $answer;
            });
    }

    public function test_uploading_a_readable_file_makes_it_searchable(): void
    {
        Storage::fake('local');
        $crew = $this->crewFor('programming', 'python');
        $module = $crew->modules()->create(['title' => 'Week 3']);

        $this->actingAs($crew->faculty)->post(route('faculty.materials.store', $module), [
            'files' => [UploadedFile::fake()->createWithContent('handout.txt', self::CRYPTO_TEXT)],
        ])->assertSessionHasNoErrors();

        $material = $module->materials()->firstOrFail();
        $this->assertNotNull($material->indexed_at);
        $this->assertNull($material->index_error);
        $this->assertGreaterThan(0, $material->chunks()->count());
        $this->assertStringContainsString('Confidentiality', $material->chunks()->first()->text);
    }

    public function test_a_file_astro_cannot_read_is_kept_and_explained_not_deleted(): void
    {
        Storage::fake('local');
        $crew = $this->crewFor('programming', 'python');
        $module = $crew->modules()->create(['title' => 'Week 3']);

        $this->actingAs($crew->faculty)->post(route('faculty.materials.store', $module), [
            'files' => [UploadedFile::fake()->create('diagram.png', 10)],
        ])->assertSessionHasNoErrors();

        $material = $module->materials()->firstOrFail();
        // Regression: reading a zip-backed file used to delete the faculty's own upload.
        Storage::disk('local')->assertExists($material->path);
        $this->assertSame(0, $material->chunks()->count());
        $this->assertNotNull($material->index_error);
    }

    public function test_a_plain_question_never_pulls_in_a_material_the_student_did_not_attach(): void
    {
        [$student] = $this->crewWithMaterial();
        $this->captureContext($captured, 'Answer.');

        $response = $this->actingAs($student)->postJson('/chat/message', ['message' => 'What is cryptography?']);

        $response->assertOk()->assertJsonPath('materials', []);
        $this->assertStringNotContainsString('[Material', $captured);
    }

    public function test_astro_answers_from_an_attached_material(): void
    {
        [$student, , $material] = $this->crewWithMaterial();
        config(['web_search.endpoint' => 'http://searxng.test/search', 'web_search.api_key' => 'test-key']);
        Http::fake();
        $this->captureContext($captured, 'Cryptography has four goals [Material 1].');

        $response = $this->actingAs($student)->postJson('/chat/message', [
            'message' => 'What is cryptography?',
            'materials' => [$material->id],
        ]);

        $response->assertOk()
            ->assertJsonCount(1, 'materials')
            ->assertJsonPath('materials.0.title', 'Cryptography Basics.pdf')
            ->assertJsonPath('web', []);

        $this->assertStringContainsString('[Material 1: Cryptography Basics.pdf', $captured);
        $this->assertStringContainsString('Confidentiality', $captured);
        // The material never leaks the raw snippet to the browser.
        $this->assertArrayNotHasKey('text', $response->json('materials.0'));
        Http::assertNothingSent();
    }

    public function test_an_attached_material_skips_the_web_fallback_even_off_topic(): void
    {
        // Attaching a material is a deliberate choice, same as a Planet lesson — Astro trusts
        // it in Fast mode rather than second-guessing with a web search.
        [$student, , $material] = $this->crewWithMaterial();
        config(['web_search.endpoint' => 'http://searxng.test/search', 'web_search.api_key' => 'test-key']);
        Http::fake();
        $this->captureContext($captured, 'That is not covered here, but in general...');

        $this->actingAs($student)->postJson('/chat/message', [
            'message' => 'How does subnetting work?',
            'materials' => [$material->id],
        ])->assertOk()->assertJsonPath('web', []);

        Http::assertNothingSent();
    }

    public function test_a_plain_question_with_nothing_attached_falls_back_to_the_web(): void
    {
        [$student] = $this->crewWithMaterial();
        config(['web_search.endpoint' => 'http://searxng.test/search', 'web_search.api_key' => 'test-key']);
        Http::fake(['*' => Http::response(['results' => [
            ['title' => 'Subnetting explained', 'url' => 'https://example.org/subnetting', 'content' => 'A subnet is...'],
        ]])]);
        $this->captureContext($captured, 'Answer [Web 1]');

        $this->actingAs($student)->postJson('/chat/message', ['message' => 'How does subnetting work?'])
            ->assertOk()
            ->assertJsonPath('materials', [])
            ->assertJsonCount(1, 'web')
            ->assertJsonPath('web.0.domain', 'example.org');

        $this->assertStringContainsString('[Web 1: Subnetting explained', $captured);
    }

    public function test_a_student_cannot_attach_another_courses_material(): void
    {
        [, , $material] = $this->crewWithMaterial();
        $outsider = $this->studentIn('networking', 'networking-fundamentals');

        $this->assertSame([], app(CourseMaterialSourceService::class)->resolve([$material->id], $outsider));
    }

    public function test_sources_are_stored_with_the_answer_and_survive_a_reload(): void
    {
        [$student, , $material] = $this->crewWithMaterial();
        $this->captureContext($captured, 'Cryptography has four goals [Material 1].');

        $conversationId = $this->actingAs($student)
            ->postJson('/chat/message', ['message' => 'What is cryptography?', 'materials' => [$material->id]])
            ->assertOk()->json('conversation_id');

        $stored = Message::where('conversation_id', $conversationId)->where('role', 'assistant')->firstOrFail();
        $this->assertSame('material', $stored->sources[0]['kind']);
        $this->assertSame('Cryptography Basics.pdf', $stored->sources[0]['title']);
        // Only the title and the link are kept — never the page text.
        $this->assertArrayNotHasKey('text', $stored->sources[0]);

        // Reopening the conversation hands the sources back, so a refresh doesn't lose them.
        $messages = $this->actingAs($student)->getJson('/chat/conversations/'.$conversationId)
            ->assertOk()->json('messages');
        $assistant = collect($messages)->firstWhere('role', 'assistant');
        $this->assertSame('Cryptography Basics.pdf', $assistant['sources'][0]['title']);
        $this->assertSame([], collect($messages)->firstWhere('role', 'user')['sources']);
    }

    public function test_a_link_is_cited_but_never_downloaded(): void
    {
        $crew = $this->crewFor('programming', 'python');
        $module = $crew->modules()->create(['title' => 'Week 3']);

        $this->actingAs($crew->faculty)->post(route('faculty.materials.link', $module), [
            'name' => 'Eye on Tech — Cryptography', 'url' => 'https://youtube.com/watch?v=abc',
        ])->assertSessionHasNoErrors();

        $link = $module->materials()->firstOrFail();
        $this->assertTrue($link->isLink());
        $this->assertSame('https://youtube.com/watch?v=abc', $link->openUrl());

        // There is no file behind it, so the download route must not pretend there is.
        $student = $this->studentIn('programming', 'python');
        $this->actingAs($student)->get(route('materials.download', $link))->assertNotFound();
    }

    public function test_a_link_must_be_a_real_web_address(): void
    {
        $crew = $this->crewFor('programming', 'python');
        $module = $crew->modules()->create(['title' => 'Week 3']);

        $this->actingAs($crew->faculty)->post(route('faculty.materials.link', $module), [
            'name' => 'Sketchy', 'url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('url');
    }

    public function test_attaching_one_material_does_not_force_the_slow_model(): void
    {
        [$student, , $material] = $this->crewWithMaterial();
        $this->captureContext($captured);

        // Attached materials count toward "multiple sources" the same as Planet lessons, so
        // one alone must not push a plain question into Deep mode.
        $response = $this->actingAs($student)->withHeaders(['Accept' => 'application/json, text/event-stream'])
            ->post('/chat/message', ['message' => 'What is cryptography?', 'materials' => [$material->id]]);
        $response->streamedContent();
        $response->assertHeader('X-Astro-Mode', 'fast');
    }

    public function test_chunking_keeps_paragraphs_whole(): void
    {
        $long = implode("\n\n", array_fill(0, 12, str_repeat('word ', 60)));
        $chunks = ExtractMaterialText::chunk($long);

        $this->assertGreaterThan(1, count($chunks));
        foreach ($chunks as $chunk) {
            $this->assertLessThanOrEqual(ExtractMaterialText::CHUNK_CHARS, mb_strlen($chunk));
            $this->assertSame(trim($chunk), $chunk);
        }
        $this->assertSame([], ExtractMaterialText::chunk("   \n\n  "));
    }
}
