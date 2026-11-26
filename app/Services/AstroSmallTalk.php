<?php

namespace App\Services;

/**
 * Instant replies for messages that are ONLY small talk: "hi", "hello Astro",
 * "are you working?", "thanks!", "ok cool", "bye".
 *
 * These don't need the model. Sending "hi" to a 550B model on NVIDIA's shared endpoint
 * took 1-9 seconds (queue time varies a lot), so Astro answers them here in microseconds
 * with no network call and no XP cost.
 *
 * The whole message must be small talk. Anything with a real question in it
 * ("hi, what is a loop?", "thanks, but why does it crash?") returns null and goes to
 * the AI as usual, routed Fast or Deep by AstroRouter.
 */
class AstroSmallTalk
{
    /** Longer messages are never treated as small talk, whatever they contain. */
    public const MAX_CHARS = 60;

    private const GREETING = '(hi+|hello+|hey+|heya|hiya|howdy|yo|sup|wassup|whats up|hola|kumusta|good (morning|afternoon|evening|day)|greetings)( there)?';

    private const CHECK_IN = '(are|r) (you|u) (there|here|working|online|alive|awake|ok|okay|up|on|ready|active)( (now|right now|today|again|yet))?'
        .'|(you|u) (there|here|working|online|up)'
        .'|(is )?(this|it) working( (now|right now|again))?'
        .'|can (you|u) (hear|see|read) me'
        .'|how are (you|u)( doing| today)?|hows it going|how is it going';

    private const THANKS = '((ok|okay|cool|nice|great|awesome|got it|alright|perfect|yay|wow) ){0,2}(thanks+|thank (you|u)|thx|ty|tysm|tyvm|salamat)( (so|very) much| a lot| again| astro)?';

    private const ACK = 'ok+|okay|k|kk|cool|nice|great|awesome|got it|gotcha|alright|perfect|sure|i see|understood|makes sense|noted|yay|wow|lol|haha+';

    private const BYE = 'bye+|goodbye|good bye|bye bye|see (you|u|ya)( (later|soon|tomorrow))?|later|good ?night|gtg|got to go|gotta go|ttyl|cya|take care';

    /** @var array<string, list<string>> {who} becomes "astronaut" or "Captain" */
    private const REPLIES = [
        'check_in' => [
            "Yes, I'm here and working, {who}! Ask me anything about programming, networking or cybersecurity.",
            "I'm up and running, {who}! What would you like to learn today?",
            'All systems go, {who}! What are we exploring today?',
        ],
        'greeting' => [
            'Hi, {who}! What would you like to learn today?',
            'Hello, {who}! Ready to explore programming, networking or cybersecurity?',
            'Hey there, {who}! What can I help you with?',
        ],
        'thanks' => [
            "You're welcome, {who}! Ask me anytime.",
            'Happy to help, {who}! Anything else you want to explore?',
            'Anytime, {who}! Keep up the great work.',
        ],
        'ack' => [
            'Got it! What would you like to explore next, {who}?',
            'Great! Ask me whenever you have a question, {who}.',
        ],
        'bye' => [
            'See you next time, {who}! Keep exploring.',
            'Bye for now, {who}! Come back anytime.',
        ],
    ];

    /**
     * The built-in reply for a pure small-talk message, or null when the AI should answer.
     *
     * Mid-conversation, a bare "ok" / "sure" is usually a yes to something Astro just
     * offered ("Want to practice writing one?"), so only the AI can answer it properly.
     */
    public function reply(string $message, string $who = 'astronaut', bool $midConversation = false): ?string
    {
        $kind = $this->kind($message);
        if ($kind === null || ($kind === 'ack' && $midConversation)) {
            return null;
        }

        $options = self::REPLIES[$kind];

        return str_replace('{who}', $who, $options[array_rand($options)]);
    }

    /**
     * Which kind of small talk the message is (check_in, greeting, thanks, ack, bye), or null.
     */
    public function kind(string $message): ?string
    {
        if (mb_strlen($message) > self::MAX_CHARS) {
            return null;
        }

        $text = $this->normalize($message);
        if ($text === '') {
            return null;
        }

        $greet = '(('.self::GREETING.') )?';

        // Most specific first: "hi are you working" is a check-in, not just a greeting.
        $kinds = [
            'check_in' => $greet.'('.self::CHECK_IN.')',
            'greeting' => '('.self::GREETING.')',
            'thanks' => '('.self::THANKS.')',
            'bye' => '(('.self::THANKS.') )?('.self::BYE.')',
            'ack' => '('.self::ACK.')( ('.self::ACK.'))?',
        ];

        foreach ($kinds as $kind => $pattern) {
            if (preg_match('/^(?:'.$pattern.')$/u', $text)) {
                return $kind;
            }
        }

        return null;
    }

    /**
     * Lowercase, drop Astro's name, emoji and punctuation, squash spaces, so
     * "Hi Astro!! 👋", "hi astro" and "hi" all compare the same.
     */
    private function normalize(string $message): string
    {
        $text = mb_strtolower(trim($message));
        $text = str_replace(['’', "'"], '', $text);               // what's -> whats, how's -> hows
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text);   // punctuation + emoji -> space
        $text = preg_replace('/\b(astro|bot|teacher|po|pls|please)\b/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}
