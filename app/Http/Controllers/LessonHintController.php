<?php

namespace App\Http\Controllers;

use App\Models\XpSpend;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
use App\Services\StudentDashboardService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Astro's hint in the Python editor costs XP. The hint text only lives on the server (it is stripped from the
 * lesson page); a student buys it once per lesson, the price is deducted from their XP, and flipping back to it
 * afterwards is free.
 */
class LessonHintController extends Controller
{
    /** GET: price, the student's XP, and the hint itself if they already bought it. */
    public function status(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $hint = $this->hintFor($slug, $module, $lesson);
        $bought = $hint !== null && $this->spend($request, $slug, $module, $lesson)->exists();

        return response()->json($this->payload($request, $hint, $bought));
    }

    /** POST: spend the XP (if they can afford it and have not bought it yet) and reveal the hint. */
    public function buy(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $hint = $this->hintFor($slug, $module, $lesson);
        if ($hint === null) {
            return response()->json(['error' => 'This exercise has no hint.'], 404);
        }

        $user = $request->user();
        $cost = StudentDashboardService::XP_HINT_COST;
        $spent = 0;

        try {
            DB::transaction(function () use ($user, $slug, $module, $lesson, $cost, $hint, &$spent) {
                DB::table('users')->where('id', $user->id)->lockForUpdate()->first();   // serialise this student's purchases

                if ($this->spend(request(), $slug, $module, $lesson)->exists()) {
                    return;   // already bought: nothing to charge
                }
                if (StudentDashboardService::balance($user) < $cost) {
                    abort(response()->json($this->payload(request(), $hint, false) + ['error' => 'Not enough XP.'], 402));
                }

                XpSpend::create(['user_id' => $user->id, 'course' => $slug, 'module' => $module, 'lesson' => $lesson, 'item' => 'hint', 'cost' => $cost]);
                $spent = $cost;
            });
        } catch (QueryException) {
            // a parallel request bought it first (unique key): treat as already bought, charge nothing
        }

        return response()->json($this->payload($request, $hint, true) + ['ok' => true, 'spent' => $spent]);
    }

    /** @return array<string, mixed> */
    private function payload(Request $request, ?array $hint, bool $bought): array
    {
        $balance = StudentDashboardService::balance($request->user());

        return [
            'hasHint' => $hint !== null,
            'cost' => StudentDashboardService::XP_HINT_COST,
            'balance' => $balance,
            'canAfford' => $balance >= StudentDashboardService::XP_HINT_COST,
            'bought' => $bought,
            'hint' => $bought ? $hint : null,
        ];
    }

    private function hintFor(string $slug, string $module, string $lesson): ?array
    {
        $html = LessonQuizService::source($module, $lesson, $slug);

        return $html === null ? null : LessonQuizService::hint($html);
    }

    private function spend(Request $request, string $slug, string $module, string $lesson)
    {
        return XpSpend::where(['user_id' => $request->user()->id, 'course' => $slug, 'module' => $module, 'lesson' => $lesson, 'item' => 'hint']);
    }

    private function guard(Request $request, string $slug, string $module, string $lesson): ?JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        if (! CourseProgressService::exists($slug) || CourseProgressService::indexOf($module, $lesson, $slug) === null) {
            return response()->json(['error' => 'Lesson not found.'], 404);
        }
        if (! CourseProgressService::isUnlocked($user, $module, $lesson, $slug)) {
            return response()->json(['error' => 'This lesson is locked.'], 403);
        }

        return null;
    }
}
