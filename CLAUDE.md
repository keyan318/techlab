# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# TechLab

TechLab is a Laravel 13 app that teaches programming/networking/cybersecurity through an AI tutor named **Astro**, backed by NVIDIA NIM (Nemotron models). Frontend is server-rendered Blade + Alpine.js + Tailwind (Vite build), not a SPA — React is used only for isolated widgets (`resources/js/components/ui/*.tsx`).

## Commands

```bash
composer install && npm install          # setup
composer run dev                          # runs `php artisan dev`: serves app + queue listener + logs + vite, concurrently
php artisan test                          # full test suite (phpunit, in-memory sqlite — see phpunit.xml)
php artisan test tests/Feature/AstroAnalogyTest.php   # single test file
php artisan test --filter=test_method_name            # single test by name
./vendor/bin/pint                         # PHP code style (Laravel Pint)
npm run dev                               # Vite dev server (main app assets only)
npm run build                             # Vite production build (main app assets only)
```

Local dev DB is Postgres via a Supabase pooler (see `.env` `DB_*`); tests always run against in-memory sqlite regardless of `.env`, so no DB setup is needed to run the suite.

## Architecture

**Domain model:** `User` (role + `crew_id`) → `Crew` (a classroom/group, roster via `crew_members` pivot) · `Conversation` → `Message` (Astro chat history). Course content ("Planets": programming/networking/cybersecurity) is **not** database-backed — it's static Blade views plus metadata.

**Self-paced vs Join Class:** every catalog course has one `self_paced` Crew (no teacher, default course, default final built by `DefaultFinalExamService`, watched by admin at `/admin/self-paced`, prerequisites apply) and one `class` Crew per assigned faculty member (many per course; faculty accepts/declines, no prerequisite check, certificate carries their `signature_path`). A student is on one crew per course (`Crew::forStudent`, `Crew::admit` switches). Faculty edits (`lesson_contents`, `lesson_labs`, `lesson_sim_labs`, `lesson_quizzes`, `course_customizations`) are keyed by `crew_id` and resolved via `CourseTrack::classId()` — self-paced always gets the default. `lesson_progress.crew_id` + `shelved_at`: switching track shelves the old rows (global `active` scope hides them); XP counts every row once (`LessonProgress::XP_COUNT`).

**Mini bosses:** every module ends with a full-screen exam (`MiniBossService`, `MiniBossController`, same page as the Final Boss: `student/course/final-exam.blade.php` with `$mini`). A mini boss is a `crew_quizzes` row with `module` = `m1`…, built lazily from `resources/mini-bosses/{course key}/{module}.json` (30+ default questions per module, right letter spread over A-D, never the longest; `MiniBossTest` checks it). 10 XP per right answer (`crew_quiz_attempts.xp`). Self-paced: the next module's first lesson and the Final Boss stay locked until it is passed, a fail earns 0 XP and waits `COOLDOWN_MINUTES`. Class: one try, recorded, no lock; the faculty member edits it in the quiz builder and sees scores on the Mini bosses tab (`MiniBossEditorController`).

**Course content structure:** Lessons live as individual Blade files under `resources/views/student/planets/{course}/M{module}/lesson-{nn}.blade.php`. `config/course-structure.php` defines the COURSE → MODULE → LESSON blueprint (titles, slugs, ordering, view paths) that `PlanetController` (the largest controller — show/viewModuleLesson/lessonFragment/generatePlan/viewLesson/editor/launchEditor) reads to render course overviews and lesson pages. To edit a specific lesson, go straight to its Blade file rather than searching the controller.

**AI pipeline (NVIDIA NIM / Nemotron), intentionally split per feature so tuning one doesn't affect another:**
- `NvidiaNimService` — live Astro chat (`config/nvidia_nim.php`), streaming.
- `InfographicNimService` (`config/infographic.php`) — infographic/lesson-visual JSON generation, non-streaming, own model config.
- `QuizNimService` (`config/quiz.php`) — quiz generation, own model config.
Each has its own `*ContentService`/`*Schema`/`*SourceResolver` for prompt building and JSON-schema normalization of model output.

