<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Random\Engine\Mt19937;
use Random\Randomizer;

/**
 * A quiz authored by a faculty member for their crew. Scored server-side; one attempt per student.
 *
 * One quiz per crew can be the final exam (`is_final`): students take it full screen once every
 * lesson is done, and passing it (`pass_percent` or better) is what earns the certificate.
 *
 * A quiz with a `module` ("m1") is that module's mini boss (MiniBossService): taken full screen like
 * the final, once the module's lessons are done.
 */
class CrewQuiz extends Model
{
    /** The pass mark a final exam starts with until the faculty member changes it. */
    public const DEFAULT_PASS_PERCENT = 75;

    /** Seconds allowed after the clock hits zero for the page's own auto-submit to arrive. */
    public const LATE_GRACE_SECONDS = 90;

    protected $fillable = ['crew_id', 'title', 'description', 'minutes', 'due_at', 'published_at', 'is_final', 'module', 'pass_percent'];

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

    /** The ordinary quizzes, without the final exam or the mini bosses (which have their own place on every page). */
    public function scopeRegular(Builder $query): Builder
    {
        return $query->where('is_final', false)->whereNull('module');
    }

    /** The module mini bosses. */
    public function scopeMiniBosses(Builder $query): Builder
    {
        return $query->whereNotNull('module');
    }

    public function isMiniBoss(): bool
    {
        return $this->module !== null;
    }

    /** Taken on the full-screen exam page, with its rules: the final exam and every mini boss. */
    public function isProctored(): bool
    {
        return $this->is_final || $this->isMiniBoss();
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

    /** The score (in %) needed to pass the final exam or a mini boss. */
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

    /** Has this attempt's clock run out, grace included? Never true for a quiz with no time limit. */
    public function isOverTime(CrewQuizAttempt $attempt): bool
    {
        $ends = $attempt->endsAt((int) $this->minutes);

        return $ends !== null && now()->greaterThan($ends->copy()->addSeconds(self::LATE_GRACE_SECONDS));
    }

    /**
     * Grades an ordinary quiz attempt and closes it. Conditional on it still being open, so a
     * double submit (or a submit racing the clock) can't grade it twice: the first one stands.
     */
    public function finish(CrewQuizAttempt $attempt, array $answers): CrewQuizAttempt
    {
        $this->loadMissing('questions');
        $graded = $this->grade($answers);

        CrewQuizAttempt::whereKey($attempt->id)->where('status', CrewQuizAttempt::IN_PROGRESS)->update([
            'score' => $graded['score'],
            'total' => $graded['total'],
            'answers' => json_encode($graded['answers']),
            'status' => null,
            'finished_at' => now(),
        ]);

        return $attempt->refresh();
    }

    /**
     * The clock keeps running when a student closes the page. Once it has run out, their attempt
     * is graded from the answers the page saved along the way. Final exams have their own rules.
     */
    public function closeIfExpired(CrewQuizAttempt $attempt): CrewQuizAttempt
    {
        if (! $this->isProctored() && $attempt->inProgress() && $this->isOverTime($attempt)) {
            $this->finish($attempt, $attempt->answers ?? []);
        }

        return $attempt;
    }

    /** closeIfExpired() for every student at once, before the faculty member reads the results. */
    public function closeExpiredAttempts(): void
    {
        if ($this->isProctored() || ! $this->minutes) {
            return;
        }

        $this->attempts()->where('status', CrewQuizAttempt::IN_PROGRESS)
            ->where('started_at', '<', now()->subMinutes($this->minutes)->subSeconds(self::LATE_GRACE_SECONDS))
            ->get()->each(fn (CrewQuizAttempt $a) => $this->finish($a, $a->answers ?? []));
    }

    /**
     * What a student sees once they have finished: every question with what they picked and
     * the right answer. Self-contained (prompt and options included) so a page that never
     * loaded the questions, like a reopened result, can still show it.
     *
     * @return array<int, array<string, mixed>>
     */
    public function reviewFor(CrewQuizAttempt $attempt): array
    {
        $this->loadMissing('questions');

        return $this->questions->map(function (CrewQuizQuestion $q) use ($attempt) {
            $chosen = $attempt->answers[$q->id] ?? null;

            return [
                'id' => $q->id,
                'prompt' => $q->prompt,
                'type' => $q->type,
                'options' => $q->options,
                'chosen' => $chosen,
                'correct' => $q->isMultiAnswer() ? ($q->correct_indexes ?? []) : $q->correct_index,
                'right' => $q->isCorrect($chosen),
            ];
        })->values()->all();
    }

    /**
     * The questions as a student may see them: no answers and no source tag. Given a seed (the attempt's id),
     * they come in an order of their own that stays the same for that attempt.
     *
     * @return array<int, array<string, mixed>>
     */
    public function studentQuestions(?int $seed = null): array
    {
        $this->loadMissing('questions');

        $questions = $this->questions->map(fn (CrewQuizQuestion $q) => [
            'id' => $q->id,
            'prompt' => $q->prompt,
            'type' => $q->type,
            'options' => $q->options,
            'required' => $q->required,
        ])->values()->all();

        return $seed === null ? $questions : (new Randomizer(new Mt19937($seed)))->shuffleArray($questions);
    }
}
