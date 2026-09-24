<?php

return [
    // Fail-fast limits for the faculty member schedule upload (seconds). NIM latency spikes, so never wait minutes.
    'vision_timeout' => (int) env('SCHEDULE_VISION_TIMEOUT', 12),   // per request
    'text_timeout' => (int) env('SCHEDULE_TEXT_TIMEOUT', 20),       // per model in the failover chain

    // Hedged vision call: if the first request has not answered after `hedge_after` seconds, a second one is
    // fired in parallel and whichever answers first wins. NIM latency is bimodal (6s or 60s), so this cuts the tail.
    // Worst case is therefore hedge_after + vision_timeout seconds, not 2 × vision_timeout.
    'hedge_after' => (float) env('SCHEDULE_HEDGE_AFTER', 5),

    // Ordered vision models. The first serves attempt 1, the second (if any) serves the hedge — so a slow or
    // overloaded model is raced against a different one. Empty → NVIDIA_NIM_VISION_MODEL.
    'vision_models' => array_values(array_filter(array_map('trim', explode(',', (string) env('SCHEDULE_VISION_MODEL', ''))))),

    // The vision model only has to COPY the picture; a compact line per class keeps its output (the slow part) short.
    'vision_max_tokens' => (int) env('SCHEDULE_VISION_MAX_TOKENS', 700),

    // Same file uploaded again (retry, re-upload) → answered from cache, no AI call. Seconds; 0 disables.
    'cache_ttl' => (int) env('SCHEDULE_CACHE_TTL', 86400),
];
