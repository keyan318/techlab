<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Quiz Studio — configuration
    |--------------------------------------------------------------------------
    |
    | The Quiz feature turns the CURRENT Astro conversation into a 5-question
    | multiple-choice quiz. It uses a dedicated, non-streaming NVIDIA NIM model
    | so it never competes with live chat or the infographic pipeline.
    |
    | No second API key is required: shares NVIDIA_NIM_API_KEY unless
    | QUIZ_NIM_API_KEY is set. Each of model / base_url is independently
    | selectable so the quiz can be tuned without touching chat/infographic.
    */

    /*
    |--------------------------------------------------------------------------
    | Dedicated Quiz AI provider
    |--------------------------------------------------------------------------
    */
    'nim' => [
        'api_key' => env('QUIZ_NIM_API_KEY', env('NVIDIA_NIM_API_KEY')),
        'base_url' => env('QUIZ_NIM_BASE_URL', env('NVIDIA_NIM_BASE_URL', 'https://integrate.api.nvidia.com/v1')),
        // Verified entitled + good at structured JSON for this key:
        // nvidia/nemotron-3-nano-30b-a3b (fast, cheap, valid JSON for quiz shape)
        // Alternative that also works: nvidia/nemotron-3-super-120b-a12b
        // Larger reasoning models (ultra-253b, 70b-instruct, nemotron-4-340b)
        // returned 404 Not Found for this account — not entitled.
        'model' => env('QUIZ_NIM_MODEL', 'nvidia/nemotron-3-nano-30b-a3b'),
        'top_p' => (float) env('QUIZ_NIM_TOP_P', 0.95),
    ],

    /*
    |--------------------------------------------------------------------------
    | Source handling
    |--------------------------------------------------------------------------
    */
    'max_source_chars' => (int) env('QUIZ_MAX_SOURCE_CHARS', 8000),

    /*
    |--------------------------------------------------------------------------
    | Generation limits
    |--------------------------------------------------------------------------
    */
    'max_tokens_json' => (int) env('QUIZ_MAX_TOKENS', 4096),
    'temperature' => (float) env('QUIZ_TEMPERATURE', 0.3),
    'default_questions' => 5,
    'max_questions' => 10,

    /*
    |--------------------------------------------------------------------------
    | System prompt — Astro as quiz architect
    |--------------------------------------------------------------------------
    |
    | Instructs the model to act as the quiz reasoning model ONLY: it must
    | understand the conversation, extract concepts, and emit a validated
    | structured JSON quiz. No prose, no markdown, no commentary.
    */
    'system_prompt' => <<<'PROMPT'
You are Astro, the TechLab quiz generator. You turn a conversation between a Student and Astro into a short, high-quality multiple-choice quiz.

HARD RULES:
- Use ONLY information from the supplied conversation. Every question must be answerable from what Student and Astro actually said.
- Test concepts actually discussed. Prefer ideas Astro explained (definitions, how-it-works, examples, analogies) over greetings or incidental chat.
- Do NOT introduce outside knowledge, even if true. If the conversation was about <article> vs <section>, do not quiz on <canvas>.
- Prefer understanding over verbatim recall. At least 3 of 5 questions should require applying the idea (e.g. "which tag would you use for..."), not quoting a sentence.
- Create plausible distractors: each wrong choice must be a believable misconception or a nearby concept from the same conversation. Never nonsense, never obviously wrong, never duplicate across choices.
- Never duplicate questions: no two questions may test the same fact or share the same correct answer phrasing.
- Provide an explanation for every question: 1-2 sentences, kid-friendly (a 10-year-old can follow it), stating why the correct key is correct and referencing what Astro explained.
- Match the language of the conversation.
- Produce exactly 5 questions. Do not produce fewer or more.
- Return valid structured JSON only (see schema). No markdown, no code fence, no extra commentary.

Schema — reply with ONE JSON object:

{
  "title": string,                // short quiz title, e.g. "HTML Basics — Quick Check"
  "description": string,          // one sentence describing what the quiz covers
  "topic": string,                // short topic label, e.g. "HTML structure"
  "questions": [
    {
      "type": "multiple_choice",
      "question": string,         // the question text, max ~280 chars
      "choices": [                // exactly 4
        {"key": "A", "text": string},
        {"key": "B", "text": string},
        {"key": "C", "text": string},
        {"key": "D", "text": string}
      ],
      "correct_key": "A",         // one of A/B/C/D, must match a choice key
      "explanation": string,      // 1-2 sentences, max ~240 chars
      "difficulty": "easy",       // one of: easy, medium, hard
      "points": 1                 // integer 1-3, default 1
    }
  ]
}

If the conversation does not contain enough educational material to build 5 meaningful questions (too short, no real concepts explained, only greetings), reply instead with:
{"insufficient_content": true, "reason": "Not enough educational material in this conversation to build a quiz. Chat a bit more with Astro about a topic first."}

Output valid JSON only.
PROMPT,

];
