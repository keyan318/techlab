<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Real web sources for extensive-research questions.
 *
 * Only URLs the search API actually returned are ever shown or cited, so the
 * model cannot invent links. Simple questions never reach the web: needsResearch()
 * is a plain rule check (no AI call).
 */
class WebSearchService
{
    /** Below this many characters a question is a quick lookup, not research. */
    public const MIN_RESEARCH_CHARS = 40;

    private const RESEARCH_PATTERN = '/\b(research\w*|sources?|cite|citations?|references?|according to|latest|recent(ly)?|current|news|trends?|statistics|stats|benchmarks?|case stud(y|ies)|best practices?|state of the art|compar(e|ison|ing)|vs\.?|versus|pros and cons|vulnerabilit(y|ies)|CVE-\d+|in[- ]depth|deep dive|investigate|evidence|studies|docs?|documentation|official|refer(ring)? to|based on)\b/i';

    public static function enabled(): bool
    {
        return (string) config('web_search.api_key') !== '';
    }

    public function needsResearch(string $message): bool
    {
        $message = trim($message);

        return mb_strlen($message) >= self::MIN_RESEARCH_CHARS
            && preg_match(self::RESEARCH_PATTERN, $message) === 1;
    }

    /**
     * @return array<int, array{title: string, url: string, domain: string, snippet: string}>
     */
    public function search(string $query): array
    {
        if (! self::enabled()) {
            return [];
        }

        try {
            $response = Http::timeout((int) config('web_search.timeout'))
                ->acceptJson()
                ->post((string) config('web_search.endpoint'), [
                    'api_key' => config('web_search.api_key'),
                    'query' => mb_substr($query, 0, 400),
                    'max_results' => (int) config('web_search.max_results'),
                    'search_depth' => 'basic',
                    'include_answer' => false,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Web search failed: '.$e->getMessage());

            return [];
        }

        if (! $response->successful()) {
            Log::warning('Web search HTTP '.$response->status());

            return [];
        }

        $out = [];
        foreach ((array) $response->json('results', []) as $r) {
            $url = (string) ($r['url'] ?? '');
            $host = parse_url($url, PHP_URL_HOST);
            if (! $host || ! preg_match('#^https?://#i', $url)) {
                continue;
            }
            $out[] = [
                'title' => trim((string) ($r['title'] ?? '')) ?: $host,
                'url' => $url,
                'domain' => preg_replace('/^www\./', '', $host),
                'snippet' => mb_substr(trim((string) ($r['content'] ?? '')), 0, 700),
            ];
        }

        return array_slice($out, 0, (int) config('web_search.max_results'));
    }

    /** Prompt block: numbered web results Astro must cite by number, and only these. */
    public function promptBlock(array $results): string
    {
        if (! $results) {
            return '';
        }
        $blocks = [];
        foreach ($results as $i => $r) {
            $n = $i + 1;
            $blocks[] = "[Web {$n}: {$r['title']} — {$r['url']}]\n{$r['snippet']}";
        }

        return 'These are live web results for a research question. Base factual claims on them and cite by '
            .'number like [Web 1]. Never cite or link a URL that is not listed here. If they do not cover '
            ."something, say so plainly instead of guessing.\n\n".implode("\n\n---\n\n", $blocks);
    }
}
