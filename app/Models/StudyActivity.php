<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyActivity extends Model
{
    protected $table = 'study_activity';

    protected $fillable = ['user_id', 'date', 'minutes', 'last_ping_at'];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'last_ping_at' => 'datetime',
        ];
    }
}
