<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A quiz authored by a faculty member for their crew. Scored server-side; one attempt per student.
 *
 * One quiz per crew can be the final exam (`is_final`): students take it full screen once every
 * lesson is done, and passing it (`pass_percent` or better) is what earns the certificate.
 */
class CrewQuiz extends Model
{
    /** The pass mark a final exam starts with until the faculty member changes it. */
    public const DEFAULT_PASS_PERCENT = 75;

    protected $fillable = ['crew_id', 'title', 'description', 'minutes', 'due_at', 'published_at', 'is_final', 'pass_percent'];

    protected function casts(): array
    {
        return ['due_at' => 'datetime', 'published_at' => 'datetime', 'minutes' => 'integer', 'is_final' => 'boolean', 'pass_percent' => 'integer'];
    }

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(CrewQuizQuestion::class)->orderBy('position')->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(CrewQuizAttempt::class);
    }

    /** Only a published quiz exists as far as students are concerned. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at');
    }

    /** The ordinary quizzes, without the final exam (which has its own place on every page). */
    public function scopeRegular(Builder $query): Builder
    {
        return $query->where('is_final', false);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    /** Past the deadline. A student who has already submitted keeps their attempt. */
    public function isPastDue(): bool
    {
        return $this->due_at !== null && $this->due_at->isPast();
    }

    /** The score (in %) needed to pass the final exam. */
    public function passPercent(): int
    {
        return $this->pass_percent ?? self::DEFAULT_PASS_PERCENT;
    }

    /**
     * Scores a student's answers against the (loaded) questions. Checkboxes come back as a
     * list, everything else as one index; anything unanswered or malformed counts as wrong.
     *
     * @return array{score: int, total: int, answers: array<int, mixed>}
     */
    public function grade(array $given): array
    {
        $answers = [];
        $score = 0;

        foreach ($this->questions as $q) {
            $chosen = $given[$q->id] ?? null;
            $chosen = $q->isMultiAnswer()
                ? array_values(array_map('intval', array_filter((array) $chosen, 'is_numeric')))
                : (is_numeric($chosen) ? (int) $chosen : null);
            $answers[$q->id] = $chosen;
            if ($q->isCorrect($chosen)) {
                $score++;
            }
        }

        return ['score' => $score, 'total' => $this->questions->count(), 'answers' => $answers];
    }
}
