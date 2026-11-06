<?php

namespace Tests\Feature;

use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\CrewQuizAttempt;
use App\Models\LessonContent;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\CourseTrack;
use App\Services\DefaultFinalExamService;
use App\Services\LessonContentService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Two ways into a course: Self-paced (the default course, the admin looks after it) or Join Class (pick a
 * faculty member, they accept you, you get their version of the course). A faculty member's edits reach
 * only their own class, and joining a class starts progress fresh without losing XP.
 */
class SelfPacedAndClassTest extends TestCase
{
    use RefreshDatabase;

    private function student(string $name = 'Kai'): User
    {
        return User::factory()->create(['role' => 'student', 'name' => $name, 'planets' => ['programming']]);
    }

    /** A faculty member teaching Python, with their class open. */
    private function classOf(string $name): Crew
    {
        $faculty = User::factory()->create(['role' => 'faculty', 'name' => $name]);

        return CourseAssignment::create(['planet' => 'programming', 'course_slug' => 'python', 'faculty_id' => $faculty->id])->openClass();
    }

    private function selfPaced(User $student): void
    {
        $this->actingAs($student)->postJson(route('student.course.self-paced', ['planet' => 'programming', 'course' => 'python']))->assertOk();
    }

    public function test_self_paced_opens_the_course_right_away_with_no_faculty(): void
    {
        $student = $this->student();

        $this->actingAs($student)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertRedirect(route('student.planet', 'programming'));

        $this->selfPaced($student);

        $crew = Crew::forStudent($student, 'programming', 'python');
        $this->assertTrue($crew->isSelfPaced());
        $this->assertNull($crew->teacher_id);
        $this->actingAs($student)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertSee('Self-paced');
    }

    public function test_join_class_lists_every_faculty_member_teaching_the_course(): void
    {
        $this->classOf('Joy Santos');
        $this->classOf('Kent Reyes');

        $this->actingAs($this->student())->get(route('student.planet', 'programming'))
            ->assertOk()->assertSee('Who is your instructor?')->assertSeeInOrder(['Joy Santos', 'Kent Reyes']);
    }

    public function test_asking_a_second_faculty_member_withdraws_the_first_request(): void
    {
        $joy = $this->classOf('Joy');
        $kent = $this->classOf('Kent');
        $student = $this->student();

        $this->actingAs($student)->postJson(route('student.join-request.store', $joy))->assertOk();
        $this->actingAs($student)->postJson(route('student.join-request.store', $kent))->assertOk();

        $this->assertDatabaseMissing('course_join_requests', ['crew_id' => $joy->id, 'user_id' => $student->id]);
        $this->assertDatabaseHas('course_join_requests', ['crew_id' => $kent->id, 'user_id' => $student->id, 'status' => 'pending']);
    }

    public function test_the_self_paced_crew_takes_no_join_requests(): void
    {
        $this->actingAs($this->student())->postJson(route('student.join-request.store', Crew::selfPaced('programming', 'python')))->assertNotFound();
    }

