<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
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

    /** Below this, a message is a reply or a greeting rather than a question worth searching. */
    public const MIN_QUESTION_CHARS = 12;

    /** Openers, thanks and chit-chat: never worth a web search. */
    private const SMALL_TALK_PATTERN = '/^\s*(hi|hey|hello|yo|sup|thanks|thank you|ty|ok|okay|k|cool|nice|got it|gotcha|sure|yes|yeah|yep|no|nope|bye|goodbye|good (morning|afternoon|evening|night)|how are you|what\'s up|whats up)\b/i';

    private const RESEARCH_PATTERN = '/\b(research\w*|sources?|cite|citations?|references?|according to|latest|recent(ly)?|current|news|trends?|statistics|stats|benchmarks?|case stud(y|ies)|best practices?|state of the art|compar(e|ison|ing)|vs\.?|versus|pros and cons|vulnerabilit(y|ies)|CVE-\d+|in[- ]depth|deep dive|investigate|evidence|studies|docs?|documentation|official|refer(ring)? to|based on)\b/i';

    public static function enabled(): bool
    {
        return (string) config('web_search.endpoint') !== '' && (string) config('web_search.api_key') !== '';
    }

    public function needsResearch(string $message): bool
    {
        $message = trim($message);

        return mb_strlen($message) >= self::MIN_RESEARCH_CHARS
            && preg_match(self::RESEARCH_PATTERN, $message) === 1;
    }

    /**
     * A plain question about a topic — "what is cryptography", "how does DNS work".
     *
     * This is the fallback trigger: when the faculty's own materials have nothing on the
     * subject, a question like this should still get real sources rather than whatever the
     * model remembers. Greetings, thanks and one-word replies are not questions, and never
     * reach the web.
     */
    public function looksFactual(string $message): bool
    {
        $message = trim($message);

        if (mb_strlen($message) < self::MIN_QUESTION_CHARS) {
            return false;
        }

        if (preg_match(self::SMALL_TALK_PATTERN, $message) === 1) {
            return false;
        }

        // At least three words, so "ok thanks" and "yes please" stay out.
        $words = preg_split('/\s+/', $message, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return count($words) >= 3;
    }

    /** Reasons AstroRouter can hand back where the paste itself is the ground truth, not the web. */
    private const NO_SEARCH_DEEP_REASONS = ['code_block', 'traceback'];

    /**
     * Whether this turn should hit the web, given AstroRouter's Fast/Deep decision.
     *
     * Materials are checked first, but a Deep question — one Astro itself judged needs real
     * reasoning — always gets web sources too, even when a faculty upload already matched:
     * one grazing keyword hit in a PDF shouldn't be the only grounding for a hard question.
     * Below Deep, the web stays a fallback for whatever materials didn't cover.
     *
     * A selected Planet lesson is different: the student picked it so Astro answers from that
     * lesson, so the web is never searched — not even for a Deep question.
     *
     * @param  array{mode: string, reason: string|null}  $route
     */
    public function shouldSearch(string $message, array $route, bool $hasMaterials, bool $hasLessons = false): bool
    {
        if (! self::enabled() || $hasLessons || ! $this->looksFactual($message)) {
            return false;
        }

        if (in_array($route['reason'] ?? null, self::NO_SEARCH_DEEP_REASONS, true)) {
            return false;
        }

        if (($route['mode'] ?? null) === 'deep') {
            return true;
        }

        return ! $hasMaterials && ($this->needsResearch($message) || $this->looksFactual($message));
    }

    /**
     * @return array<int, array{title: string, url: string, domain: string, snippet: string}>
     */
    public function search(string $query): array
    {
        if (! self::enabled()) {
            return [];
        }

        $query = mb_substr(trim($query), 0, 400);
        $cacheKey = 'web_search:'.sha1(mb_strtolower($query));

        // Same question within a few hours: reuse it, so the free monthly allowance lasts.
        if (is_array($cached = Cache::get($cacheKey))) {
            return $cached;
        }

        try {
            $response = Http::timeout((int) config('web_search.timeout'))
                ->withToken((string) config('web_search.api_key'))
                ->acceptJson()
                ->post((string) config('web_search.endpoint'), [
                    'query' => $query,
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

        $out = array_slice($out, 0, (int) config('web_search.max_results'));

        if ($out) {
            Cache::put($cacheKey, $out, now()->addHours((int) config('web_search.cache_hours')));
        }

        return $out;
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

    /**
     * Belt-and-suspenders against a hallucinated link: the prompt already forbids citing a URL
     * that wasn't handed to the model, but nothing stops the model from doing it anyway. This
     * strips any markdown link or bare URL whose href isn't in $allowedUrls before the answer is
     * ever persisted, collapsing it to plain text instead of deleting the surrounding sentence.
     *
     * @param  string[]  $allowedUrls
     */
    public static function stripUnlistedLinks(string $answer, array $allowedUrls): string
    {
        $allowed = array_flip($allowedUrls);

        $answer = preg_replace_callback(
            '/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/i',
            fn (array $m) => isset($allowed[$m[2]]) ? $m[0] : $m[1],
            $answer
        ) ?? $answer;

        return preg_replace_callback(
            '/(?<![(\[])\bhttps?:\/\/[^\s)]+/i',
            function (array $m) use ($allowed) {
                if (isset($allowed[$m[0]])) {
                    return $m[0];
                }

                return parse_url($m[0], PHP_URL_HOST) ?: $m[0];
            },
            $answer
        ) ?? $answer;
    }
}
