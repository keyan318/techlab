<?php

namespace App\Http\Controllers;

use App\Exceptions\InfographicGenerationException;
use App\Exceptions\InfographicSourceException;
use App\Services\Infographic\InfographicContentService;
use App\Services\Infographic\InfographicSourceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Infographic Studio endpoint.
 *
 * Kept separate from the live chat stream so normal messages never wait on (or
 * pay for) infographic generation. All NVIDIA calls happen server-side; the
 * API key never reaches the browser.
 */
class InfographicController extends Controller
{
    public function __construct(
        protected InfographicContentService $content,
        protected InfographicSourceResolver $resolver
    ) {}

    /**
     * POST /chat/infographic
     *
     * Accepts the student's learning material (pasted text, current chat
     * transcript, or an uploaded file) and returns a structured, image-enhanced
     * infographic plan for the in-/chat viewer.
     */
    public function generate(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'source_text' => ['nullable', 'string', 'max:60000'],
            'transcript' => ['nullable', 'array'],
            'transcript.*.role' => ['nullable', 'string'],
            'transcript.*.content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        // 1) Resolve the real learning material. No material -> friendly nudge.
        try {
            $source = $this->resolver->resolve($request);
        } catch (InfographicSourceException $e) {
            return response()->json(['error' => $e->getMessage(), 'kind' => 'source'], $e->status());
        }

        if (trim($source) === '') {
            return response()->json([
                'error' => "Chat with Astro first — the infographic is built from your current conversation.",
                'kind' => 'empty_source',
            ], 422);
        }

        // 2) Generate the structured lesson plan (Astro = reasoning model).
        set_time_limit(0);
        try {
            $plan = $this->content->generate($source);
        } catch (InfographicSourceException $e) {
            return response()->json(['error' => $e->getMessage(), 'kind' => 'source'], $e->status());
        } catch (InfographicGenerationException $e) {
            $status = $e->category === 'NIM_RATE_LIMIT' ? 429 : $e->status();
            Log::warning('Infographic generation failed', [
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
            Log::error('Infographic backend error', [
                'request_id' => $rid,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => "Astro's backend hit a problem building your infographic. Please try again.",
                'kind' => 'server',
                'request_id' => $rid,
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'infographic' => $plan,
        ]);
    }
}
