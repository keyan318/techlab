<?php

namespace Tests\Feature;

use App\Models\LessonCheckpoint;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonCheckpointService;
use App\Services\LessonQuizService;
use App\Services\LessonSourceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * In-lesson checkpoints (Computer Programming 1): each stage ends with one question, and the Knowledge Check
 * only takes answers once every checkpoint is passed. A miss never reveals the answer and waits longer each time.
 */
class LessonCheckpointTest extends TestCase
{
    use RefreshDatabase;

    private const COURSE = 'computer-programming-1';

    private function student(): User
    {
        return $this->studentIn('programming', self::COURSE, ['role' => 'student']);
    }

    private function url(string $lesson = 'lesson01', string $what = 'checkpoint'): string
    {
        return '/student/planet/'.self::COURSE."/{$what}/m1/{$lesson}";
    }

    private function key(string $module = 'm1', string $lesson = 'lesson01'): array
    {
        return LessonCheckpointService::key(LessonQuizService::source($module, $lesson, self::COURSE));
    }

    private function wrong(string $correct): string
    {
        return $correct === 'A' ? 'B' : 'A';
    }

    /** Every course with checkpoints: Computer Programming 1, both Networking courses and both Cybersecurity courses. */
    private const CHECKPOINT_COURSES = ['computer-programming-1', 'networking', 'networking-2', 'cybersecurity', 'information-assurance-security-2'];

    public function test_every_checkpoint_lesson_has_three_checkpoints_hidden_from_the_browser_and_astro(): void
    {
        foreach (self::CHECKPOINT_COURSES as $course) {
            foreach (CourseProgressService::order($course) as $item) {
                $this->assertLessonCheckpoints($course, $item['module'], $item['lesson']);
            }
        }
    }

    private function assertLessonCheckpoints(string $course, string $module, string $lesson): void
    {
        $html = LessonQuizService::source($module, $lesson, $course);
        $key = LessonCheckpointService::key($html);
        $where = "{$course} {$module}/{$lesson}";

        $this->assertSame([1, 2, 3], array_keys($key), "{$where} should have checkpoints 1-3");
        foreach ($key as $cp) {
            $this->assertNotSame('', $cp['explanation'], "{$where} checkpoint needs an explanation");
        }
        // Adding checkpoints left the Knowledge Check alone.
        $this->assertCount(5, LessonQuizService::key($html), "{$where} quiz");

        $served = LessonQuizService::strip($html);
        $this->assertStringContainsString('checkpoint-block', $served);
        $this->assertStringNotContainsString('quiz-correct', $served);
        $this->assertStringNotContainsString('✓', $served);
        $this->assertStringNotContainsString('quiz-explanation', $served);

        $this->assertStringNotContainsString($key[1]['explanation'], LessonSourceService::plainText($html));
    }

    public function test_a_miss_hides_the_answer_and_waits_longer_each_time_then_a_right_answer_passes(): void
    {
        Carbon::setTestNow('2026-10-15 10:00:00');
        $user = $this->student();
        $right = $this->key()[1]['correct'];

        $this->actingAs($user)->postJson($this->url(), ['cp' => 1, 'choice' => $this->wrong($right)])
            ->assertOk()->assertJson(['correct' => false, 'wait' => 5])->assertJsonMissingPath('correctChoice');

        // Too soon: refused, nothing recorded.
        $this->actingAs($user)->postJson($this->url(), ['cp' => 1, 'choice' => $right])->assertStatus(429)->assertJson(['wait' => 5]);
        $this->actingAs($user)->getJson($this->url())->assertJsonPath('waits.1', 5);

        Carbon::setTestNow('2026-10-15 10:00:06');
        $this->actingAs($user)->postJson($this->url(), ['cp' => 1, 'choice' => $this->wrong($right)])->assertJson(['correct' => false, 'wait' => 10]);

        Carbon::setTestNow('2026-10-15 10:00:17');
        $this->actingAs($user)->postJson($this->url(), ['cp' => 1, 'choice' => $right])
            ->assertOk()->assertJson(['correct' => true, 'correctChoice' => $right]);

        $this->assertNotNull(LessonCheckpoint::where('user_id', $user->id)->where('checkpoint', 1)->value('passed_at'));
        $this->actingAs($user)->getJson($this->url())->assertJsonPath('passed.1.correctChoice', $right)->assertJsonPath('total', 3);
        Carbon::setTestNow();
    }

    public function test_the_knowledge_check_waits_for_every_checkpoint(): void
    {
        $user = $this->student();
        $key = $this->key();

        $this->actingAs($user)->postJson($this->url('lesson01', 'quiz'), ['q' => 1, 'choice' => 'A'])->assertForbidden();

        foreach ($key as $cp => $k) {
            $this->actingAs($user)->postJson($this->url(), ['cp' => $cp, 'choice' => $k['correct']])->assertJson(['correct' => true]);
        }

        $this->actingAs($user)->postJson($this->url('lesson01', 'quiz'), ['q' => 1, 'choice' => 'A'])->assertOk();
    }

    public function test_finished_lessons_and_faculty_see_every_stage_open(): void
    {
        $user = $this->student();
        CourseProgressService::markComplete($user, 'm1', 'lesson01', self::COURSE);
        $this->actingAs($user)->getJson($this->url())->assertOk()->assertJsonPath('passed.3.correctChoice', $this->key()[3]['correct']);
        $this->assertTrue(LessonCheckpointService::cleared($user, self::COURSE, 'm1', 'lesson01', true));

        $faculty = $this->studentIn('programming', self::COURSE, ['role' => 'faculty']);
        $this->assertTrue(LessonCheckpointService::cleared($faculty, self::COURSE, 'm1', 'lesson01', false));
    }

    public function test_a_locked_lesson_refuses_its_checkpoints(): void
    {
        $this->actingAs($this->student())->postJson($this->url('lesson02'), ['cp' => 1, 'choice' => 'A'])->assertForbidden();
    }

    public function test_lessons_without_checkpoints_are_unaffected(): void
    {
        $user = $this->studentIn('programming', 'python', ['role' => 'student']);

        $this->assertTrue(LessonCheckpointService::cleared($user, 'programming', 'm1', 'lesson01', false));
    }
}
