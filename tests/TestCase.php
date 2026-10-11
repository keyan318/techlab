<?php

namespace Tests;

use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    /**
     * Python is hidden from students (config/course-catalog.php) but its lessons are still
     * the fullest blueprint, so the suite keeps using it as its example course. Tests about
     * hiding it switch it back off — see HiddenCourseTest.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config(['course-catalog.programming.courses.python.hidden' => false]);
    }

    /**
     * A course only opens once a faculty captain has claimed it and accepted a student
     * — see EnsureCourseMember and PlanetController::courses(). Most Feature tests are
     * about lesson content itself, not the join-request gate, so this is the shortcut
     * every one of them uses to get a student past that gate.
     */
    protected function crewFor(string $planet, string $courseSlug): Crew
    {
        return Crew::firstOrCreate(
            ['planet' => $planet, 'course_slug' => $courseSlug],
            [
                'name' => $courseSlug,
                'code' => strtoupper(Str::random(6)),
                'teacher_id' => User::factory()->create(['role' => 'faculty'])->id,
            ],
        );
    }

    /** Puts an existing user on a course's roster, as if their join request had been accepted and the code redeemed. */
    protected function enroll(User $user, string $planet, string $courseSlug): User
    {
        $crew = $this->crewFor($planet, $courseSlug);

        $user->update(['crew_id' => $crew->id]);
        if (! $crew->roster()->where('user_id', $user->id)->exists()) {
            $crew->roster()->attach($user->id, ['role' => 'student']);
        }

        return $user;
    }

    /** A student already enrolled in a course — the default (programming/python) covers most Feature tests. */
    protected function studentIn(string $planet = 'programming', string $courseSlug = 'python', array $attrs = []): User
    {
        return $this->enroll(User::factory()->create($attrs), $planet, $courseSlug);
    }
}
