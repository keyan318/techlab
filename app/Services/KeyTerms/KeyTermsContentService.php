<?php

namespace App\Services\KeyTerms;

use App\Exceptions\NvidiaNimException;
use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Services\Quiz\QuizNimService;

/**
 * Astro as the content model for Key Terms: important vocabulary from the
 * current conversation, each with a kid-friendly definition (key idea in **bold**)
 * and a short "imagine..." analogy. Shares QuizNimService with Quiz/Flashcards.
 */
class KeyTermsContentService
{
    private const INSUFFICIENT = 'Not enough educational material in this conversation to pick key terms. Chat a bit more with Astro about a topic first.';

    public function __construct(protected QuizNimService $nim) {}

    /**
     * @return array{title: string, topic: string, terms: array<int, array{id: int, term: string, definition: string, analogy: string}>}
     *
     * @throws QuizSourceException
     * @throws QuizGenerationException
     */
    public function generate(string $source, int $count): array
    {
        $source = trim($source);
        if (mb_strlen($source) < 80) {
            throw new QuizSourceException(self::INSUFFICIENT, 422);
        }

        $count = max((int) config('keyterms.min_terms', 3), min($count, (int) config('keyterms.max_terms', 12)));
        $system = str_replace('{count}', (string) $count, (string) config('keyterms.system_prompt', ''));

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => "Pick the {$count} most important key terms from this conversation:\n\n"
                .mb_substr($source, 0, (int) config('quiz.max_source_chars', 8000))],
        ];

        try {
            $raw = $this->nim->completeJson($messages, [
                'temperature' => (float) config('keyterms.temperature', 0.4),
                'max_tokens' => min((int) config('keyterms.max_tokens', 4096), 1000 + $count * 200),
            ]);
        } catch (NvidiaNimException $e) {
            throw new QuizGenerationException($e->getMessage(), $e->category, $e->status ?? 502, $e->requestId, $e);
        }

        if (! is_array($raw) || $raw === []) {
            throw new QuizGenerationException(
                "Astro couldn't pick key terms from that conversation just now. Please try again in a moment.",
                'NIM_EMPTY_RESPONSE', 502
            );
        }

        if (! empty($raw['insufficient_content'])) {
            $reason = is_string($raw['reason'] ?? null) && trim($raw['reason']) !== '' ? trim($raw['reason']) : self::INSUFFICIENT;
            throw new QuizSourceException($reason, 422);
        }

        $result = $this->normalize($raw, $count);
        if ($result === null) {
            throw new QuizGenerationException(
                "Astro's response didn't form a usable list. Please try again — sometimes the model needs another pass.",
                'SCHEMA_ERROR', 502
            );
        }

        return $result;
    }

    /** Validate + clean model output; drops malformed/duplicate terms and caps at $count. */
    public function normalize(array $raw, int $count): ?array
    {
        $rawTerms = $raw['terms'] ?? null;
        if (! is_array($rawTerms)) {
            return null;
        }

        $terms = [];
        $seen = [];
        foreach ($rawTerms as $t) {
            if (! is_array($t)) {
                continue;
            }
            $term = $this->text($t['term'] ?? null, 60, false);
            $definition = $this->text($t['definition'] ?? null, 220, true);
            $analogy = $this->text($t['analogy'] ?? null, 240, false);
            $key = mb_strtolower($term);
            if ($term === '' || $definition === '' || $analogy === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $terms[] = [
                'id' => count($terms) + 1,
                'term' => $term,
                'definition' => $definition,
                'analogy' => $analogy,
            ];
            if (count($terms) >= $count) {
                break;
            }
        }

        if (count($terms) < (int) config('keyterms.min_terms', 3)) {
            return null;
        }

        return [
            'title' => $this->text($raw['title'] ?? null, 80, false) ?: 'Key Terms',
            'topic' => $this->text($raw['topic'] ?? null, 80, false),
            'terms' => $terms,
        ];
    }

    /** Plain text only. `**bold**` survives when $allowBold, but only as balanced pairs. */
    private function text(mixed $v, int $max, bool $allowBold): string
    {
        if (! is_string($v)) {
            return '';
        }
        $v = trim(preg_replace('/\s+/', ' ', strip_tags($v)));
        if (! $allowBold) {
            $v = str_replace('**', '', $v);
        }
        if (mb_strlen($v) > $max) {
            $v = rtrim(mb_substr($v, 0, $max - 1)).'…';
        }
        if ($allowBold && substr_count($v, '**') % 2 === 1) {
            $v = str_replace('**', '', $v);
        }

        return $v;
    }
}
