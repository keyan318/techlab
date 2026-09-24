<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Models\XpSpend;
use App\Services\CourseProgressService;
use App\Services\StudentDashboardService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Shared shape for "pay XP once per lesson, unlock an item forever" features
 * (Astro's static hint, Astro's live lab-help chat): the same auth/lesson/lock
 * guard, the same "has this student already bought it" check, and the same
 * race-safe charge-once-then-unlock transaction.
 */
trait XpGatedItem
{
    /** 401 unauthenticated, 404 unknown lesson, 403 locked — or null when the request may proceed. */
    protected function xpGuard(Request $request, string $slug, string $module, string $lesson): ?JsonResponse
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

    protected function xpBought(int $userId, string $slug, string $module, string $lesson, string $item): bool
    {
        return XpSpend::where([
            'user_id' => $userId, 'course' => $slug, 'module' => $module, 'lesson' => $lesson, 'item' => $item,
        ])->exists();
    }

    /**
     * Charge XP for an item this student hasn't bought yet, once. Safe under concurrent requests:
     * a row lock serialises this student's purchases, and a unique-key race falls back to "already bought".
     *
     * @return array{bought: bool, spent: int, error: ?string} 'error' is set only on insufficient balance;
     *                                                         'bought' true with spent 0 means the student already had it (nothing charged).
     */
    protected function xpUnlock(User $user, string $slug, string $module, string $lesson, string $item, int $cost): array
    {
        $result = ['bought' => false, 'spent' => 0, 'error' => null];

        try {
            DB::transaction(function () use ($user, $slug, $module, $lesson, $item, $cost, &$result) {
                DB::table('users')->where('id', $user->id)->lockForUpdate()->first();   // serialise this student's purchases

                if ($this->xpBought($user->id, $slug, $module, $lesson, $item)) {
                    $result['bought'] = true;

                    return;
                }
                if (StudentDashboardService::balance($user) < $cost) {
                    $result['error'] = 'Not enough XP.';

                    return;
                }

                XpSpend::create(['user_id' => $user->id, 'course' => $slug, 'module' => $module, 'lesson' => $lesson, 'item' => $item, 'cost' => $cost]);
                $result['bought'] = true;
                $result['spent'] = $cost;
            });
        } catch (QueryException) {
            $result['bought'] = true;   // a parallel request bought it first: treat as already bought, charge nothing
        }

        return $result;
    }
}
