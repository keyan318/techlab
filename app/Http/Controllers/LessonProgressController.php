<?php

namespace App\Http\Controllers;

use App\Services\CourseProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    /**
     * POST /student/planet/{slug}/lesson/{module}/{lesson}/complete
     *
     * The editor sends the output the student's code produced. The answer key
     * lives only on the server; a completion is recorded solely when the
     * output matches AND the lesson is itself unlocked (so completions can't
     * be POSTed out of order to skip ahead).
     */
    public function complete(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if ($slug !== CourseProgressService::COURSE) {
            return response()->json(['error' => 'Planet not found.'], 404);
        }

        if (CourseProgressService::indexOf($module, $lesson, $slug) === null) {
            return response()->json(['error' => 'Lesson not found.'], 404);
        }

        if (! CourseProgressService::isUnlocked($user, $module, $lesson, $slug)) {
            return response()->json(['error' => 'This lesson is locked.'], 403);
        }

        $expected = CourseProgressService::expectedFor($module, $lesson, $slug);

        if ($expected === null) {
            return response()->json(['error' => 'This lesson has no coding challenge to submit.'], 422);
        }

        $output = (string) $request->input('output', '');

        if (! CourseProgressService::outputMatches($expected, $output)) {
            return response()->json(['ok' => false, 'error' => 'Output does not match.'], 422);
        }

        CourseProgressService::markComplete($user, $module, $lesson, $slug);

        $next = CourseProgressService::nextLesson($module, $lesson, $slug);

        return response()->json([
            'ok'   => true,
            'next' => $next ? route('student.planet.module.lesson', [
                'slug'   => $slug,
                'module' => $next['module'],
                'lesson' => $next['lesson'],
            ]) : null,
        ]);
    }
}
