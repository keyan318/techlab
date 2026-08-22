<?php

namespace App\Services\Quiz;

use App\Models\Conversation;
use Illuminate\Http\Request;

/**
 * Resolves a quiz source from a persisted Conversation.
 *
 * The quiz MUST be based on what Student and Astro actually discussed.
 * The database is the source of truth — we load the conversation's messages
 * server-side (ownership already enforced by the controller) and render them
 * as a "Student: ...\n\nAstro: ..." transcript. The browser never decides
 * what the quiz is about.
 *
 * A frontend-supplied `transcript` array is NOT accepted for quiz — only
 * `conversation_id`. This prevents tampering and keeps the security model
 * identical to /chat/analogy (AstroController::drawAnalogy).
 */
class QuizSourceResolver
{
    /**
     * Build the quiz source text from a Conversation's messages.
     * Returns '' when there is nothing usable.
     */
    public function fromConversation(Conversation $conversation): string
    {
        $messages = $conversation->messages()
            ->orderBy('id')
            ->get(['role', 'content']);

        if ($messages->isEmpty()) {
            return '';
        }

        $lines = [];
        foreach ($messages as $m) {
            $content = trim(preg_replace('/\s+/', ' ', (string) $m->content));
            if ($content === '') {
                continue;
            }
            $label = $m->role === 'assistant' ? 'Astro' : 'Student';
            $lines[] = $label.': '.$content;
        }

        if ($lines === []) {
            return '';
        }

        return $this->clean(implode("\n\n", $lines));
    }

    /**
     * Convenience: resolve directly from a Request that carries conversation_id.
     * Kept for symmetry with InfographicSourceResolver::resolve(Request).
     * The controller should already have loaded and ownership-checked the
     * Conversation; this just delegates to fromConversation().
     */
    public function resolve(Request $request, Conversation $conversation): string
    {
        return $this->fromConversation($conversation);
    }

    /**
     * Normalize whitespace and cap length so we never blow the model context.
     * Mirrors InfographicSourceResolver::clean().
     */
    protected function clean(string $text): string
    {
        // Collapse whitespace but preserve the blank-line boundary between
        // Student/Astro turns so looksInsufficient() can split on it.
        // First normalize \r\n, then collapse spaces/tabs inside each line, but
        // keep \n\n as the turn delimiter.
        $text = preg_replace('/\r\n?/', "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = trim($text);
        $max = (int) config('quiz.max_source_chars', 8000);

        if (mb_strlen($text) > $max) {
            $text = mb_substr($text, 0, $max).' …';
        }

        return $text;
    }

    /**
     * Quick heuristic: does this source look like it has enough substance to quiz?
     * Not authoritative — the model makes the final insufficient_content call —
     * but lets us short-circuit obviously empty one-liners without a NIM round-trip.
     *
     * Note: blocks are "Student: ..." / "Astro: ..." lines. A rich conversation
     * is typically 4+ blocks even though we join with \n\n, so the threshold is
     * intentionally lenient — only truly trivial chats should be rejected here.
     */
    public function looksInsufficient(string $source): bool
    {
        if (mb_strlen(trim($source)) < 80) {
            return true;
        }

        $blocks = preg_split('/\n\n+/', $source);
        $substantive = 0;
        foreach ($blocks as $b) {
            if (mb_strlen(trim($b)) >= 40) {
                $substantive++;
            }
        }

        // Require at least 2 substantive turns (e.g. one Q + one answered).
        // Higher thresholds were marking valid 4-turn HTML chats as insufficient.
        return $substantive < 2;
    }
}
