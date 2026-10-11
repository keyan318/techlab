<?php

namespace Tests\Feature;

use App\Services\CourseProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Each course card on a planet page shows that course's own progress, not the planet's.
 * A planet with two courses (IAS 1 + IAS 2) used to hand both cards the planet total, so a
 * finished IAS 1 (24 of the planet's 64 lessons) read "38% · Continue".
 */
class CourseCardProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_finished_course_shows_100_percent_even_when_its_planet_has_another_course(): void
    {
        $user = $this->studentIn('cybersecurity', 'information-security-1', ['role' => 'student', 'planets' => ['cybersecurity']]);

        foreach (CourseProgressService::order('cybersecurity') as $item) {
            CourseProgressService::markComplete($user, $item['module'], $item['lesson'], 'cybersecurity');
        }

        $this->assertSame(100, CourseProgressService::percent($user, 'cybersecurity'));
        $this->assertSame(0, CourseProgressService::percent($user, 'information-assurance-security-2'));

        $this->actingAs($user)->get(route('student.planet', 'cybersecurity'))
            ->assertOk()
            ->assertSee('Information Assurance and Security 1 course, 100% complete')
            ->assertSee('Review')
            ->assertDontSee('38%');
    }

    public function test_percent_ignores_completions_from_other_courses_and_unknown_lessons(): void
    {
        $user = $this->studentIn('cybersecurity', 'information-security-1', ['role' => 'student', 'planets' => ['cybersecurity']]);
        $first = CourseProgressService::order('cybersecurity')[0];

        CourseProgressService::markComplete($user, $first['module'], $first['lesson'], 'cybersecurity');
        CourseProgressService::markComplete($user, $first['module'], $first['lesson'], 'information-assurance-security-2');

        // 1 of IAS 1's 24 lessons = 4%; IAS 2's completion doesn't count toward it.
        $this->assertSame(4, CourseProgressService::percent($user, 'cybersecurity'));
        $this->assertSame(0, CourseProgressService::percent($user, 'no-such-course'));
    }
}
