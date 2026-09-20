<?php

namespace App\Services\Quiz;

use App\Exceptions\NvidiaNimException;
use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;

/**
 * Astro as the CONTENT / REASONING model for the Quiz feature.
 *
 * Responsibilities:
 *   - receive clean conversation source text (from QuizSourceResolver)
 *   - enforce insufficient-content short-circuit
 *   - call the dedicated QuizNimService with the quiz system prompt
 *   - validate the model's JSON via QuizSchema
 *
 * Does NOT generate UI, persist results, or touch chat/infographic.
 */
class QuizContentService
{
    public function __construct(
        protected QuizNimService $nim,
        protected QuizSchema $schema
    ) {}

    /**
     * Turn a conversation transcript into a validated quiz.
     *
     * @return array<string, mixed> normalized quiz
     *
     * @throws QuizSourceException when source is empty/insufficient
     * @throws QuizGenerationException on model/schema failures
     */
    public function generate(string $source, int $count = 5): array
    {
        $source = trim($source);

        if ($source === '') {
            throw new QuizSourceException(
                'Chat with Astro first — the quiz is built from your current conversation.',
                422
            );
        }

        // Quick local insufficient-content check to avoid a wasted NIM call.
        // The model also has the authoritative check (see system prompt).
        if (mb_strlen($source) < 80) {
            throw new QuizSourceException(
                'Not enough educational material in this conversation to build a quiz. Chat a bit more with Astro about a topic first.',
                422
            );
        }

        $count = max(1, min($count, (int) config('quiz.max_questions', 20)));
        $sizes = $this->batchSizes($count);
        $source = mb_substr($source, 0, (int) config('quiz.max_source_chars', 8000));

        // One job per batch, all concurrent. Generation time tracks tokens
        // produced, so 10 questions as 2x5 in parallel is ~half the wall time.
        $jobs = [];
        foreach ($sizes as $i => $size) {
            $system = str_replace('{count}', (string) $size, (string) config('quiz.system_prompt', ''));
            if (count($sizes) > 1) {
                $system .= "\n\nThis is part ".($i + 1).' of '.count($sizes).' of a longer quiz. Draw your questions mainly from the '
                    .$this->portion($i, count($sizes)).' of the conversation so the parts do not overlap. Do not add a title-only or empty part.';
            }
            $jobs[$i] = [
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => "Create a {$size}-question quiz from this conversation:\n\n".$source],
                ],
                'options' => [
                    'temperature' => (float) config('quiz.temperature', 0.3),
                    // ~300 tokens per question + headroom for models that still reason.
                    'max_tokens' => min((int) config('quiz.max_tokens_json', 8192), 1000 + $size * 300),
                ],
            ];
        }

        $results = $this->nim->completeJsonMany($jobs);

        $merged = null;
        $questions = [];
        $insufficient = null;
        $firstError = null;
        foreach ($results as $r) {
            if ($r instanceof NvidiaNimException) {
                $firstError ??= $r;

                continue;
            }
            if (! empty($r['insufficient_content'])) {
                $insufficient ??= $r;

                continue;
            }
            $merged ??= $r;
            foreach ((array) ($r['questions'] ?? []) as $q) {
                $questions[] = $q;
            }
        }

        if ($merged === null) {
            if ($insufficient !== null) {
                $reason = is_string($insufficient['reason'] ?? null) && trim($insufficient['reason']) !== ''
                    ? trim($insufficient['reason'])
                    : 'Not enough educational material in this conversation to build a quiz. Chat a bit more with Astro about a topic first.';

                throw new QuizSourceException($reason, 422);
            }
            if ($firstError !== null) {
                throw new QuizGenerationException(
                    $firstError->getMessage(),
                    $firstError->category,
                    $firstError->status ?? 502,
                    $firstError->requestId,
                    $firstError
                );
            }
            throw new QuizGenerationException(
                "Astro couldn't shape a quiz from that conversation just now. Please try again in a moment.",
                'NIM_EMPTY_RESPONSE',
                502
            );
        }

        $merged['questions'] = $this->dedupe($questions);
        $raw = $merged;

        $quiz = $this->schema->normalize($raw, $count);
        if ($quiz === null) {
            throw new QuizGenerationException(
                "Astro's response didn't form a usable quiz. Please try again — sometimes the model needs another pass.",
                'SCHEMA_ERROR',
                502
            );
        }

        // Schema may return insufficient_content sentinel (if model used a different shape).
        if (! empty($quiz['insufficient_content'])) {
            throw new QuizSourceException($quiz['reason'] ?? 'Not enough educational material in this conversation to build a quiz.', 422);
        }

        return $quiz;
    }

    /** @return array<int, int> e.g. 12 with batch 5 => [4,4,4]; 10 => [5,5]; 5 => [5] */
    protected function batchSizes(int $count): array
    {
        $batch = max(1, (int) config('quiz.batch_size', 5));
        if ($count <= $batch + 1) {
            return [$count];
        }
        $parts = (int) ceil($count / $batch);
        $base = intdiv($count, $parts);
        $extra = $count % $parts;

        return array_map(fn ($i) => $base + ($i < $extra ? 1 : 0), range(0, $parts - 1));
    }

    protected function portion(int $i, int $n): string
    {
        if ($n === 2) {
            return $i === 0 ? 'first half' : 'second half';
        }
        $names = ['first', 'second', 'third', 'fourth', 'fifth'];

        return ($names[$i] ?? ($i + 1).'th').' portion';
    }

    /** Drop questions whose text repeats one already kept (parts can overlap). */
    protected function dedupe(array $questions): array
    {
        $seen = [];
        $out = [];
        foreach ($questions as $q) {
            $text = is_array($q) ? (string) ($q['question'] ?? $q['text'] ?? '') : '';
            $key = preg_replace('/[^a-z0-9]+/', ' ', mb_strtolower($text));
            if (trim($key) === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $q;
        }

        return $out;
    }
}
