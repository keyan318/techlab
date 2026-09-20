<?php

namespace App\Services\Infographic;

use App\Exceptions\InfographicSourceException;
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
     * Extract text from an uploaded file by type.
     *
     * @param  UploadedFile  $file
     */
    protected function fromFile($file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        return match ($ext) {
            'txt', 'md', 'markdown', 'csv', 'text', 'json', 'log' => $this->readPlain($path),
            'docx' => $this->fromDocx($path),
            'pptx' => $this->fromPptx($path),
            'pdf' => $this->fromPdf($path),
            default => throw new InfographicSourceException(
                "Astro can't read that file type yet. Please paste the text instead.",
                415
            ),
        };
    }

    protected function readPlain(string $path): string
    {
        $content = @file_get_contents($path);

        return is_string($content) ? $content : '';
    }

    /**
     * DOCX is a zip of XML. Pull visible text from word/document.xml.
     */
    protected function fromDocx(string $path): string
    {
        $xml = $this->readZipEntry($path, 'word/document.xml');
        if ($xml === null) {
            throw new InfographicSourceException(
                "Astro couldn't read that Word file. Please paste the text instead.",
                422
            );
        }

        return $this->xmlText($xml);
    }

    /**
     * PPTX is a zip of slide XML. Concatenate text from every slide.
     */
    protected function fromPptx(string $path): string
    {
        if (! class_exists(\ZipArchive::class)) {
            throw new InfographicSourceException(
                "Astro couldn't read that PowerPoint file. Please paste the text instead.",
                422
            );
        }

        $zip = new \ZipArchive;
        if ($zip->open($path) !== true) {
            throw new InfographicSourceException(
                "Astro couldn't open that PowerPoint file. Please paste the text instead.",
                422
            );
        }

        $parts = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name && preg_match('#^ppt/slides/slide\d+\.xml$#', $name)) {
                $parts[] = $this->xmlText((string) $zip->getFromName($name));
            }
        }
        $zip->close();

        $text = implode("\n\n", array_filter($parts));
        if ($text === '') {
            throw new InfographicSourceException(
                'That PowerPoint had no readable text. Please paste the text instead.',
                422
            );
        }

        return $text;
    }

    /**
     * PDF: best-effort. Tries a naive content-stream text extract; many simple
     * PDFs yield readable text this way. If little is recovered, say so.
     */
    protected function fromPdf(string $path): string
    {
        $raw = @file_get_contents($path);
        if (! is_string($raw)) {
            throw new InfographicSourceException(
                "Astro couldn't read that PDF. Please paste the text instead.",
                422
            );
        }

        $text = $this->extractPdfText($raw);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        if (mb_strlen($text) < 40) {
            throw new InfographicSourceException(
                "Astro couldn't extract enough text from that PDF. Please paste the text instead.",
                422
            );
        }

        return $text;
    }

    /**
     * Very small PDF text extractor: pulls strings shown by Tj/TJ operators.
     * Not a full PDF parser — good enough for many text-based PDFs.
     */
    protected function extractPdfText(string $raw): string
    {
        $out = '';
        // Match text between BT and ET, then capture (...) Tj / TJ strings.
        if (! preg_match_all('/BT\s*(.*?)\s*ET/s', $raw, $blocks)) {
            return '';
        }
        foreach ($blocks[1] as $block) {
            if (preg_match_all('/\((?:[^()\\\\]|\\\\.)*\)\s*Tj/s', $block, $m)) {
                foreach ($m[0] as $t) {
                    $out .= $this->unescapePdfString($t).' ';
                }
            }
            if (preg_match_all('/\[(.*?)\]\s*TJ/s', $block, $m)) {
                foreach ($m[1] as $t) {
                    if (preg_match_all('/\((?:[^()\\\\]|\\\\.)*\)/', $t, $mm)) {
                        foreach ($mm[0] as $s) {
                            $out .= $this->unescapePdfString($s).' ';
                        }
                    }
                }
            }
        }

        return $out;
    }

    protected function unescapePdfString(string $s): string
    {
        $s = preg_replace('/^\(|\)$/', '', $s);
        $s = str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $s);
        // Drop octal escapes (e.g. \045) without the removed /e modifier.
        $s = preg_replace_callback('/\\\\([0-7]{1,3})/', function ($m) {
            return chr((int) octdec($m[1]));
        }, $s);

        return $s;
    }

    /**
     * Read a single entry from a zip archive (DOCX/PPTX are zips).
     */
    protected function readZipEntry(string $path, string $entry): ?string
    {
        if (! class_exists(\ZipArchive::class)) {
            return null;
        }
        $zip = new \ZipArchive;
        if ($zip->open($path) !== true) {
            return null;
        }
        $content = $zip->getFromName($entry);
        $zip->close();

        return is_string($content) ? $content : null;
    }

    /**
     * Strip XML tags but keep <w:p>/<a:p> paragraph breaks as newlines.
     */
    protected function xmlText(string $xml): string
    {
        // Insert line breaks at paragraph boundaries before stripping tags.
        $xml = preg_replace('#</w:p>|</a:p>#', "\n", $xml);
        $xml = preg_replace('#<w:br\s*/?>#', "\n", $xml);
        $text = strip_tags($xml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return trim(preg_replace('/\n{2,}/', "\n", $text));
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
