<?php

namespace App\Models;

use App\Services\LessonQuizService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A faculty member's edited Knowledge Check for one lesson. It replaces the default quiz in the lesson Blade
 * for everyone in the course; deleting it brings the default back.
 *
 * `questions` is a list of {prompt, options: [A, B, ...], correct: "B", explanation}.
 */
class LessonQuiz extends Model
{
    protected $fillable = ['crew_id', 'course', 'module', 'lesson', 'questions', 'updated_by'];

    protected function casts(): array
    {
        return ['questions' => 'array'];
    }

    protected static function booted(): void
    {
        static::saved(fn (self $q) => LessonQuizService::forget($q->course));
        static::deleted(fn (self $q) => LessonQuizService::forget($q->course));
    }

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }
}
