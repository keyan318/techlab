<?php

namespace Tests\Feature;

use App\Mail\FinalExamViolation;
use App\Models\CourseAssignment;
use App\Models\Crew;
use App\Models\CrewQuiz;
use App\Models\CrewQuizAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\CourseStructure;
use App\Services\MiniBossService;
use App\Services\StudentDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Mini bosses: a full-screen exam after every module. Self-paced has to beat it to open the next module,
 * and a fail earns no XP and waits before a retry. A class takes it once, moves on either way, and its
 * faculty member can rewrite it and sees every score. Each right answer is worth 10 XP.
 */
class MiniBossTest extends TestCase
{
    use RefreshDatabase;

    private const KEY = 'computer-programming-1';

    private const HUB = ['slug' => 'programming', 'course' => 'computer-programming-1'];

    private function selfPacedStudent(): User
    {
        $student = User::factory()->create(['role' => 'student', 'name' => 'Rosa Mendez', 'planets' => ['programming']]);
        Crew::selfPaced('programming', self::KEY)->admit($student);

        return $student;
    }

    /** @return array{0: User, 1: User, 2: Crew} faculty, student, class */
    private function classStudent(): array
    {
        $faculty = User::factory()->create(['role' => 'faculty', 'name' => 'Prof Reyes']);
        $crew = CourseAssignment::create(['planet' => 'programming', 'course_slug' => self::KEY, 'faculty_id' => $faculty->id])->openClass();
        $student = User::factory()->create(['role' => 'student', 'name' => 'Kai Tan', 'planets' => ['programming']]);
        $crew->admit($student);

        return [$faculty, $student, $crew];
    }

    private function finishModule(User $user, string $module, ?Crew $crew = null): void
    {
        foreach (CourseProgressService::order(self::KEY) as $item) {
            if ($item['module'] === $module) {
                LessonProgress::create(['user_id' => $user->id, 'course' => self::KEY, 'module' => $item['module'], 'lesson' => $item['lesson'], 'completed_at' => now(), 'crew_id' => $crew?->id]);
            }
        }
    }

    /** Answers the first `$right` questions correctly and the rest wrongly. */
    private function answers(CrewQuiz $quiz, int $right): array
    {
        $answers = [];
        foreach ($quiz->questions()->get() as $i => $q) {
            $answers[$q->id] = $i < $right ? $q->correct_index : ($q->correct_index + 1) % count($q->options);
        }

        return $answers;
    }

    private function lessonUrl(string $module, string $lesson = 'lesson01'): string
    {
        return route('student.planet.module.lesson', ['slug' => self::KEY, 'module' => $module, 'lesson' => $lesson]);
    }

    public function test_every_open_course_has_a_mini_boss_of_at_least_30_fair_questions_per_module(): void
    {
        foreach (array_keys(config('course-structure')) as $key) {
            // The hidden Python course is left alone (the test setup un-hides it, so it is named here).
            if ($key === 'programming' || CourseProgressService::isHidden((string) $key)) {
                continue;
            }
            foreach (array_keys(CourseStructure::modules((string) $key)) as $module) {
                $questions = MiniBossService::defaults((string) $key, $module);
                $this->assertGreaterThanOrEqual(MiniBossService::MIN_DEFAULT_QUESTIONS, count($questions), "{$key}/{$module} needs at least 30 questions");

                $letters = array_count_values(array_map(fn ($q) => $q['correct'], $questions));
                foreach ($questions as $q) {
                    $this->assertCount(4, $q['options'], "{$key}/{$module}: {$q['prompt']}");
                    $this->assertArrayHasKey($q['correct'], $q['options']);
                    // The right answer never gives itself away by being the longest.
                    $others = array_diff_key($q['options'], [$q['correct'] => true]);
                    $this->assertLessThanOrEqual(max(array_map('mb_strlen', $others)), mb_strlen($q['options'][$q['correct']]), "{$key}/{$module}: {$q['prompt']}");
                }
                // The right letter is spread over A-D.
                $this->assertCount(4, $letters, "{$key}/{$module} uses every letter");
                $this->assertLessThanOrEqual(ceil(count($questions) / 4) + 1, max($letters), "{$key}/{$module} spreads the right letter");
            }
        }
    }

