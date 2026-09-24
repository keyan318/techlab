<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\XpGatedItem;
use App\Services\CourseProgressService;
use App\Services\LessonQuizService;
use App\Services\StudentDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Astro's hint in the Python editor costs XP. The hint text only lives on the server (it is stripped from the
 * lesson page); a student buys it once per lesson, the price is deducted from their XP, and flipping back to it
 * afterwards is free.
 */
class LessonHintController extends Controller
{
    use XpGatedItem;

    private const ITEM = 'hint';

    /** GET: price, the student's XP, and the hint itself if they already bought it. */
    public function status(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->xpGuard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $hint = $this->hintFor($slug, $module, $lesson);
        $bought = $hint !== null && $this->xpBought($request->user()->id, $slug, $module, $lesson, self::ITEM);

        return response()->json($this->payload($request, $hint, $bought));
    }

    /** POST: spend the XP (if they can afford it and have not bought it yet) and reveal the hint. */
    public function buy(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->xpGuard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $hint = $this->hintFor($slug, $module, $lesson);
        if ($hint === null) {
            return response()->json(['error' => 'This exercise has no hint.'], 404);
        }

        $result = $this->xpUnlock($request->user(), $slug, $module, $lesson, self::ITEM, StudentDashboardService::XP_HINT_COST);

        if ($result['error'] !== null) {
            return response()->json($this->payload($request, $hint, false) + ['error' => $result['error']], 402);
        }

        return response()->json($this->payload($request, $hint, true) + ['ok' => true, 'spent' => $result['spent']]);
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
}
