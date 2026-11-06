<?php

namespace App\Http\Controllers;

use App\Models\QuizAnswer;
use App\Services\CourseProgressService;
use App\Services\LessonCheckpointService;
use App\Services\LessonQuizService;
use App\Services\QuizGateService;
use App\Services\StudentDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Interactive lesson quizzes. The answer key never reaches the browser up front: a student's first answer to a
 * question is checked here, recorded, and (if correct) earns StudentDashboardService::XP_PER_QUIZ_QUESTION.
 */
class LessonQuizController extends Controller
{
    /** GET: what this student has already answered in the lesson (so a reload restores the results). */
    public function status(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson, $completed)) {
            return $error;
        }

        $key = $this->key($slug, $module, $lesson);
        $rows = QuizAnswer::where('user_id', $request->user()->id)->where(compact('module', 'lesson') + ['course' => $slug])
            ->where('choice', '!=', QuizGateService::REOPENED)->get();

        return response()->json([
            'perQuestion' => StudentDashboardService::XP_PER_QUIZ_QUESTION,
            'total' => count($key),
            'answered' => $rows->filter(fn (QuizAnswer $a) => isset($key[$a->question]))->mapWithKeys(fn (QuizAnswer $a) => [$a->question => [
                'choice' => $a->choice,
                'correct' => $a->is_correct,
                'correctChoice' => $key[$a->question]['correct'] ?? null,
                'explanation' => $key[$a->question]['explanation'] ?? '',
                'xp' => $a->xp,
            ]])->all(),
            'gate' => $this->gate($request, $slug, $module, $lesson, $completed),
        ]);
    }

    /**
     * POST {q, choice}: check the first answer to a question, record it, award XP if correct.
     *
     * The database is a remote pooler (~0.25s a round trip), and the student waits on this after every
     * click, so it is kept to as few queries as possible: one for the lesson locks, one to save the answer,
     * one for the quiz's answers (plus one for the review, only after a failed quiz).
     */
    public function answer(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson, $completed)) {
            return $error;
        }

        $data = $request->validate([
            'q' => ['required', 'integer', 'min:1', 'max:50'],
            'choice' => ['required', 'string', 'in:A,B,C,D'],
        ]);

        // The Knowledge Check opens only once the lesson's checkpoints are passed (they lock the stages before it).
        $html = LessonQuizService::source($module, $lesson, $slug);
        if (! LessonCheckpointService::cleared($request->user(), $slug, $module, $lesson, (bool) $completed, $html)) {
            return response()->json(['error' => 'Pass the checkpoints in the lesson first.'], 403);
        }

        $key = $html === null ? [] : LessonQuizService::key($html);
        $q = (int) $data['q'];
        if (! isset($key[$q])) {
            return response()->json(['error' => 'Question not found.'], 404);
        }

        $where = ['user_id' => $request->user()->id, 'course' => $slug, 'module' => $module, 'lesson' => $lesson, 'question' => $q];
        $correct = $data['choice'] === $key[$q]['correct'];
        $answer = new QuizAnswer($where + [
            'choice' => $data['choice'],
            'is_correct' => $correct,
            'xp' => $correct ? StudentDashboardService::XP_PER_QUIZ_QUESTION : 0,
        ]);

        // Insert-or-ignore on the unique key, in one round trip: a double-click or a second tab
        // can't award XP twice. Only when the question already had an answer is it read back.
        $now = now();
        $created = QuizAnswer::insertOrIgnore($answer->getAttributes() + ['created_at' => $now, 'updated_at' => $now]) === 1;

        // A question reopened for a retry (after the mistake review) takes the new answer, but earns
        // no XP: the first answer's XP is the only XP a question ever gives.
        $retried = false;
        if (! $created) {
            $answer = QuizAnswer::where($where)->first();
            if ($answer->choice === QuizGateService::REOPENED) {
                $answer->update(['choice' => $data['choice'], 'is_correct' => $correct]);
                $retried = true;
            }
        }

        return response()->json([
            'ok' => true,
            'first' => $created || $retried,
            'choice' => $answer->choice,
            'correct' => $answer->is_correct,
            'correctChoice' => $key[$q]['correct'],
            'explanation' => $key[$q]['explanation'],
            'xp' => $created ? $answer->xp : 0,
            'gate' => $this->gate($request, $slug, $module, $lesson, $completed),
        ]);
    }

    /** POST: open the student's mistake review with Astro (Astro's answer is already written). */
    public function review(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $id = app(QuizGateService::class)->review($request->user(), $slug, $module, $lesson);
        if ($id === null) {
            return response()->json(['error' => 'There are no mistakes to review here.'], 422);
        }

        return response()->json(['ok' => true, 'url' => route('student.chat', ['c' => $id])]);
    }

    /** POST: reopen the questions the student missed, once they have reviewed them with Astro. */
    public function retake(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        if (app(QuizGateService::class)->retake($request->user(), $slug, $module, $lesson) === 0) {
            return response()->json(['error' => 'Review your mistakes with Astro first.'], 422);
        }

        return response()->json(['ok' => true, 'gate' => $this->gate($request, $slug, $module, $lesson)]);
    }

    /** The lesson's lab lock, plus the URLs the quiz needs to act on it. */
    private function gate(Request $request, string $slug, string $module, string $lesson, ?bool $completed = null): array
    {
        $params = compact('slug', 'module', 'lesson');

        return app(QuizGateService::class)->state($request->user(), $slug, $module, $lesson, $completed) + [
            'reviewUrl' => route('student.planet.quiz.review', $params),
            'retakeUrl' => route('student.planet.quiz.retake', $params),
        ];
    }

    /** Refuses a missing or locked lesson. Sets $completed to whether this lesson is already finished. */
    private function guard(Request $request, string $slug, string $module, string $lesson, ?bool &$completed = null): ?JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        if (! CourseProgressService::exists($slug) || CourseProgressService::indexOf($module, $lesson, $slug) === null) {
            return response()->json(['error' => 'Lesson not found.'], 404);
        }
        [$unlocked, $completed] = CourseProgressService::unlockedAndCompleted($user, $module, $lesson, $slug);
        if (! $unlocked) {
            return response()->json(['error' => 'This lesson is locked.'], 403);
        }

        return null;
    }

    /** @return array<int, array{correct: string, explanation: string}> */
    private function key(string $slug, string $module, string $lesson): array
    {
        $html = LessonQuizService::source($module, $lesson, $slug);

        return $html === null ? [] : LessonQuizService::key($html);
    }
}
