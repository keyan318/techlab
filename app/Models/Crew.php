<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crew extends Model
{
    protected $fillable = ['name', 'code', 'teacher_id'];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('position')->orderBy('id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(CrewQuiz::class)->orderBy('id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Explicit roster: every crew member (teacher + students) with their role.
    public function roster()
    {
        return $this->belongsToMany(User::class, 'crew_members')
            ->withPivot('role')
            ->withTimestamps();
    }
}
