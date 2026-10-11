<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrewQuizAttempt extends Model
{
    /** Final exam states. An ordinary quiz's attempt has no status: it is simply submitted. */
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

    protected $fillable = ['crew_quiz_id', 'user_id', 'score', 'total', 'answers', 'status', 'started_at', 'finished_at', 'violation', 'faculty_seen_at'];

    protected $casts = ['answers' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime', 'faculty_seen_at' => 'datetime'];

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

    public function passed(): bool
    {
        return $this->status === self::PASSED;
    }

    /** The plain-words reason the exam was ended early, or null if it was not. */
    public function violationText(): ?string
    {
        return $this->violation ? (self::VIOLATIONS[$this->violation] ?? $this->violation) : null;
    }
}
