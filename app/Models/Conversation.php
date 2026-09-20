<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'pinned_at',
    ];

    protected $casts = [
        'pinned_at' => 'datetime',
    ];

    /**
     * The student who owns this conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Messages belonging to this conversation, in chronological order.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('id');
    }
}
