<?php

namespace App\Services;

use App\Models\GemSpend;
use App\Models\ShopPurchase;
use App\Models\User;

/**
 * The pixel-art animal astronaut (config/avatar.php) students and captains (faculty) wear: checking a saved look, rolling a
 * random one, drawing it as SVG rectangles, and their codename (checking and suggesting one).
 *
 * A look is ['animal', 'coat', 'suit', 'suit_color', 'patch', 'helmet', 'visor'].
 * The onboarding builder draws the same layers in the browser (resources/views/student/welcome.blade.php);
 * keep the two in step if the drawing rules change.
 */
class AvatarService
{
    /** viewBox per crop: the whole astronaut, or head and shoulders for small round spots. */
    public const CROPS = ['full' => '0 0 24 30', 'bust' => '3 0 18 18'];

    /** Letters, digits and underscores, 3 to 20 of them. */
    public const CODENAME_PATTERN = '/^[A-Za-z0-9_]{3,20}$/';

    /** Codenames nobody can take: they'd pass for the captain or staff. */
    public const RESERVED_CODENAMES = ['astro', 'captain', 'admin', 'faculty', 'teacher', 'techlab'];

    public static function config(): array
    {
        return config('avatar');
    }

    /**
     * The animals and suits $role can pick: the captain-only ones for faculty (and admins, who use the
     * captain builder too), the rest for students.
     *
     * @return array{animals: array, suits: array}
     */
    public static function choicesFor(string $role): array
    {
        $c = self::config();
        $captain = in_array($role, ['faculty', 'admin'], true);
        $mine = fn (array $items) => array_filter($items, fn (array $item) => ($item['captain'] ?? false) === $captain);

        return ['animals' => $mine($c['animals']), 'suits' => $mine($c['suits'])];
    }

    /** An animal's or suit's price in XP (0 = free). $kind is "animal" or "suit". */
    public static function price(string $kind, string $key): int
    {
        return (int) (self::config()[$kind === 'animal' ? 'animals' : 'suits'][$key]['xp'] ?? 0);
    }

    /** What a Shop item is called in shop_purchases: "animal:dog", "suit:pilot", "frame:nova-b". */
    public static function item(string $kind, string $key): string
    {
        return "{$kind}:{$key}";
    }

    /**
     * Every item $user has bought: in the Shop, plus suits bought with gems before the Shop existed.
     *
     * @return list<string>
     */
    public static function purchased(User $user): array
    {
        return ShopPurchase::where('user_id', $user->id)->select('item')->toBase()
            ->unionAll(GemSpend::where('user_id', $user->id)->select('item')->toBase())
            ->pluck('item')->unique()->values()->all();
    }

    /**
     * The animals or suits $user may wear: the free ones and the ones they bought. Captains own all of theirs.
     *
     * @param  list<string>|null  $purchased  AvatarService::purchased($user), when the caller already has it
     * @return list<string>
     */
    public static function owned(User $user, string $kind, ?array $purchased = null): array
    {
        $purchased ??= self::purchased($user);
        $choices = self::choicesFor($user->role ?? 'student')[$kind === 'animal' ? 'animals' : 'suits'];

        return array_values(array_filter(array_keys($choices),
            fn (string $key) => self::price($kind, $key) === 0 || in_array(self::item($kind, $key), $purchased, true)));
    }

    /** @return list<string> */
    public static function ownedSuits(User $user, ?array $purchased = null): array
    {
        return self::owned($user, 'suit', $purchased);
    }

    /** @return list<string> */
    public static function ownedAnimals(User $user, ?array $purchased = null): array
    {
        return self::owned($user, 'animal', $purchased);
    }

    /**
     * The frames $user may wear at $level: the free default of every rank they've reached, and the ones they bought.
     *
     * @return list<string>
     */
    public static function ownedFrames(User $user, int $level, ?array $purchased = null): array
    {
        $purchased ??= self::purchased($user);

        return array_values(array_filter(array_keys(LevelService::frames()), fn (string $key) => LevelService::frameUnlocked($key, $level)
            && (LevelService::frame($key)['price'] === 0 || in_array(self::item('frame', $key), $purchased, true))));
    }

    /**
     * A valid look from whatever was saved or posted: unknown values fall back to the default,
     * and a coat that belongs to another animal falls back to that animal's first coat.
     * Looks saved before the animals (human bodies) keep their suit, patch, helmet and visor.
     * With a $role, the animal and suit must also be ones that role can pick (captains start as a lion).
     */
    public static function normalize(?array $look, ?string $role = null): array
    {
        $c = self::config();
        $default = in_array($role, ['faculty', 'admin'], true) ? $c['captain_default'] : $c['default'];
        ['animals' => $animals, 'suits' => $suits] = $role ? self::choicesFor($role) : $c;
        $look = array_merge($default, array_filter((array) $look, fn ($v) => $v !== null));
        $pick = fn (string $key, array $options) => array_key_exists((string) $look[$key], $options) ? (string) $look[$key] : (string) $default[$key];

        $animal = $pick('animal', $animals);
        $coats = $c['animals'][$animal]['coats'];

        return [
            'animal' => $animal,
            'coat' => array_key_exists((string) $look['coat'], $coats) ? (string) $look['coat'] : array_key_first($coats),
            'suit' => $pick('suit', $suits),
            'suit_color' => $pick('suit_color', $c['suit_colors']),
            'patch' => $pick('patch', $c['patches']),
            'helmet' => filter_var($look['helmet'], FILTER_VALIDATE_BOOL),
            'visor' => $pick('visor', $c['visors']),
        ];
    }

