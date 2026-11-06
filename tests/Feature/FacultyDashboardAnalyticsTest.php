<?php

namespace Tests\Feature;

use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\StudyActivity;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\FacultyDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The faculty dashboard's charts are drawn in the browser from per-day series the server sends,
 * so these tests pin down that data: who is on the roster, which day each lesson landed on,
 * active minutes, and each student's final exam state.
 */
class FacultyDashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function course(User $faculty): Crew
    {
        CourseAssignment::create(['planet' => 'networking', 'course_slug' => 'networking-fundamentals', 'faculty_id' => $faculty->id]);
        $crew = Crew::create(['name' => 'Networking 1', 'code' => 'NET001', 'teacher_id' => $faculty->id, 'planet' => 'networking', 'course_slug' => 'networking-fundamentals']);
        $crew->roster()->attach($faculty->id, ['role' => 'faculty']);

        return $crew;
    }

    public function test_the_dashboard_sends_each_students_daily_lessons_minutes_and_final_state(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $crew = $this->course($faculty);
        [$ana, $ben] = [User::factory()->create(['name' => 'Ana']), User::factory()->create(['name' => 'Ben'])];
        $crew->roster()->attach([$ana->id => ['role' => 'student'], $ben->id => ['role' => 'student']]);

        $lessons = CourseProgressService::order('networking');
        foreach (array_slice($lessons, 0, 2) as $l) {
            LessonProgress::create(['user_id' => $ana->id, 'course' => 'networking', 'module' => $l['module'], 'lesson' => $l['lesson'], 'completed_at' => now()->subDays(2)]);
        }
        // Another course's lessons never count toward this one.
        LessonProgress::create(['user_id' => $ana->id, 'course' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01', 'completed_at' => now()]);
        StudyActivity::create(['user_id' => $ben->id, 'date' => now()->toDateString(), 'minutes' => 25]);

        $final = CrewQuiz::create(['crew_id' => $crew->id, 'title' => 'Final', 'is_final' => true, 'published_at' => now()]);
        CrewQuizAttempt::create(['crew_quiz_id' => $final->id, 'user_id' => $ben->id, 'score' => 9, 'total' => 10, 'answers' => [], 'status' => CrewQuizAttempt::PASSED, 'finished_at' => now()]);

        $data = FacultyDashboardService::build(collect([$crew]));
        $last = FacultyDashboardService::DAYS - 1;

        $this->assertCount(FacultyDashboardService::DAYS, $data['days']);
        $this->assertSame(now()->toDateString(), $data['days'][$last]['date']);
        $this->assertSame([['id' => $crew->id, 'name' => 'Networking 1', 'lessons' => count($lessons), 'students' => 2]], $data['courses']);

        [$a, $b] = $data['students'];
        $this->assertSame('Ana', $a['name']);
        $this->assertSame(2, $a['done']);
        $this->assertSame(2, $a['lessons'][$last - 2]);
        $this->assertSame(2, array_sum($a['lessons']));
        $this->assertSame((int) round(2 / count($lessons) * 100), $a['percent']);
        $this->assertSame(now()->subDays(2)->toDateString(), $a['lastSeen']);
        $this->assertSame('studying', $a['final']);

        $this->assertSame(0, $b['done']);
        $this->assertSame('passed', $b['final']);
        $this->assertSame(now()->toDateString(), $b['lastSeen']);
        $this->assertSame(25, $data['minutes'][$ben->id][$last]);
        $this->assertArrayNotHasKey($ana->id, $data['minutes']);
    }

    public function test_the_dashboard_page_renders_the_charts_with_the_courses_students(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $crew = $this->course($faculty);
        $crew->roster()->attach(User::factory()->create(['name' => 'Cara Student'])->id, ['role' => 'student']);

        $this->actingAs($faculty)->get(route('faculty.dashboard'))
            ->assertOk()
            ->assertSee('Course progress')
            ->assertSee('Final exam')
            ->assertSee('Cara Student')
            ->assertSee('1 joined');
    }

    public function test_another_faculty_members_students_stay_off_the_dashboard(): void
    {
        $faculty = User::factory()->create(['role' => 'faculty']);
        $crew = $this->course(User::factory()->create(['role' => 'faculty']));
        $crew->roster()->attach(User::factory()->create(['name' => 'Someone Elses Student'])->id, ['role' => 'student']);

        $this->actingAs($faculty)->get(route('faculty.dashboard'))
            ->assertOk()
            ->assertDontSee('Someone Elses Student');
    }
}
