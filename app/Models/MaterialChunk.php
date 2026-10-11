<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One searchable piece of a faculty upload. See CourseMaterialSourceService. */
class MaterialChunk extends Model
{
    protected $fillable = ['module_material_id', 'crew_id', 'position', 'text'];

    public function material(): BelongsTo
    {
        return $this->belongsTo(ModuleMaterial::class, 'module_material_id');
    }
}
