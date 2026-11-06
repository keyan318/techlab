<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Gems a student traded for spendable XP at the Shop (config ranks.xp_per_gem). */
class GemExchange extends Model
{
    protected $fillable = ['user_id', 'gems', 'xp'];

    protected function casts(): array
    {
        return ['gems' => 'integer', 'xp' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
