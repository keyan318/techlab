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
     * @return array<string, mixed>  normalized quiz
     * @throws QuizSourceException       when source is empty/insufficient
     * @throws QuizGenerationException   on model/schema failures
     */
    public function generate(string $source): array
    {
        $source = trim($source);

        if ($source === '') {
            throw new QuizSourceException(
                "Chat with Astro first — the quiz is built from your current conversation.",
                422
            );
        }

        // Quick local insufficient-content check to avoid a wasted NIM call.
        // The model also has the authoritative check (see system prompt).
        if (mb_strlen($source) < 80) {
            throw new QuizSourceException(
                "Not enough educational material in this conversation to build a quiz. Chat a bit more with Astro about a topic first.",
                422
            );
        }

        $messages = [
            ['role' => 'system', 'content' => (string) config('quiz.system_prompt', '')],
            [
                'role' => 'user',
                'content' => "Create a 5-question quiz from this conversation:\n\n"
                    .mb_substr($source, 0, (int) config('quiz.max_source_chars', 8000)),
            ],
        ];

        try {
            $raw = $this->nim->completeJson($messages, [
                'temperature' => (float) config('quiz.temperature', 0.3),
                'max_tokens' => (int) config('quiz.max_tokens_json', 4096),
            ]);
        } catch (NvidiaNimException $e) {
            throw new QuizGenerationException(
                $e->getMessage(),
                $e->category,
                $e->status ?? 502,
                $e->requestId,
                $e
            );
        }

        if (! is_array($raw) || $raw === []) {
            throw new QuizGenerationException(
                "Astro couldn't shape a quiz from that conversation just now. Please try again in a moment.",
                'NIM_EMPTY_RESPONSE',
                502
            );
        }

        // Model-declared insufficient content — surface as QuizSourceException
        // so the controller can return kind: insufficient_content (422).
        if (! empty($raw['insufficient_content'])) {
            $reason = is_string($raw['reason'] ?? null) && trim($raw['reason']) !== ''
                ? trim($raw['reason'])
                : 'Not enough educational material in this conversation to build a quiz. Chat a bit more with Astro about a topic first.';

            throw new QuizSourceException($reason, 422);
        }

        $quiz = $this->schema->normalize($raw);
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
}
