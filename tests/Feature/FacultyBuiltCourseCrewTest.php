<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Crew;
use App\Models\CrewQuizAttempt;
use App\Models\User;
use App\Services\CourseStructure;
use App\Services\CourseTrack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** A course a faculty member built from scratch: its crews, who watches them, and what its students see. */
class FacultyBuiltCourseCrewTest extends TestCase
{
    use RefreshDatabase;

    private function faculty(): User
    {
        return User::factory()->create(['role' => 'faculty']);
    }

    /** "DSA", built by $faculty, with one module of one lesson. */
    private function build(User $faculty): Course
    {
        $this->actingAs($faculty)->post(route('faculty.courses.store'), ['planet' => 'programming', 'title' => 'DSA'])->assertRedirect();
        $this->actingAs($faculty)->putJson(route('faculty.courses.outline.update', 'dsa'), ['outline' => [
            ['key' => null, 'title' => 'Arrays', 'hidden' => false, 'lessons' => [['key' => null, 'title' => 'What is an array?', 'hidden' => false]]],
        ]])->assertOk();

        return Course::where('slug', 'dsa')->firstOrFail();
    }

    public function test_its_crews_are_named_after_the_course_title_not_its_slug(): void
    {
        $this->build($this->faculty());

        $this->assertSame(['DSA', 'DSA · Self-paced'], Crew::forCourse('programming', 'dsa')->orderBy('type')->pluck('name')->all());
    }

    public function test_the_creator_watches_its_self_paced_students_and_nobody_else_does(): void
    {
        $faculty = $this->faculty();
        $this->build($faculty);
        $selfPaced = Crew::selfPaced('programming', 'dsa');
        $student = User::factory()->create(['role' => 'student']);
        $selfPaced->admit($student);

        $this->actingAs($faculty)->get(route('faculty.course.roster', $selfPaced))->assertOk()
            ->assertSee($student->name)->assertSee('Self-paced')->assertDontSee('aria-label="Course sections"', false);
        $this->actingAs($this->faculty())->get(route('faculty.course.roster', $selfPaced))->assertForbidden();

        // A config course's self-paced run stays the admin's.
        $this->actingAs($faculty)->get(route('faculty.course.roster', Crew::selfPaced('programming', 'computer-programming-1')))->assertForbidden();

        $this->assertContains($student->name, collect($this->actingAs($faculty)->get(route('faculty.dashboard'))->assertOk()
            ->viewData('analytics')['students'])->pluck('name')->all());
    }

    public function test_a_self_paced_student_sees_the_course_the_creator_built(): void
    {
        $this->build($this->faculty());
        $student = User::factory()->create(['role' => 'student']);
        Crew::selfPaced('programming', 'dsa')->admit($student);

        $this->actingAs($student);
        CourseTrack::forget();

        $this->assertSame(['Arrays'], collect(CourseStructure::modules('dsa'))->pluck('title')->all());
    }

    public function test_the_crew_page_groups_every_crew_under_its_planet(): void
    {
        $faculty = $this->faculty();
        $this->build($faculty);

        $this->actingAs($faculty)->get(route('faculty.crew'))->assertOk()
            ->assertSeeInOrder(['Programming City', 'DSA', 'Class', 'Self-paced'])
            // The sidebar lists them too.
            ->assertSee('id="tl-crew-list"', false);
    }

    public function test_self_paced_is_blocked_until_module_one_of_the_base_course_is_passed(): void
    {
        $faculty = $this->faculty();
        $this->build($faculty);
        $this->actingAs($faculty)->post(route('faculty.courses.publish', 'dsa'))->assertRedirect();
        $student = User::factory()->create(['role' => 'student', 'planets' => ['programming']]);

        $this->actingAs($student)->postJson(route('student.course.self-paced', ['planet' => 'programming', 'course' => 'dsa']))
            ->assertStatus(422)->assertJsonPath('requires', 'computer-programming-1');
        $this->assertNull(Crew::forStudent($student, 'programming', 'dsa'));

        $quiz = $this->crewFor('programming', 'computer-programming-1')->quizzes()->create(['title' => 'Module 1', 'published_at' => now(), 'module' => 'm1', 'pass_percent' => 50]);
        CrewQuizAttempt::create([
            'crew_quiz_id' => $quiz->id, 'user_id' => $student->id, 'score' => 2, 'total' => 2, 'answers' => [],
            'status' => CrewQuizAttempt::PASSED, 'started_at' => now(), 'finished_at' => now(),
        ]);

        $this->actingAs($student)->postJson(route('student.course.self-paced', ['planet' => 'programming', 'course' => 'dsa']))
            ->assertOk()->assertJsonPath('redirect', route('student.course.overview', ['slug' => 'programming', 'course' => 'dsa']));
        $this->assertTrue(Crew::forStudent($student, 'programming', 'dsa')->isSelfPaced());
    }

    public function test_publishing_needs_a_lesson_and_says_so_instead_of_failing(): void
    {
        $faculty = $this->faculty();
        $this->actingAs($faculty)->post(route('faculty.courses.store'), ['planet' => 'programming', 'title' => 'Empty']);

        $this->actingAs($faculty)->post(route('faculty.courses.publish', 'empty'))
            ->assertRedirect(route('faculty.courses.index'))->assertSessionHas('status');
        $this->assertSame(Course::DRAFT, Course::where('slug', 'empty')->value('status'));

        $this->build($faculty);
        $this->actingAs($faculty)->get(route('faculty.courses.index'))->assertOk()->assertSee('Only you can see this course.');
        $this->actingAs($faculty)->post(route('faculty.courses.publish', 'dsa'))->assertRedirect();
        $this->assertSame(Course::PUBLISHED, Course::where('slug', 'dsa')->value('status'));
    }
}
