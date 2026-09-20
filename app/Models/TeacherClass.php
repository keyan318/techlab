<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One weekly slot in a teacher's schedule (day is ISO: 1 = Monday … 7 = Sunday), repeating from starts_on onward. */
class TeacherClass extends Model
{
    protected $fillable = ['user_id', 'subject', 'class_name', 'day', 'starts_at', 'ends_at', 'room', 'starts_on'];

    protected $casts = ['day' => 'integer', 'starts_on' => 'date:Y-m-d'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The shape the dashboard card (and its JSON responses) use. */
    public function toCard(): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'class' => $this->class_name,
            'day' => $this->day,
            'start' => $this->starts_at,
            'end' => $this->ends_at,
            'room' => $this->room,
            'from' => $this->starts_on?->toDateString() ?? $this->created_at?->toDateString(),
        ];
    }
}
