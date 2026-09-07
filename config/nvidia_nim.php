<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NVIDIA NIM API Key
    |--------------------------------------------------------------------------
    |
    | The secret key used to authenticate with NVIDIA NIM. It MUST stay
    | server-side and is never exposed to the browser. Set it in your .env:
    |
    |   NVIDIA_NIM_API_KEY=<your-nvidia-nim-api-key>
    |
    | Never hardcode the key here or commit it to version control.
    |
    */
    'api_key' => env('NVIDIA_NIM_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | NVIDIA NIM Base URL
    |--------------------------------------------------------------------------
    |
    | NVIDIA NIM exposes an OpenAI-compatible Chat Completions endpoint.
    */
    'base_url' => env('NVIDIA_NIM_BASE_URL', 'https://integrate.api.nvidia.com/v1'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | Astro runs on NVIDIA NIM. Kept configurable so the model can be swapped
    | later without touching the service or controllers.
    |
    | IMPORTANT: there is NO hardcoded default. NVIDIA_NIM_MODEL MUST be set in
    | your .env (e.g. nvidia/nemotron-3-super-120b-a12b). If it is missing,
    | Astro refuses to start a request with a clear CONFIGURATION_ERROR.
    |
    | NOTE: not every model slug listed by NVIDIA actually serves chat on the
    | free tier — some queue forever instead of 404-ing. If you pick a new slug,
    | test an actual chat call before trusting it in production.
    |
    */
    'model' => env('NVIDIA_NIM_MODEL'),

    /*
    |--------------------------------------------------------------------------
    | Model label (shown in the chat UI)
    |--------------------------------------------------------------------------
    */
    'model_label' => env('NVIDIA_NIM_MODEL_LABEL', 'Astro · Nemotron 3 Ultra'),

    /*
    |--------------------------------------------------------------------------
    | Generation limits
    |--------------------------------------------------------------------------
    */
    'max_tokens' => env('NVIDIA_NIM_MAX_TOKENS', 2048),
    'temperature' => env('NVIDIA_NIM_TEMPERATURE', 0.7),
    'top_p' => (float) env('NVIDIA_NIM_TOP_P', 0.95),

    /*
    |--------------------------------------------------------------------------
    | Reasoning ("thinking") mode — Nemotron 3.5 is a reasoning model
    |--------------------------------------------------------------------------
    |
    | With enable_thinking on, the model streams a private "reasoning_content"
    | trace and then the final answer in "content". Astro's stream() surfaces
    | ONLY "content" to students — the reasoning trace is never sent to the
    | browser.
    |
    | DEFAULT IS OFF. A reasoning model with reasoning_budget=16384 can spend
    | tens of seconds "thinking" before emitting the first *visible* token,
    | which produced the old ~30s chat delay. With thinking off the model
    | streams its answer immediately (measured ~2s to first token). Speed is
    | the priority for live chat, so thinking stays off here.
    |
    | The Draw Analogy JSON path (completeJson) keeps thinking OFF for speed and
    | determinism; it does not read these settings.
    |
    | reasoning_budget is the token allowance for the model's private thinking
    | step (separate from max_tokens). It only applies when thinking is on.
    |
    */
    'thinking_enabled' => filter_var(env('NVIDIA_NIM_ENABLE_THINKING', false), FILTER_VALIDATE_BOOLEAN),
    'reasoning_budget' => (int) env('NVIDIA_NIM_REASONING_BUDGET', 1024),
    'medium_effort' => filter_var(env('NVIDIA_NIM_MEDIUM_EFFORT', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | System Prompt — Astro, the TechLab AI teacher
    |--------------------------------------------------------------------------
    |
    | Astro is a teacher, not a generic chatbot. The goal is UNDERSTANDING,
    | not just answers.
    |
    */
    'system_prompt' => <<<'PROMPT'
You are Astro. Give direct answers only.

RULES:
1. NEVER show your thinking process
2. NEVER say "The user is asking...", "I should...", "Let me think...", etc.
3. ALWAYS give only the final answer
4. If you think about how to answer, ignore those thoughts and just answer
5. Be helpful and friendly
6. Keep explanations simple and clear
7. Never make students feel stupid
8. Match depth to question complexity
PROMPT,

    /*
    |--------------------------------------------------------------------------
    | Analogy extraction prompt — used by the "Draw Analogy" feature
    |--------------------------------------------------------------------------
    |
    | When a student clicks "Draw Analogy", Astro's last explanation is sent to
    | the model with this prompt to extract a structured, visualizable analogy.
    | The model MUST reply with a single JSON object only (no prose, no markdown
    | fence) matching the schema below.
    |
    */
    'analogy_prompt' => <<<'PROMPT'
You are the analogy-extraction module for Astro, an AI teacher. You are given a previous explanation written by Astro. Your job is to identify the central analogy (or, if none exists, the core concept) and break it into simple visual elements a comic-style illustration can depict.

Reply with ONE JSON object and NOTHING else (no markdown, no code fence, no commentary). Schema:

{
  "title": string,            // short phrase summarizing the analogy, e.g. "An API is like a waiter in a restaurant"
  "elements": [               // 2-5 concrete things to draw, in story order
    { "name": string, "role": string }  // name = what it is, role = one short phrase of what it does
  ],
  "flow": [                   // labels for the arrows BETWEEN consecutive elements (should be length elements.length - 1)
    string
  ],
  "caption": string           // one friendly sentence explaining how the analogy maps to the real concept
}

Guidelines:
- elements and flow MUST match the EXACT analogy Astro used in the text. Do not invent a different analogy.
- Keep element names short (1-3 words). Keep roles to a few words.
- If the text contains no analogy, set title to a short summary of the key concept and build elements around that concept.
- Output valid JSON only.
PROMPT,
];
