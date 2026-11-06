<?php

namespace App\Http\Controllers;

use App\Models\LessonCheckpoint;
use App\Services\CourseProgressService;
use App\Services\LessonCheckpointService;
use App\Services\LessonQuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * In-lesson checkpoints (see LessonCheckpointService): which ones this student has passed, and checking one answer.
 * A miss never reveals the right answer; it only says how long to wait before trying again.
 */
class LessonCheckpointController extends Controller
{
    /** GET: the checkpoints passed so far (all of them for a finished lesson or a non-student), and any wait left. */
    public function status(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson, $completed)) {
            return $error;
        }

        $key = $this->key($slug, $module, $lesson);
        $all = LessonCheckpointService::exempt($request->user(), $completed, $key);
        $rows = $all ? collect() : LessonCheckpointService::rows($request->user(), $slug, $module, $lesson);

        $passed = [];
        $waits = [];
        foreach ($key as $cp => $k) {
            $row = $rows->get($cp);
            if ($all || $row?->passed_at) {
                $passed[$cp] = ['correctChoice' => $k['correct'], 'explanation' => $k['explanation']];
            } elseif ($wait = LessonCheckpointService::waitFor($row)) {
                $waits[$cp] = $wait;
            }
        }

        return response()->json(['total' => count($key), 'passed' => (object) $passed, 'waits' => (object) $waits]);
    }

    /** POST {cp, choice}: right → the checkpoint is passed for good; wrong → count the miss and say how long to wait. */
    public function answer(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $data = $request->validate([
            'cp' => ['required', 'integer', 'min:1', 'max:20'],
            'choice' => ['required', 'string', 'in:A,B,C,D'],
        ]);

        $key = $this->key($slug, $module, $lesson);
        $cp = (int) $data['cp'];
        if (! isset($key[$cp])) {
            return response()->json(['error' => 'Checkpoint not found.'], 404);
        }

        $row = LessonCheckpoint::firstOrNew(['user_id' => $request->user()->id, 'course' => $slug, 'module' => $module, 'lesson' => $lesson, 'checkpoint' => $cp]);
        $pass = fn () => response()->json(['ok' => true, 'correct' => true, 'correctChoice' => $key[$cp]['correct'], 'explanation' => $key[$cp]['explanation']]);

        if ($row->passed_at) {
            return $pass();
        }
        if ($wait = LessonCheckpointService::waitFor($row)) {
            return response()->json(['error' => 'Re-read the stage first.', 'wait' => $wait], 429);
        }

        if ($data['choice'] === $key[$cp]['correct']) {
            $row->passed_at = now();
            $row->save();

            return $pass();
        }

        $row->misses++;
        $row->last_miss_at = now();
        $row->save();

        return response()->json(['ok' => true, 'correct' => false, 'wait' => LessonCheckpointService::cooldown($row->misses)]);
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

        return $html === null ? [] : LessonCheckpointService::key($html);
    }
}
