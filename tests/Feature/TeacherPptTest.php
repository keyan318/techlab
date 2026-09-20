<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use App\Services\Ppt\PptBuilder;
use App\Services\Ppt\PptContentService;
use App\Services\Quiz\QuizNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherPptTest extends TestCase
{
    use RefreshDatabase;

    private function deckJson(): array
    {
        return [
            'title' => 'Python Loops', 'theme' => 'sunrise', 'accent' => '#12ab34',
            'slides' => [
                ['layout' => 'cover', 'title' => 'Loops', 'kicker' => 'Python', 'subtitle' => 'For beginners', 'notes' => 'Hi'],
                ['layout' => 'statement', 'text' => 'A loop repeats work for you.'],
                ['layout' => 'section', 'number' => 1, 'title' => 'The for loop'],
                ['layout' => 'split', 'title' => 'Walk a list', 'points' => ['One', 'Two'], 'aside' => 'Remember zero.'],
                ['layout' => 'code', 'title' => 'First loop', 'code' => "for i in range(3):\n    print(i)"],
                ['layout' => 'closing', 'title' => 'Takeaways', 'points' => ['A', 'B']],
                ['layout' => 'bogus', 'title' => 'dropped'],
                ['layout' => 'cards', 'title' => 'Too few', 'items' => [['title' => 'x', 'text' => 'y']]],
            ],
        ];
    }

    public function test_normalize_keeps_valid_slides_drops_bad_ones_and_validates_style(): void
    {
        $deck = app(PptContentService::class)->normalize($this->deckJson());

        $this->assertSame('sunrise', $deck['theme']);
        $this->assertSame('12AB34', $deck['accent']);
        $this->assertSame(['cover', 'statement', 'section', 'split', 'code', 'closing'], array_column($deck['slides'], 'layout'));

        $bad = $this->deckJson();
        $bad['theme'] = 'neon-rainbow';
        $bad['accent'] = 'red';
        $deck = app(PptContentService::class)->normalize($bad);
        $this->assertSame('midnight', $deck['theme']);
        $this->assertNull($deck['accent']);

        $this->assertNull(app(PptContentService::class)->normalize(['slides' => array_slice($this->deckJson()['slides'], 0, 3)]));
    }

    public function test_builder_writes_a_real_pptx_file(): void
    {
        $deck = app(PptContentService::class)->normalize($this->deckJson());
        $file = app(PptBuilder::class)->build($deck);

        $this->assertFileExists($file);
        $this->assertSame('PK', file_get_contents($file, false, null, 0, 2));   // .pptx is a zip
        $zip = new \ZipArchive;
        $zip->open($file);
        $this->assertNotFalse($zip->locateName('ppt/slides/slide6.xml'));
        $this->assertNotFalse($zip->locateName('ppt/notesSlides/notesSlide1.xml'));
        $zip->close();
        @unlink($file);
    }

    public function test_teacher_downloads_a_pptx_from_their_chat(): void
    {
        $this->mock(QuizNimService::class, fn ($m) => $m->shouldReceive('completeJson')->once()->andReturn($this->deckJson()));

        $teacher = User::factory()->create(['role' => 'teacher']);
        $conv = Conversation::create(['user_id' => $teacher->id, 'title' => 'Loops']);
        $conv->messages()->create(['role' => 'user', 'content' => 'Make a presentation about Python loops, playful style for 12 year olds']);
        $conv->messages()->create(['role' => 'assistant', 'content' => 'Sure! Loops repeat instructions.']);

        $res = $this->actingAs($teacher)->post(route('teacher.chat.ppt'), ['conversation_id' => $conv->id]);

        $res->assertOk();
        $this->assertStringContainsString('presentationml.presentation', $res->headers->get('Content-Type'));
        $this->assertSame('6', $res->headers->get('X-Ppt-Slides'));
    }

    public function test_ppt_needs_a_teacher_and_their_own_conversation(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $other = Conversation::create(['user_id' => $student->id, 'title' => 'x']);

        $this->actingAs($student)->postJson(route('teacher.chat.ppt'), ['conversation_id' => $other->id])->assertForbidden();
        $this->actingAs($teacher)->postJson(route('teacher.chat.ppt'), ['conversation_id' => $other->id])->assertNotFound();

        $empty = Conversation::create(['user_id' => $teacher->id, 'title' => 'y']);
        $this->actingAs($teacher)->postJson(route('teacher.chat.ppt'), ['conversation_id' => $empty->id])->assertStatus(422);
    }

    public function test_teacher_studio_shows_ppt_tile(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher)->get(route('teacher.chat'))->assertOk()->assertSee('Presentation from this chat')->assertSee(route('teacher.chat.ppt'), false);
    }
}
