<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One row per privileged action taken from the admin panel — who did what, to what, and when. */
class AdminActionLog extends Model
{
    protected $fillable = ['admin_id', 'action', 'subject', 'details'];

    protected $casts = ['details' => 'array'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
