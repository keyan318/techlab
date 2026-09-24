<?php

namespace App\Http\Controllers;

use App\Exceptions\NvidiaNimException;
use App\Http\Controllers\Concerns\XpGatedItem;
use App\Services\CourseProgressService;
use App\Services\NvidiaNimService;
use App\Services\StudentDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Astro's live help inside a Citadel Sim lab. Unlike the static Python hint, this is a real NIM
 * chat that can see the student's exact terminal transcript — but the server never sees the lab's
 * own file contents or flags (those only ever exist in citadel-sim's client-side JSON and on the
 * student's own screen). The client sends up whatever the student can already see; Astro reasons
 * over that, never over hidden lab data it was never given.
 *
 * XP is spent once per lab attempt (same XpSpend shape as the hint, see XpGatedItem) and unlocks
 * unlimited follow-up questions in that session — Astro needs the running transcript on every
 * message anyway, and a second paywall mid-diagnosis would break the point of a live chat.
 */
class LessonLabHelpController extends Controller
{
    use XpGatedItem;

    private const ITEM = 'lab_help';

    private const GUARDRAIL = <<<'TXT'
        You are Astro, coaching a student who is stuck INSIDE a hands-on cybersecurity lab (Citadel Sim, a
        simulated Windows command prompt). Below is the lab's current mission (title, instructions, objectives
        with pass/fail) followed by the student's actual terminal transcript so far - everything they can already
        see on their own screen. You were not given the lab's underlying files or flags; work only from what's
        shown below.

        Coach them toward the next command:
        - Diagnose from the transcript why their last attempt failed, or what to try next, in plain terms.
        - Tell them the exact next command to type and explain briefly why - spoonfeed it, the same direct style
          as the lab's own instructions. This is an early lesson: be concrete, not vague hinting.
        - Never state a file's contents or a flag's text verbatim, even if it already appears in the transcript
          below. If they want to see it again, tell them which command shows it (usually `type <file>`) - don't
          just repeat it for them. Reading it themselves is the point.
        - Keep it short: a few sentences, not a lecture.

        --- LAB STATE (what the student can already see) ---

        TXT;

    /** GET: price, the student's XP, and whether they've already unlocked this lab's help chat. */
    public function status(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        return response()->json($this->payload($request, $this->xpBought($request->user()->id, $slug, $module, $lesson, self::ITEM)));
    }

    /** POST: spend the XP (if they can afford it and haven't already) and unlock the chat. */
    public function unlock(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $result = $this->xpUnlock($request->user(), $slug, $module, $lesson, self::ITEM, StudentDashboardService::XP_LAB_HELP_COST);

        if ($result['error'] !== null) {
            return response()->json($this->payload($request, false) + ['error' => $result['error']], 402);
        }

        return response()->json($this->payload($request, true) + ['ok' => true, 'spent' => $result['spent']]);
    }

    /** POST: ask Astro a question. Requires the chat to already be unlocked for this lab attempt. */
    public function ask(Request $request, string $slug, string $module, string $lesson): JsonResponse
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        if ($error = $this->guard($request, $slug, $module, $lesson)) {
            return $error;
        }

        $user = $request->user();
        if (! $this->xpBought($user->id, $slug, $module, $lesson, self::ITEM)) {
            return response()->json(['error' => "Unlock Astro's help first."], 403);
        }

        $validated = $request->validate([
            'lab' => 'required|string|in:'.CourseProgressService::labFor($module, $lesson, $slug),
            'question' => 'required|string|max:600',
            'context' => 'nullable|string|max:8000',
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'required_with:history|in:user,assistant',
            'history.*.content' => 'required_with:history|string|max:2000',
        ], [
            'lab.in' => 'Wrong lab for this lesson.',
        ]);

        $messages = array_map(
            fn (array $m) => ['role' => $m['role'], 'content' => $m['content']],
            $validated['history'] ?? []
        );
        $messages[] = ['role' => 'user', 'content' => $validated['question']];

        $sourceContext = self::GUARDRAIL.trim($validated['context'] ?? '(no terminal output yet)');

        try {
            $answer = app(NvidiaNimService::class)->stream($messages, function (string $chunk): void {
                // Buffered: nothing to forward mid-stream, the return value carries the full text.
            }, $sourceContext);
        } catch (NvidiaNimException $e) {
            Log::error('NvidiaNimException in LessonLabHelpController::ask: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            Log::error('Unexpected exception in LessonLabHelpController::ask: '.$e->getMessage());

            return response()->json(['error' => 'Astro could not respond just now. Try again.'], 500);
        }

        return response()->json(['answer' => $answer]);
    }

    /** The shared XP guard, plus: only a lesson whose lab runs in Citadel Sim has this chat. */
    private function guard(Request $request, string $slug, string $module, string $lesson): ?JsonResponse
    {
        if ($error = $this->xpGuard($request, $slug, $module, $lesson)) {
            return $error;
        }
        if (CourseProgressService::labFor($module, $lesson, $slug) === null
            || CourseProgressService::simFor($module, $lesson, $slug) !== 'citadel') {
            return response()->json(['error' => 'This lesson has no lab Astro can help with.'], 404);
        }

        return null;
    }

    /** @return array<string, mixed> */
    private function payload(Request $request, bool $unlocked): array
    {
        $balance = StudentDashboardService::balance($request->user());

        return [
            'cost' => StudentDashboardService::XP_LAB_HELP_COST,
            'balance' => $balance,
            'canAfford' => $balance >= StudentDashboardService::XP_LAB_HELP_COST,
            'unlocked' => $unlocked,
        ];
    }
}
