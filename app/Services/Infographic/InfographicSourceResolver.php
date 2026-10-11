<?php

namespace App\Services\Infographic;

use App\Exceptions\DocumentTextException;
use App\Exceptions\InfographicSourceException;
use App\Services\DocumentTextExtractor;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * Resolves a student's "learning material" into one clean text blob.
 *
 * The Infographic feature must work from REAL material, following whatever
 * resource input the existing app supports. Source priority:
 *   1. current chat transcript (transcript)  — primary for infographic (live conversation)
 *   2. pasted text   (source_text)  — legacy/fallback
 *   3. uploaded file (file)  — legacy/fallback (TXT/MD/DOCX/PPTX/PDF)
 *
 * No separate attachment/resource pipeline existed in /chat, so this resolver
 * is deliberately pluggable: a future real upload pipeline can feed `file` (or
 * a richer resolver) without touching the orchestrator or the models.
 */
class InfographicSourceResolver
{
    /**
     * Resolve the request into source text. Returns '' when nothing usable.
     */
    public function resolve(Request $request): string
    {
        // Transcript-first: the infographic now derives from the live chat.
        // source_text/file remain as fallback for legacy/attachment paths.
        $transcript = $request->input('transcript');
        if (is_array($transcript) && $transcript !== []) {
            $fromTranscript = $this->clean($this->fromTranscript($transcript));
            if ($fromTranscript !== '') {
                return $fromTranscript;
            }
        }

        $text = trim((string) $request->input('source_text', ''));
        if ($text !== '') {
            return $this->clean($text);
        }

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            return $this->clean($this->fromFile($request->file('file')));
        }

        return '';
    }

    /**
     * Build a readable transcript from the chat messages the frontend sends.
     *
     * @param  array<int, array{role?: string, content?: string}>  $messages
     */
    protected function fromTranscript(array $messages): string
    {
        $lines = [];
        foreach ($messages as $m) {
            $role = is_string($m['role'] ?? null) ? $m['role'] : 'user';
            $content = is_string($m['content'] ?? null) ? $m['content'] : '';
            $content = trim(preg_replace('/\s+/', ' ', $content));
            if ($content === '') {
                continue;
            }
            $label = $role === 'assistant' ? 'Astro' : 'Student';
            $lines[] = $label.': '.$content;
        }

        return implode("\n\n", $lines);
    }

    /**
     * Extract text from an uploaded file, using the same reader as chat attachments and
     * faculty course materials (DocumentTextExtractor) so all three behave identically.
     *
     * @param  UploadedFile  $file
     */
    protected function fromFile($file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if (! DocumentTextExtractor::supports($ext)) {
            throw new InfographicSourceException(
                "Astro can't read that file type yet. Please paste the text instead.",
                415
            );
        }

        try {
            return app(DocumentTextExtractor::class)->extract(
                $file->getRealPath(), $ext, $file->getClientOriginalName()
            );
        } catch (DocumentTextException $e) {
            throw new InfographicSourceException($e->getMessage().' You can paste the text instead.', 422);
        }
    }

    /**
     * Normalize whitespace and cap length so we never blow the model context.
     */
    protected function clean(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));
        $max = (int) config('infographic.max_source_chars', 14000);

        if (mb_strlen($text) > $max) {
            $text = mb_substr($text, 0, $max).' …';
        }

        return $text;
    }
}
