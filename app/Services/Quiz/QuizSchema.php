<?php

namespace App\Services\Quiz;

/**
 * Validates and coerces the quiz model's JSON into a safe, normalized
 * structure the future quiz UI can render without surprises.
 *
 * The model is NOT trusted. Every field is type-checked and coerced; invalid
 * questions are dropped; if no valid questions remain, normalize() returns
 * null and the caller treats it as a generation failure. Mirrors the discipline
 * of App\Services\Infographic\InfographicSchema.
 *
 * v1 contract: exactly 5 multiple_choice questions, each with 4 choices A-D.
 */
class QuizSchema
{
    /**
     * @return array<string, mixed>|null normalized quiz, or null if unusable
     */
    public function normalize(?array $raw, int $count = 5): ?array
    {
        if (! is_array($raw)) {
            return null;
        }

        // Insufficient-content sentinel — surface as a distinct shape so the
        // caller can return 422 insufficient_content instead of 502.
        if (! empty($raw['insufficient_content'])) {
            return [
                'insufficient_content' => true,
                'reason' => $this->asText($raw['reason'] ?? 'Not enough educational material in this conversation to build a quiz. Chat a bit more with Astro about a topic first.'),
            ];
        }

        $title = $this->asText($raw['title'] ?? '');
        $description = $this->asText($raw['description'] ?? '');
        $topic = $this->asText($raw['topic'] ?? '');

        if ($title === '') {
            $title = 'Quick Check — What Did We Learn?';
        }
        $title = mb_substr($title, 0, 80);

        if ($description !== '') {
            $description = mb_substr($description, 0, 160);
        }
        if ($topic !== '') {
            $topic = mb_substr($topic, 0, 60);
        }

        $questionsRaw = $raw['questions'] ?? null;
        if (! is_array($questionsRaw) || $questionsRaw === []) {
            return null;
        }

        $normalized = [];
        foreach ($questionsRaw as $q) {
            $nq = $this->normalizeQuestion($q);
            if ($nq !== null) {
                $normalized[] = $nq;
            }
        }

        if ($normalized === []) {
            return null;
        }

        // Cap to the requested count (model is told the count, but we enforce).
        if (count($normalized) > $count) {
            $normalized = array_slice($normalized, 0, $count);
        }

        // Assign stable ids server-side (q1..qN), ignore whatever the model sent.
        foreach ($normalized as $i => &$q) {
            $q['id'] = 'q'.($i + 1);
        }
        unset($q);

        // Accept a slightly short batch (a dropped bad question shouldn't fail the
        // whole quiz), but not a mostly-empty one.
        if (count($normalized) < min($count, max(3, (int) ceil($count * 0.4)))) {
            return null;
        }

        return [
            'title' => $title,
            'description' => $description,
            'topic' => $topic,
            'question_count' => count($normalized),
            'questions' => $normalized,
        ];
    }

