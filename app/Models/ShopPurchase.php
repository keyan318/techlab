<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Something a student bought with XP in the Shop: "frame:<key>", "animal:<key>" or "suit:<key>". */
class ShopPurchase extends Model
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
