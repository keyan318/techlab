<?php

namespace App\Http\Middleware;

use App\Services\AiXpService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Charges a student XP for one Studio tool, e.g. `ai.xp:quiz` (price in StudentDashboardService::AI_COSTS).
 * Not enough XP → 402 and the AI is never called. If the tool fails, the XP is given back.
 */
class ChargeAiXp
{
    public function __construct(private AiXpService $xp) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);   // the controller answers 401
        }

        $charge = $this->xp->charge($user, $feature);
        if (! $charge['ok']) {
            return response()->json([
                'ok' => false, 'kind' => 'xp', 'error' => $charge['error'], 'cost' => $charge['cost'], 'balance' => $charge['balance'],
            ], 402);
        }

        $response = $next($request);

        // Studio tools report failure either with an error status or with `ok: false`.
        $failed = $response->getStatusCode() >= 400
            || ($response instanceof JsonResponse && ($response->getData(true)['ok'] ?? true) === false);
        if ($failed) {
            $this->xp->refund($charge['usageId']);

            return $response;
        }

        if (AiXpService::charges($user)) {
            $response->headers->set('X-Xp-Balance', (string) $charge['balance']);
            $response->headers->set('X-Ai-Free-Left', (string) $charge['freeLeft']);
        }

        return $response;
    }
}
