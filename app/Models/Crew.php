<?php

namespace App\Models;

use App\Services\CourseProgressService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * A run of a catalog course. Two kinds:
 *  - `class`: a faculty member's class. They accept who joins, and their lesson edits reach only this roster.
 *  - `self_paced`: one per course, no teacher. Anyone may join (prerequisites apply), it always shows the
 *    default course, and the admin looks after it.
 * A student is on at most one crew per course; that crew is their "track" (see CourseProgressService::switchTrack).
 */
class Crew extends Model
{
    public const CLASS_TYPE = 'class';

    public const SELF_PACED = 'self_paced';

    protected $fillable = ['name', 'code', 'teacher_id', 'planet', 'course_slug', 'type'];

    protected $attributes = ['type' => self::CLASS_TYPE];

    public function isSelfPaced(): bool
    {
        return $this->type === self::SELF_PACED;
    }

    /** Who runs this crew: a class's own faculty member; for a self-paced run, the admins. */
    public function managedBy(?User $user): bool
    {
        return match ($user?->role) {
            'faculty' => ! $this->isSelfPaced() && $this->teacher_id === $user->id,
            'admin' => $this->isSelfPaced(),
            default => false,
        };
    }

    /** @param  Builder<Crew>  $query */
    public function scopeForCourse(Builder $query, string $planet, string $course): void
    {
        $query->where('planet', $planet)->where('course_slug', $course);
    }

    /** @param  Builder<Crew>  $query */
    public function scopeClasses(Builder $query): void
    {
        $query->where('type', self::CLASS_TYPE);
    }

    /**
     * Every crew a faculty member watches over: their own classes, plus the self-paced run of any course
     * they built from scratch (app/Models/Course) — nobody else looks after those students.
     *
     * @param  Builder<Crew>  $query
     */
    public function scopeWatchedBy(Builder $query, User $faculty): void
    {
        $query->where(fn ($q) => $q
            ->where(fn ($c) => $c->where('type', self::CLASS_TYPE)->where('teacher_id', $faculty->id))
            ->orWhere(fn ($s) => $s->where('type', self::SELF_PACED)->whereExists(fn ($e) => $e
                ->from('courses')->whereColumn('courses.planet', 'crews.planet')
                ->whereColumn('courses.slug', 'crews.course_slug')->where('courses.created_by', $faculty->id))));
    }

    /** Whether this faculty member may see this crew's roster and progress (scopeWatchedBy, for one crew). */
    public function isWatchedBy(?User $user): bool
    {
        if (($user?->role ?? null) !== 'faculty') {
            return false;
        }
        if (! $this->isSelfPaced()) {
            return $this->teacher_id === $user->id;
        }

        return Course::where('planet', $this->planet)->where('slug', $this->course_slug)->where('created_by', $user->id)->exists();
    }

    /**
     * The sidebar's Crew list: every crew this faculty member watches, grouped by planet (catalog order).
     * Cached briefly on the local cache, since the sidebar is on every page and the database is remote;
     * forgetNav() clears it when a crew is opened.
     *
     * @return array<string, array{title: string, crews: list<array{id: int, name: string, self_paced: bool}>}>
     */
    public static function navFor(User $faculty): array
    {
        return Cache::remember("crew-nav:{$faculty->id}", now()->addMinutes(5), function () use ($faculty) {
            $order = array_keys(config('course-catalog'));

            return self::watchedBy($faculty)->orderBy('name')->get(['id', 'name', 'planet', 'type'])
                ->groupBy('planet')
                ->sortBy(fn ($crews, $planet) => array_search($planet, $order, true))
                ->map(fn ($crews, $planet) => [
                    'title' => config("course-catalog.{$planet}.title", ucfirst((string) $planet)),
                    'crews' => $crews->map(fn (self $c) => [
                        'id' => $c->id,
                        'name' => Str::before($c->name, ' · Self-paced'),
                        'self_paced' => $c->isSelfPaced(),
                    ])->values()->all(),
                ])->all();
        });
    }

    public static function forgetNav(int $facultyId): void
    {
        Cache::forget("crew-nav:{$facultyId}");
    }

    /** A course's name for a new crew: its catalog title, or a faculty-built course's own title. */
    public static function courseTitle(string $planet, string $course): string
    {
        return config("course-catalog.{$planet}.courses.{$course}.title")
            ?? Course::where('planet', $planet)->where('slug', $course)->value('title')
            ?? $course;
    }

    /** The course's self-paced crew, made the first time anyone needs it. */
    public static function selfPaced(string $planet, string $course): self
    {
        return self::firstOrCreate(
            ['planet' => $planet, 'course_slug' => $course, 'type' => self::SELF_PACED],
            ['name' => self::courseTitle($planet, $course).' · Self-paced'],
        );
    }

    /** The crew a student is on for this course (self-paced or a class), or null. */
    public static function forStudent(User $user, string $planet, string $course): ?self
    {
        return self::forCourse($planet, $course)
            ->whereHas('roster', fn ($q) => $q->whereKey($user->id))->first();
    }

    /** A faculty member's own class for this course, or null until they open it. */
    public static function classOf(User $faculty, string $planet, string $course): ?self
    {
        return self::forCourse($planet, $course)->classes()->where('teacher_id', $faculty->id)->first();
    }

    /**
     * The crew whose version of the course this user looks at: a student's own track, a faculty
     * member's own class, and for anyone else the self-paced (default) run.
     */
    public static function viewFor(?User $user, string $planet, string $course): ?self
    {
        return match ($user?->role ?? 'guest') {
            'student' => self::forStudent($user, $planet, $course),
            'faculty' => self::classOf($user, $planet, $course) ?? self::selfPaced($planet, $course),
            default => self::selfPaced($planet, $course),
        };
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('position')->orderBy('id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(CrewQuiz::class)->orderBy('id');
    }

    // The course's final exam, draft or published: the one quiz that unlocks the certificate.
    public function finalExam(): HasOne
    {
        return $this->hasOne(CrewQuiz::class)->where('is_final', true);
    }

    // One mini boss per module (MiniBossService builds them from the default questions when first needed).
    public function miniBosses(): HasMany
    {
        return $this->hasMany(CrewQuiz::class)->whereNotNull('module');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Students asking to join this course; the faculty member accepts or declines them.
    public function joinRequests(): HasMany
    {
        return $this->hasMany(CourseJoinRequest::class);
    }

    /**
     * Put a student on this crew's roster (and make it their current crew). Safe to call twice.
     * A student has one track per course, so this takes them off any other crew of the same
     * course and swaps their progress over: a class starts fresh, and coming back finds the old progress.
     */
    public function admit(User $student): void
    {
        if ($this->planet && $this->course_slug) {
            $others = self::forCourse($this->planet, $this->course_slug)->whereKeyNot($this->id)->pluck('id');
            if ($others->isNotEmpty()) {
                $student->crewMemberships()->detach($others);
            }
            CourseProgressService::switchTrack($student, CourseProgressService::keyFor($this->planet, $this->course_slug), $this);
        }

        $student->update(['crew_id' => $this->id]);
        $this->roster()->syncWithoutDetaching([$student->id => ['role' => 'student']]);
    }

    // Explicit roster: every crew member (faculty + students) with their role.
    public function roster()
    {
        return $this->belongsToMany(User::class, 'crew_members')
            ->withPivot('role')
            ->withTimestamps();
    }
}
