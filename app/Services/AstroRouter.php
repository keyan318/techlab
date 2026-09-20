<?php

namespace App\Services;

/**
 * Picks Astro's effort level for one message — Fast or Deep — with plain rules.
 * No AI request, no I/O: it only reads the student's message and how many Planet
 * lessons are attached, so routing costs microseconds.
 *
 * Both modes use the same model; only thinking and the token budget differ
 * (see `modes` in config/nvidia_nim.php). Fast is the default; Deep is chosen
 * when ANY rule below matches, and `reason` records which one (never the text).
 */
class AstroRouter
{
    /** Messages longer than this many characters are treated as Deep. */
    public const LONG_MESSAGE_CHARS = 400;

    /** Attached Planet lessons at which Astro has to synthesise, not just answer. */
    public const DEEP_SOURCE_COUNT = 2;

    /** rule name => regex (case-insensitive). First match wins. */
    private const RULES = [
        'code_block' => '/```|~~~/',
        'traceback' => '/Traceback \(most recent call last\)|stack ?trace|^\s*File ".+", line \d+|^\s+at \S+ \(.+:\d+(:\d+)?\)|\b[A-Z]\w*(Error|Exception)\s*:/m',
        'debugging' => '/\b(debug\w*|errors?|exceptions?|not working|(doesn\'?t|does not|won\'?t|isn\'?t|why doesn\'?t) work\w*|why (doesn\'?t|does not|won\'?t|isn\'?t)|fix(es|ed|ing)?|broken)\b/i',
        'engineering' => '/\b(design\w*|architect\w*|plan(s|ning|ned)?|projects?|labs?|step[- ]by[- ]step|compar(e|es|ing|ison))\b/i',
        'asks_for_depth' => '/\b(think (carefully|deeply|hard|it through)|in[- ]depth|in detail|deep dive|explain (thoroughly|deeply))\b/i',
    ];

    /**
     * @return array{mode: string, reason: string|null, thinking: bool, max_tokens: int, thinking_token_budget?: int}
     */
    public function route(string $message, int $sourceCount = 0): array
    {
        $reason = $this->deepReason($message, $sourceCount);
        $mode = $reason === null ? 'fast' : 'deep';

        return ['mode' => $mode, 'reason' => $reason] + config("nvidia_nim.modes.{$mode}");
    }

    private function deepReason(string $message, int $sourceCount): ?string
    {
        if (mb_strlen($message) > self::LONG_MESSAGE_CHARS) {
            return 'long_message';
        }

        if ($sourceCount >= self::DEEP_SOURCE_COUNT) {
            return 'multiple_sources';
        }

        foreach (self::RULES as $reason => $pattern) {
            if (preg_match($pattern, $message)) {
                return $reason;
            }
        }

        return null;
    }
}
