<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The course hub: the page a course card now opens, with its Home / Materials / Quiz / Crew tabs.
 */
class CourseHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_course_card_opens_the_hub_not_the_lesson_player(): void
    {
        $student = $this->studentIn('programming', 'python');

        $this->actingAs($student)->get(route('student.planet', 'programming'))
            ->assertOk()
            ->assertSee(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']), false)
            ->assertDontSee(route('student.planet.play', ['slug' => 'programming', 'course' => 'python']), false);
    }

    public function test_the_hub_has_home_materials_and_crew_tabs_and_the_lessons_are_one_click_further_in(): void
    {
        $student = $this->studentIn('programming', 'python');

        $this->actingAs($student)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()
            ->assertSee('Home')
            ->assertSee('Supplementary Materials')
            ->assertSee('Crew')
            ->assertSee(route('student.course.materials', ['slug' => 'programming', 'course' => 'python']), false)
            ->assertDontSee(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']), false)
            ->assertSee(route('student.course.crew', ['slug' => 'programming', 'course' => 'python']), false)
            // The hub still leads into the lessons, it just doesn't skip straight there.
            ->assertSee(route('student.planet.module.lesson', ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01']), false);
    }

    public function test_a_self_paced_course_has_no_supplementary_materials_tab(): void
    {
        $student = User::factory()->create(['role' => 'student', 'planets' => ['programming']]);
        Crew::selfPaced('programming', 'python')->admit($student);
        $args = ['slug' => 'programming', 'course' => 'python'];

        $this->actingAs($student)->get(route('student.course.overview', $args))
            ->assertOk()
            ->assertDontSee('Supplementary Materials')
            ->assertDontSee(route('student.course.materials', $args), false);
        $this->actingAs($student)->get(route('student.course.materials', $args))
            ->assertRedirect(route('student.course.overview', $args));
    }

    public function test_the_crew_tab_names_the_captain_and_the_roster(): void
    {
        $student = $this->studentIn('programming', 'python', ['name' => 'Rosa Mendez']);
        $crew = $this->crewFor('programming', 'python');
        $crew->faculty->update(['name' => 'Joy Santos']);
        $classmate = User::factory()->create(['role' => 'student', 'name' => 'Ben Cruz']);
        $crew->roster()->attach($classmate->id, ['role' => 'student']);

        $this->actingAs($student)->get(route('student.course.crew', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()
            ->assertSee('Joy Santos')
            ->assertSee('Rosa Mendez')
            ->assertSee('Ben Cruz')
            // The captain is not counted as a crew member.
            ->assertSee('2 members');
    }

    public function test_my_courses_lists_every_course_a_student_is_in(): void
    {
        $student = $this->studentIn('programming', 'python');
        $this->enroll($student, 'networking', 'networking-fundamentals');
        $student->update(['planets' => ['programming', 'networking']]);

        $this->actingAs($student)->get(route('student.courses'))
            ->assertOk()
            ->assertSee('Python')
            ->assertSee('Networking')
            ->assertSee(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']), false)
            ->assertSee(route('student.course.overview', ['slug' => 'networking', 'course' => 'networking-fundamentals']), false);
    }

    public function test_a_student_who_has_joined_nothing_is_pointed_at_the_planets(): void
    {
        $student = User::factory()->create(['role' => 'student', 'planets' => ['programming']]);

        $this->actingAs($student)->get(route('student.courses'))
            ->assertOk()
            ->assertSee("You haven't joined a course yet", false)
            ->assertSee(route('student.planets'), false);
    }

    public function test_a_non_member_cannot_open_the_hub(): void
    {
        $outsider = User::factory()->create(['role' => 'student', 'planets' => ['programming']]);
        $this->crewFor('programming', 'python');

        $this->actingAs($outsider)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertRedirect(route('student.planet', 'programming'));
        $this->actingAs($outsider)->get(route('student.course.materials', ['slug' => 'programming', 'course' => 'python']))
            ->assertRedirect(route('student.planet', 'programming'));
        $this->actingAs($outsider)->get(route('student.course.quiz', ['slug' => 'programming', 'course' => 'python']))
            ->assertRedirect(route('student.planet', 'programming'));
    }

    public function test_joining_with_a_code_lands_in_that_courses_hub(): void
    {
        $crew = Crew::create([
            'name' => 'Python', 'code' => 'ZZZ999', 'planet' => 'programming', 'course_slug' => 'python',
            'teacher_id' => User::factory()->create(['role' => 'faculty'])->id,
        ]);
        $student = User::factory()->create(['role' => 'student', 'planets' => ['programming']]);
        $crew->joinRequests()->create(['user_id' => $student->id, 'status' => 'accepted']);

        $this->actingAs($student)->postJson(route('student.crew.join'), ['code' => 'ZZZ999'])
            ->assertOk()
            ->assertJsonPath('redirect', route('student.course.overview', ['slug' => 'programming', 'course' => 'python']));
    }
}
