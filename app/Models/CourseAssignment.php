<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Who's in charge of a catalog course (config/course-catalog.php's planet + course id).
 * Set by an admin. A faculty member only gets a Crew (roster, code, materials, quizzes)
 * once they generate a code for their assignment — see FacultyController::generateCode().
 */
class CourseAssignment extends Model
{
    protected $fillable = ['planet', 'course_slug', 'faculty_id'];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function crew(): ?Crew
    {
        return Crew::where('planet', $this->planet)->where('course_slug', $this->course_slug)->first();
    }
}
