<?php

namespace App\Services;

/**
 * Levels, ranks and avatar frames (config/ranks.php).
 *
 * The level comes from XP earned (StudentDashboardService::earnedXp), never from the balance, so spending XP in the
 * Shop or on Astro never lowers it. Going from level n to n+1 costs BASE_STEP + STEP_GROWTH × (n − 1) XP: 50 for the
 * first, 55 for the next, and so on, so early levels come quickly. Finishing every course with every answer right
 * first time lands around level 100.
 */
class LevelService
{
    public const BASE_STEP = 50;

    public const STEP_GROWTH = 5;

    /** Earned XP needed to be at $level. */
    public static function xpToReach(int $level): int
    {
        $n = max(0, $level - 1);

        return self::BASE_STEP * $n + intdiv(self::STEP_GROWTH * $n * ($n - 1), 2);
    }

    public static function levelFor(int $earnedXp): int
    {
        $xp = max(0, $earnedXp);
        $a = self::STEP_GROWTH / 2;
        $b = self::BASE_STEP - $a;
        $n = (int) floor((-$b + sqrt($b * $b + 4 * $a * $xp)) / (2 * $a));

        // The square root can land one off on big numbers; settle on the exact level.
        while (self::xpToReach($n + 2) <= $xp) {
            $n++;
        }
        while ($n > 0 && self::xpToReach($n + 1) > $xp) {
            $n--;
        }

        return $n + 1;
    }

    /**
     * The level and total XP progress toward the next one: "into" of "needed" XP overall, so this
     * matches the XP number shown everywhere else (e.g. the Shop's "XP to spend") instead of only
     * counting what was earned since the last level-up.
     *
     * @return array{level: int, into: int, needed: int}
     */
    public static function progress(int $earnedXp): array
    {
        $level = self::levelFor($earnedXp);

        return ['level' => $level, 'into' => max(0, $earnedXp), 'needed' => self::xpToReach($level + 1)];
    }

    public static function tiers(): array
    {
        return config('ranks.tiers');
    }

    /** The rank index (0 = Stardust … 10 = Galaxy Legend) for a level. */
    public static function tierFor(int $level): int
    {
        return min(count(self::tiers()) - 1, intdiv(max(1, $level), config('ranks.levels_per_tier')));
    }

    /** The first level of rank $tier (Stardust starts at 1). */
    public static function tierStart(int $tier): int
    {
        return max(1, $tier * config('ranks.levels_per_tier'));
    }

    /**
     * Everything a badge needs: the rank, its colours, and for Galaxy Legend the stars (levels past 99).
     *
     * @return array{index: int, key: string, name: string, label: string, glyph: string, motion: int, colors: list<string>, stars: int, level: int}
     */
    public static function rank(int $level): array
    {
        $index = self::tierFor($level);
        $tier = self::tiers()[$index];
        $stars = $index === count(self::tiers()) - 1 ? $level - self::tierStart($index) + 1 : 0;

        return [
            'index' => $index,
            'key' => $tier['key'],
            'name' => $tier['name'],
            'label' => $stars ? "{$tier['name']} ★{$stars}" : $tier['name'],
            'glyph' => $tier['glyph'],
            'motion' => $tier['motion'],
            'colors' => $tier['colors'],
            'stars' => $stars,
            'level' => $level,
        ];
    }

    /**
     * Every frame, keyed "{rank}-{variant}": each rank's free default, then A and B from the Shop.
     *
     * @return array<string, array{key: string, tier: int, variant: string, name: string, price: int}>
     */
    public static function frames(): array
    {
        static $frames;
        if ($frames !== null) {
            return $frames;
        }

        $frames = [];
        foreach (self::tiers() as $i => $tier) {
            $frames["{$tier['key']}-default"] = ['key' => "{$tier['key']}-default", 'tier' => $i, 'variant' => 'default', 'name' => $tier['name'], 'price' => 0];
            foreach (['a', 'b'] as $j => $variant) {
                $frames["{$tier['key']}-{$variant}"] = ['key' => "{$tier['key']}-{$variant}", 'tier' => $i, 'variant' => $variant,
                    'name' => $tier['frames'][$j], 'price' => config("ranks.frame_prices.{$variant}") * ($i + 2)];
            }
        }

        return $frames;
    }

    public static function frame(string $key): ?array
    {
        return self::frames()[$key] ?? null;
    }

    public static function defaultFrame(int $level): string
    {
        return self::tiers()[self::tierFor($level)]['key'].'-default';
    }

    /** On sale (or free to wear, for a default) once the student has reached that frame's rank. */
    public static function frameUnlocked(string $key, int $level): bool
    {
        $frame = self::frame($key);

        return $frame !== null && $frame['tier'] <= self::tierFor($level);
    }

    /**
     * The frame a student wears: the one they picked, if it still exists and they've reached its rank;
     * otherwise their rank's default, so it upgrades by itself as they rank up.
     */
    public static function wornFrame(?string $picked, int $level): string
    {
        return $picked && self::frameUnlocked($picked, $level) ? $picked : self::defaultFrame($level);
    }
}
