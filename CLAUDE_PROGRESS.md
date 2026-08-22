# TechLab — Astro Progress

## Completed
- Step 1: Inspected Astro chat architecture (route `POST /chat/message` → `ChatController::send` → `NvidiaNimService` → NVIDIA NIM). Confirmed thinking mode is already OFF (old ~30s delay resolved). No real file/resource attachment in chat flow yet.
- Step 2: Improved Astro response performance.
  - Root cause (measured): output verbosity, not architecture. The teaching system prompt drove 4,000–6,800-char essays; at ~26 tok/s on `nemotron-3-super-120b` that is 17–40s for long answers. Thinking mode already off.
  - Fix: tightened the system prompt's length guidance in `config/nvidia_nim.php` (concise, ~120–250 words default, full depth only when asked). No change to NIM client, routes, controllers, or UI.

## Current state
- Normal message: ~6.9s → ~2.5s total (service-level). Long message: ~17s → ~10s, and answers are more focused.
- Full `ChatController::send()` flow verified: returns JSON meta line `{conversation_id, user_message_id, assistant_message_id}`, streams content, ends with `__END__`, no `__ERROR__`.

## Tested
- Service-level latency probe (normal + long) before/after change.
- End-to-end `ChatController::send()` for one normal and one longer message: both passed (meta + content + `__END__`, no error).
- Laravel logs: no errors from successful chat runs. Pre-existing unrelated env issues remain (Supabase pgsql DNS failure on `sessions`; Infographic `SCHEMA_ERROR`).

## Remaining issue
- Long answers still take ~10s (inherent to 120b model throughput + real teaching depth). Could be reduced further by lowering `max_tokens` (truncates) or a smaller/faster model (risk: some models hang per prior notes) — not done to preserve quality/integration.
- DB session driver points at unreachable Supabase pgsql host (env issue, unrelated).

## Next recommended step
- Begin real file/resource attachment handling (uploaded learning resources → Astro summarize/synthesize). Currently `sourceCount` is hardcoded 0 and `NvidiaNimService` has no document ingestion.

---

## Infographic dedicated AI provider (foundation) — COMPLETED
- Goal: give Infographic content generation its OWN NVIDIA NIM model/service, separate from live Astro chat (`NvidiaNimService`).
- Added `config/infographic.php` -> `nim` section: `api_key` (defaults to `NVIDIA_NIM_API_KEY`), `base_url` (defaults to `NVIDIA_NIM_BASE_URL`), `model` (`INFOGRAPHIC_NIM_MODEL`, default `nvidia/nemotron-3-super-120b-a12b`), `top_p`. API key stays server-side.
- Added `app/Services/Infographic/InfographicNimService.php`: standalone NIM client (non-streaming only), its own config + `assertConfigured`, `ping()`, robust string-aware JSON extractor (fixes the prior `SCHEMA_ERROR` from the shared fragile recursive-regex extractor that broke on braces inside code-snippet strings). Reuses `NvidiaNimException` type so `InfographicContentService` catch block is unchanged.
- Swapped `InfographicContentService` dependency from `NvidiaNimService` -> `InfographicNimService` (container resolves it; verified). Normal Astro chat flow untouched.
- `.env`: added `INFOGRAPHIC_NIM_MODEL`.
- Verified backend: `ping()` reachable = true; real infographic JSON generated via dedicated model + `InfographicSchema::normalize()` succeeded (5 slides, valid cover). No SCHEMA_ERROR.

## Tested
- Backend connectivity: `InfographicNimService::ping()` -> true.
- End-to-end generation through dedicated service + schema normalization: passed.
- Container resolution of `InfographicController` and `InfographicContentService` (dependency = `InfographicNimService`): passed.

## Remaining (infographic)
- Frontend infographic viewer — not built (explicitly deferred).
- Attachment/file ingestion — not built (deferred).
- Image generation (`NvidiaImageProvider`) left as-is; still config-gated + local SVG fallback.
- Could later point `INFOGRAPHIC_NIM_MODEL` at a different/tuned model without code changes.

## Next recommended step
- Build the frontend infographic viewer for `/chat/infographic` output (deferred). Or, if priority returns to chat, resume file/resource attachment handling.
