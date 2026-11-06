<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class CrewQuizAttempt extends Model
{
    /**
     * Attempt states. A quiz is in_progress from Start until it is submitted (or its clock runs
     * out); a finished quiz has no status. A final exam ends as passed or failed.
     */
    public const IN_PROGRESS = 'in_progress';

    public const PASSED = 'passed';

    public const FAILED = 'failed';

    /** Ways a final exam can be ended early, and how the faculty member reads each one. */
    public const VIOLATIONS = [
        'left_fullscreen' => 'Left full screen',
        'switched_away' => 'Switched to another window or tab',
        'left_page' => 'Left or reloaded the exam page',
        'screenshot' => 'Tried to take a screenshot',
        'time_up' => 'Submitted after the time ran out',
    ];

    /** The same, short enough for a table cell. */
    public const VIOLATIONS_SHORT = [
        'left_fullscreen' => 'left full screen',
        'switched_away' => 'switched tab or window',
        'left_page' => 'left the exam page',
        'screenshot' => 'tried a screenshot',
        'time_up' => 'ran out of time',
    ];

    /** Caught cheating this many times (across tries) and the student is banned from the final exam. */
    public const STRIKE_LIMIT = 2;

    protected $fillable = ['crew_quiz_id', 'user_id', 'score', 'total', 'xp', 'answers', 'status', 'started_at', 'finished_at', 'violation', 'wiped_progress', 'try_number', 'strikes', 'faculty_seen_at'];

    protected $casts = ['answers' => 'array', 'wiped_progress' => 'array', 'try_number' => 'integer', 'strikes' => 'integer', 'xp' => 'integer', 'started_at' => 'datetime', 'finished_at' => 'datetime', 'faculty_seen_at' => 'datetime'];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(CrewQuiz::class, 'crew_quiz_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function percent(): int
    {
        return $this->total > 0 ? (int) round($this->score / $this->total * 100) : 0;
    }

    public function inProgress(): bool
    {
        return $this->status === self::IN_PROGRESS;
    }

    /** When this attempt's clock runs out, or null if the quiz has no time limit. */
    public function endsAt(int $minutes): ?Carbon
    {
        return $minutes > 0 && $this->started_at ? $this->started_at->copy()->addMinutes($minutes) : null;
    }

    /** Seconds left on the clock (never below zero), or null with no time limit. */
    public function secondsLeft(int $minutes): ?int
    {
        $ends = $this->endsAt($minutes);

        return $ends ? max(0, (int) ceil(now()->diffInSeconds($ends, false))) : null;
    }

    public function passed(): bool
    {
        return $this->status === self::PASSED;
    }

    /** The plain-words reason the exam was ended early, or null if it was not. */
    public function violationText(): ?string
    {
        return $this->violation ? (self::VIOLATIONS[$this->violation] ?? $this->violation) : null;
    }

    /** The reason in a few words, for the dashboard's student table. */
    public function violationShort(): ?string
    {
        return $this->violation ? (self::VIOLATIONS_SHORT[$this->violation] ?? $this->violation) : null;
    }

    /** Ended for breaking an exam rule (running out of time is not cheating, so it is not a strike). */
    public function cheated(): bool
    {
        return $this->violation !== null && $this->violation !== 'time_up';
    }

    /** Caught cheating on STRIKE_LIMIT tries: no more tries unless the faculty member allows a retake. */
    public function banned(): bool
    {
        return $this->status === self::FAILED && $this->cheated() && $this->strikes >= self::STRIKE_LIMIT;
    }
}
