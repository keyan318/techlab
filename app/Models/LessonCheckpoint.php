<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A student's progress on one in-lesson checkpoint: wrong tries so far, and when it was passed. */
class LessonCheckpoint extends Model
{
    protected $fillable = ['user_id', 'course', 'module', 'lesson', 'checkpoint', 'misses', 'last_miss_at', 'passed_at'];

    protected function casts(): array
    {
        return ['checkpoint' => 'integer', 'misses' => 'integer', 'last_miss_at' => 'datetime', 'passed_at' => 'datetime'];
    }
}
