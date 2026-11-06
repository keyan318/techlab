<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;

/**
 * A course a faculty member built from scratch (as opposed to one baked into
 * config/course-structure.php + config/course-catalog.php). Its slug is used everywhere else a
 * config course's catalog id is used — CourseProgressService falls back to this table when a key
 * isn't in config, so the rest of the app (outline editor, lesson editor, labs, quizzes, mini
 * bosses, the final exam, certificates) treats it exactly like a config course.
 */
class Course extends Model
{
    public const DRAFT = 'draft';

    public const PUBLISHED = 'published';

    protected $fillable = ['planet', 'slug', 'title', 'blurb', 'level', 'art_path', 'lab_kind', 'status', 'created_by'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPublished(): bool
    {
        return $this->status === self::PUBLISHED;
    }

    /** The lab engine a faculty-created course declared, or null if the slug isn't one. */
    public static function labKindFor(string $slug): ?string
    {
        try {
            return self::where('slug', $slug)->value('lab_kind');
        } catch (QueryException) {
            return null;   // table not migrated yet
        }
    }
}
