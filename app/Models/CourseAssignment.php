<?php

namespace App\Models;

use App\Services\CourseProgressService;
use App\Services\CourseTrack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * A faculty member teaching a catalog course (config/course-catalog.php's planet + course id).
 * Set by an admin; a course can have several. Each faculty member gets their own class (a Crew:
 * roster, code, materials, quizzes, lesson edits) once they open it — see openClass().
 */
class CourseAssignment extends Model
{
    protected $fillable = ['planet', 'course_slug', 'faculty_id'];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    /** This faculty member's own class for the course, once they've opened it (generated its code). */
    public function crew(): ?Crew
    {
        return Crew::forCourse($this->planet, $this->course_slug)->classes()->where('teacher_id', $this->faculty_id)->first();
    }

    /**
     * This faculty member's class for the course, opened (with a fresh class code) if it isn't yet.
     * Generating the code opens it; so does the first edit, since edits are saved onto the class.
     */
    public function openClass(): Crew
    {
        if ($crew = $this->crew()) {
            return $crew;
        }

        do {
            $code = strtoupper(Str::random(6));
        } while (Crew::where('code', $code)->exists());

        $crew = Crew::create([
            'name' => Crew::courseTitle($this->planet, $this->course_slug),
            'code' => $code,
            'teacher_id' => $this->faculty_id,
            'planet' => $this->planet,
            'course_slug' => $this->course_slug,
            'type' => Crew::CLASS_TYPE,
        ]);
        $crew->roster()->attach($this->faculty_id, ['role' => 'faculty']);
        CourseTrack::forget();
        Crew::forgetNav($this->faculty_id);

        return $crew;
    }

    /** Whether this user is the faculty member assigned to a course (by its course-structure key) — who may edit it. */
    public static function canEdit(?User $user, string $courseKey): bool
    {
        if (! $user || ($user->role ?? 'student') !== 'faculty' || ! CourseProgressService::exists($courseKey)) {
            return false;
        }
        $id = CourseProgressService::catalogFor($courseKey);

        return $id !== null && self::where('planet', CourseProgressService::planetFor($courseKey))
            ->where('course_slug', $id)->where('faculty_id', $user->id)->exists();
    }
}
