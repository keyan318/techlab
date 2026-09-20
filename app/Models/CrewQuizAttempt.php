<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrewQuizAttempt extends Model
{
    protected $fillable = ['crew_quiz_id', 'user_id', 'score', 'total', 'answers'];

    protected $casts = ['answers' => 'array'];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(CrewQuiz::class, 'crew_quiz_id');
    }

    public function percent(): int
    {
        return $this->total > 0 ? (int) round($this->score / $this->total * 100) : 0;
    }
}
