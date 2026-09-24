<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A student asking to join a code-gated course (a Crew). The course's faculty member
 * accepts or declines it; only an accepted student can redeem the course code, which
 * is emailed to them on acceptance — see CourseJoinRequestController.
 */
class CourseJoinRequest extends Model
{
    public const PENDING = 'pending';

    public const ACCEPTED = 'accepted';

    public const DECLINED = 'declined';

    protected $fillable = ['crew_id', 'user_id', 'status', 'decided_at'];

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }
}
