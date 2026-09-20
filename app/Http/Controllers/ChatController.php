<?php

namespace App\Http\Controllers;

use App\Exceptions\AttachmentException;
use App\Exceptions\NvidiaNimException;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\AstroRouter;
use App\Services\AttachmentService;
use App\Services\DeckGeneratorService;
use App\Services\LessonSourceService;
use App\Services\NvidiaNimService;
use App\Services\WebSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function __construct(
        protected NvidiaNimService $nimService,
        protected DeckGeneratorService $deckService,
        protected LessonSourceService $lessonSources,
        protected AstroRouter $router
    ) {}

    /**
     * Show the chat interface.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Check if user is authenticated
        if (! Auth::check()) {
            // Redirect to login or show guest view
            return redirect('/login');
        }

        if ((Auth::user()->role ?? 'student') === 'teacher') {
            return redirect(route('teacher.chat'));
        }

        return view('student.chat', [
            'userName' => Auth::user()->name ?? 'Explorer',
            'userInitial' => mb_strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)),
            'userRole' => ucfirst(Auth::user()->role ?? 'student'),
            'modelLabel' => config('nvidia_nim.model_label', 'Astro · NVIDIA NIM'),
            'planetCatalog' => $this->lessonSources->catalog(),
            // ?source=programming/M1/lesson-02 — "Ask Astro about this lesson" deep link.
            'preselectSource' => $this->parseSourceParam((string) $request->query('source', '')),
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
     * Have Astro name a conversation from what it is actually about (rather than
     * the first words the student typed). Falls back to the current title on any failure.
     */
    public function title(Request $request, int $conversation): JsonResponse
    {
        $conversation = Conversation::where('user_id', $request->user()->id)
            ->where('id', $conversation)
            ->firstOrFail();

        $excerpt = $conversation->messages()->limit(4)->get(['role', 'content'])
            ->map(fn ($m) => ($m->role === 'assistant' ? 'Astro' : 'Student').': '.Str::limit(trim((string) $m->content), 400))
            ->implode("\n");

        if ($excerpt !== '') {
            try {
                $raw = $this->nimService->complete([
                    ['role' => 'system', 'content' => 'You write short titles for chat conversations. Reply with ONLY a title of 2 to 6 words that captures the topic of the conversation. Title Case, no quotes, no trailing punctuation, no emoji.'],
                    ['role' => 'user', 'content' => "Conversation:\n".$excerpt."\n\nTitle:"],
                ], ['max_tokens' => 24, 'temperature' => 0.3]);

                $title = trim(strtok(trim($raw), "\n") ?: '', " \t\"'`*#.");
                if ($title !== '') {
                    $conversation->timestamps = false;   // naming must not reorder Recents
                    $conversation->update(['title' => Str::limit($title, 60, '')]);
                }
            } catch (\Throwable $e) {
                Log::warning('Conversation title generation failed: '.$e->getMessage());
            }
        }

        return response()->json(['conversation' => $this->formatConversation($conversation->fresh())]);
    }

    /**
     * Toggle a conversation's pinned state (owner only).
     */
    public function pin(Request $request, int $conversation): JsonResponse
    {
        $conversation = Conversation::where('user_id', $request->user()->id)->where('id', $conversation)->firstOrFail();

        // Query-builder update: pinning must not touch updated_at (it would reorder Recents).
        Conversation::whereKey($conversation->id)->toBase()->update(['pinned_at' => $conversation->pinned_at ? null : now()]);

        return response()->json(['conversation' => $this->formatConversation($conversation->fresh())]);
    }

    /**
     * Delete a conversation and its messages (owner only).
     */
    public function destroy(Request $request, int $conversation): JsonResponse
    {
        $conversation = Conversation::where('user_id', $request->user()->id)->where('id', $conversation)->firstOrFail();

        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Format a conversation for API responses.
     */
    protected function formatConversation(Conversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'title' => $conversation->title,
            'pinned' => $conversation->pinned_at !== null,
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
                'attachments' => AttachmentService::publicMeta($message->attachments),
                'created_at' => $message->created_at->toISOString(),
            ];
        })->toArray();
    }

    /**
     * Send a message to Astro.
     *
     * Two response shapes over one code path:
     *  - `Accept: text/event-stream` (the chat UI): a Server-Sent-Events stream
     *    that forwards each NIM delta to the browser the moment it arrives.
     *  - anything else: the original buffered JSON `{success, conversation_id,
     *    message_id, response, sources}` (fallback / API clients).
     *
     * SSE events (each `data:` is JSON):
     *   start  {}                              stream is open (sent immediately)
     *   delta  {t}                             a chunk of visible answer text
     *   end    {sources}                       answer is complete (before any DB work)
     *   done   {conversation_id,message_id,saved}   persisted; ids are now known
     *   error  {error}                         NIM failed; partial text (if any) already sent
     *
     * Persistence happens AFTER the answer has been streamed, so no database
     * write sits between the model's first token and the browser.
     */
    public function send(Request $request): JsonResponse|StreamedResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'message' => 'required_without:files|nullable|string|max:2000',
            'files' => 'nullable|array|max:'.AttachmentService::MAX_FILES,
            'files.*' => 'file|max:'.AttachmentService::maxFileKb(),
            'sources' => 'nullable|array|max:'.LessonSourceService::MAX_SOURCES,
            'sources.*.planet' => 'required|string',
            'sources.*.module' => 'required|string',
            'sources.*.lesson' => 'required|string',
        ], [
            'files.max' => 'Too many files — you can attach up to '.AttachmentService::MAX_FILES.' per message.',
            'files.*.max' => ':attribute is too large — the limit is '.AttachmentService::mb(AttachmentService::maxFileKb()).' per file.',
            'files.*.uploaded' => ':attribute is too large — the limit is '.AttachmentService::mb(AttachmentService::maxFileKb()).' per file.',
            'files.*.file' => ':attribute could not be uploaded.',
        ], collect($request->file('files') ?? [])->mapWithKeys(fn ($f, $i) => ["files.$i" => '"'.$f->getClientOriginalName().'"'])->all());

        $userId = Auth::id();
        $userMessage = trim((string) $request->input('message'));

        // Attachments: text/PDF become prompt text, images go to the vision model.
        $attachments = ['items' => [], 'images' => []];
        if ($request->hasFile('files')) {
            try {
                $attachments = app(AttachmentService::class)->process($request->file('files'));
            } catch (AttachmentException $e) {
                return response()->json(['error' => $e->getMessage()], 422);
            }

            if ($attachments['images'] && ! AttachmentService::visionEnabled()) {
                return response()->json(['error' => "Astro can't look at images yet — image understanding isn't switched on. Attach a PDF or a text file instead."], 422);
            }

            if ($userMessage === '') {
                $userMessage = 'Please take a look at what I attached.';
            }
        }
        $attachmentItems = $attachments['items'];
        $visionModel = $attachments['images'] ? (string) config('nvidia_nim.vision_model') : null;

        // Connected Planet lessons -> grounding context for Astro (empty when none).
        $planetSources = $this->lessonSources->resolve($request->input('sources', []));
        $sourceContext = $this->lessonSources->promptBlock($planetSources);
        $sourceLabels = array_column($planetSources, 'label');

        // Existing conversation: prior history, in ONE query that also enforces ownership
        // (the only DB read before the first token). A NEW conversation is created after streaming.
        $conversationId = $request->input('conversation_id') ? (int) $request->input('conversation_id') : null;
        $history = [];
        if ($conversationId !== null) {
            $rows = Message::query()
                ->join('conversations', 'conversations.id', '=', 'messages.conversation_id')
                ->where('conversations.user_id', $userId)
                ->where('conversations.id', $conversationId)
                ->orderBy('messages.created_at')->orderBy('messages.id')
                ->get(['messages.role', 'messages.content', 'messages.attachments']);

            if ($rows->isEmpty()) {
                // No messages yet — still refuse a conversation that isn't theirs (404).
                Conversation::where('user_id', $userId)->where('id', $conversationId)->firstOrFail();
            }

            $history = $rows->map(fn ($m) => ['role' => $m->role, 'content' => $m->content.AttachmentService::promptBlock($m->attachments)])->all();
        }
        $turnText = $userMessage.AttachmentService::promptBlock(array_filter($attachmentItems, fn ($i) => $i['kind'] === 'text'));
        $history[] = ['role' => 'user', 'content' => $attachments['images']
            ? array_merge([['type' => 'text', 'text' => $turnText]], array_map(fn ($img) => ['type' => 'image_url', 'image_url' => ['url' => $img['url']]], $attachments['images']))
            : $turnText];

        // Fast vs Deep for this message (deterministic rules, no extra AI call).
        $route = $this->router->route($userMessage, count($planetSources));

        $wantsStream = str_contains((string) $request->header('Accept'), 'text/event-stream');
        $startedAt = microtime(true);

        // Extensive research only: real web sources. Quick questions skip the web entirely.
        $web = app(WebSearchService::class);
        $researching = WebSearchService::enabled() && $web->needsResearch($userMessage);

        if (! $wantsStream) {
            $webResults = $researching ? $web->search($userMessage) : [];

            return $this->sendBuffered($userId, $conversationId, $userMessage, $history, trim($sourceContext."\n\n".$web->promptBlock($webResults)), $sourceLabels, $startedAt, $route, $attachmentItems, $visionModel, $webResults);
        }

        return response()->stream(function () use ($userId, $conversationId, $userMessage, $history, $sourceContext, $sourceLabels, $startedAt, $route, $attachmentItems, $visionModel, $web, $researching) {
            // A long NIM stream must not be killed by PHP limits or a closed tab:
            // we still want the finished answer persisted.
            set_time_limit(0);
            ignore_user_abort(true);

            $emit = function (string $event, array $data = []): void {
                echo "event: {$event}\n";
                echo 'data: '.json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE)."\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                flush();
            };

            $emit('start');

            $webResults = [];
            if ($researching) {
                $emit('researching');
                $webResults = $web->search($userMessage);
                $emit('web', ['results' => array_map(fn ($r) => array_diff_key($r, ['snippet' => 1]), $webResults)]);
                $sourceContext = trim($sourceContext."\n\n".$web->promptBlock($webResults));
            }

            $answer = '';
            $firstDeltaMs = null;
            $failed = null;

            try {
                $answer = $this->nimService->stream($history, function (string $chunk) use ($emit, &$firstDeltaMs, &$answer, $startedAt) {
                    $firstDeltaMs ??= (int) round((microtime(true) - $startedAt) * 1000);
                    $emit('delta', ['t' => $chunk]);   // to the browser first...
                    $answer .= $chunk;                 // ...then remembered, so a mid-stream failure keeps what was shown
                }, $sourceContext, $route, $visionModel);
            } catch (NvidiaNimException $e) {
                $failed = $e->getMessage();
                Log::error('NvidiaNimException in ChatController::send: '.$failed);
            } catch (\Throwable $e) {
                $failed = 'An unexpected error occurred';
                Log::error('Unexpected exception in ChatController::send: '.$e->getMessage());
            }

            $streamedMs = (int) round((microtime(true) - $startedAt) * 1000);

            if ($failed !== null) {
                $emit('error', ['error' => $failed]);
            } else {
                $emit('end', ['sources' => $sourceLabels]);
            }

            // Persist AFTER the stream. Partial text from an interrupted stream is kept.
            $ids = ['conversation_id' => null, 'message_id' => null, 'saved' => false];
            try {
                $ids = $this->persistExchange($userId, $conversationId, $userMessage, $answer, $attachmentItems) + ['saved' => true];
            } catch (\Throwable $e) {
                Log::error('Failed to persist Astro exchange: '.$e->getMessage());
            }
            $emit('done', $ids);

            Log::info('Astro chat timing', [
                'first_delta_ms' => $firstDeltaMs,
                'stream_ms' => $streamedMs,
                'total_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'chars' => strlen($answer),
                'sources' => count($sourceLabels),
                'failed' => $failed !== null,
                'astro_mode' => $route['mode'],
                'astro_reason' => $route['reason'],
            ]);
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=utf-8',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
            'X-Astro-Mode' => $route['mode'],
        ]);
    }

    /**
     * Original buffered behaviour: run the whole stream, then return one JSON body.
     */
    protected function sendBuffered(int $userId, ?int $conversationId, string $userMessage, array $history, string $sourceContext, array $sourceLabels, float $startedAt, array $route, array $attachmentItems = [], ?string $visionModel = null, array $webResults = []): JsonResponse
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $answer = '';
        try {
            $answer = $this->nimService->stream($history, function (string $chunk) {
                // Buffered: nothing to forward; the return value carries the full text.
            }, $sourceContext, $route, $visionModel);
            $response = null;
        } catch (NvidiaNimException $e) {
            Log::error('NvidiaNimException in ChatController::send: '.$e->getMessage());
            $response = response()->json(['error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            Log::error('Unexpected exception in ChatController::send: '.$e->getMessage());
            $response = response()->json(['error' => 'An unexpected error occurred'], 500);
        }

        $ids = $this->persistExchange($userId, $conversationId, $userMessage, $answer, $attachmentItems);

        Log::info('Astro chat timing', [
            'first_delta_ms' => null,
            'stream_ms' => null,
            'total_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'chars' => strlen($answer),
            'sources' => count($sourceLabels),
            'failed' => $response !== null,
            'buffered' => true,
            'astro_mode' => $route['mode'],
            'astro_reason' => $route['reason'],
        ]);

        return $response ?? response()->json([
            'success' => true,
            'conversation_id' => $ids['conversation_id'],
            'message_id' => $ids['message_id'],
            'response' => $answer,
            'sources' => $sourceLabels,
            'web' => array_map(fn ($r) => array_diff_key($r, ['snippet' => 1]), $webResults),
        ]);
    }

    /**
     * Save the user message + Astro's answer (creating the conversation if new).
     * An empty answer (NIM failed before any text) saves only the user message.
     *
     * Database round trips are the cost here (each is a network hop), so: at most one
     * conversation insert, ONE multi-row insert for both messages, and one timestamp
     * update for an existing conversation.
     *
     * @return array{conversation_id:int,message_id:int}
     */
    protected function persistExchange(int $userId, ?int $conversationId, string $userMessage, string $answer, array $attachmentItems = []): array
    {
        $now = now();
        $isNew = $conversationId === null;

        if ($isNew) {
            $conversationId = Conversation::create([
                'user_id' => $userId,
                'title' => Str::limit($userMessage, 50),
            ])->id;
        }

        $rows = [['conversation_id' => $conversationId, 'role' => 'user', 'content' => $userMessage, 'attachments' => $attachmentItems ? json_encode($attachmentItems) : null, 'created_at' => $now, 'updated_at' => $now]];
        if (trim($answer) !== '') {
            $rows[] = ['conversation_id' => $conversationId, 'role' => 'assistant', 'content' => $answer, 'attachments' => null, 'created_at' => $now, 'updated_at' => $now];
        }

        // Multi-row INSERT ... RETURNING (Postgres/SQLite). Nothing here can conflict, so the
        // "ignore" is inert; it is simply the framework's portable way to get the new ids back.
        $saved = DB::table('messages')->insertOrIgnoreReturning($rows, ['id', 'role']);

        // A brand-new conversation already carries this timestamp; only bump an existing one.
        if (! $isNew) {
            Conversation::whereKey($conversationId)->update(['updated_at' => $now]);
        }

        return [
            'conversation_id' => $conversationId,
            'message_id' => (int) $saved->firstWhere('role', 'user')->id,
        ];
    }

    /**
     * Parse "planet/M1/lesson-02" into a source ref, or null if it isn't a real lesson.
     */
    protected function parseSourceParam(string $raw): ?array
    {
        $parts = explode('/', $raw);
        if (count($parts) !== 3) {
            return null;
        }
        [$planet, $module, $lesson] = $parts;

        return $this->lessonSources->resolve([compact('planet', 'module', 'lesson')])
            ? compact('planet', 'module', 'lesson')
            : null;
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
                'pinned' => $conversation->pinned_at !== null,
                'created_at' => $conversation->created_at->toISOString(),
                'updated_at' => $conversation->updated_at->toISOString(),
            ];
        })->toArray();
    }
}