    public function test_joining_a_class_starts_fresh_and_the_class_track_is_final(): void
    {
        $student = $this->student();
        // Lessons done before the course had tracks (no crew yet) still count for XP.
        CourseProgressService::markComplete($student, 'm1', 'lesson01', 'programming');
        CourseProgressService::markComplete($student, 'm1', 'lesson02', 'programming');
        $xp = StudentDashboardService::earnedXp($student);

        $class = $this->classOf('Joy');
        $this->actingAs($student)->postJson(route('student.join-request.store', $class))->assertOk();
        $request = $class->joinRequests()->where('user_id', $student->id)->firstOrFail();
        $this->actingAs($class->faculty)->post(route('faculty.join-requests.accept', $request));
        CourseTrack::forget();

        // In the class now: progress starts at 0, but no XP is lost.
        $this->assertSame($class->id, Crew::forStudent($student, 'programming', 'python')->id);
        $this->assertSame([], CourseProgressService::completedKeys($student, 'programming'));
        $this->assertSame($xp, StudentDashboardService::earnedXp($student));

        // Redoing a lesson in the class doesn't earn its XP twice.
        CourseProgressService::markComplete($student, 'm1', 'lesson01', 'programming');
        $this->assertSame($xp, StudentDashboardService::earnedXp($student));

        // A track, once picked, is final: no going back to Self-paced, and no asking another class.
        $this->actingAs($student)->postJson(route('student.course.self-paced', ['planet' => 'programming', 'course' => 'python']))
            ->assertStatus(422)->assertJsonFragment(['message' => "You're in Joy's class for this course. Your track can't be changed."]);
        $this->actingAs($student)->postJson(route('student.join-request.store', $this->classOf('Kent')))->assertStatus(422);
        CourseTrack::forget();
        $this->assertSame($class->id, Crew::forStudent($student, 'programming', 'python')->id);
        $this->actingAs($student)->get(route('student.course.overview', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertDontSee('Change how you learn this course');
    }

    public function test_a_self_paced_student_cannot_join_a_class_and_a_waiting_request_is_withdrawn(): void
    {
        $student = $this->student();
        $class = $this->classOf('Joy');
        $this->actingAs($student)->postJson(route('student.join-request.store', $class))->assertOk();

        // Picking Self-paced while the request waits withdraws it, so Joy can't pull them over later.
        $this->selfPaced($student);
        $this->assertDatabaseMissing('course_join_requests', ['crew_id' => $class->id, 'user_id' => $student->id]);

        $this->actingAs($student)->postJson(route('student.join-request.store', $class))
            ->assertStatus(422)->assertJsonFragment(['message' => "You're learning this course Self-paced. Your track can't be changed."]);
        CourseTrack::forget();
        $this->assertTrue(Crew::forStudent($student, 'programming', 'python')->isSelfPaced());
    }

    public function test_a_faculty_edit_reaches_only_their_own_class(): void
    {
        $joy = $this->classOf('Joy');
        $kent = $this->classOf('Kent');
        LessonContent::create([
            'crew_id' => $joy->id, 'course' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01',
            'title' => "Joy's version", 'objective' => 'x', 'sections' => [['type' => 'custom', 'heading' => 'Only in Joy class', 'html' => '<p>Hi</p>']],
        ]);

        $inJoy = $this->student('A');
        $joy->admit($inJoy);
        $inKent = $this->student('B');
        $kent->admit($inKent);
        $solo = $this->student('C');
        Crew::selfPaced('programming', 'python')->admit($solo);

        $page = function (User $u) {
            $this->actingAs($u);

            return (string) LessonContentService::raw('programming', 'm1', 'lesson01');
        };

        $this->assertStringContainsString('Only in Joy class', $page($inJoy));
        $this->assertStringContainsString('Only in Joy class', $page($joy->faculty));
        $this->assertStringNotContainsString('Only in Joy class', $page($inKent));
        $this->assertStringNotContainsString('Only in Joy class', $page($solo));
        $this->assertStringNotContainsString('Only in Joy class', $page($kent->faculty));
    }

    public function test_a_faculty_members_quiz_edit_is_saved_onto_their_class(): void
    {
        $joy = $this->classOf('Joy');
        $questions = [['prompt' => 'Joy asks?', 'options' => ['Yes', 'No'], 'correct' => 'A', 'explanation' => '']];

        $this->actingAs($joy->faculty)->putJson(route('faculty.lesson-quizzes.update', ['crew' => $joy, 'module' => 'm1', 'lesson' => 'lesson01']), ['questions' => $questions])
            ->assertOk();

        $this->assertDatabaseHas('lesson_quizzes', ['crew_id' => $joy->id, 'course' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01']);
    }

    public function test_self_paced_gets_a_default_final_exam_built_from_the_lessons(): void
    {
        $exam = DefaultFinalExamService::ensure(Crew::selfPaced('programming', 'python'));

        $this->assertNotNull($exam);
        $this->assertTrue($exam->is_final && $exam->isPublished());
        $this->assertGreaterThan(0, $exam->questions()->count());
        $this->assertLessThanOrEqual(DefaultFinalExamService::MAX_QUESTIONS, $exam->questions()->count());
        $this->assertNull(DefaultFinalExamService::ensure($this->classOf('Joy')));
    }

    public function test_the_certificate_is_signed_by_the_faculty_member_or_issued_by_techlab(): void
    {
        $this->certificateAsHtml();
        foreach (['class' => $this->classOf('Joy Santos'), 'self' => Crew::selfPaced('programming', 'python')] as $kind => $crew) {
            $student = $this->student();
            $crew->admit($student);
            foreach (CourseProgressService::order('programming') as $item) {
                CourseProgressService::markComplete($student, $item['module'], $item['lesson'], 'programming');
            }
            $exam = $crew->quizzes()->create(['title' => 'Final', 'published_at' => now(), 'is_final' => true, 'pass_percent' => 50]);
            CrewQuizAttempt::create([
                'crew_quiz_id' => $exam->id, 'user_id' => $student->id, 'score' => 2, 'total' => 2, 'answers' => [],
                'status' => CrewQuizAttempt::PASSED, 'started_at' => now(), 'finished_at' => now(),
            ]);

            $page = $this->actingAs($student)->get(route('student.course.certificate', ['slug' => 'programming', 'course' => 'python']))->assertOk();
            $page->assertHeader('Content-Type', 'application/pdf');
            $kind === 'class'
                ? $page->assertSee('Joy Santos')->assertSee('Instructor, TechLab')->assertSee('Class Learning')->assertDontSee('Self-paced Learning Program')
                : $page->assertSee('Self-paced Learning Program')->assertDontSee('Instructor, TechLab');
            CourseTrack::forget();
        }
    }

    public function test_admins_see_self_paced_learners_and_can_lift_a_ban(): void
    {
        $student = $this->student('Mia');
        $crew = Crew::selfPaced('programming', 'python');
        $crew->admit($student);
        $exam = $crew->quizzes()->create(['title' => 'Final', 'published_at' => now(), 'is_final' => true]);
        $attempt = CrewQuizAttempt::create([
            'crew_quiz_id' => $exam->id, 'user_id' => $student->id, 'score' => 0, 'total' => 2, 'answers' => [],
            'status' => CrewQuizAttempt::FAILED, 'violation' => 'left_page', 'strikes' => 2, 'started_at' => now(), 'finished_at' => now(),
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.self-paced'))->assertOk()->assertSee('Mia')->assertSee('Lift ban')
            ->assertSee('Computer Programming 2')->assertSee('Networking 2')->assertSee('Information Assurance and Security 2');
        $this->actingAs(User::factory()->create(['role' => 'faculty']))->post(route('faculty.final-exam.retake', $attempt))->assertForbidden();
        $this->actingAs($admin)->post(route('faculty.final-exam.retake', $attempt))->assertRedirect(route('admin.self-paced'));
        $this->assertModelMissing($attempt);
    }

    public function test_the_crew_leaderboard_lists_only_that_crews_students_ranked_by_level(): void
    {
        $joy = $this->classOf('Joy');
        $ana = $this->student('Ana');
        $ben = $this->student('Ben');
        $solo = $this->student('Solo');
        foreach ([$ana, $ben] as $s) {
            $this->actingAs($s)->postJson(route('student.join-request.store', $joy))->assertOk();
            $this->actingAs($joy->faculty)->post(route('faculty.join-requests.accept', $joy->joinRequests()->where('user_id', $s->id)->first()));
        }
        $this->selfPaced($solo);
        CourseTrack::forget();

        // Ben did a lesson before joining (no crew): his level counts it, like every lesson he ever finished.
        LessonProgress::withoutGlobalScope('active')->create(['user_id' => $ben->id, 'course' => 'programming', 'module' => 'm1', 'lesson' => 'lesson03', 'crew_id' => null, 'completed_at' => now()]);
        CourseProgressService::markComplete($ana, 'm1', 'lesson01', 'programming');
        CourseProgressService::markComplete($ana, 'm1', 'lesson02', 'programming');
        CourseProgressService::markComplete($ben, 'm1', 'lesson01', 'programming');
        CourseProgressService::markComplete($solo, 'm1', 'lesson01', 'programming');

        $board = StudentDashboardService::crewLeaderboard($joy, $ana);
        $this->assertSame(['Ana', 'Ben'], array_column($board, 'name'));
        $this->assertSame([200, 200], array_column($board, 'xp'));
        $this->assertSame([4, 4], array_column($board, 'level'));
        $this->assertSame([1, 1], array_column($board, 'rank'));
        $this->assertTrue($board[0]['me']);

        $this->actingAs($ana)->get(route('student.course.crew', ['slug' => 'programming', 'course' => 'python']))
            ->assertOk()->assertSee('Crew leaderboard')->assertSeeInOrder(['Ana', 'Ben'])->assertDontSee('Solo');

        // The home leaderboard is the whole planet: both tracks, Solo too.
        $planet = StudentDashboardService::leaderboard($ana, 'programming');
        $this->assertEqualsCanonicalizing(['Ana', 'Ben', 'Solo'], array_column($planet, 'name'));
    }
}
