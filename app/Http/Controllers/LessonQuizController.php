<?php

namespace App\Http\Controllers;

use App\Models\QuizAnswer;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
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
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $key = $this->key($module, $lesson);
        $rows = QuizAnswer::where('user_id', $request->user()->id)->where(compact('module', 'lesson') + ['course' => $slug])->get();

        return response()->json([
            'perQuestion' => StudentDashboardService::XP_PER_QUIZ_QUESTION,
            'total' => count($key),
            'answered' => $rows->mapWithKeys(fn (QuizAnswer $a) => [$a->question => [
                'choice' => $a->choice,
                'correct' => $a->is_correct,
                'correctChoice' => $key[$a->question]['correct'] ?? null,
                'explanation' => $key[$a->question]['explanation'] ?? '',
                'xp' => $a->xp,
            ]])->all(),
        ]);
    }

    /** POST {q, choice}: check the first answer to a question, record it, award XP if correct. */
    public function answer(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $data = $request->validate([
            'q' => ['required', 'integer', 'min:1', 'max:50'],
            'choice' => ['required', 'string', 'in:A,B,C,D'],
        ]);

        $key = $this->key($module, $lesson);
        $q = (int) $data['q'];
        if (! isset($key[$q])) {
            return response()->json(['error' => 'Question not found.'], 404);
        }

        // firstOrCreate on the unique key: a double-click or a second tab can't award XP twice.
        $answer = QuizAnswer::firstOrCreate(
            ['user_id' => $request->user()->id, 'course' => $slug, 'module' => $module, 'lesson' => $lesson, 'question' => $q],
            [
                'choice' => $data['choice'],
                'is_correct' => $data['choice'] === $key[$q]['correct'],
                'xp' => $data['choice'] === $key[$q]['correct'] ? StudentDashboardService::XP_PER_QUIZ_QUESTION : 0,
            ]
        );

        return response()->json([
            'ok' => true,
            'first' => $answer->wasRecentlyCreated,
            'choice' => $answer->choice,
            'correct' => $answer->is_correct,
            'correctChoice' => $key[$q]['correct'],
            'explanation' => $key[$q]['explanation'],
            'xp' => $answer->wasRecentlyCreated ? $answer->xp : 0,
        ]);
    }

    private function guard(Request $request, string $slug, string $module, string $lesson): ?JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        if ($slug !== CourseProgressService::COURSE || CourseProgressService::indexOf($module, $lesson, $slug) === null) {
            return response()->json(['error' => 'Lesson not found.'], 404);
        }
        if (! CourseProgressService::isUnlocked($user, $module, $lesson, $slug)) {
            return response()->json(['error' => 'This lesson is locked.'], 403);
        }

        return null;
    }

    /** @return array<int, array{correct: string, explanation: string}> */
    private function key(string $module, string $lesson): array
    {
        $html = LessonQuizService::source($module, $lesson);

        return $html === null ? [] : LessonQuizService::key($html);
    }
}
