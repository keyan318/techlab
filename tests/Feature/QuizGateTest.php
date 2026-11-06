<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
use App\Services\NvidiaNimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A lesson's hands-on stage opens only after its Knowledge Check is passed (at most 2 mistakes).
 * Failing it offers a review with Astro (answer already written), then a retry of the missed ones.
 */
class QuizGateTest extends TestCase
{
    use RefreshDatabase;

    private const LESSON = ['slug' => 'networking', 'module' => 'm1', 'lesson' => 'lesson01'];

    private function student(): User
    {
        return $this->studentIn('networking', 'networking-fundamentals');
    }

    private function key(): array
    {
        return LessonQuizService::key((string) LessonQuizService::source('m1', 'lesson01', 'networking'));
    }

    /** The review is written by the system, never by the AI. */
    private function fakeAstro(): void
    {
        $this->mock(NvidiaNimService::class, fn ($m) => $m->shouldReceive('stream')->never());
    }

    private function gate(User $u): array
    {
        return $this->actingAs($u)->getJson(route('student.planet.quiz.status', self::LESSON))->assertOk()->json('gate');
    }

    public function test_the_lab_stays_locked_until_the_quiz_is_answered(): void
    {
        $u = $this->student();
        $this->assertGreaterThanOrEqual(3, count($this->key()));

        $this->actingAs($u)->get(route('student.planet.lab', self::LESSON))
            ->assertRedirect(route('student.planet.module.lesson', self::LESSON));
        $this->assertFalse($this->gate($u)['allowed']);
    }

    public function test_two_mistakes_still_pass_and_open_the_lab(): void
    {
        $u = $this->student();
        $this->answerQuiz($u, 'networking', 'm1', 'lesson01', mistakes: 2);

        $gate = $this->gate($u);
        $this->assertTrue($gate['allowed']);
        $this->assertFalse($gate['failed']);
        $this->actingAs($u)->get(route('student.planet.lab', self::LESSON))->assertOk();
    }

    public function test_three_mistakes_fail_and_keep_the_lab_and_completion_locked(): void
    {
        $u = $this->student();
        $this->answerQuiz($u, 'networking', 'm1', 'lesson01', mistakes: 3);

        $gate = $this->gate($u);
        $this->assertTrue($gate['failed']);
        $this->assertFalse($gate['allowed']);
        $this->assertSame(3, $gate['mistakes']);
        $this->assertFalse($gate['reviewed']);

        $this->actingAs($u)->get(route('student.planet.lab', self::LESSON))->assertRedirect();
        $this->actingAs($u)->postJson(route('student.planet.lab.complete', self::LESSON), ['lab' => 'm1-l1'])->assertForbidden();
        // No retry before talking it through with Astro.
        $this->actingAs($u)->postJson(route('student.planet.quiz.retake', self::LESSON))->assertStatus(422);
    }

    public function test_review_opens_a_chat_where_astro_has_already_answered(): void
    {
        $u = $this->student();
        $this->answerQuiz($u, 'networking', 'm1', 'lesson01', mistakes: 3);
        $this->fakeAstro();

        $url = $this->actingAs($u)->postJson(route('student.planet.quiz.review', self::LESSON))->assertOk()->json('url');

        $conversation = Conversation::where('user_id', $u->id)->sole();
        $this->assertSame(route('student.chat', ['c' => $conversation->id]), $url);
        $messages = $conversation->messages()->orderBy('id')->get();
        $this->assertSame(['user', 'assistant'], $messages->pluck('role')->all());
        $this->assertStringContainsString('got 3 of', $messages[0]->content);
        $this->assertStringContainsString('My answer:', $messages[0]->content);
        $this->assertStringContainsString('Right answer:', $messages[0]->content);
        $this->assertStringContainsString('3 of', $messages[1]->content);
        $this->assertStringContainsString('**'.$this->key()[array_key_first($this->key())]['correct'].'. ', $messages[1]->content);

        // Asking again reopens the same chat.
        $this->actingAs($u)->postJson(route('student.planet.quiz.review', self::LESSON))->assertOk()->assertJson(['url' => $url]);
        $this->assertTrue($this->gate($u)['reviewed']);
    }

    public function test_the_review_explains_each_mistake_from_the_lesson(): void
    {
        $u = $this->student();
        $this->answerQuiz($u, 'networking', 'm1', 'lesson01', mistakes: 3);
        $this->fakeAstro();

        $this->actingAs($u)->postJson(route('student.planet.quiz.review', self::LESSON))->assertOk();

        $answer = Conversation::where('user_id', $u->id)->sole()->messages()->where('role', 'assistant')->value('content');
        $this->assertSame(3, preg_match_all('/^\*\*\d\. /m', $answer));   // one section per mistake
        $this->assertStringContainsString('Quick tip', $answer);
        $this->assertStringContainsString('astronaut', $answer);
    }

    public function test_retry_reopens_only_the_missed_questions_and_earns_no_xp(): void
    {
        $u = $this->student();
        $this->answerQuiz($u, 'networking', 'm1', 'lesson01', mistakes: 3);
        $this->fakeAstro();
        $this->actingAs($u)->postJson(route('student.planet.quiz.review', self::LESSON))->assertOk();

        $this->actingAs($u)->postJson(route('student.planet.quiz.retake', self::LESSON))->assertOk();

        $status = $this->actingAs($u)->getJson(route('student.planet.quiz.status', self::LESSON))->json();
        $this->assertCount(count($this->key()) - 3, $status['answered']);   // the 3 missed are open again
        $this->assertFalse($status['gate']['allowed']);

        $xpBefore = (int) $u->quizAnswers()->sum('xp');
        foreach (array_slice($this->key(), 0, 3, true) as $q => $k) {
            $this->actingAs($u)->postJson(route('student.planet.quiz.answer', self::LESSON), ['q' => $q, 'choice' => $k['correct']])
                ->assertOk()->assertJson(['correct' => true, 'xp' => 0]);
        }
        $this->assertSame($xpBefore, (int) $u->quizAnswers()->sum('xp'));

        $this->assertTrue($this->gate($u)['allowed']);
        $this->actingAs($u)->get(route('student.planet.lab', self::LESSON))->assertOk();
    }

    public function test_code_it_yourself_is_locked_until_the_quiz_is_passed(): void
    {
        $u = $this->studentIn();
        $lesson = ['slug' => 'programming', 'module' => 'm1', 'lesson' => 'lesson01'];
        $launch = fn () => $this->actingAs($u)->post(route('student.planet.editor.launch', ['slug' => 'programming']), [
            'title' => 'Exercise', 'return_to' => route('student.planet.module.lesson', $lesson),
        ]);

        $launch()->assertRedirect(route('student.planet.module.lesson', $lesson));

        $this->answerQuiz($u, 'programming', 'm1', 'lesson01');
        $launch()->assertRedirect(route('student.planet.editor', ['slug' => 'programming']));
    }

    public function test_lessons_already_finished_stay_open(): void
    {
        $u = $this->student();
        CourseProgressService::markComplete($u, 'm1', 'lesson01', 'networking');

        $this->assertTrue($this->gate($u)['allowed']);
        $this->actingAs($u)->get(route('student.planet.lab', self::LESSON))->assertOk();
    }
}
