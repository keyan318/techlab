<?php

namespace Tests\Feature;

use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacultyCrewRosterTest extends TestCase
{
    use RefreshDatabase;

    private function setUpCrew(): array
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $crew = Crew::create(['name' => 'Alpha', 'code' => 'ABC123', 'teacher_id' => $faculty->id, 'planet' => 'programming', 'course_slug' => 'python']);
        $faculty->update(['crew_id' => $crew->id]);
        $crew->roster()->attach($faculty->id, ['role' => 'faculty']);
        $student = User::factory()->create(['role' => 'student', 'crew_id' => $crew->id, 'planets' => ['programming']]);
        $crew->roster()->attach($student->id, ['role' => 'student']);

        return [$faculty, $student, $crew];
    }

    public function test_roster_page_shows_progress_xp_and_gems_for_students_only(): void
    {
        [$faculty, $student, $crew] = $this->setUpCrew();

        LessonProgress::create([
            'user_id' => $student->id,
            'course' => 'programming',
            'module' => 'm1',
            'lesson' => 'l1',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($faculty)->get(route('faculty.course.roster', $crew));

        $response->assertOk()
            ->assertSee('Crew · TechLab', false)
            ->assertSee($student->name)
            ->assertSee('100 XP')
            ->assertSee('Gem');
    }

    public function test_crew_shortcut_redirects_to_active_crew_roster(): void
    {
        [$faculty, , $crew] = $this->setUpCrew();
        CourseAssignment::create(['planet' => 'programming', 'course_slug' => 'python', 'faculty_id' => $faculty->id]);

        $this->actingAs($faculty)->get(route('faculty.crew'))
            ->assertRedirect(route('faculty.course.roster', $crew));
    }

    public function test_materials_shortcut_redirects_to_active_crew_course(): void
    {
        [$faculty, , $crew] = $this->setUpCrew();
        CourseAssignment::create(['planet' => 'programming', 'course_slug' => 'python', 'faculty_id' => $faculty->id]);

        $this->actingAs($faculty)->get(route('faculty.materials'))
            ->assertRedirect(route('faculty.course', $crew));
    }

    public function test_crew_shortcut_redirects_to_dashboard_when_no_crew_yet(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);

        $this->actingAs($faculty)->get(route('faculty.crew'))
            ->assertRedirect(route('faculty.dashboard'));
    }
}
