<?php

namespace App\Http\Controllers;

use App\Models\GemExchange;
use App\Models\ShopPurchase;
use App\Models\User;
use App\Services\AvatarService;
use App\Services\LevelService;
use App\Services\StudentDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * The Shop (sidebar): frames, animals and suits bought with XP, and gems traded for XP.
 *
 * Buying spends XP from the balance, never the level (the level counts XP earned). Each rank's frames go on sale
 * when the student reaches that rank; its default frame is free. Every purchase and trade locks the student's row,
 * so two clicks can't spend the same XP or gems twice.
 */
class ShopController extends Controller
{
    private const KINDS = ['frame', 'animal', 'suit'];

    public function index(Request $request): View
    {
        $user = $request->user();
        $earned = StudentDashboardService::earnedXp($user);
        $level = LevelService::levelFor($earned);
        $purchased = AvatarService::purchased($user);
        $look = AvatarService::normalize($user->avatar, 'student');
        $c = AvatarService::config();
        $choices = AvatarService::choicesFor('student');

        $frames = collect(LevelService::frames())->groupBy('tier')->map(fn ($frames, $tier) => [
            'rank' => LevelService::rank(LevelService::tierStart($tier)),
            'from' => LevelService::tierStart($tier),
            'unlocked' => $tier <= LevelService::tierFor($level),
            'frames' => $frames->values()->all(),
        ])->values()->all();

        return view('student.shop', [
            'user' => $user,
            'look' => $look,
            'level' => $level,
            'progress' => LevelService::progress($earned),
            'tier' => LevelService::rank($level),
            'xp' => StudentDashboardService::balance($user),
            'gems' => StudentDashboardService::gems($user),
            'xpPerGem' => (int) config('ranks.xp_per_gem'),
            'frameTiers' => $frames,
            'animals' => collect($choices['animals'])->map(fn ($a, $key) => ['key' => $key, 'label' => $a['label'], 'price' => AvatarService::price('animal', $key),
                'look' => ['animal' => $key, 'coat' => array_key_first($c['animals'][$key]['coats'])] + $look])->values()->all(),
            'suits' => collect($choices['suits'])->map(fn ($s, $key) => ['key' => $key, 'label' => $s['label'], 'price' => AvatarService::price('suit', $key),
                'look' => ['suit' => $key, 'helmet' => false] + $look])->values()->all(),
            'state' => self::state($user, $level, $purchased),
        ]);
    }

    /** Buy a frame, animal or suit with XP. Bought once; buying it again is free and changes nothing. */
    public function buy(Request $request, string $kind, string $key): JsonResponse
    {
        $user = $request->user();
        $item = self::item($kind, $key);
        if (! $item) {
            return response()->json(['ok' => false, 'error' => "That's not for sale."], 404);
        }

        $level = StudentDashboardService::levelOf($user);
        if ($kind === 'frame' && ! LevelService::frameUnlocked($key, $level)) {
            $from = LevelService::tierStart($item['tier']);

            return response()->json(['ok' => false, 'error' => "Reach level {$from} to unlock the {$item['name']} frame."], 403);
        }

        $spent = DB::transaction(function () use ($user, $kind, $key, $item) {
            DB::table('users')->where('id', $user->id)->lockForUpdate()->first();

            if (in_array(AvatarService::item($kind, $key), AvatarService::purchased($user), true)) {
                return 0;
            }
            if (StudentDashboardService::balance($user) < $item['price']) {
                return null;
            }
            ShopPurchase::create(['user_id' => $user->id, 'item' => AvatarService::item($kind, $key), 'cost' => $item['price']]);

            return $item['price'];
        });
        $xp = StudentDashboardService::balance($user);

        if ($spent === null) {
            return response()->json(['ok' => false, 'xp' => $xp,
                'error' => 'You need '.number_format($item['price'] - $xp).' more XP for this. Finish a lesson or trade some gems!'], 402);
        }

        return response()->json(['ok' => true, 'spent' => $spent] + self::state($user, $level));
    }

