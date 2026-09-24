<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'crew_id', 'planets'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const PLANETS = ['programming', 'networking', 'cybersecurity'];

    /**
     * Conversations this user has had with Astro.
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Lessons this user has completed (server-side progression state).
     */
    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Lesson-quiz answers (first attempt per question; correct ones carry XP).
     */
    public function quizAnswers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * XP this student has spent (e.g. on Astro's hints).
     */
    public function xpSpends(): HasMany
    {
        return $this->hasMany(XpSpend::class);
    }

    /**
     * Planets this user enrolled in. Accounts from before enrollment (null) and
     * faculty get every planet.
     *
     * @return array<int, string>
     */
    public function enrolledPlanets(): array
    {
        if ($this->planets === null || $this->role === 'faculty') {
            return self::PLANETS;
        }

        return array_values(array_intersect(self::PLANETS, $this->planets));
    }

    public function isEnrolledIn(string $planet): bool
    {
        return in_array($planet, $this->enrolledPlanets(), true);
    }

    /**
     * Every crew this user is on the roster of (faculty or student), unlike `crew_id`
     * which only ever points at one — a student can now hold a code-gated seat in
     * several courses' crews at once.
     */
    public function crewMemberships(): BelongsToMany
    {
        return $this->belongsToMany(Crew::class, 'crew_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function belongsToCrew(int $crewId): bool
    {
        return $this->crewMemberships()->where('crews.id', $crewId)->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'planets' => 'array',
        ];
    }
}
