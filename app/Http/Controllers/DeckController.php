<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DeckController extends Controller
{
    /**
     * Store a newly created deck.
     *
     * Expected JSON body: { conversation_id, slides_json, theme? }
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
            'slides_json'     => ['required', 'array'],
            'theme'           => ['nullable', 'string'],
        ]);

        $deck = Deck::create([
            'conversation_id' => $request->integer('conversation_id'),
            'slides_json'     => $request->validated('slides_json'),
            'theme'           => $request->validated('theme'),
        ]);

        return response()->json(['id' => $deck->id], 201);
    }

    /**
     * Return the deck's theme and slides as JSON (served at
     * /api/decks/{id}.json for the Bolt Slides engine).
     */
    public function show(int $id): JsonResponse
    {
        $deck = Deck::findOrFail($id);

        return response()->json([
            'theme' => $deck->theme,
            'slides' => $deck->slides_json,
        ]);
    }

    /**
     * Serve the built Bolt Slides app (no Blade layout).
     */
    public function showPage(int $id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path = public_path('decks-app/index.html');

        if (! file_exists($path)) {
            abort(404, 'Bolt Slides build not found — run `npm run build` inside resources/decks-app/.');
        }

        return Response::file($path, [
            'Content-Type'        => 'text/html',
            'Cache-Control'       => 'no-cache',
            'X-Deck-Id'           => (string) $id,
        ]);
    }
}
