<?php

namespace App\Http\Controllers;

use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Models\Conversation;
use App\Services\Quiz\QuizContentService;
use App\Services\Quiz\QuizSourceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Quiz Studio endpoint — backend only for Step 3 (no UI).
 *
 * Kept separate from the live chat stream so normal messages never wait on
 * (or pay for) quiz generation. All NVIDIA calls happen server-side; the API
 * key never reaches the browser.
 *
 * Security: conversation_id is loaded server-side with ownership enforcement.
 * No transcript from the browser is trusted.
 */
class QuizController extends Controller
{
    public function __construct(
        protected QuizContentService $content,
        protected QuizSourceResolver $resolver
    ) {}

    /**
     * POST /chat/quiz
     *
     * Accepts a conversation_id, loads THAT conversation's messages
     * (ownership enforced), and returns a structured 5-question quiz.
     */
    public function generate(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'conversation_id' => ['required', 'integer'],
        ]);

        $user = $request->user();

        $conversation = Conversation::where('user_id', $user->id)
            ->find($data['conversation_id']);

        if (! $conversation) {
            return response()->json(['error' => 'Conversation not found.'], 404);
        }

        // Resolve source from persisted messages (not from browser transcript).
        $source = $this->resolver->fromConversation($conversation);

        // Fast local insufficient-content short-circuit (no NIM call).
        if (trim($source) === '') {
            return response()->json([
                'error' => "Chat with Astro first — the quiz is built from your current conversation.",
                'kind' => 'insufficient_content',
            ], 422);
        }

        if ($this->resolver->looksInsufficient($source)) {
            // Still try the model path via QuizContentService for richer messaging,
            // but if it also says insufficient we surface that. For now, short-circuit
            // with the same 422 so the future UI gets a consistent kind.
            // Let QuizContentService be the authority — if source is borderline,
            // let the model decide. So only short-circuit when source is clearly tiny.
            if (mb_strlen(trim($source)) < 80) {
                return response()->json([
                    'error' => "Not enough educational material in this conversation to build a quiz. Chat a bit more with Astro about a topic first.",
                    'kind' => 'insufficient_content',
                ], 422);
            }
        }

        set_time_limit(0);

        try {
            $quiz = $this->content->generate($source);
        } catch (QuizSourceException $e) {
            return response()->json(['error' => $e->getMessage(), 'kind' => 'insufficient_content'], $e->status());
        } catch (QuizGenerationException $e) {
            $status = $e->category === 'NIM_RATE_LIMIT' ? 429 : $e->status();
            Log::warning('Quiz generation failed', [
                'category' => $e->category,
                'request_id' => $e->requestId,
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'kind' => 'generation',
                'category' => $e->category,
                'request_id' => $e->requestId,
            ], $status);
        } catch (\Throwable $e) {
            $rid = bin2hex(random_bytes(6));
            Log::error('Quiz backend error', [
                'request_id' => $rid,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => "Astro's backend hit a problem building your quiz. Please try again.",
                'kind' => 'server',
                'request_id' => $rid,
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'quiz' => $quiz,
        ]);
    }
}
