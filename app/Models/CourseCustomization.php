<?php

namespace App\Models;

use App\Services\CourseStructure;
use Illuminate\Database\Eloquent\Model;

/**
 * A faculty member's changes to a course: its catalog card and its module/lesson outline.
 * Deleting the row brings the course back to its config defaults.
 */
class CourseCustomization extends Model
{
    protected $fillable = ['crew_id', 'course', 'card', 'outline', 'updated_by'];

    protected function casts(): array
    {
        return ['card' => 'array', 'outline' => 'array'];
    }

    protected static function booted(): void
    {
        static::saved(fn (self $c) => CourseStructure::forget($c->course));
        static::deleted(fn (self $c) => CourseStructure::forget($c->course));
    }
}
