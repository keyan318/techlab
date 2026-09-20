<?php

namespace App\Services\Reports;

use App\Exceptions\NvidiaNimException;
use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Services\Quiz\QuizNimService;
use Illuminate\Support\Facades\Log;

/**
 * Astro as the content model for Reports.
 *
 * Reuses QuizNimService (non-streaming, ordered model failover). Source text
 * comes from QuizSourceResolver — the persisted conversation, never the browser.
 */
class ReportContentService
{
    private const INSUFFICIENT = 'Not enough educational material in this conversation to write a report. Chat a bit more with Astro about a topic first.';

    public function __construct(protected QuizNimService $nim) {}

    /**
     * @return array{title: string, topic: string, sections: array<int, array{id: int, heading: string, body: string}>}
     *
     * @throws QuizSourceException
     * @throws QuizGenerationException
     */
    public function generate(string $source): array
    {
        $source = trim($source);
        if (mb_strlen($source) < 80) {
            throw new QuizSourceException(self::INSUFFICIENT, 422);
        }

        $bodies = max(2, (int) config('reports.max_sections', 8) - 2);
        $system = str_replace('{sections}', '3-'.min($bodies, 5), (string) config('reports.system_prompt', ''));

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => "Write a study report from this conversation:\n\n"
                .mb_substr($source, 0, (int) config('quiz.max_source_chars', 8000))],
        ];

        try {
            $raw = $this->nim->completeJson($messages, [
                'temperature' => (float) config('reports.temperature', 0.4),
                'max_tokens' => (int) config('reports.max_tokens', 6000),
                'timeout' => (int) config('reports.timeout', 75),
            ]);
        } catch (NvidiaNimException $e) {
            throw new QuizGenerationException($e->getMessage(), $e->category, $e->status ?? 502, $e->requestId, $e);
        }

        if (! is_array($raw) || $raw === []) {
            throw new QuizGenerationException(
                "Astro couldn't shape a report from that conversation just now. Please try again in a moment.",
                'NIM_EMPTY_RESPONSE', 502
            );
        }

        if (! empty($raw['insufficient_content'])) {
            $reason = is_string($raw['reason'] ?? null) && trim($raw['reason']) !== '' ? trim($raw['reason']) : self::INSUFFICIENT;
            throw new QuizSourceException($reason, 422);
        }

        $report = $this->normalize($raw);
        if ($report === null) {
            Log::warning('Report model output unusable', ['keys' => array_keys($raw), 'head' => mb_substr(json_encode($raw), 0, 500)]);

            throw new QuizGenerationException(
                "Astro's response didn't form a usable report. Please try again — sometimes the model needs another pass.",
                'SCHEMA_ERROR', 502
            );
        }

        return $report;
    }

    /** Validate + clean model output; drops empty sections and caps the count. */
    public function normalize(array $raw): ?array
    {
        $rawSections = $raw['sections'] ?? $raw['report']['sections'] ?? $raw['chapters'] ?? $raw['parts'] ?? null;
        if (is_array($rawSections) && $rawSections !== [] && ! array_is_list($rawSections)) {
            // {"Introduction": "text", ...} or {"intro": {heading, body}} → list
            $list = [];
            foreach ($rawSections as $k => $v) {
                $list[] = is_array($v) ? $v + ['heading' => is_string($k) ? $k : ''] : ['heading' => (string) $k, 'body' => $v];
            }
            $rawSections = $list;
        }
        if (! is_array($rawSections)) {
            return null;
        }

        $sections = [];
        foreach ($rawSections as $s) {
            if (! is_array($s)) {
                continue;
            }
            $heading = $this->line($s['heading'] ?? $s['title'] ?? $s['name'] ?? null, 90);
            $body = $this->body($s['body'] ?? $s['content'] ?? $s['text'] ?? $s['paragraphs'] ?? null);
            if ($heading === '' || $body === '') {
                continue;
            }
            $sections[] = ['id' => count($sections) + 1, 'heading' => $heading, 'body' => $body];
            if (count($sections) >= (int) config('reports.max_sections', 8)) {
                break;
            }
        }

        if (count($sections) < 2) {
            return null;
        }

        return [
            'title' => $this->line($raw['title'] ?? null, 100) ?: 'Study report',
            'topic' => $this->line($raw['topic'] ?? null, 80),
            'sections' => $sections,
        ];
    }

    private function line(mixed $v, int $max): string
    {
        if (! is_string($v)) {
            return '';
        }
        $v = trim(preg_replace('/\s+/', ' ', strip_tags($v)));

        return mb_strlen($v) > $max ? rtrim(mb_substr($v, 0, $max - 1)).'…' : $v;
    }

    /** Markdown body: keep line breaks, strip raw HTML, cap length. */
    private function body(mixed $v): string
    {
        if (is_array($v)) {
            $v = implode("\n\n", array_filter(array_map(fn ($x) => is_string($x) ? $x : '', $v)));
        }
        if (! is_string($v)) {
            return '';
        }
        $v = trim(preg_replace("/\n{3,}/", "\n\n", strip_tags(str_replace("\r\n", "\n", $v))));

        return mb_substr($v, 0, 3000);
    }
}
