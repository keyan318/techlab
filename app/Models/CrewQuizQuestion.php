<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrewQuizQuestion extends Model
{
    /** The answer shapes a faculty member can pick, in the order the builder lists them. */
    public const TYPES = ['multiple_choice', 'true_false', 'checkboxes', 'dropdown'];

    /** Types where exactly one option is right (graded against correct_index). */
    public const SINGLE_ANSWER_TYPES = ['multiple_choice', 'true_false', 'dropdown'];

    protected $fillable = ['crew_quiz_id', 'prompt', 'source', 'options', 'type', 'correct_index', 'correct_indexes', 'required', 'position'];

    protected $casts = ['options' => 'array', 'correct_indexes' => 'array', 'required' => 'boolean'];

    public function isMultiAnswer(): bool
    {
        return $this->type === 'checkboxes';
    }

    /**
     * Is this answer right? A single-answer question takes one index; checkboxes take a set,
     * and the student has to pick exactly the right ones — no partial credit.
     *
     * @param  int|int[]|null  $chosen
     */
    public function isCorrect(mixed $chosen): bool
    {
        if ($this->isMultiAnswer()) {
            $picked = collect(is_array($chosen) ? $chosen : [])
                ->filter(fn ($i) => is_numeric($i))->map(fn ($i) => (int) $i)->unique()->sort()->values()->all();
            $answer = collect($this->correct_indexes ?? [])->map(fn ($i) => (int) $i)->unique()->sort()->values()->all();

            return $answer !== [] && $picked === $answer;
        }

        return is_numeric($chosen) && (int) $chosen === $this->correct_index;
    }
}
