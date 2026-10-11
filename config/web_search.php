<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Web research sources (Tavily)
    |--------------------------------------------------------------------------
    | Extensive-research questions are grounded in real web results so Astro can
    | cite valid links instead of guessing. Simple questions ("what is Python")
    | never hit the web. Backed by the hosted Tavily search API: no server or
    | Docker to run, just an API key (free tier). Leave WEB_SEARCH_API_KEY empty
    | to switch web research off.
    */
    'endpoint' => env('WEB_SEARCH_ENDPOINT', 'https://api.tavily.com/search'),
    'api_key' => env('WEB_SEARCH_API_KEY', ''),
    'max_results' => (int) env('WEB_SEARCH_MAX_RESULTS', 5),
    'timeout' => (int) env('WEB_SEARCH_TIMEOUT', 8),
    // Identical questions reuse the saved result for this long, so the free allowance lasts.
    'cache_hours' => (int) env('WEB_SEARCH_CACHE_HOURS', 6),
];
