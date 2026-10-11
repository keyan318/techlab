<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudioOutput extends Model
{
    protected $fillable = ['user_id', 'uid', 'type', 'payload'];
}
