<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Gems a student spent on something (currently: suits for their astronaut, item "suit:<key>"). */
class GemSpend extends Model
{
    protected $fillable = ['user_id', 'item', 'cost'];

    protected function casts(): array
    {
        return ['cost' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
