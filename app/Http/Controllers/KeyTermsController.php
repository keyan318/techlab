<?php

namespace App\Http\Controllers;

use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Models\Conversation;
use App\Services\KeyTerms\KeyTermsContentService;
use App\Services\Quiz\QuizSourceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Key Terms Studio endpoint. Mirrors QuizController: the conversation is
 * loaded server-side with ownership enforced; no browser transcript is trusted.
 */
class KeyTermsController extends Controller
{
    public function __construct(
        protected KeyTermsContentService $content,
        protected QuizSourceResolver $resolver
    ) {}

    public function generate(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'conversation_id' => ['required', 'integer'],
            'count' => ['nullable', 'integer', 'min:'.config('keyterms.min_terms', 5), 'max:'.config('keyterms.max_terms', 30)],
        ]);
        $count = (int) ($data['count'] ?? config('keyterms.default_terms', 10));

        $conversation = Conversation::where('user_id', $request->user()->id)->find($data['conversation_id']);
        if (! $conversation) {
            return response()->json(['error' => 'Conversation not found.'], 404);
        }

        $source = $this->resolver->fromConversation($conversation);
        if (mb_strlen(trim($source)) < 80) {
            return response()->json([
                'error' => 'Chat with Astro first — key terms are picked from your current conversation.',
                'kind' => 'insufficient_content',
            ], 422);
        }

        set_time_limit(0);

        try {
            $result = $this->content->generate($source, $count);
        } catch (QuizSourceException $e) {
            return response()->json(['error' => $e->getMessage(), 'kind' => 'insufficient_content'], $e->status());
        } catch (QuizGenerationException $e) {
            $status = match ($e->category) {
                'NIM_RATE_LIMIT' => 429,
                'NIM_MODEL_ERROR' => 503,
                default => $e->status(),
            };
            Log::warning('Key terms generation failed', ['category' => $e->category, 'request_id' => $e->requestId]);

            return response()->json([
                'error' => $e->getMessage(),
                'kind' => 'generation',
                'category' => $e->category,
                'request_id' => $e->requestId,
            ], $status);
        } catch (\Throwable $e) {
            $rid = bin2hex(random_bytes(6));
            Log::error('Key terms backend error', ['request_id' => $rid, 'message' => $e->getMessage()]);

            return response()->json([
                'error' => "Astro's backend hit a problem picking your key terms. Please try again.",
                'kind' => 'server',
                'request_id' => $rid,
            ], 500);
        }

        return response()->json(['ok' => true, 'terms' => $result]);
    }
}
