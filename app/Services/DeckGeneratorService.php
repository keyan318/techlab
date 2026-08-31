<?php

namespace App\Services;

use App\Http\Controllers\DeckController;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

/**
 * Generates a slide deck from an Astro conversation.
 *
 * Steps:
 *  1. Load the conversation's messages (user + Astro turns) from the DB.
 *  2. Send them to Nemotron with a strict prompt that returns ONLY valid JSON:
 *     an array of { type, props } objects where "type" is one of the supported
 *     slide block types (Cover, Agenda, Split, Comparison, StatGrid, Timeline,
 *     CodeWindow, Steps, Quote) and "props" matches that component's real prop
 *     shape.
 *  3. Parse the response; if it's not valid JSON or uses an unknown type,
 *     retry ONCE with the error appended to the prompt.
 *  4. Save the result via DeckController::store() (conversation_id, the parsed
 *     slides array, a chosen theme).
 *  5. Return the new deck's id.
 */
class DeckGeneratorService
{
    /** @var array<string, string[]> Map of valid slide types to their required prop keys */
    protected const VALID_TYPES = [
        'Cover'       => ['title'],                    // kicker?, title, subtitle?, image?, foot?
        'Agenda'      => ['items'],                   // kicker?, title?, items: AgendaItem[]
        'Split'       => ['title', 'media'],          // kicker?, title, body?, media, flip?
        'Comparison'  => ['cols', 'rows'],            // cols: string[], rows: CompRow[], highlight?
        'StatGrid'    => ['stats'],                   // kicker?, title?, stats: Stat[]
        'Timeline'    => ['items'],                   // items: TimelineItem[]
        'CodeWindow'  => ['code'],                    // title?, code, highlight?
        'Steps'       => ['items'],                   // kicker?, title?, items: Step[]
        'Quote'       => ['text'],                    // text, name?, role?, img?, image?
    ];

    /** @var string[] List of valid type names for error messages */
    protected const VALID_TYPE_NAMES = [
        'Cover', 'Agenda', 'Split', 'Comparison', 'StatGrid',
        'Timeline', 'CodeWindow', 'Steps', 'Quote',
    ];

    public function __construct(
        protected NvidiaNimService $nim,
        protected DeckController $deckController
    ) {}

    /**
     * Generate a deck from a conversation and persist it.
     *
     * @return int  the new deck's id
     */
    public function generateDeckFromConversation(int $conversationId): int
    {
        // 1. Load conversation messages
        $messages = $this->loadConversationMessages($conversationId);
        if ($messages === []) {
            throw new \RuntimeException("Conversation {$conversationId} has no messages to generate a deck from.");
        }

        // 2. Build the prompt for the model
        $prompt = $this->buildGenerationPrompt($messages);

        // 3. Call Nemotron with retry logic
        $slides = $this->callNemotronWithRetry($prompt);

        // 4. Validate slide types and props
        $this->validateSlides($slides);

        // 5. Choose a theme (can be expanded later)
        $theme = $this->chooseTheme($messages);

        // 6. Save via DeckController::store()
        $deckId = $this->saveDeck($conversationId, $slides, $theme);

        return $deckId;
    }

    /**
     * Load all messages for a conversation in chronological order.
     *
     * @return array<int, array{role: string, content: string}>
     */
    protected function loadConversationMessages(int $conversationId): array
    {
        $conversation = Conversation::with(['messages' => fn ($q) => $q->orderBy('id')])
            ->findOrFail($conversationId);

        return $conversation->messages
            ->map(fn (Message $m) => [
                'role'    => $m->role,      // 'user' or 'assistant'
                'content' => $m->content,
            ])
            ->toArray();
    }