    /**
     * @param  mixed  $q
     * @return array<string, mixed>|null
     */
    protected function normalizeQuestion($q): ?array
    {
        if (! is_array($q)) {
            return null;
        }

        $type = strtolower(trim((string) ($q['type'] ?? 'multiple_choice')));
        if ($type !== 'multiple_choice') {
            // v1 allowlist: only multiple_choice. Coerce unknowns to it if the
            // rest of the shape is valid; otherwise drop.
            $type = 'multiple_choice';
        }

        $question = $this->asText($q['question'] ?? $q['text'] ?? '');
        if ($question === '') {
            return null;
        }
        $question = mb_substr($question, 0, 280);

        $choices = $this->normalizeChoices($q['choices'] ?? $q['options'] ?? null);
        if ($choices === null) {
            return null;
        }

        $correctKeyRaw = strtoupper(trim((string) ($q['correct_key'] ?? $q['correct_answer'] ?? $q['answer'] ?? '')));
        $choicesBeforeRekey = $q['choices'] ?? $q['options'] ?? null;
        // If choices were re-keyed, map correct_key by text/position so we don't desync.
        $correctKey = $correctKeyRaw;
        if (! in_array($correctKey, ['A', 'B', 'C', 'D'], true)) {
            return null;
        }
        // If the model's original choices used A/B/C/D already, no remap needed
        // (common path). If they used different ordering, map by matching the
        // choice text that the model marked as correct.
        if (is_array($choicesBeforeRekey) && $this->choicesNeedRemap($choicesBeforeRekey)) {
            $origText = null;
            foreach ($choicesBeforeRekey as $orig) {
                if (is_array($orig) && strtoupper(trim((string) ($orig['key'] ?? ''))) === $correctKeyRaw) {
                    $origText = $this->asText($orig['text'] ?? $orig['label'] ?? '');
                    break;
                }
            }
            if ($origText !== null && $origText !== '') {
                foreach ($choices as $idx => $c) {
                    if ($c['text'] === $origText) {
                        $correctKey = $c['key'];
                        break;
                    }
                }
            }
        }
        $choiceKeys = array_column($choices, 'key');
        if (! in_array($correctKey, $choiceKeys, true)) {
            return null;
        }

        $explanation = $this->asText($q['explanation'] ?? $q['reason'] ?? '');
        if ($explanation === '') {
            $explanation = 'The correct answer is '.$correctKey.' based on what Astro explained in the conversation.';
        }
        $explanation = mb_substr($explanation, 0, 240);

        $difficulty = strtolower(trim((string) ($q['difficulty'] ?? 'easy')));
        if (! in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
            $difficulty = 'easy';
        }

        $points = (int) ($q['points'] ?? 1);
        if ($points < 1) {
            $points = 1;
        }
        if ($points > 3) {
            $points = 3;
        }

        return [
            'id' => '', // filled by normalize() loop
            'type' => $type,
            'question' => $question,
            'choices' => $choices,
            'correct_key' => $correctKey,
            'explanation' => $explanation,
            'difficulty' => $difficulty,
            'points' => $points,
        ];
    }

    /**
     * @param  mixed  $raw
     * @return array<int, array{key: string, text: string}>|null
     */
    protected function normalizeChoices($raw): ?array
    {
        if (! is_array($raw)) {
            return null;
        }

        // Filter to valid choice-like entries, tolerate both {key,text} and plain strings.
        $out = [];
        $nextKey = 0;
        $keys = ['A', 'B', 'C', 'D'];

        foreach ($raw as $item) {
            if (count($out) >= 4) {
                break;
            }

            if (is_string($item)) {
                $text = $this->asText($item);
                if ($text === '') {
                    continue;
                }
                $out[] = ['key' => $keys[$nextKey++], 'text' => mb_substr($text, 0, 120)];

                continue;
            }

            if (! is_array($item)) {
                continue;
            }

            $text = $this->asText($item['text'] ?? $item['label'] ?? $item['content'] ?? '');
            if ($text === '') {
                continue;
            }

            $key = strtoupper(trim((string) ($item['key'] ?? '')));
            if (! in_array($key, ['A', 'B', 'C', 'D'], true)) {
                $key = $keys[$nextKey];
            }

            $out[] = ['key' => $key, 'text' => mb_substr($text, 0, 120)];
            $nextKey++;
        }

        if (count($out) !== 4) {
            return null;
        }

        // Re-key to A/B/C/D in order for a stable frontend contract.
        // Correct-key remapping (when the model used a non-standard order) is
        // handled in normalizeQuestion() via text matching.
        $rekeyed = [];
        foreach ($out as $i => $c) {
            $rekeyed[] = ['key' => $keys[$i], 'text' => $c['text']];
        }

        return $rekeyed;
    }

    protected function choicesNeedRemap(array $raw): bool
    {
        $keys = [];
        foreach ($raw as $item) {
            if (! is_array($item)) {
                return true;
            }
            $k = strtoupper(trim((string) ($item['key'] ?? '')));
            if (! in_array($k, ['A', 'B', 'C', 'D'], true)) {
                return true;
            }
            $keys[] = $k;
        }

        return $keys !== ['A', 'B', 'C', 'D'];
    }

    protected function asText($value): string
    {
        if (is_string($value)) {
            return trim(preg_replace('/\s+/', ' ', $value));
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        return '';
    }
}
