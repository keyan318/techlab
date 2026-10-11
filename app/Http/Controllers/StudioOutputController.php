<?php

namespace App\Http\Controllers;

use App\Models\StudioOutput;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Saved Studio outputs (quiz, flashcards, key terms, infographic, report, notes),
 * stored per user so they survive refreshes, new chats, and other devices.
 */
class StudioOutputController extends Controller
{
    private const KEEP = 20;

    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user(), 401);
        $items = StudioOutput::where('user_id', $request->user()->id)
            ->latest('updated_at')->limit(self::KEEP)->get()
            ->map(fn ($o) => json_decode($o->payload, true))
            ->filter()->values();

        return response()->json(['items' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user(), 401);
        $data = $request->validate([
            'item' => ['required', 'array'],
            'item.id' => ['required', 'string', 'max:64'],
            'item.type' => ['required', 'string', 'in:quiz,flashcards,keyterms,infographic,report,note'],
        ]);

        $json = json_encode($data['item']);
        abort_if(strlen($json) > 2_000_000, 413, 'Output too large.');

        $user = $request->user();
        StudioOutput::updateOrCreate(
            ['user_id' => $user->id, 'uid' => $data['item']['id']],
            ['type' => $data['item']['type'], 'payload' => $json],
        );

        // Keep only the newest outputs.
        $old = StudioOutput::where('user_id', $user->id)->latest('updated_at')->skip(self::KEEP)->take(100)->pluck('id');
        if ($old->isNotEmpty()) {
            StudioOutput::whereIn('id', $old)->delete();
        }

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, string $uid): JsonResponse
    {
        abort_unless($request->user(), 401);
        StudioOutput::where('user_id', $request->user()->id)->where('uid', $uid)->delete();

        return response()->json(['ok' => true]);
    }
}
