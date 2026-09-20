<?php

namespace App\Services\Flashcards;

use App\Exceptions\NvidiaNimException;
use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Services\Quiz\QuizNimService;

/**
 * Astro as the content model for Flashcards.
 *
 * Reuses QuizNimService (non-streaming, parallel-raced fast models) so cards
 * come back quickly and a retired/overloaded model costs nothing. Source text
 * comes from QuizSourceResolver — the persisted conversation, never the browser.
 */
class FlashcardContentService
{
    private const INSUFFICIENT = 'Not enough educational material in this conversation to build flashcards. Chat a bit more with Astro about a topic first.';

    public function __construct(protected QuizNimService $nim) {}

    /**
     * @return array{title: string, topic: string, cards: array<int, array{id: int, front: string, back: string, hint: string}>}
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

        $count = max((int) config('flashcards.min_cards', 5), min($count, (int) config('flashcards.max_cards', 30)));
        $system = str_replace('{count}', (string) $count, (string) config('flashcards.system_prompt', ''));

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => "Create {$count} flashcards from this conversation:\n\n"
                .mb_substr($source, 0, (int) config('quiz.max_source_chars', 8000))],
        ];

        try {
            $raw = $this->nim->completeJson($messages, [
                'temperature' => (float) config('flashcards.temperature', 0.3),
                'max_tokens' => min((int) config('flashcards.max_tokens', 8192), 1000 + $count * 150),
            ]);
        } catch (NvidiaNimException $e) {
            throw new QuizGenerationException($e->getMessage(), $e->category, $e->status ?? 502, $e->requestId, $e);
        }

        if (! is_array($raw) || $raw === []) {
            throw new QuizGenerationException(
                "Astro couldn't shape flashcards from that conversation just now. Please try again in a moment.",
                'NIM_EMPTY_RESPONSE', 502
            );
        }

        if (! empty($raw['insufficient_content'])) {
            $reason = is_string($raw['reason'] ?? null) && trim($raw['reason']) !== '' ? trim($raw['reason']) : self::INSUFFICIENT;
            throw new QuizSourceException($reason, 422);
        }

        $deck = $this->normalize($raw, $count);
        if ($deck === null) {
            throw new QuizGenerationException(
                "Astro's response didn't form a usable deck. Please try again — sometimes the model needs another pass.",
                'SCHEMA_ERROR', 502
            );
        }

        return $deck;
    }

    /** Validate + clean model output; drops malformed/duplicate cards and caps at $count. */
    public function normalize(array $raw, int $count): ?array
    {
        $rawCards = $raw['cards'] ?? null;
        if (! is_array($rawCards)) {
            return null;
        }

        $cards = [];
        $seen = [];
        foreach ($rawCards as $c) {
            if (! is_array($c)) {
                continue;
            }
            $front = $this->text($c['front'] ?? null, 200);
            $back = $this->text($c['back'] ?? null, 400);
            $key = mb_strtolower($front);
            if ($front === '' || $back === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $cards[] = [
                'id' => count($cards) + 1,
                'front' => $front,
                'back' => $back,
                'hint' => $this->text($c['hint'] ?? null, 120),
            ];
            if (count($cards) >= $count) {
                break;
            }
        }

        if (count($cards) < 3) {
            return null;
        }

        return [
            'title' => $this->text($raw['title'] ?? null, 80) ?: 'Flashcards',
            'topic' => $this->text($raw['topic'] ?? null, 80),
            'cards' => $cards,
        ];
    }

    private function text(mixed $v, int $max): string
    {
        if (! is_string($v)) {
            return '';
        }
        $v = trim(preg_replace('/\s+/', ' ', strip_tags($v)));

        return mb_strlen($v) > $max ? rtrim(mb_substr($v, 0, $max - 1)).'…' : $v;
    }
}