    /** A random cadet look (captains' animals and suits are left out). */
    public static function random(): array
    {
        $c = self::config();
        $choices = self::choicesFor('student');
        $animal = array_rand($choices['animals']);

        return [
            'animal' => $animal,
            'coat' => array_rand($c['animals'][$animal]['coats']),
            'suit' => array_rand($choices['suits']),
            'suit_color' => array_rand($c['suit_colors']),
            'patch' => array_rand($c['patches']),
            'helmet' => (bool) random_int(0, 1),
            'visor' => array_rand($c['visors']),
        ];
    }

    /**
     * The look as two passes of pixels, [x, y, colour, opacity]: the solid body, then the
     * see-through helmet on top. With the helmet on, the head outside the glass is left out.
     *
     * @return array{0: list<array{int,int,string,float}>, 1: list<array{int,int,string,float}>}
     */
    public static function pixels(?array $look): array
    {
        $c = self::config();
        $look = self::normalize($look);
        $animal = $c['animals'][$look['animal']];
        $coat = $animal['coats'][$look['coat']];

        $palette = $c['ink'] + ['f' => $coat['f'], 'F' => $coat['F'], 'k' => $coat['k'], 'K' => $coat['K']]
            + $c['suit_colors'][$look['suit_color']] + ['p' => $c['patches'][$look['patch']]['p']];
        $helmet = $look['helmet'] ? $c['helmet'] : [];
        $inGlass = fn (int $x, int $y) => isset($helmet[$y]) && $helmet[$y][$x] !== '.';

        $grid = [];
        $layers = [
            [$animal['rows'], (bool) $helmet],
            [$c['suits'][$look['suit']]['rows'], false],
        ];

        foreach ($layers as [$rows, $clip]) {
            foreach ($rows as $y => $row) {
                foreach (str_split($row) as $x => $ch) {
                    if ($ch !== '.' && (! $clip || $inGlass($x, $y))) {
                        $grid[$y][$x] = $palette[$ch];
                    }
                }
            }
        }

        $solid = [];
        ksort($grid);
        foreach ($grid as $y => $cols) {
            ksort($cols);
            foreach ($cols as $x => $color) {
                $solid[] = [$x, $y, $color, 1.0];
            }
        }

        $glass = [];
        foreach ($helmet as $y => $row) {
            foreach (str_split($row) as $x => $ch) {
                if ($ch === '.') {
                    continue;
                }
                $color = in_array($ch, ['v', 'V'], true) ? $c['visors'][$look['visor']] : $palette[$ch];
                $glass[] = [$x, $y, $color, (float) ($c['glass'][$ch] ?? 1)];
            }
        }

        return [$solid, $glass];
    }

    /**
     * The inside of the <svg>: one <rect> per horizontal run of same-coloured pixels.
     */
    public static function rects(?array $look): string
    {
        $out = '';

        foreach (self::pixels($look) as $pass) {
            $run = null;
            foreach ([...$pass, null] as $px) {
                if ($run && $px && $px[1] === $run[1] && $px[0] === $run[0] + $run[4] && $px[2] === $run[2] && $px[3] === $run[3]) {
                    $run[4]++;

                    continue;
                }
                if ($run) {
                    $out .= sprintf('<rect x="%d" y="%d" width="%d" height="1" fill="%s"%s/>', $run[0], $run[1], $run[4], $run[2],
                        $run[3] < 1 ? sprintf(' fill-opacity="%s"', $run[3]) : '');
                }
                $run = $px ? [...$px, 1] : null;
            }
        }

        return $out;
    }

    /**
     * Why this codename can't be used by $user, or null when it's free. Codenames are unique
     * whatever the capitals ("NovaPaws" and "novapaws" are the same astronaut).
     */
    public static function codenameProblem(string $codename, ?User $user = null): ?string
    {
        if (! preg_match(self::CODENAME_PATTERN, $codename)) {
            return 'Use 3 to 20 letters, numbers or _ (no spaces).';
        }
        if (in_array(strtolower($codename), self::RESERVED_CODENAMES, true)) {
            return 'That codename is reserved. Try another one!';
        }
        $taken = User::whereRaw('lower(codename) = ?', [strtolower($codename)])
            ->when($user, fn ($q) => $q->whereKeyNot($user->getKey()))
            ->exists();

        return $taken ? 'Another astronaut already has that codename.' : null;
    }

    /** A free codename idea for this animal, like "CosmicPaws42". */
    public static function suggestCodename(?string $animal = null): string
    {
        $c = self::config()['codenames'];
        $nouns = $c['nouns'][$animal] ?? array_merge(...array_values($c['nouns']));

        for ($i = 0; $i < 10; $i++) {
            $name = $c['adjectives'][array_rand($c['adjectives'])].$nouns[array_rand($nouns)].random_int(10, 99);
            if (! self::codenameProblem($name)) {
                break;
            }
        }

        return $name;
    }
}
