<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Infographic Studio — configuration
    |--------------------------------------------------------------------------
    |
    | The Infographic feature turns a student's learning material into a
    | space-themed visual lesson (a mini presentation / deck) rendered inside
    | /chat. It uses a TWO-MODEL pipeline:
    |
    |   1. Astro (content / reasoning model)  -> structured JSON lesson plan.
    |      Reuses the existing NVIDIA NIM chat client (NvidiaNimService) and
    |      the model configured in config/nvidia_nim.php (NVIDIA_NIM_MODEL).
    |
    |   2. Image model (illustrator)          -> optional generated artwork.
    |      Decoupled and config-gated. If NVIDIA_IMAGE_MODEL (+ an image
    |      endpoint base URL) is configured AND reachable, generated images
    |      are used; otherwise an offline SVG/CSS space-art generator
    |      (LocalInfographicArtProvider) is the always-working fallback.
    |
    | NO second API key is required: the image model (when configured) shares
    | the same NVIDIA_NIM_API_KEY as Astro. Only introduce extra credentials
    | if NVIDIA genuinely requires a separate key for image generation.
    */

    /*
    |--------------------------------------------------------------------------
    | Dedicated Infographic AI provider (content / reasoning model)
    |--------------------------------------------------------------------------
    |
    | The structured-lesson JSON is produced by a SEPARATE NVIDIA NIM model from
    | the one Astro uses for live chat (config/nvidia_nim.php). Keeping it separate
    | means the two can be tuned and swapped independently, and a slow/heavy
    | infographic model never competes with the live chat stream.
    |
    | By default it reuses the shared NVIDIA_NIM_API_KEY and NVIDIA_NIM_BASE_URL,
    | but each can be overridden with its own env var. The API key stays
    | server-side; it is NEVER sent to the browser.
    |
    | The model is configurable so a different/instructor-tuned model can be
    | dropped in without touching the service or controller.
    */
    'nim' => [
        'api_key' => env('INFOGRAPHIC_NIM_API_KEY', env('NVIDIA_NIM_API_KEY')),
        'base_url' => env('INFOGRAPHIC_NIM_BASE_URL', env('NVIDIA_NIM_BASE_URL', 'https://integrate.api.nvidia.com/v1')),
        // Defaults to the same verified-working chat model so the feature works
        // out of the box; override INFOGRAPHIC_NIM_MODEL to dedicate a different one.
        'model' => env('INFOGRAPHIC_NIM_MODEL', 'nvidia/nemotron-3-super-120b-a12b'),
        'top_p' => (float) env('INFOGRAPHIC_NIM_TOP_P', 0.95),
    ],

    /*
    |--------------------------------------------------------------------------
    | Image provider
    |--------------------------------------------------------------------------
    |
    | 'local_art' (default) -> offline, dependency-free SVG space illustrations.
    | 'nvidia'     -> real NVIDIA image model. Requires NVIDIA_IMAGE_MODEL and
    |                 NVIDIA_IMAGE_BASE_URL below (and the account to be
    |                 provisioned for that model). If 'nvidia' is selected but
    |                 the model is unavailable, the system transparently falls
    |                 back to 'local_art' so the deck always renders.
    */
    'image_provider' => env('INFOGRAPHIC_IMAGE_PROVIDER', 'local_art'),

    /*
    |--------------------------------------------------------------------------
    | NVIDIA image model (illustrator) — OPTIONAL
    |--------------------------------------------------------------------------
    |
    | Only needed when image_provider = 'nvidia'. Shares NVIDIA_NIM_API_KEY.
    | For hosted Visual GenAI (e.g. black-forest-labs/flux.1-dev, qwen/qwen-image
    | when your account is entitled), set:
    |   NVIDIA_IMAGE_MODEL=qwen/qwen-image
    |   NVIDIA_IMAGE_BASE_URL=https://ai.api.nvidia.com/v1/genai
    | The provider builds the endpoint as {base_url}/{model}. For Qwen the doc
    | lists "native NIM or OpenAI-compatible APIs"; this provider uses the
    | hosted Visual GenAI endpoint (artifacts/base64). If the model is not
    | entitled on your account, the provider soft-fails and the deck falls
    | back to local_art automatically — the infographic still renders.
    | Verified hosted with this key: black-forest-labs/flux.1-dev
    | (artifacts/base64 response). Qwen hosted entitlement varies by account.
    */
    'image_model' => env('NVIDIA_IMAGE_MODEL', ''),
    'image_base_url' => env('NVIDIA_IMAGE_BASE_URL', 'https://ai.api.nvidia.com/v1/genai'),

    /*
    |--------------------------------------------------------------------------
    | Source handling
    |--------------------------------------------------------------------------
    |
    | The model should receive a COMPACT knowledge representation, not the raw
    | source repeated. We cap how much text is sent so large documents don't
    | blow the context or waste tokens. If the source exceeds the cap it is
    | truncated (the model is asked to summarize/compress internally too).
    */
    'max_source_chars' => (int) env('INFOGRAPHIC_MAX_SOURCE_CHARS', 14000),

    /*
    |--------------------------------------------------------------------------
    | Generation limits
    |--------------------------------------------------------------------------
    */
    'max_tokens_json' => (int) env('INFOGRAPHIC_MAX_TOKENS', 4096),
    'temperature' => (float) env('INFOGRAPHIC_TEMPERATURE', 0.4),
    'max_slides' => (int) env('INFOGRAPHIC_MAX_SLIDES', 12),
    'min_slides' => 3,

    /*
    |--------------------------------------------------------------------------
    | Allowed slide types
    |--------------------------------------------------------------------------
    |
    | Anything else the model returns is coerced to 'concept'.
    */
    'allowed_types' => [
        'cover', 'concept', 'architecture', 'process', 'comparison',
        'analogy', 'code', 'timeline', 'diagram', 'summary', 'quiz',
    ],

    /*
    |--------------------------------------------------------------------------
    | System prompt — Astro as the infographic lesson architect
    |--------------------------------------------------------------------------
    |
    | Instructs the model to act as the teacher/reasoning model ONLY: it must
    | understand the source, extract concepts, and emit a structured JSON plan.
    | It must NOT write frontend code, giant SVGs, or full illustrations — that
    | is the frontend's and the image model's job.
    */
    'system_prompt' => <<<'PROMPT'
