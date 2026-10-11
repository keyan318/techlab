<?php

namespace App\Models;

use App\Services\DocumentTextExtractor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;

class ModuleMaterial extends Model
{
    /** Extensions a faculty member may upload, mapped to the badge the student page colours by. */
    public const KINDS = [
        'pdf' => 'pdf', 'ppt' => 'pptx', 'pptx' => 'pptx', 'doc' => 'docx', 'docx' => 'docx',
        'xls' => 'xlsx', 'xlsx' => 'xlsx', 'csv' => 'xlsx', 'zip' => 'zip',
        'png' => 'img', 'jpg' => 'img', 'jpeg' => 'img', 'gif' => 'img', 'webp' => 'img',
        'txt' => 'txt', 'md' => 'txt',
    ];

    /** A link is stored with ext 'link' and a url instead of a file on disk. */
    public const LINK_EXT = 'link';

    protected $fillable = ['course_module_id', 'name', 'ext', 'path', 'url', 'mime', 'size', 'indexed_at', 'index_error'];

    protected function casts(): array
    {
        return ['indexed_at' => 'datetime', 'size' => 'integer'];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(MaterialChunk::class);
    }

    /** CSS/badge kind used by the student view. */
    public function getKindAttribute(): string
    {
        return $this->isLink() ? 'link' : (self::KINDS[$this->ext] ?? 'txt');
    }

    public function isLink(): bool
    {
        return $this->ext === self::LINK_EXT;
    }

    /** True when Astro can read what is inside this material (not an image, ZIP or spreadsheet). */
    public function isReadable(): bool
    {
        return ! $this->isLink() && DocumentTextExtractor::supports($this->ext);
    }

    /** Where a student opens it: the link itself, or our gated download route. */
    public function openUrl(): string
    {
        return $this->isLink() ? (string) $this->url : route('materials.download', $this);
    }

    /** Same-origin URL that renders the file inline (not a forced download), for our own preview overlay. */
    public function previewUrl(): string
    {
        return route('materials.preview', $this);
    }

    /** A short-lived signed URL so a third party (Google Docs Viewer) can fetch the file without our login. */
    public function publicPreviewUrl(): string
    {
        return URL::temporarySignedRoute('materials.preview', now()->addHours(2), ['material' => $this->id]);
    }

    /** Google Docs Viewer renders these office/document kinds; images and archives don't need it. */
    public function canOpenInGoogleDocs(): bool
    {
        return in_array($this->kind, ['pdf', 'docx', 'pptx', 'xlsx', 'txt'], true);
    }

    /** "Open with Google Docs" — a new tab, same as Google Classroom's file preview offers. */
    public function googleDocsUrl(): ?string
    {
        return $this->canOpenInGoogleDocs()
            ? 'https://docs.google.com/viewer?url='.urlencode($this->publicPreviewUrl()).'&embedded=false'
            : null;
    }

    /** The embeddable variant of the same viewer, for our in-page preview overlay's <iframe>. */
    public function googleDocsEmbedUrl(): ?string
    {
        return $this->canOpenInGoogleDocs()
            ? 'https://docs.google.com/viewer?url='.urlencode($this->publicPreviewUrl()).'&embedded=true'
            : null;
    }
}
