<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One use of Astro's AI by a student (a chat message or a Studio tool) and the XP it cost (0 when free). */
class AiUsage extends Model
{
    protected $fillable = ['user_id', 'feature', 'cost'];

    protected function casts(): array
    {
        return ['cost' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
