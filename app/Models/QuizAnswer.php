<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A student's first answer to one lesson-quiz question (XP is stored on correct ones). */
class QuizAnswer extends Model
{
    protected $fillable = ['user_id', 'course', 'module', 'lesson', 'question', 'choice', 'is_correct', 'xp'];

    protected function casts(): array
    {
        return ['is_correct' => 'boolean', 'question' => 'integer', 'xp' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