    /**
     * Build the strict system + user prompt for deck generation.
     */
    protected function buildGenerationPrompt(array $messages): string
    {
        $conversationText = $this->formatMessagesForPrompt($messages);

        return <<<PROMPT
You are a slide deck author for a premium React presentation engine (Vite + React, "Bolt Slides").

Your task: Convert the following Astro conversation into a structured slide deck.

The conversation contains a student learning a technical topic with Astro (the AI teacher). Create slides that would make a great presentation of what was taught.

---

CONVERSATION:
{$conversationText}

---

OUTPUT FORMAT (STRICT):
Return ONLY a valid JSON array of slide objects. Each object must have:
- "type": ONE of these exact strings: {{VALID_TYPES}}
- "props": an object matching that component's prop shape (see below)

VALID TYPES & PROP SHAPES:

1. Cover
   { "type": "Cover", "props": { "kicker?": string, "title": ReactNode, "subtitle?": string, "image?": string, "foot?": string } }

2. Agenda
   { "type": "Agenda", "props": { "kicker?": string, "title?": string, "items": (string | { title: string, hint?: string })[] } }

3. Split
   { "type": "Split", "props": { "kicker?": string, "title": ReactNode, "body?": ReactNode, "media": ReactNode, "flip?": boolean } }

4. Comparison
   { "type": "Comparison", "props": { "cols": string[], "rows": { label: string, values: (boolean | string)[] }[], "highlight?": number } }
   - cols[0] is the label column; highlight indexes the VALUE columns (0 = first value column)

5. StatGrid
   { "type": "StatGrid", "props": { "kicker?": string, "title?": string, "stats": { value?: ReactNode, label: string, caption?: string }[] } }

6. Timeline
   { "type": "Timeline", "props": { "items": { time: string, title: string, body?: ReactNode }[] } }

7. CodeWindow
   { "type": "CodeWindow", "props": { "title?": string, "code": string, "highlight?": number[] } }
   - highlight is 1-indexed line numbers

8. Steps
   { "type": "Steps", "props": { "kicker?": string, "title?": string, "items": { title: string, body?: ReactNode }[] } }

9. Quote
   { "type": "Quote", "props": { "text": string, "name?": string, "role?": string, "img?": string, "image?": string } }

---

RULES:
- Output VALID JSON ONLY — no markdown, no code fences, no commentary.
- Every slide MUST have a valid "type" from the list above.
- "props" MUST match the shape exactly. Unknown keys will be rejected.
- ReactNode values can be strings (they'll be rendered as JSX text).
- For "media" in Split: use a string describing the visual (e.g., "<BrowserFrame url='...' />" or "<img src='...' />").
- Aim for 6–12 slides. Cover first, then logical flow.
- Base the deck ENTIRELY on the conversation content — no fabrication.
- If the conversation covers code, include a CodeWindow slide.
- If it has a process, use Steps or Timeline.
- If it compares things, use Comparison.
- If it has key metrics, use StatGrid.

Return the JSON array now.
PROMPT;
    }

    /**
     * Format messages as a readable transcript for the prompt.
     */
    protected function formatMessagesForPrompt(array $messages): string
    {
        return collect($messages)
            ->map(fn (array $m) => ucfirst($m['role']) . ': ' . $m['content'])
            ->implode("\n\n");
    }

    /**
     * Call Nemotron with retry-on-invalid-JSON logic.
     *
     * @return array<int, array{type: string, props: array}>
     */
    protected function callNemotronWithRetry(string $prompt): array
    {
        $systemPrompt = 'You are a slide deck author. Output ONLY a valid JSON array matching the specified schema. No prose, no markdown, no code fences.';

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $prompt],
        ];

        // First attempt
        $result = $this->nim->completeJson($messages, [
            'temperature' => 0.3,
            'max_tokens'  => 4096,
        ]);

        if ($this->isValidSlidesArray($result)) {
            return $result;
        }

        // Retry once with error context
        $errorMsg = $this->buildValidationErrorMessage($result);
        $retryPrompt = $prompt . "\n\n---\nPREVIOUS ATTEMPT FAILED:\n" . $errorMsg . "\n\nReturn ONLY the corrected JSON array.";

