<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One finished lesson. Saved under the student's track (crew_id: their self-paced crew or their class).
 * Switching track shelves the old track's rows (shelved_at) rather than deleting them, and every query
 * only sees unshelved rows (the "active" scope) — so a class starts at zero and the old progress comes
 * back if the student returns. XP counts every row, shelved or not: XP once earned is never lost.
 */
class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    /** XP comes once per lesson, even when it was finished on two tracks. Use on an unscoped query. */
    public const XP_COUNT = "count(distinct course || '/' || module || '/' || lesson)";

    protected static function booted(): void
    {
        static::addGlobalScope('active', fn (Builder $q) => $q->whereNull('lesson_progress.shelved_at'));
    }

    protected $fillable = [
        'user_id',
        'course',
        'module',
        'lesson',
        'code',
        'crew_id',
        'completed_at',
        'shelved_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'shelved_at' => 'datetime',
        ];
    }

    /**
     * The student who completed the lesson.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
