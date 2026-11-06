<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A student's "review my mistakes with Astro" chat for one lesson quiz (see QuizGateService). */
class QuizReview extends Model
{
    protected $fillable = ['user_id', 'course', 'module', 'lesson', 'conversation_id', 'mistakes_hash'];
}
