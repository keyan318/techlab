<?php

namespace App\Models;

use App\Services\SimLabService;
use Illuminate\Database\Eloquent\Model;

/**
 * A faculty member's version of a lesson's simulator lab (NetSim or Relay Console): the title, each step's
 * instructions, the objectives and the values they check, written over the lab file. `data` is the patch
 * SimLabService::apply() lays on that file. Deleting the row brings the file's lab back.
 */
class LessonSimLab extends Model
{
    protected $fillable = ['crew_id', 'course', 'module', 'lesson', 'data', 'updated_by'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    protected static function booted(): void
    {
        static::saved(fn (self $l) => SimLabService::forget($l->course));
        static::deleted(fn (self $l) => SimLabService::forget($l->course));
    }
}