    /** Wear something already owned: a frame, an animal or a suit. */
    public function equip(Request $request): JsonResponse
    {
        $user = $request->user();
        ['kind' => $kind, 'key' => $key] = $request->validate([
            'kind' => ['required', 'in:'.implode(',', self::KINDS)],
            'key' => ['required', 'string', 'max:40'],
        ]);
        $level = StudentDashboardService::levelOf($user);
        $owned = match ($kind) {
            'frame' => AvatarService::ownedFrames($user, $level),
            'animal' => AvatarService::ownedAnimals($user),
            'suit' => AvatarService::ownedSuits($user),
        };
        if (! in_array($key, $owned, true)) {
            return response()->json(['ok' => false, 'error' => 'Buy it first, then you can wear it.'], 403);
        }

        if ($kind === 'frame') {
            // Picking their own rank's default means "always my rank's default", so it keeps upgrading as they rank up.
            $user->forceFill(['frame' => $key === LevelService::defaultFrame($level) ? null : $key])->save();
        } else {
            $look = AvatarService::normalize($user->avatar, 'student');
            if ($kind === 'animal' && $look['animal'] !== $key) {
                $from = array_keys(config("avatar.animals.{$look['animal']}.coats"));
                $to = array_keys(config("avatar.animals.{$key}.coats"));
                $at = array_search($look['coat'], $from, true);
                $look['coat'] = $to[min($at === false ? 0 : $at, count($to) - 1)];
            }
            $look[$kind] = $key;
            $user->update(['avatar' => AvatarService::normalize($look, 'student')]);
        }

        return response()->json(['ok' => true] + self::state($user->refresh(), $level));
    }

    /** Trade gems for spendable XP (config ranks.xp_per_gem). The XP never counts toward the level. */
    public function exchange(Request $request): JsonResponse
    {
        $user = $request->user();
        ['gems' => $gems] = $request->validate(['gems' => ['required', 'integer', 'min:1', 'max:100000']]);
        $rate = (int) config('ranks.xp_per_gem');

        $ok = DB::transaction(function () use ($user, $gems, $rate) {
            DB::table('users')->where('id', $user->id)->lockForUpdate()->first();
            if (StudentDashboardService::gems($user) < $gems) {
                return false;
            }
            GemExchange::create(['user_id' => $user->id, 'gems' => $gems, 'xp' => $gems * $rate]);

            return true;
        });

        if (! $ok) {
            return response()->json(['ok' => false, 'gems' => StudentDashboardService::gems($user),
                'error' => "You don't have that many gems. Finish a module to earn ".StudentDashboardService::GEMS_PER_MODULE.'!'], 422);
        }

        return response()->json(['ok' => true, 'traded' => $gems, 'gained' => $gems * $rate] + self::state($user, StudentDashboardService::levelOf($user)));
    }

    /** A Shop item and its price, or null when it isn't for sale (free items aren't sold). */
    private static function item(string $kind, string $key): ?array
    {
        $item = match ($kind) {
            'frame' => LevelService::frame($key),
            'animal', 'suit' => array_key_exists($key, AvatarService::choicesFor('student')[$kind === 'animal' ? 'animals' : 'suits'])
                ? ['price' => AvatarService::price($kind, $key)] : null,
            default => null,
        };

        return $item && $item['price'] > 0 ? $item : null;
    }

    /** What the page keeps in step after every action: balances, what's owned, what's worn. */
    private static function state(User $user, int $level, ?array $purchased = null): array
    {
        $purchased ??= AvatarService::purchased($user);
        $look = AvatarService::normalize($user->avatar, 'student');

        return [
            'xp' => StudentDashboardService::balance($user),
            'gems' => StudentDashboardService::gems($user),
            'owned' => [
                'frame' => AvatarService::ownedFrames($user, $level, $purchased),
                'animal' => AvatarService::ownedAnimals($user, $purchased),
                'suit' => AvatarService::ownedSuits($user, $purchased),
            ],
            'worn' => ['frame' => LevelService::wornFrame($user->frame, $level), 'animal' => $look['animal'], 'suit' => $look['suit']],
        ];
    }
}