        $retryMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $retryPrompt],
        ];

        $retryResult = $this->nim->completeJson($retryMessages, [
            'temperature' => 0.2,
            'max_tokens'  => 4096,
        ]);

        if ($this->isValidSlidesArray($retryResult)) {
            return $retryResult;
        }

        // Final failure — log and throw
        Log::error('Deck generation failed after retry', [
            'conversation_id' => $messages[1]['content'] ?? 'unknown',
            'first_attempt'   => $result,
            'retry_attempt'   => $retryResult,
        ]);

        throw new \RuntimeException('Nemotron failed to generate valid deck JSON after 2 attempts.');
    }

    /**
     * Check if the decoded result is a valid slides array.
     */
    protected function isValidSlidesArray(?array $data): bool
    {
        if (! is_array($data)) {
            return false;
        }
        if ($data === []) {
            return false;
        }
        foreach ($data as $slide) {
            if (! is_array($slide)) {
                return false;
            }
            if (! isset($slide['type'], $slide['props'])) {
                return false;
            }
            if (! in_array($slide['type'], self::VALID_TYPE_NAMES, true)) {
                return false;
            }
            if (! is_array($slide['props'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate all slides have known types and required props.
     *
     * @param array<int, array{type: string, props: array}> $slides
     */
    protected function validateSlides(array $slides): void
    {
        foreach ($slides as $i => $slide) {
            $type = $slide['type'];
            $props = $slide['props'];

            if (! in_array($type, self::VALID_TYPE_NAMES, true)) {
                throw new \InvalidArgumentException("Slide {$i}: unknown type '{$type}'. Valid: " . implode(', ', self::VALID_TYPE_NAMES));
            }

            $required = self::VALID_TYPES[$type] ?? [];
            foreach ($required as $req) {
                if (! array_key_exists($req, $props)) {
                    throw new \InvalidArgumentException("Slide {$i} ({$type}): missing required prop '{$req}'");
                }
            }
        }
    }

    /**
     * Build a helpful error message for the retry prompt.
     */
    protected function buildValidationErrorMessage(?array $data): string
    {
        if (! is_array($data)) {
            return 'Response was not a JSON array (got ' . gettype($data) . ').';
        }
        if ($data === []) {
            return 'Response was an empty array — at least one slide is required.';
        }

        $errors = [];
        foreach ($data as $i => $slide) {
            if (! is_array($slide)) {
                $errors[] = "Slide {$i}: not an object";
                continue;
            }
            if (! isset($slide['type'])) {
                $errors[] = "Slide {$i}: missing 'type'";
                continue;
            }
            if (! in_array($slide['type'], self::VALID_TYPE_NAMES, true)) {
                $errors[] = "Slide {$i}: unknown type '{$slide['type']}'";
                continue;
            }
            if (! isset($slide['props']) || ! is_array($slide['props'])) {
                $errors[] = "Slide {$i} ({$slide['type']}): 'props' missing or not an object";
                continue;
            }
            $required = self::VALID_TYPES[$slide['type']] ?? [];
            foreach ($required as $req) {
                if (! array_key_exists($req, $slide['props'])) {
                    $errors[] = "Slide {$i} ({$slide['type']}): missing required prop '{$req}'";
                }
            }
        }

        return $errors ? implode('; ', $errors) : 'Unknown validation error.';
    }

    /**
     * Choose a theme based on conversation content (placeholder for now).
     */
    protected function chooseTheme(array $messages): string
    {
        // Simple heuristic: if conversation mentions code/programming, use a technical theme
        $text = collect($messages)->pluck('content')->implode(' ');

        if (preg_match('/\b(code|program|function|class|api|database|sql|javascript|typescript|python|react|laravel)\b/i', $text)) {
            return 'dark-technical';
        }

        return 'default';
    }

    /**
     * Persist the deck via DeckController::store().
     */
    protected function saveDeck(int $conversationId, array $slides, string $theme): int
    {
        // Create a request-like object for DeckController::store()
        $request = new \Illuminate\Http\Request([
            'conversation_id' => $conversationId,
            'slides_json'     => $slides,
            'theme'           => $theme,
        ]);

        // Call store() and extract the returned id
        $response = $this->deckController->store($request);
        $content = json_decode($response->getContent(), true);

        if (! isset($content['id'])) {
            throw new \RuntimeException('DeckController::store did not return an id.');
        }

        return (int) $content['id'];
    }
}