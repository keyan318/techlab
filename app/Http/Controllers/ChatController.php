<?php

namespace App\Http\Controllers;

use App\Exceptions\NvidiaNimException;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\NvidiaNimService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(
        protected NvidiaNimService $nim
    ) {}

    /**
     * TechLab Chat — the main student interface after login.
     *
     * Renders the ChatGPT-like shell and seeds it with the student's existing
     * conversations so the history sidebar is populated on first paint.
     */
    public function index(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }

        if ((Auth::user()->role ?? 'student') === 'teacher') {
            return redirect(route('teacher.dashboard'));
        }

        return view('student.chat', [
            'userName' => Auth::user()->name ?? 'Explorer',
            'userInitial' => mb_strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)),
            'userRole' => ucfirst(Auth::user()->role ?? 'student'),
            'modelLabel' => config('nvidia_nim.model_label', 'Astro · NVIDIA NIM'),
            'conversations' => $this->formatConversations(
                Auth::user()->conversations()->latest('updated_at')->get()
            ),
        ]);
    }

    /**
     * List the authenticated user's conversations (JSON, for the sidebar).
     * A student can only ever see their own conversations.
     */
    public function conversations(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $list = $this->formatConversations(
            $request->user()->conversations()->latest('updated_at')->get()
        );

        return response()->json(['conversations' => $list]);
    }

    /**
     * Load a single conversation's messages. Ownership is enforced: a student
     * may only open a conversation that belongs to them.
     */
    public function show(Request $request, int $conversation): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $conversation = Conversation::where('user_id', $request->user()->id)
            ->find($conversation);

        if (! $conversation) {
            return response()->json(['error' => 'Conversation not found.'], 404);
        }

        // Skip empty placeholder assistant rows left behind by an aborted stream.
        $messages = $conversation->messages()
            ->where(function ($q) {
                $q->where('role', '!=', 'assistant')
                    ->orWhere('content', '!=', '');
            })
            ->orderBy('id')
            ->get(['id', 'role', 'content', 'created_at'])
            ->map(fn ($m) => [
                'id' => $m->id,
                'role' => $m->role,
                'content' => $m->content,
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message and stream the assistant's reply back to the browser.
     *
     * Protocol (text stream, UTF-8):
     *   line 1  : JSON meta  { conversation_id, user_message_id }
     *   remainder: assistant content (raw text, may contain newlines)
     *   end     : "\n__END__" on success, or "\n__ERROR__:{json}" on failure
     *
     * The conversation (and the user message) are persisted before streaming
     * so history is preserved even if the model call fails partway through.
     */
    public function send(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:8000'],
            'conversation_id' => ['nullable', 'integer'],
            'level' => ['sometimes', 'in:auto,beginner,intermediate,advanced'],
            // `context` is optional free-form text. The frontend sends an empty
            // string ("") when there is no course topic, and Laravel's
            // ConvertEmptyStringsToNull middleware turns "" into null, so the
            // rule must accept null — otherwise a valid chat message fails
            // validation and Laravel replies with a 302 redirect (which the
            // browser follows to /chat, surfacing the generic error fallback).
            'context' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $content = trim($data['message']);

        if ($content === '') {
            return response()->json(['error' => 'Message cannot be empty.'], 422);
        }

        // Course-aware, level-aware system prompt for Astro.
        $systemPrompt = $this->nim->systemPrompt([
            'level' => $data['level'] ?? 'auto',
            'context' => $data['context'] ?? '',
        ]);

        // Resolve an existing conversation (ownership enforced) or start a new one.
        // Wrapped so any persistence failure returns a clean JSON error (not an
        // HTML 500) — that lets the frontend show the real reason instead of the
        // generic "Astro couldn't respond right now".
        try {
            if (! empty($data['conversation_id'])) {
                $conversation = Conversation::where('user_id', $user->id)
                    ->find($data['conversation_id']);

                if (! $conversation) {
                    return response()->json(['error' => 'Conversation not found.'], 404);
                }
            } else {
                $conversation = Conversation::create([
                    'user_id' => $user->id,
                    'title' => $this->deriveTitle($content),
                ]);
            }

            $userMessage = $conversation->messages()->create([
                'role' => 'user',
                'content' => $content,
            ]);

            // Build the message history (system + prior turns) for the model.
            $history = $conversation->messages()->orderBy('id')->get();
            $apiMessages = $history
                ->map(fn (Message $m) => ['role' => $m->role, 'content' => $m->content])
                ->prepend(['role' => 'system', 'content' => $systemPrompt])
                ->all();

            $conversationId = $conversation->id;
            $userMessageId = $userMessage->id;

            // Create the assistant row up front (before streaming) so the client
            // receives its id in the meta line and the Draw Analogy button can target
            // it. An aborted/failed stream deletes this placeholder row.
            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => '',
            ]);
            $assistantMessageId = $assistantMessage->id;
        } catch (\Throwable $e) {
            $rid = bin2hex(random_bytes(6));
            Log::error('Astro failed to persist message before streaming', [
                'request_id' => $rid,
                'message' => $e->getMessage(),
            ]);

            $detail = config('app.debug')
                ? ('Could not save your message: '.$e->getMessage())
                : "Astro couldn't save your message right now.";

            return response()->json(['error' => $detail, 'request_id' => $rid], 500);
        }

        $service = $this->nim;

        return response()->stream(function () use ($service, $apiMessages, $conversation, $conversationId, $userMessageId, $assistantMessage, $assistantMessageId) {
            // A 550B reasoning model can take well over PHP's default 30s
            // max_execution_time — both for its "thinking" phase before the
            // first token AND for follow-ups that send the full history.
            // Lift the limit for this request so the stream isn't killed
            // mid-response. A "time limit exceeded" fatal is NOT catchable by
            // the try/catch below, so without this the client gets an
            // uncaught 500 HTML dump instead of the stream. Guzzle's own
            // timeout=300 stays the real upper bound; any network overrun
            // there is caught and surfaced as a categorized __ERROR__.
            set_time_limit(0);

            // Flush output as soon as we echo.
            if (function_exists('ob_implicit_flush')) {
                ob_implicit_flush(1);
            }

            // Release the session write lock so other requests aren't blocked
            // while this (potentially long) stream is in flight.
            if (session()->isStarted()) {
                session()->save();
            }

            echo json_encode([
                'conversation_id' => $conversationId,
                'user_message_id' => $userMessageId,
                'assistant_message_id' => $assistantMessageId,
            ])."\n";

            $full = '';

            try {
                $full = $service->stream($apiMessages, function (string $delta) {
                    echo $delta;
                });

                echo "\n__END__";

                $assistantMessage->update(['content' => $full]);
            } catch (NvidiaNimException $e) {
                Log::warning('Astro NIM stream aborted', [
                    'request_id' => $e->requestId,
                    'category' => $e->category,
                    'message' => $e->getMessage(),
                ]);

                $assistantMessage->delete();

                echo "\n__ERROR__:".json_encode([
                    'message' => $e->getMessage(),
                    'category' => $e->category,
                    'request_id' => $e->requestId,
                ]);
            } catch (\Throwable $e) {
                $rid = bin2hex(random_bytes(6));
                Log::error('Astro backend error', [
                    'request_id' => $rid,
                    'message' => $e->getMessage(),
                ]);

                $assistantMessage->delete();

                echo "\n__ERROR__:".json_encode([
                    'message' => "Astro's backend encountered an internal error while processing the request.",
                    'category' => 'SERVER_ERROR',
                    'request_id' => $rid,
                ]);
            }
        }, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Delete a conversation and its messages. Ownership enforced.
     */
    public function destroy(Request $request, int $conversation): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $conversation = Conversation::where('user_id', $request->user()->id)
            ->find($conversation);

        if (! $conversation) {
            return response()->json(['error' => 'Conversation not found.'], 404);
        }

        $conversation->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Build the sidebar-friendly shape for a collection of conversations.
     */
    protected function formatConversations($conversations): array
    {
        return $conversations->map(function (Conversation $c) {
            $firstUser = $c->messages()
                ->where('role', 'user')
                ->orderBy('id')
                ->first();

            return [
                'id' => $c->id,
                'title' => $c->title,
                'preview' => $firstUser ? Str::limit($firstUser->content, 60) : '',
                'message_count' => $c->messages()->count(),
                'updated_at' => $c->updated_at->toIso8601String(),
                'created_at' => $c->created_at->toIso8601String(),
            ];
        })->all();
    }

    /**
     * Derive a short, readable title from the first message.
     */
    protected function deriveTitle(string $content): string
    {
        $firstLine = strtok($content, "\n") ?: $content;

        return Str::limit(preg_replace('/\s+/', ' ', trim($firstLine)), 50) ?: 'New chat';
    }
}
