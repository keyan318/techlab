<?php

namespace App\Http\Controllers;

use App\Exceptions\QuizGenerationException;
use App\Exceptions\QuizSourceException;
use App\Models\Conversation;
use App\Services\Ppt\PptBuilder;
use App\Services\Ppt\PptContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Faculty Studio "PPT": builds a designed .pptx from the faculty member's current Astro chat.
 * The conversation is loaded server-side with ownership enforced (same model as ReportController).
 */
class PptController extends Controller
{
    public function __construct(
        protected PptContentService $content,
        protected PptBuilder $builder
    ) {}

    public function generate(Request $request): JsonResponse|BinaryFileResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        if ((Auth::user()->role ?? 'student') !== 'faculty') {
            return response()->json(['error' => 'Faculty only.'], 403);
        }

        $data = $request->validate(['conversation_id' => ['required', 'integer']]);

        $conversation = Conversation::where('user_id', Auth::id())->find($data['conversation_id']);
        if (! $conversation) {
            return response()->json(['error' => 'Conversation not found.'], 404);
        }

        $source = $this->content->transcript($conversation);
        if (mb_strlen(trim($source)) < 40) {
            return response()->json([
                'error' => 'Chat with Astro first — tell it the topic and the look you want, then press PPT.',
                'kind' => 'insufficient_content',
            ], 422);
        }

        set_time_limit(0);

        try {
            $deck = $this->content->generate($source);
            $file = $this->builder->build($deck);
        } catch (QuizSourceException $e) {
            return response()->json(['error' => $e->getMessage(), 'kind' => 'insufficient_content'], $e->status());
        } catch (QuizGenerationException $e) {
            Log::warning('PPT generation failed', ['category' => $e->category, 'request_id' => $e->requestId]);

            return response()->json([
                'error' => $e->getMessage(),
                'kind' => 'generation',
                'category' => $e->category,
            ], match ($e->category) {
                'NIM_RATE_LIMIT' => 429,
                'NIM_MODEL_ERROR' => 503,
                default => $e->status(),
            });
        } catch (\Throwable $e) {
            $rid = bin2hex(random_bytes(6));
            Log::error('PPT backend error', ['request_id' => $rid, 'message' => $e->getMessage()]);

            return response()->json([
                'error' => "Astro's backend hit a problem building your presentation. Please try again.",
                'kind' => 'server',
                'request_id' => $rid,
            ], 500);
        }

        $name = (Str::slug($deck['title']) ?: 'presentation').'.pptx';

        return response()->download($file, $name, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'X-Ppt-Slides' => (string) count($deck['slides']),
            'Access-Control-Expose-Headers' => 'Content-Disposition, X-Ppt-Slides',
        ])->deleteFileAfterSend(true);
    }
}
