<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ImageGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Astro's supplementary endpoints — kept separate from the live chat stream so
 * normal messages never wait on (or pay for) image generation.
 */
class AstroController extends Controller
{
    public function __construct(
        protected ImageGenerationService $imageGeneration
    ) {}

    /**
     * Draw Analogy.
     *
     * The student clicks the "🎨 Draw Analogy" button on one of Astro's replies.
     * We load THAT assistant message (ownership enforced), extract its analogy,
     * generate an illustration, and return the image URL + structured data.
     *
     * This is a distinct request from normal chat — a normal message never
     * triggers image generation.
     */
    public function drawAnalogy(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'conversation_id' => ['required', 'integer'],
            'message_id' => ['required', 'integer'],
        ]);

        $user = $request->user();

        // Ownership: the conversation must belong to the authenticated student.
        $conversation = Conversation::where('user_id', $user->id)
            ->find($data['conversation_id']);

        if (! $conversation) {
            return response()->json(['error' => 'Conversation not found.'], 404);
        }

        // The message must belong to that conversation AND be an assistant reply.
        $message = $conversation->messages()
            ->where('id', $data['message_id'])
            ->where('role', 'assistant')
            ->first();

        if (! $message) {
            return response()->json(['error' => 'That message cannot be illustrated.'], 404);
        }

        if (trim($message->content) === '') {
            return response()->json(['error' => 'There is nothing to draw for this message yet.'], 422);
        }

        try {
            $result = $this->imageGeneration->drawFromMessage($message->content);
        } catch (\Throwable $e) {
            Log::error('Draw Analogy failed', ['message' => $e->getMessage()]);

            return response()->json([
                'error' => 'Astro couldn\'t draw that analogy right now. Please try again in a moment.',
            ], 502);
        }

        return response()->json([
            'ok' => true,
            'image_url' => $result['image_url'],
            'title' => $result['title'],
            'caption' => $result['caption'],
            'elements' => $result['elements'],
        ]);
    }

    /**
     * Lightweight health/identity probe (used by the UI to confirm Astro is up).
     */
    public function ping(): JsonResponse
    {
        return response()->json(['astro' => 'online']);
    }
}
