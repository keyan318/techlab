<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** XP a student spent on something (currently: Astro's hint for an exercise). */
class XpSpend extends Model
{
    protected $fillable = ['user_id', 'course', 'module', 'lesson', 'item', 'cost'];

    protected function casts(): array
    {
        return ['cost' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