    public function test_self_paced_must_beat_the_mini_boss_to_open_the_next_module(): void
    {
        $student = $this->selfPacedStudent();
        $this->finishModule($student, 'm1');

        // The next module's first lesson is held: the way on is through the mini boss.
        $this->actingAs($student)->get($this->lessonUrl('m2'))->assertRedirect(MiniBossService::url(Crew::selfPaced('programming', self::KEY), 'm1'));
        $this->actingAs($student)->get(route('student.course.overview', self::HUB))->assertOk()
            ->assertSee('Mini Boss')->assertSee('Face it')->assertSee('Face the Mini Boss')->assertSee('beat it to go on');

        $this->actingAs($student)->get(route('student.course.mini-boss', self::HUB + ['module' => 'm1']))->assertOk()
            ->assertSee('Rules of the arena')->assertSee('+10 XP')->assertSee('try again after 60 minutes');

        $quiz = Crew::selfPaced('programming', self::KEY)->miniBosses()->where('module', 'm1')->firstOrFail();
        $total = $quiz->questions()->count();
        $this->assertGreaterThanOrEqual(30, $total);

        // Only now do the questions leave the server, and never with their answers.
        $start = $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertOk();
        $this->assertCount($total, $start->json('questions'));
        $this->assertArrayNotHasKey('correct_index', $start->json('questions.0'));

        $result = $this->actingAs($student)->postJson(route('student.mini-boss.submit', $quiz), ['answers' => $this->answers($quiz, $total)])
            ->assertOk()->json('result');
        $this->assertSame('passed', $result['status']);
        $this->assertSame($total * 10, $result['xp']);
        $this->assertArrayNotHasKey('review', $result);

        $this->actingAs($student)->get($this->lessonUrl('m2'))->assertOk();
        $this->assertSame($total * 10, MiniBossService::xp($student));
        $this->assertSame(StudentDashboardService::earnedXp($student), 6 * StudentDashboardService::XP_PER_LESSON + $total * 10);
    }

    public function test_a_self_paced_fail_earns_no_xp_and_waits_before_a_retry(): void
    {
        $student = $this->selfPacedStudent();
        $this->finishModule($student, 'm1');
        $this->actingAs($student)->get(route('student.course.mini-boss', self::HUB + ['module' => 'm1']))->assertOk();
        $quiz = Crew::selfPaced('programming', self::KEY)->miniBosses()->where('module', 'm1')->firstOrFail();

        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertOk();
        $result = $this->actingAs($student)->postJson(route('student.mini-boss.submit', $quiz), ['answers' => $this->answers($quiz, 10)])->json('result');
        $this->assertSame('failed', $result['status']);
        $this->assertSame(10, $result['score']);
        $this->assertSame(0, $result['xp']);   // a fail wipes the XP the right answers would have earned
        $this->assertNotNull($result['retry_at']);

        // Still locked, and no second try until the wait is over.
        $this->actingAs($student)->get($this->lessonUrl('m2'))->assertRedirect();
        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertStatus(409);
        $this->actingAs($student)->get(route('student.course.mini-boss', self::HUB + ['module' => 'm1']))->assertSee('Study Module 1 again');

        $this->travel(61)->minutes();
        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertOk();
        $this->assertSame(2, $quiz->attempts()->where('user_id', $student->id)->value('try_number'));
    }

    public function test_leaving_the_mini_boss_fails_it_and_alerts_whoever_watches_the_crew(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $student = $this->selfPacedStudent();
        $this->finishModule($student, 'm1');
        $this->actingAs($student)->get(route('student.course.mini-boss', self::HUB + ['module' => 'm1']));
        $quiz = Crew::selfPaced('programming', self::KEY)->miniBosses()->where('module', 'm1')->firstOrFail();

        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertOk();
        $result = $this->actingAs($student)->postJson(route('student.mini-boss.violation', $quiz), ['reason' => 'switched_away'])->json('result');

        $this->assertSame('failed', $result['status']);
        $this->assertTrue($result['caught']);
        $this->assertSame(0, $result['xp']);
        Mail::assertQueued(FinalExamViolation::class, fn ($m) => $m->hasTo($admin->email));

        // A late submit can't overwrite it.
        $this->actingAs($student)->postJson(route('student.mini-boss.submit', $quiz), ['answers' => $this->answers($quiz, 30)])
            ->assertJsonPath('result.status', 'failed');
    }

    public function test_loading_another_page_mid_exam_counts_as_leaving_it(): void
    {
        $student = $this->selfPacedStudent();
        $this->finishModule($student, 'm1');
        $this->actingAs($student)->get(route('student.course.mini-boss', self::HUB + ['module' => 'm1']));
        $quiz = Crew::selfPaced('programming', self::KEY)->miniBosses()->where('module', 'm1')->firstOrFail();
        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertOk();

        $this->actingAs($student)->get(route('student.course.overview', self::HUB))->assertOk();

        $this->assertSame('left_page', $quiz->attempts()->value('violation'));
    }

    public function test_a_class_takes_it_once_keeps_its_xp_and_moves_on_whatever_the_score(): void
    {
        [, $student, $crew] = $this->classStudent();
        $this->finishModule($student, 'm1', $crew);

        // Nothing holds a class at the end of a module.
        $this->actingAs($student)->get($this->lessonUrl('m2'))->assertOk();

        $this->actingAs($student)->get(route('student.course.mini-boss', self::HUB + ['module' => 'm1']))->assertOk()
            ->assertSee('You get <b>one try</b>', false);
        $quiz = $crew->miniBosses()->where('module', 'm1')->firstOrFail();

        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertOk();
        $result = $this->actingAs($student)->postJson(route('student.mini-boss.submit', $quiz), ['answers' => $this->answers($quiz, 12)])->json('result');
        $this->assertSame('failed', $result['status']);
        $this->assertSame(120, $result['xp']);   // a class keeps the XP for its right answers
        $this->assertNull($result['retry_at']);

        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertStatus(409);
        $this->actingAs($student)->get(route('student.course.overview', self::HUB))->assertSee('recorded');
    }

