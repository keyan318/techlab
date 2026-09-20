<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrewQuizQuestion extends Model
{
    protected $fillable = ['crew_quiz_id', 'prompt', 'options', 'correct_index', 'position'];

    protected $casts = ['options' => 'array'];
}
