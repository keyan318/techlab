<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'first_name', 'last_name', 'email', 'password', 'role', 'crew_id', 'planets', 'avatar', 'codename', 'program', 'year_level'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const PLANETS = ['programming', 'networking', 'cybersecurity'];

    /** CICT programs a student picks at sign-up (and in Settings); the admin dashboard counts students by them. */
    public const PROGRAMS = [
        'IT' => 'Information Technology',
        'IS' => 'Information Systems',
        'CS' => 'Computer Science',
        'BLIS' => 'Library and Information Science',
        'EMC' => 'Entertainment and Multimedia Computing',
    ];

    public const YEAR_LEVELS = [1 => '1st year', 2 => '2nd year', 3 => '3rd year', 4 => '4th year'];

    /** The full name the app shows everywhere, from the separate first and last names. */
    public static function joinName(string $first, string $last): string
    {
        return trim(preg_replace('/\s+/', ' ', trim($first).' '.trim($last)));
    }

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
     * Each use of Astro's AI (chat, Studio tools) and the XP it cost.
     */
    public function aiUsages(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }

    /**
     * The first admin ever made (lowest id): the only admin who can add or remove other admins.
     */
    public function isOwnerAdmin(): bool
    {
        return $this->role === 'admin'
            && (int) $this->id === (int) self::where('role', 'admin')->min('id');
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

    /**
     * What classmates see: the student's codename, or their full name until they pick one.
     */
    protected function displayName(): Attribute
    {
        return Attribute::get(fn () => $this->codename ?: $this->name);
    }

    /** Agreed to the current Terms (config/terms.php)? Until then EnsureTermsAccepted keeps them on /terms. */
    public function hasAcceptedTerms(): bool
    {
        return $this->terms_accepted_at !== null && $this->terms_version === config('terms.version');
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
            'avatar' => 'array',
            'tour_seen' => 'array',
            'streak_seen_on' => 'date',
            'first_login_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'rank_seen' => 'integer',
            'year_level' => 'integer',
        ];
    }
}
