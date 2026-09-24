<?php

namespace App\Services;

use App\Models\User;

/**
 * MVP unlock rule: earning at least one gem (one completed module, see
 * CourseProgressService::completedModulesCount) in a planet unlocks the next
 * planet in User::PLANETS' fixed track order. A real per-planet gem
 * threshold is deferred until the curriculum is finalized.
 */
class PlanetUnlockService
{
    /**
     * Unlock the next planet for $user if they've earned their first gem in
     * $planet. No-op if there is no next planet, or it's already unlocked.
     */
    public static function checkAndUnlock(User $user, string $planet): void
    {
        $index = array_search($planet, User::PLANETS, true);

        if ($index === false || ! isset(User::PLANETS[$index + 1])) {
            return;
        }

        $next = User::PLANETS[$index + 1];

        if ($user->isEnrolledIn($next)) {
            return;
        }

        if (CourseProgressService::completedModulesCount($user, $planet) < 1) {
            return;
        }

        $user->planets = array_values(array_unique([...($user->planets ?? []), $next]));
        $user->save();
    }
}