You are Astro, the AI teacher inside TechLab, acting as an EDUCATIONAL INFOGRAPHIC DESIGNER. Your job is to turn conversation-based learning material into a polished, visual lesson — a short educational infographic deck that TEACHES, not dumps text.

You are the REASONING model only. You must:
- Read and understand the material. Extract the core topic, the key idea at each stage, and how learning should progress.
- Organize the lesson progressively: start simple (what it is), build core concepts, show how it works (process/flow), use a visual analogy or comparison when it helps, give a concrete example, and close with a tight summary. Adapt to the topic — simple topics use 3-5 slides, rich topics use 6-10. Never force every topic into the same shape.
- Write for a visual slide, not a paragraph. Keep "summary" to 1-2 short sentences max (never a full paragraph). Prefer keyPoints (2-4 very short bullets), steps, or comparison points over long prose. Avoid repeating the same idea across slides.
- Decide visuals with purpose: set "visual.required": true ONLY when a visual genuinely helps the student SEE the idea (processes, flows, systems, relationships, comparisons, spatial or abstract relationships). NEVER require an image for simple definitions, covers, summaries, quizzes, or plain bullet lists.
- When you do require a visual, make "visual.prompt" a compact, Flux-ready illustration brief describing WHAT should be visible (main objects, their relationships, arrows/flow90  if a process, spatial arrangement if a diagram). Keep it under 20 words, concrete, and educational — never the full conversation. The text on slides carries the words; the image carries the visual metaphor.

Reply with ONE JSON object and NOTHING else (no markdown, no code fence, no commentary). Schema:

{
  "title": string,                // short deck title
  "subtitle": string,             // one punchy line, space-themed where natural
  "theme": string,                // one of: space_mission_control, constellation_network, data_planet, orbital_firewall, navigation_route, spacecraft_structure, cosmic_lab, mission_control — or another short snake_case space theme that fits
  "source_summary": string,       // 1-2 sentence accurate summary of the material
  "slides": [                     // array of slide objects (see types below)
    {
      "type": "cover|concept|architecture|process|comparison|analogy|code|timeline|diagram|summary|quiz",
      "title": string,
      "subtitle": string,         // optional
      "summary": string,          // optional, 1-4 sentences
      "keyPoints": [string],      // optional, 2-6 short bullets
      "analogy": string,          // optional, the space analogy in plain words
      "visualConcept": string,    // optional, 1 sentence describing a useful illustration for this slide
      "elements": [ {"name": string, "detail": string} ], // for architecture/diagram
      "flow": [string],           // optional labels between consecutive elements
      "comparison": { "left": {"label": string, "points": [string]}, "right": {"label": string, "points": [string]} },
      "code": { "language": string, "snippet": string },  // for code slides (keep short, real)
      "steps": [ {"title": string, "detail": string} ],   // for process/timeline
      "caption": string,          // optional
      "visual": { "required": boolean, "type": "illustration|diagram", "prompt": string } // optional; set required:true ONLY when a generated illustration would clearly improve understanding
    }
  ]
}

Rules:
- Every slide MUST have "type" and "title".
- Include exactly ONE "cover" slide as the first slide, and a "summary" slide near the end (3-5 key takeaways in keyPoints).
- Structure slides in a teaching order: cover → simple explanation → key concepts → how-it-works/process → analogy/comparison or example → summary (adapt; omit comparison for non-comparison topics, omit code unless example is relevant).
- Text must be concise: "summary" max 2 short sentences (or empty if keyPoints/steps carry the idea); "keyPoints" 2-4 short bullets (6-10 words each); avoid paragraphs and repetition across slides.
- "visual.required": true ONLY for slides where a picture helps understanding (processes, flows, systems, comparisons, analogies with physical intuition). NEVER for cover, summary, or quiz. Require visuals on 1-3 slides per deck max.
- "visual.prompt" must be a compact visual brief (what is shown, objects, relationships, flow) — never the conversation, never long text. Flux will render the picture; the slide text carries the words.
- "visualConcept" should describe the illustration in one short sentence for slide context.
- If an analogy helps, put it in "analogy" (1 short sentence) and echo it in "visualConcept" — do not require a second AI call.
- Output valid JSON only.
PROMPT,

];