    public function test_the_faculty_member_edits_their_class_mini_boss_and_sees_every_score(): void
    {
        [$faculty, $student, $crew] = $this->classStudent();

        $this->actingAs($faculty)->get(route('faculty.exams.index', $crew))->assertOk()
            ->assertSee('Mini bosses')->assertSee('Module 1')->assertSee('Show the')->assertSee('Not taken yet')->assertSee('Kai Tan');
        $quiz = $crew->miniBosses()->where('module', 'm1')->firstOrFail();
        $this->assertCount(count(MiniBossService::modules(self::KEY)), $crew->miniBosses()->get());

        // Rewritten in the quiz builder: their questions, time and pass mark.
        $this->actingAs($faculty)->get(route('faculty.quizzes.edit', $quiz))->assertOk()->assertSee('Module 1 mini boss');
        $this->actingAs($faculty)->putJson(route('faculty.quizzes.update', $quiz), [
            'title' => 'Checkpoint One', 'minutes' => 20, 'pass_percent' => 60, 'questions' => [
                ['prompt' => 'What does %d print?', 'options' => ['An int', 'A float'], 'correct' => 0],
                ['prompt' => 'Where does a program start?', 'options' => ['printf', 'main'], 'correct' => 1],
            ],
        ])->assertOk();
        $this->assertSame(2, $quiz->questions()->count());
        $this->assertSame(60, $quiz->fresh()->passPercent());

        // Only their class: self-paced still has TechLab's.
        $solo = $this->selfPacedStudent();
        $this->finishModule($solo, 'm1');
        $this->actingAs($solo)->get(route('student.course.mini-boss', self::HUB + ['module' => 'm1']))->assertDontSee('Checkpoint One');

        $this->finishModule($student, 'm1', $crew);
        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertOk();
        $this->actingAs($student)->postJson(route('student.mini-boss.submit', $quiz), ['answers' => $this->answers($quiz, 2)]);
        $this->actingAs($faculty)->get(route('faculty.exams.index', $crew))->assertSee('Passed · 2/2 (100%) · +20 XP');

        // It can be reset to the default, not deleted.
        $this->actingAs($faculty)->delete(route('faculty.quizzes.destroy', $quiz))->assertStatus(422);
        $this->actingAs($faculty)->post(route('faculty.mini-bosses.reset', $quiz))->assertRedirect();
        $this->assertSame(count(MiniBossService::defaults(self::KEY, 'm1')), $quiz->questions()->count());
        $this->assertSame(1, $quiz->attempts()->count());   // results stay

        // Nobody else's class.
        $other = User::factory()->create(['role' => 'faculty']);
        $this->actingAs($other)->get(route('faculty.exams.index', $crew))->assertForbidden();
    }

    public function test_a_mini_boss_is_not_an_ordinary_quiz(): void
    {
        [, $student, $crew] = $this->classStudent();
        $quiz = MiniBossService::ensure($crew, 'm1');

        $this->actingAs($student)->getJson(route('student.quiz.show', $quiz))->assertNotFound();
        $this->actingAs($student)->get(route('student.course.quiz', self::HUB))->assertOk()->assertDontSee($quiz->title);
        // Its lessons aren't done: it stays shut.
        $this->actingAs($student)->postJson(route('student.mini-boss.start', $quiz))->assertForbidden();
    }

    public function test_self_paced_final_waits_for_every_mini_boss(): void
    {
        $student = $this->selfPacedStudent();
        foreach (array_keys(MiniBossService::modules(self::KEY)) as $module) {
            $this->finishModule($student, $module);
        }
        $crew = Crew::selfPaced('programming', self::KEY);

        $this->actingAs($student)->get(route('student.course.final-exam', self::HUB))->assertOk()->assertSee('Clear every lesson first');

        foreach (array_keys(MiniBossService::modules(self::KEY)) as $module) {
            $quiz = MiniBossService::ensure($crew, $module);
            CrewQuizAttempt::create(['crew_quiz_id' => $quiz->id, 'user_id' => $student->id, 'score' => 30, 'total' => 30, 'xp' => 300, 'answers' => [], 'status' => CrewQuizAttempt::PASSED, 'started_at' => now(), 'finished_at' => now()]);
        }
        $this->actingAs($student)->get(route('student.course.final-exam', self::HUB))->assertOk()->assertSee('Rules of the arena');
    }

    public function test_lessons_done_before_mini_bosses_existed_stay_open(): void
    {
        $student = $this->selfPacedStudent();
        $this->finishModule($student, 'm1');
        $this->finishModule($student, 'm2');

        $this->actingAs($student)->get($this->lessonUrl('m2', 'lesson03'))->assertOk();
        $this->actingAs($student)->get($this->lessonUrl('m3'))->assertRedirect(MiniBossService::url(Crew::selfPaced('programming', self::KEY), 'm2'));
    }
}
