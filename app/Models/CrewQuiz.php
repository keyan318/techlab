<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A quiz authored by a teacher for their crew. Scored server-side; one attempt per student. */
class CrewQuiz extends Model
{
    protected $fillable = ['crew_id', 'title', 'minutes'];

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
}
