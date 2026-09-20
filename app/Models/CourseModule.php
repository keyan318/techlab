<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    protected $fillable = ['crew_id', 'title', 'description', 'position'];

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ModuleMaterial::class)->orderBy('id');
    }
}
