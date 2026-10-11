<?php

namespace App\Services;

use App\Exceptions\DocumentTextException;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * Plain text out of a file on disk — PDF, Word, PowerPoint, or anything already text.
 *
 * One copy of this logic, used by every caller: chat attachments (AttachmentService),
 * faculty course materials (ExtractMaterialText) and the infographic source resolver.
 * It works from a path, not an upload, so a queued job can read a stored file.
 */
class DocumentTextExtractor
{
    /** Read as-is, no parsing. */
    public const TEXT_EXTENSIONS = [
        'txt', 'md', 'markdown', 'csv', 'tsv', 'json', 'xml', 'yaml', 'yml', 'html', 'htm', 'css', 'log', 'ini', 'toml', 'sql',
        'py', 'js', 'ts', 'jsx', 'tsx', 'php', 'java', 'c', 'h', 'cpp', 'cs', 'go', 'rs', 'rb', 'sh',
    ];

    /** Documents we unpack to get at the text inside. */
    public const DOC_EXTENSIONS = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];

    /** True when extract() knows how to read this extension. Images, ZIPs and the rest are false. */
    public static function supports(string $ext): bool
    {
        $ext = strtolower($ext);

        return in_array($ext, self::DOC_EXTENSIONS, true) || in_array($ext, self::TEXT_EXTENSIONS, true);
    }

    /**
     * @param  string  $path  an absolute path on disk
     * @param  string  $name  the human name used in error messages
     *
     * @throws DocumentTextException
     */
    public function extract(string $path, string $ext, string $name): string
    {
        return match (strtolower($ext)) {
            'pdf' => $this->pdf($path, $name),
            // .doc/.ppt are only openable when they are really the modern zipped format
            // under an old extension; a true binary Office 97 file raises a clear error.
            'docx', 'doc' => $this->office($path, $name, ['word/document.xml'], 'Word document'),
            'pptx', 'ppt' => $this->office($path, $name, null, 'presentation'),
            default => $this->plain($path, $name),
        };
    }

    private function plain(string $path, string $name): string
    {
        $raw = (string) file_get_contents($path);

        if (str_contains(substr($raw, 0, 4096), "\0")) {
            throw new DocumentTextException("\"{$name}\" looks like a binary file, not text.");
        }

        return trim(mb_convert_encoding($raw, 'UTF-8', 'UTF-8'));
    }

    private function pdf(string $path, string $name): string
    {
        $text = '';

        // Some PDFs (e.g. re-saved by macOS Preview/Quartz) have a page tree the parser can't
        // walk: it warns (which Laravel raises as an exception) and reports 0 pages.
        try {
            $parsed = (new PdfParser)->parseFile($path);
        } catch (\Throwable $e) {
            Log::warning("PDF parse failed for {$name}: ".$e->getMessage());

            throw new DocumentTextException("Astro couldn't open \"{$name}\". Is it a valid PDF?");
        }

        try {
            $text = trim($parsed->getText());
        } catch (\Throwable $e) {
            Log::info("PDF page-tree text failed for {$name}, reading pages directly: ".$e->getMessage());
        }

        if ($text === '') {
            $text = $this->pdfFromPageObjects($parsed);
        }

        if ($text === '') {
            throw new DocumentTextException("\"{$name}\" has no selectable text (it may be a scan). Try attaching it as an image instead.");
        }

        return preg_replace('/[ \t]+/', ' ', $text) ?? $text;
    }

    /** Fallback: read every Page object in file order, skipping any page that fails. */
    private function pdfFromPageObjects(Document $doc): string
    {
        $out = [];
        try {
            foreach ($doc->getObjectsByType('Page') as $page) {
                try {
                    $out[] = trim($page->getText());
                } catch (\Throwable) {
                    continue;
                }
            }
        } catch (\Throwable) {
            return '';
        }

        return trim(implode("\n\n", array_filter($out)));
    }

    /**
     * Text from a .docx / .pptx (zipped XML). $parts null = every ppt/slides/slideN.xml in order.
     *
     * @param  string[]|null  $parts
     */
    private function office(string $path, string $name, ?array $parts, string $noun): string
    {
        $zip = new \ZipArchive;

        // RDONLY matters: without it libzip treats an empty or unreadable file as a new
        // archive and DELETES it on close. These are the faculty's own stored uploads, so
        // reading one must never be able to remove it.
        if ($zip->open($path, \ZipArchive::RDONLY) !== true) {
            throw new DocumentTextException("Astro couldn't open \"{$name}\". Is it a valid {$noun}?");
        }

        if ($parts === null) {
            $parts = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = (string) $zip->getNameIndex($i);
                if (preg_match('#^ppt/slides/slide\d+\.xml$#', $entry)) {
                    $parts[] = $entry;
                }
            }
            natsort($parts);
        }

        $out = [];
        foreach ($parts as $entry) {
            $xml = $zip->getFromName($entry);
            if ($xml === false) {
                continue;
            }
            // Paragraph / table-cell / tab boundaries become whitespace; every other tag is dropped.
            $xml = preg_replace(['#</(w:p|a:p)>#', '#</w:tc>#', '#<w:tab/>#', '#<w:br/>#'], ["\n", ' | ', "\t", "\n"], $xml);
            $out[] = trim(html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8'));
        }
        $zip->close();

        $text = trim(preg_replace("/\n{3,}/", "\n\n", implode("\n\n", $out)) ?? '');

        if ($text === '') {
            throw new DocumentTextException("\"{$name}\" has no readable text.");
        }

        return $text;
    }
}
