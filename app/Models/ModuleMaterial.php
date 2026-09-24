<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleMaterial extends Model
{
    /** Extensions a faculty member may upload, mapped to the badge the student page colours by. */
    public const KINDS = [
        'pdf' => 'pdf', 'ppt' => 'pptx', 'pptx' => 'pptx', 'doc' => 'docx', 'docx' => 'docx',
        'xls' => 'xlsx', 'xlsx' => 'xlsx', 'csv' => 'xlsx', 'zip' => 'zip',
        'png' => 'img', 'jpg' => 'img', 'jpeg' => 'img', 'gif' => 'img', 'webp' => 'img',
        'txt' => 'txt', 'md' => 'txt',
    ];

    protected $fillable = ['course_module_id', 'name', 'ext', 'path', 'mime', 'size'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    /** CSS/badge kind used by the student view. */
    public function getKindAttribute(): string
    {
        return self::KINDS[$this->ext] ?? 'txt';
    }
}