**Lesson labs ("Configure it yourself" / "Defend it yourself"):** a blueprint lesson's `lab` + `sim` pick the simulator; `LessonLabController::SIMS` maps `sim` to its iframe path and postMessage source, and `lab.blade.php` POSTs a pass to `completeLab` (100 XP). `netsim` = the vendored NetSim in `resources/netsim-app/` (build it there; labs in `resources/netsim-app/public/labs/`, runs Windows commands when embedded). `citadel` = Citadel Sim, plain ES modules served straight from `public/citadel-sim/` (no build; labs in `public/citadel-sim/labs/`; engine tests: `node --test tests/citadel-sim`). `console` = Relay Console (Networking 2's router-console labs: BGP, QoS, VPNs…), plain ES modules in `public/relay-console/` (labs in `public/relay-console/labs/g*.json`, each with a scripted solution in `tests/relay-console/solutions.mjs`; tests: `node --test "tests/relay-console/*.test.mjs"`).

**Networking 2 (`networking-2`, `net2_course/`, labs `g<module>-l<lesson>`):** NetSim devices with `"cli": "ios"` get a Cisco IOS console (`resources/netsim-app/src/commands/ios/`); the engine adds 802.1Q trunks and router sub-interfaces, per-VLAN spanning tree with BPDU/Root Guard (`engine/stp.ts`), port security, OSPF (`engine/ospf.ts`), HSRP/VRRP with tracking (`engine/fhrp.ts`) and IOS ACLs (`engine/acl.ts`). Every N2 NetSim lab has a typed solution in `src/labs/techlab-n2.solutions.ts` (`npm test` in `resources/netsim-app`); after editing a lab JSON there, run `npm run build` so `public/netsim-app/labs/` gets the copy.

**In-browser Python execution:** lessons embed a Pyodide-based `code-runner` Alpine component (loaded via CDN script tag) — Python runs client-side in the browser, there is no backend code-execution service.

**In-browser C execution:** the C courses' editor runs C in a Web Worker. Programming 1 (`c_course/`) uses JSCPP (`public/js/jscpp-worker.js`). Programming 2 (`c2_course/`, blueprint `'c_runtime' => 'picoc'`) uses PicoC (`public/js/picoc-worker.js`, built by `node scripts/build-picoc.mjs`) because JSCPP can't run structs, `malloc` or `FILE*`. The worker patches PicoC gaps (array parameters are copied, typedef return types fail, a last line with no `\n` is lost); PicoC also can't do struct initialisers `{...}`, `const`, `fscanf`/`scanf` into `float` (use `double` + `%lf`), or `?:` around a recursive call, so lessons avoid them. Every Programming 2 answer key is checked against its lesson's `hint_code`: `node --test "tests/picoc/*.test.mjs"`.

**Repo hygiene:** ignore stray `*.backup`, `*.backup2`, `*.bak` files sitting next to some controllers/views (e.g. `PlanetController.php.backup*`) — they're leftover manual copies, not referenced by any code or build step.

## gstack
Use /browse from gstack for all web browsing. Never use mcp__claude-in-chrome__* tools.
Available skills: /office-hours, /plan-ceo-review, /plan-eng-review, /plan-design-review,
/design-consultation, /design-shotgun, /design-html, /review, /ship, /land-and-deploy,
/canary, /benchmark, /browse, /open-gstack-browser, /qa, /qa-only, /design-review,
/setup-browser-cookies, /setup-deploy, /setup-gbrain, /retro, /investigate,
/document-release, /document-generate, /codex, /cso, /autoplan, /plan-devex-review,
/devex-review, /careful, /freeze, /guard, /unfreeze, /gstack-upgrade, /learn.

## Skill routing

When the user's request matches an available skill, invoke it via the Skill tool. When in doubt, invoke the skill.

Key routing rules:
- Product ideas/brainstorming → invoke /office-hours
- Strategy/scope → invoke /plan-ceo-review
- Architecture → invoke /plan-eng-review
- Design system/plan review → invoke /design-consultation or /plan-design-review
- Full review pipeline → invoke /autoplan
- Bugs/errors → invoke /investigate
- QA/testing site behavior → invoke /qa or /qa-only
- Code review/diff check → invoke /review
- Visual polish → invoke /design-review
- Ship/deploy/PR → invoke /ship or /land-and-deploy
- Save progress → invoke /context-save
- Resume context → invoke /context-restore
- Author a backlog-ready spec/issue → invoke /spec

## How to talk to the user

When explaining what you are doing or why, keep it simple. Explain it like you're talking to a curious 15-year-old: short sentences, everyday words, a quick example or comparison when it helps. Avoid deep technical jargon; if a technical word is really needed, say what it means in plain words right after. Code, commands and file names stay exact, but the explanation around them should be easy to follow.
