<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\AttachmentService;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ChatAttachmentsTest extends TestCase
{
    use RefreshDatabase;

    private function send(User $user, array $payload)
    {
        return $this->actingAs($user)->post('/chat/message', $payload, ['Accept' => 'application/json']);
    }

    public function test_text_file_contents_reach_astro_and_are_stored_as_metadata(): void
    {
        $seen = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history) use (&$seen) {
                $seen = $history;

                return 'It prints hello.';
            });

        $user = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent('hello.py', "print('hello')\n");

        $this->send($user, ['message' => 'What does this do?', 'files' => [$file]])
            ->assertOk()->assertJson(['success' => true]);

        $last = end($seen);
        $this->assertIsString($last['content']);
        $this->assertStringContainsString('What does this do?', $last['content']);
        $this->assertStringContainsString("print('hello')", $last['content']);

        $stored = Message::where('role', 'user')->first();
        $this->assertSame('What does this do?', $stored->content);          // typed text only
        $this->assertSame('hello.py', $stored->attachments[0]['name']);
        $this->assertSame('text', $stored->attachments[0]['kind']);
    }

    public function test_follow_up_turns_still_see_the_earlier_file(): void
    {
        $user = User::factory()->create();
        $conv = Conversation::create(['user_id' => $user->id, 'title' => 't']);
        Message::create(['conversation_id' => $conv->id, 'role' => 'user', 'content' => 'Look', 'attachments' => [
            ['name' => 'a.txt', 'kind' => 'text', 'text' => 'SECRET-CONTENT', 'truncated' => false],
        ]]);
        Message::create(['conversation_id' => $conv->id, 'role' => 'assistant', 'content' => 'Ok']);

        $seen = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history) use (&$seen) {
                $seen = $history;

                return 'Sure';
            });

        $this->send($user, ['message' => 'And now?', 'conversation_id' => $conv->id])->assertOk();

        $this->assertStringContainsString('SECRET-CONTENT', $seen[0]['content']);

        $this->getJson(route('chat.conversation.show', $conv->id))->assertOk()
            ->assertJsonMissing(['text' => 'SECRET-CONTENT'])
            ->assertJsonFragment(['attachments' => [['name' => 'a.txt', 'kind' => 'text']]]);
    }

    public function test_a_file_alone_is_enough_to_send(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()->andReturn('Read it.');

        $this->send(User::factory()->create(), ['files' => [UploadedFile::fake()->createWithContent('n.md', '# Notes')]])
            ->assertOk();

        $this->assertSame('Please take a look at what I attached.', Message::where('role', 'user')->value('content'));
    }

    public function test_unsupported_and_binary_files_are_rejected_with_a_friendly_error(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->never();
        $user = User::factory()->create();

        $this->send($user, ['message' => 'hi', 'files' => [UploadedFile::fake()->createWithContent('a.exe', 'MZ')]])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, "can't read"));

        $this->send($user, ['message' => 'hi', 'files' => [UploadedFile::fake()->createWithContent('b.txt', "ab\0cd")]])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, 'binary'));
    }

    public function test_too_large_and_too_many_files_get_distinct_messages(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->never();
        $user = User::factory()->create();

        $this->send($user, ['message' => 'hi', 'files' => [UploadedFile::fake()->create('big-report.docx', 20000)]])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['files.0' => '"big-report.docx" is too large — the limit is '.AttachmentService::mb(AttachmentService::maxFileKb()).' per file.']);

        $this->send($user, ['message' => 'hi', 'files' => array_map(fn ($i) => UploadedFile::fake()->createWithContent("f{$i}.txt", 'x'), range(1, 5))])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['files' => 'Too many files — you can attach up to 4 per message.']);
    }

    private function zip(string $ext, array $entries): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'att').'.'.$ext;
        $zip = new \ZipArchive;
        $zip->open($path, \ZipArchive::CREATE);
        foreach ($entries as $name => $xml) {
            $zip->addFromString($name, $xml);
        }
        $zip->close();

        return new UploadedFile($path, "sample.$ext", null, null, true);
    }

    public function test_word_and_powerpoint_text_is_extracted(): void
    {
        $seen = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history) use (&$seen) {
                $seen = end($history)['content'];

                return 'ok';
            });

        $docx = $this->zip('docx', ['word/document.xml' => '<w:document><w:body><w:p><w:r><w:t>Chapter 1 &amp; Intro</w:t></w:r></w:p><w:p><w:r><w:t>Second paragraph</w:t></w:r></w:p></w:body></w:document>']);
        $pptx = $this->zip('pptx', [
            'ppt/slides/slide2.xml' => '<p:sld><a:p><a:r><a:t>Slide two</a:t></a:r></a:p></p:sld>',
            'ppt/slides/slide1.xml' => '<p:sld><a:p><a:r><a:t>Slide one</a:t></a:r></a:p></p:sld>',
        ]);

        $this->send(User::factory()->create(), ['message' => 'Summarise', 'files' => [$docx, $pptx]])->assertOk();

        $this->assertStringContainsString("Chapter 1 & Intro\nSecond paragraph", $seen);
        $this->assertLessThan(strpos($seen, 'Slide two'), strpos($seen, 'Slide one'));
    }

    public function test_a_corrupt_docx_is_rejected_cleanly(): void
    {
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->never();

        $this->send(User::factory()->create(), ['message' => 'x', 'files' => [UploadedFile::fake()->createWithContent('bad.docx', 'not a zip')]])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, "couldn't open"));
    }

    public function test_images_are_declined_when_no_vision_model_is_configured(): void
    {
        config(['nvidia_nim.vision_model' => null]);
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->never();

        $this->send(User::factory()->create(), ['message' => 'see', 'files' => [UploadedFile::fake()->image('p.png')]])
            ->assertStatus(422)->assertJsonPath('error', fn ($e) => str_contains($e, "can't look at images"));
    }

    public function test_images_go_to_the_vision_model_as_image_parts(): void
    {
        config(['nvidia_nim.vision_model' => 'vendor/vision-model']);
        $seen = $model = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb, $ctx, $route, $override) use (&$seen, &$model) {
                $seen = $history;
                $model = $override;

                return 'A cat.';
            });

        $this->send(User::factory()->create(), ['message' => 'What is this?', 'files' => [UploadedFile::fake()->image('p.png', 40, 40)]])
            ->assertOk();

        $this->assertSame('vendor/vision-model', $model);
        $parts = end($seen)['content'];
        $this->assertSame('text', $parts[0]['type']);
        $this->assertSame('image_url', $parts[1]['type']);
        $this->assertStringStartsWith('data:image/png;base64,', $parts[1]['image_url']['url']);
        $this->assertNull(Message::where('role', 'user')->first()->attachments[0]['text'] ?? null);   // no bytes stored
    }

    public function test_plain_messages_without_files_are_unchanged(): void
    {
        $seen = null;
        $this->mock(NvidiaNimService::class)->shouldReceive('stream')->once()
            ->andReturnUsing(function ($history, $cb, $ctx, $route, $override = null) use (&$seen, &$model) {
                $seen = [$history, $override];

                return 'ok';
            });

        $this->send(User::factory()->create(), ['message' => 'Hi'])->assertOk();

        $this->assertSame([['role' => 'user', 'content' => 'Hi']], $seen[0]);
        $this->assertNull($seen[1]);
        $this->assertNull(Message::where('role', 'user')->first()->attachments);
    }
}
