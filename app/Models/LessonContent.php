<?php

namespace App\Models;

use App\Services\LessonContentService;
use Illuminate\Database\Eloquent\Model;

/**
 * A faculty member's edited lesson page. It replaces the lesson's Blade file for the students of their class (crew_id) only;
 * deleting it brings the file back.
 *
 * `sections` is a list of {type: "custom", heading, html} or {type: "original", index} — the second keeps
 * section N of the Blade file as it is (forms and labs, which hold live routes and answer keys). `checkpoints` maps
 * an id to a checkpoint question ({prompt, options, correct, explanation}); <div data-checkpoint="id"> places it.
 */
class LessonContent extends Model
{
    protected $fillable = ['crew_id', 'course', 'module', 'lesson', 'title', 'objective', 'sections', 'checkpoints', 'updated_by'];

    protected function casts(): array
    {
        return ['sections' => 'array', 'checkpoints' => 'array'];
    }

    protected static function booted(): void
    {
        static::saved(fn (self $c) => LessonContentService::forget($c->course));
        static::deleted(fn (self $c) => LessonContentService::forget($c->course));
    }
}
