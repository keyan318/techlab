<?php

namespace App\Http\Controllers;

use App\Exceptions\NvidiaNimException;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\DeckGeneratorService;
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
        protected NvidiaNimService $nimService,
        protected DeckGeneratorService $deckService
    ) {
    }

    /**
     * Show the chat interface.
     */
    public function index(Request $request): View|RedirectResponse
    {
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
            ->where('id', $conversation)
            ->firstOrFail();

        return response()->json([
            'conversation' => $this->formatConversation($conversation),
            'messages' => $this->formatMessages(
                $conversation->messages()->latest('id')->get()
            ),
        ]);
    }

    /**
     * Format a conversation for API responses.
     */
    protected function formatConversation(Conversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'title' => $conversation->title,
            'created_at' => $conversation->created_at->toISOString(),
            'updated_at' => $conversation->updated_at->toISOString(),
        ];
    }

    /**
     * Format messages for API responses.
     */
    protected function formatMessages($messages): array
    {
        return $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'role' => $message->role,
                'content' => $message->content,
                'created_at' => $message->created_at->toISOString(),
            ];
        })->toArray();
    }

    /**
     * Send a message to Astro and get a response.
     */
    public function send(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = $request->input('message');
        $conversationId = $request->input('conversation_id');

        // Get or create conversation
        if ($conversationId) {
            $conversation = Conversation::where('user_id', Auth::id())
                ->where('id', $conversationId)
                ->firstOrFail();
        } else {
            $conversation = Conversation::create([
                'user_id' => Auth::id(),
                'title' => Str::limit($userMessage, 50),
            ]);
        }

        // Save user message
        $userMsg = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
        ]);

        // Get conversation history for context
        $history = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
            })
            ->toArray();

        // Stream response from NVIDIA NIM
        $fullResponse = '';

        try {
            $fullResponse = $this->nimService->stream($history, function ($chunk) use (&$fullResponse) {
                echo $chunk;
                ob_flush();
                flush();
            });

            // Save assistant message
            Message::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $fullResponse,
            ]);

            // Update conversation timestamp
            $conversation->update([
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message_id' => $userMsg->id,
                'response' => $fullResponse,
            ]);
        } catch (NvidiaNimException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format conversations for API responses.
     */
    protected function formatConversations($conversations): array
    {
        return $conversations->map(function ($conversation) {
            return [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'created_at' => $conversation->created_at->toISOString(),
                'updated_at' => $conversation->updated_at->toISOString(),
            ];
        })->toArray();
    }
}