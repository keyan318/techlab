<?php

namespace App\Http\Controllers;

use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Models\Conversation;
use App\Services\Quiz\QuizSourceResolver;
use App\Services\Reports\ReportContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Reports Studio endpoint. Mirrors QuizController: the conversation is
 * loaded server-side with ownership enforced; no browser transcript is trusted.
 */
class ReportController extends Controller
{
    public function __construct(
        protected ReportContentService $content,
        protected QuizSourceResolver $resolver
    ) {}

    public function generate(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'conversation_id' => ['required', 'integer'],
        ]);

        $conversation = Conversation::where('user_id', $request->user()->id)->find($data['conversation_id']);
        if (! $conversation) {
            return response()->json(['error' => 'Conversation not found.'], 404);
        }

        $source = $this->resolver->fromConversation($conversation);
        if (mb_strlen(trim($source)) < 80) {
            return response()->json([
                'error' => 'Chat with Astro first — reports are built from your current conversation.',
                'kind' => 'insufficient_content',
            ], 422);
        }

        set_time_limit(0);

        try {
            $report = $this->content->generate($source);
        } catch (QuizSourceException $e) {
            return response()->json(['error' => $e->getMessage(), 'kind' => 'insufficient_content'], $e->status());
        } catch (QuizGenerationException $e) {
            $status = match ($e->category) {
                'NIM_RATE_LIMIT' => 429,
                'NIM_MODEL_ERROR' => 503,
                default => $e->status(),
            };
            Log::warning('Report generation failed', ['category' => $e->category, 'request_id' => $e->requestId]);

            return response()->json([
                'error' => $e->getMessage(),
                'kind' => 'generation',
                'category' => $e->category,
                'request_id' => $e->requestId,
            ], $status);
        } catch (\Throwable $e) {
            $rid = bin2hex(random_bytes(6));
            Log::error('Report backend error', ['request_id' => $rid, 'message' => $e->getMessage()]);

            return response()->json([
                'error' => "Astro's backend hit a problem building your report. Please try again.",
                'kind' => 'server',
                'request_id' => $rid,
            ], 500);
        }

        return response()->json(['ok' => true, 'report' => $report]);
    }
}
