<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Web research sources (Tavily)
    |--------------------------------------------------------------------------
    | Extensive-research questions are grounded in real web results so Astro can
    | cite valid links instead of guessing. Simple questions ("what is Python")
    | never hit the web. Leave TAVILY_API_KEY empty to switch web research off.
    */
    'api_key' => env('TAVILY_API_KEY'),
    'endpoint' => env('WEB_SEARCH_ENDPOINT', 'https://api.tavily.com/search'),
    'max_results' => (int) env('WEB_SEARCH_MAX_RESULTS', 5),
    'timeout' => (int) env('WEB_SEARCH_TIMEOUT', 8),
];
