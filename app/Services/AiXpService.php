<?php

namespace App\Services;

use App\Models\AiUsage;
use App\Models\DailyReward;
use App\Models\LessonProgress;
use App\Models\QuizAnswer;
use App\Models\User;
use App\Models\XpSpend;
use Illuminate\Support\Facades\DB;

/**
 * Astro's AI costs XP, so students earn AI help by learning (and can't spam it).
 * Each day starts with a few free uses (AI_FREE_CHATS_PER_DAY chat messages, AI_FREE_STUDIO_PER_DAY
 * Studio tools); after that every use costs its price in StudentDashboardService::AI_COSTS.
 * Only students pay: faculty and admins use Astro for free and nothing is recorded for them.
 *
 * Studio tools use charge() (lock, check, pay — then refund() if the tool fails). The live chat
 * uses quote() before the answer and record() after it, so only one database read stands between
 * the student and Astro's first word (the database is remote, every round trip is felt).
 */
class AiXpService
{
    private const CHAT_FEATURES = ['chat', 'chat_file'];

    public static function charges(User $user): bool
    {
        return ($user->role ?? 'student') === 'student';
    }

    public static function isChat(string $feature): bool
    {
        return in_array($feature, self::CHAT_FEATURES, true);
    }

    /**
     * Pay for one use of $feature now (free while today's free uses last). Safe under concurrent
     * requests: the student's row is locked, so two tabs can't both spend the same XP.
     *
     * @return array{ok: bool, cost: int, usageId: ?int, balance: int, freeLeft: int, error: ?string}
     */
    public function charge(User $user, string $feature): array
    {
        if (! self::charges($user)) {
            return $this->free();
        }

        return DB::transaction(function () use ($user, $feature) {
            $quote = $this->price($feature, $this->snapshot($user, lock: true));
            if ($quote['ok']) {
                $quote['usageId'] = $this->record($user, $feature, $quote['cost']);
            }

            return $quote;
        });
    }

    /**
     * What one use of $feature would cost right now, without paying yet (one read, no lock).
     * The caller pays with record() once the AI has answered.
     *
     * @return array{ok: bool, cost: int, usageId: null, balance: int, freeLeft: int, error: ?string}
     */
    public function quote(User $user, string $feature): array
    {
        return self::charges($user) ? $this->price($feature, $this->snapshot($user)) : $this->free();
    }

    /** Save one use and what it cost. Returns its id (null for faculty, who aren't charged). */
    public function record(User $user, string $feature, int $cost): ?int
    {
        return self::charges($user) ? AiUsage::create(['user_id' => $user->id, 'feature' => $feature, 'cost' => $cost])->id : null;
    }

    /** The AI call failed: give the XP (and the free use) back. */
    public function refund(?int $usageId): void
    {
        if ($usageId !== null) {
            AiUsage::whereKey($usageId)->delete();
        }
    }

    /**
     * What the chat page shows: XP, today's free uses left, and the prices.
     *
     * @return array{charged: bool, balance: int, freeChatsLeft: int, freeStudioLeft: int, costs: array<string, int>}
     */
    public function status(User $user): array
    {
        $base = ['charged' => self::charges($user), 'costs' => StudentDashboardService::AI_COSTS];

        if (! $base['charged']) {
            return $base + ['balance' => 0, 'freeChatsLeft' => 0, 'freeStudioLeft' => 0];
        }

        $row = $this->snapshot($user);

        return $base + [
            'balance' => $row['balance'],
            'freeChatsLeft' => max(0, StudentDashboardService::AI_FREE_CHATS_PER_DAY - $row['chatsToday']),
            'freeStudioLeft' => max(0, StudentDashboardService::AI_FREE_STUDIO_PER_DAY - $row['studioToday']),
        ];
    }

    /**
     * Free while today's free uses last, otherwise the feature's price — if the student can afford it.
     * 'freeLeft' is what's left of today's free uses of this kind after this one.
     *
     * @param  array{chatsToday: int, studioToday: int, balance: int}  $row
     * @return array{ok: bool, cost: int, usageId: null, balance: int, freeLeft: int, error: ?string}
     */
    private function price(string $feature, array $row): array
    {
        $freeLeft = self::isChat($feature)
            ? StudentDashboardService::AI_FREE_CHATS_PER_DAY - $row['chatsToday']
            : StudentDashboardService::AI_FREE_STUDIO_PER_DAY - $row['studioToday'];
        $cost = $freeLeft > 0 ? 0 : StudentDashboardService::AI_COSTS[$feature];

        if ($row['balance'] < $cost) {
            return ['ok' => false, 'cost' => $cost, 'usageId' => null, 'balance' => $row['balance'], 'freeLeft' => 0,
                'error' => "You're out of XP, astronaut! This costs {$cost} XP and you have {$row['balance']}. Finish a lesson to power Astro up."];
        }

        return ['ok' => true, 'cost' => $cost, 'usageId' => null, 'balance' => $row['balance'] - $cost,
            'freeLeft' => max(0, $freeLeft - 1), 'error' => null];
    }

    /** @return array{ok: true, cost: 0, usageId: null, balance: 0, freeLeft: 0, error: null} */
    private function free(): array
    {
        return ['ok' => true, 'cost' => 0, 'usageId' => null, 'balance' => 0, 'freeLeft' => 0, 'error' => null];
    }

    /**
     * Today's chat and Studio uses plus the student's balance, in ONE query (optionally locking
     * their row so concurrent purchases wait their turn).
     *
     * @return array{chatsToday: int, studioToday: int, balance: int}
     */
    private function snapshot(User $user, bool $lock = false): array
    {
        $today = fn () => AiUsage::where('user_id', $user->id)->where('created_at', '>=', now()->startOfDay())->selectRaw('count(*)');

        $query = DB::table('users')->where('id', $user->id)
            ->selectSub($today()->whereIn('feature', self::CHAT_FEATURES), 'chats_today')
            ->selectSub($today()->whereNotIn('feature', self::CHAT_FEATURES), 'studio_today')
            ->selectSub($user->lessonProgress()->withoutGlobalScope('active')->selectRaw(LessonProgress::XP_COUNT), 'lessons')
            ->selectSub(QuizAnswer::where('user_id', $user->id)->selectRaw('coalesce(sum(xp), 0)'), 'quiz_xp')
            ->selectSub(DailyReward::where('user_id', $user->id)->selectRaw('coalesce(sum(xp), 0)'), 'daily_xp')
            ->selectSub(XpSpend::where('user_id', $user->id)->selectRaw('coalesce(sum(cost), 0)'), 'spent')
            ->selectSub(AiUsage::where('user_id', $user->id)->selectRaw('coalesce(sum(cost), 0)'), 'ai_spent');

        $row = ($lock ? $query->lockForUpdate() : $query)->first();

        return [
            'chatsToday' => (int) $row->chats_today,
            'studioToday' => (int) $row->studio_today,
            'balance' => StudentDashboardService::xpFor((int) $row->lessons, (int) $row->quiz_xp + (int) $row->daily_xp, (int) $row->spent + (int) $row->ai_spent),
        ];
    }
}
