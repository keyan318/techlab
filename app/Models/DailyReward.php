<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** The XP a student got for logging in on a day (StudentDashboardService::claimDailyReward). One row per day. */
class DailyReward extends Model
{
    protected $fillable = ['user_id', 'date', 'xp'];

    protected function casts(): array
    {
        return ['date' => 'date', 'xp' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
