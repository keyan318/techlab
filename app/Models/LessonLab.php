<?php

namespace App\Models;

use App\Services\LessonLabService;
use Illuminate\Database\Eloquent\Model;

/**
 * A faculty member's version of a lesson's coding lab: its instructions, starter code, the output the student's
 * program must print, and the hint. It replaces the lab in the lesson file for everyone in the course; deleting
 * it brings the file's lab back.
 */
class LessonLab extends Model
{
    protected $fillable = ['crew_id', 'course', 'module', 'lesson', 'title', 'difficulty', 'instructions', 'starter_code', 'expected_output', 'hint_body', 'hint_code', 'updated_by'];

    protected static function booted(): void
    {
        static::saved(fn (self $l) => LessonLabService::forget($l->course));
        static::deleted(fn (self $l) => LessonLabService::forget($l->course));
    }
}
