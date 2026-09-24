<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('course-structure.'.($slug ?? 'programming').'.title', 'Course') }} · TechLab</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>

        :root {
            --green: #2f7de1;
            --light-green: #eaf2fd;
            --gray: #f3f3f3;
            --dark-gray: #0d0d0d;
            --border-gray: rgba(0,0,0,.09);
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;

            background-color: #f9f9f9;
            color: var(--dark-gray);
            overflow: hidden;
        }

        /* =========================================================
           MAIN CONTAINER
        ========================================================= */

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* =========================================================
           MAIN AREA
        ========================================================= */

        .main-area {
            flex: 1;
            min-width: 0;

            height: 100vh;

            display: flex;
            flex-direction: column;

            background-color: var(--white);

            overflow: hidden;
        }

        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            height: 72px;
            min-height: 72px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 0 28px;

            border-bottom: 1px solid var(--border-gray);

            background-color: var(--white);
        }

        .header-left {
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .back-icon {
            width: 34px;
            height: 34px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-right: 14px;

            color: var(--green);

            font-size: 20px;

            cursor: pointer;

            border-radius: 6px;

            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .back-icon:hover {
            background-color: var(--light-green);
            transform: translateX(-2px);
        }

        .lesson-title {
            font-size: 20px;
            font-weight: 500;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* =========================================================
           HEADER ICONS
        ========================================================= */

        .header-right {
            display: flex;
            align-items: center;

            gap: 6px;

            margin-left: 20px;
        }

        .icon {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            cursor: pointer;

            color: var(--dark-gray);

            border-radius: 8px;

            font-size: 19px;

            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .icon:hover {
            background-color: var(--gray);
            transform: translateY(-1px);
        }

        /* =========================================================
           LESSON CONTENT
        ========================================================= */

        .content {
            flex: 1;
            min-height: 0;

            padding: 42px 52px;

            overflow-y: auto;
            overflow-x: hidden;

            background-color: var(--white);
        }

        .lesson-content {
            width: 100%;
            max-width: 1100px;

            margin: 0 auto;

            min-height: 100%;

            transition: opacity 0.15s ease;
        }

        .lesson-heading {
            font-size: 32px;
            font-weight: 600;

            margin-bottom: 30px;
        }

        .secondary-heading {
            font-size: 22px;
            font-weight: 600;

            margin-top: 35px;
            margin-bottom: 14px;
        }

        .body-text {
            font-size: 16px;
            line-height: 1.7;

            color: #676767;

            margin-bottom: 20px;
        }

        .code-block {
            background: #1e1e1e;
            color: #ffffff;

            padding: 20px;

            border-radius: 8px;

            overflow-x: auto;

            margin-bottom: 40px;

            line-height: 1.6;

            font-family: 'Space Mono', monospace;
        }

        .exercise-card {
            padding: 24px;

            margin-bottom: 25px;

            border: 1px solid var(--border-gray);

            border-radius: 10px;

            background: #fafafa;
        }

        .quiz-card {
            margin-bottom: 30px;

            padding: 24px;

            border: 1px solid var(--border-gray);

            border-radius: 10px;

            background: var(--white);
        }

        .quiz-question {
            font-weight: 600;
            margin-bottom: 15px;
        }

        .quiz-option {
            padding: 10px 0;
        }

        /* =========================================================
           FALLBACK
        ========================================================= */

        .empty-lesson {
            display: flex;

            align-items: center;
            justify-content: center;

            min-height: 60vh;

            color: #777;

            text-align: center;
        }

        /* =========================================================
           NAVIGATION
        ========================================================= */

        .nav-arrows {
            display: flex;

            justify-content: space-between;
            align-items: center;

            width: 100%;

            margin-top: 50px;

            padding-bottom: 30px;
        }

        .nav-arrow {
            width: 46px;
            height: 46px;

            display: flex;
            align-items: center;
            justify-content: center;

            background-color: var(--white);

            border: 1px solid var(--green);

            border-radius: 6px;

            color: var(--green);

            cursor: pointer;

            font-size: 22px;

            transition:
                background-color 0.2s ease,
                color 0.2s ease,
                transform 0.2s ease;
        }

        .nav-arrow:hover {
            background-color: var(--green);

            color: var(--white);

            transform: translateY(-2px);
        }

        /* =========================================================
           SCROLLBAR
        ========================================================= */

        .content::-webkit-scrollbar {
            width: 8px;
        }

        .content::-webkit-scrollbar-track {
            background: #f7f7f7;
        }

        .content::-webkit-scrollbar-thumb {
            background: #cfcfcf;
            border-radius: 10px;
        }

        .content::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1200px) {

            .content {
                padding: 35px 40px;
            }
        }

        @media (max-width: 900px) {

            .content {
                padding: 30px;
            }

            .header {
                padding: 0 20px;
            }
        }

        @media (max-width: 700px) {

            .container {
                flex-direction: column;
            }

            .header {
                height: 64px;
                min-height: 64px;
            }

            .lesson-title {
                font-size: 17px;
            }

            .header-right {
                gap: 0;
            }

            .content {
                padding: 25px 20px;
            }
        }

    
        .lesson-title,
        .lesson-heading,
        .secondary-heading,
        .content h1,
        .content h2,
        .content h3,
        .content h4 {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.02em;
        }


        /* =========================================================
           FULL SCREEN MODE — lesson only, floating exit button
        ========================================================= */

        .fs-btn {
            border: 1px solid var(--border-gray);
            background: var(--white);
            color: var(--dark-gray);
        }

        .fs-btn:active,
        .fs-exit:active {
            transform: scale(0.94);
            transition-duration: 0.1s;
        }

        .fs-exit {
            display: none;
            position: fixed;
            right: 28px;
            bottom: 28px;
            z-index: 50;
            width: 52px;
            height: 52px;
            border: none;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            background: var(--green);
            box-shadow: 0 8px 24px -6px rgba(47, 125, 225, .55);
            transition: transform 0.25s cubic-bezier(.34, 1.4, .64, 1);
        }

        .fs-exit:hover { transform: scale(1.06); }

        body.is-fullscreen .programming-sidebar,
        body.is-fullscreen .header {
            display: none;
        }

        body.is-fullscreen .fs-exit {
            display: flex;
            animation: fs-pop 0.3s cubic-bezier(.34, 1.4, .64, 1);
        }

        body.is-fullscreen .content {
            padding: 56px max(48px, 8vw);
        }

        body.is-fullscreen .lesson-content {
            max-width: 1200px;
        }

        @keyframes fs-pop {
            from { opacity: 0; transform: scale(0.6); }
            to   { opacity: 1; transform: scale(1); }
        }

        @media (prefers-reduced-motion: reduce) {
            .fs-exit, body.is-fullscreen .fs-exit { animation: none; transition: none; }
        }



        /* =========================================================
           GAMIFIED LESSON LAYOUT  (hero + stage cards)
           Built by upgradeSections() from the plain lesson HTML.
        ========================================================= */

        .lesson-fragment {
            --q-accent: #2f7de1;
            --q-accent-2: #7cc0ff;
            --q-ink: #0b1230;
            --q-text: #454c60;
            display: flex;
            flex-direction: column;
            gap: 22px;
            font-family: 'Inter', system-ui, sans-serif;
            padding-bottom: 48px;
        }

        /* ---------- HERO ---------- */
        .quest-hero {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            padding: 34px 38px 32px;
            border-radius: 24px;
            color: #f4f7ff;
            background:
                radial-gradient(520px 220px at 100% 0%, rgba(124,192,255,.30), transparent 70%),
                radial-gradient(420px 220px at 0% 100%, rgba(255,212,59,.14), transparent 70%),
                linear-gradient(160deg, #16224a 0%, #0b1230 100%);
            box-shadow: 0 1px 0 rgba(255,255,255,.14) inset, 0 24px 48px -24px rgba(11,18,48,.7);
        }
        /* faint starfield */
        .quest-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            opacity: .55;
            background-image:
                radial-gradient(1.5px 1.5px at 12% 28%, #fff, transparent),
                radial-gradient(1px 1px at 34% 72%, #fff, transparent),
                radial-gradient(1.5px 1.5px at 58% 18%, #fff, transparent),
                radial-gradient(1px 1px at 76% 62%, #fff, transparent),
                radial-gradient(1.5px 1.5px at 90% 34%, #fff, transparent),
                radial-gradient(1px 1px at 22% 88%, #fff, transparent);
        }
        .quest-hero-top {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }
        .quest-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 12px;
            border-radius: 999px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: #cfe3ff;
            background: rgba(124,192,255,.14);
            border: 1px solid rgba(124,192,255,.28);
        }
        .quest-chip.xp {
            color: #ffe58a;
            background: rgba(255,212,59,.12);
            border-color: rgba(255,212,59,.32);
        }
        .quest-hero .lesson-heading {
            margin: 0 0 22px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(28px, 3.6vw, 42px);
            font-weight: 700;
            line-height: 1.08;
            letter-spacing: -0.03em;
            color: #ffffff;
        }
        .quest-hero .lesson-objective {
            margin: 0;
            padding: 16px 18px;
            border-radius: 16px;
            font-size: 16px;
            line-height: 1.65;
            color: #d5def5;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            backdrop-filter: blur(10px) saturate(160%);
            -webkit-backdrop-filter: blur(10px) saturate(160%);
        }
        .quest-hero .lesson-objective strong {
            display: block;
            margin-bottom: 4px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 12px;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #ffe58a;
        }
        .quest-hero code {
            background: rgba(124,192,255,.18);
            color: #e6f1ff;
        }

        /* ---------- STAGE CARDS ---------- */
        .quest-card {
            position: relative;
            border-radius: 22px;
            background: #ffffff;
            border: 1px solid rgba(11,18,48,.08);
            box-shadow: 0 1px 2px rgba(11,18,48,.04), 0 18px 36px -24px rgba(11,18,48,.25);
            overflow: hidden;
            animation: questRise .55s cubic-bezier(.22,1,.36,1) both;
            animation-delay: calc(var(--i, 0) * 70ms);
            transition: transform .35s cubic-bezier(.22,1,.36,1), box-shadow .35s ease;
        }
        .quest-card::before {   /* accent rail */
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 5px;
            background: linear-gradient(180deg, var(--q-accent), var(--q-accent-2));
        }
        .quest-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 1px 2px rgba(11,18,48,.04), 0 26px 44px -22px rgba(11,18,48,.32);
        }
        @keyframes questRise {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: none; }
        }

        .quest-card[data-kind="astro"]     { --q-accent: #7c5cff; --q-accent-2: #b6a3ff; }
        .quest-card[data-kind="code"]      { --q-accent: #0fa3b1; --q-accent-2: #6fe0e8; }
        .quest-card[data-kind="practice"]  { --q-accent: #f08c1a; --q-accent-2: #ffc46b; }
        .quest-card[data-kind="quiz"]      { --q-accent: #2fa56b; --q-accent-2: #86e0b0; }
        .quest-card[data-kind="diagram"]   { --q-accent: #0fa3b1; --q-accent-2: #6fe0e8; }
        .quest-card[data-kind="lab"]       { --q-accent: #2f6fe0; --q-accent-2: #73b6ff; }

        .relay-lab-note { margin-top: 16px; }

        /* Networking lesson extras: bullet lists and diagrams. */
        .body-list { margin: 0 0 1rem 1.25rem; line-height: 1.7; }
        .body-list li { margin-bottom: 4px; }
        .net-diagram { margin: 1rem 0; }
        .net-diagram svg { width: 100%; height: auto; display: block; font-family: inherit; }


        .quest-head {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 22px 28px 0 30px;
        }
        .quest-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            color: #ffffff;
            background: linear-gradient(145deg, var(--q-accent), var(--q-accent-2));
            box-shadow: 0 1px 0 rgba(255,255,255,.45) inset, 0 10px 18px -8px var(--q-accent);
            transition: transform .45s cubic-bezier(.34,1.56,.64,1);
        }
        .quest-card:hover .quest-icon { transform: rotate(-8deg) scale(1.08); }
        .quest-icon svg { width: 26px; height: 26px; }
        .quest-kicker {
            display: block;
            margin-bottom: 2px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--q-accent);
        }
        .quest-card .section-heading {
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.025em;
            color: var(--q-ink);
        }
        .quest-sub {
            margin: 26px 0 10px;
            padding-top: 20px;
            border-top: 1px solid rgba(11,18,48,.08);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.015em;
            color: var(--q-ink);
        }
        .quest-body { padding: 18px 30px 28px 34px; }
        .quest-body > :last-child { margin-bottom: 0; }

        .quest-card .body-text {
            font-size: 17px;
            line-height: 1.75;
            color: var(--q-text);
            margin-bottom: 18px;
        }
        .quest-card .body-text strong { color: var(--q-ink); font-weight: 600; }

        /* inline code becomes a small chip */
        .lesson-fragment code {
            font-family: 'Space Mono', monospace;
            font-size: .88em;
            padding: 2px 8px;
            border-radius: 8px;
            color: #1f4fa8;
            background: #eaf2fd;
        }
        .lesson-fragment .cb code, .lesson-fragment pre code { background: none; padding: 0; color: inherit; }

        /* ---------- QUIZ ---------- */
        .quiz-block {
            margin-bottom: 18px;
            padding: 20px 22px;
            border-radius: 18px;
            background: #f7faf8;
            border: 1px solid rgba(47,165,107,.16);
        }
        .quiz-prompt {
            margin: 0 0 14px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 600;
            line-height: 1.4;
            color: var(--q-ink);
        }
        .quiz-options { list-style: none; margin: 0 0 14px; padding: 0; display: grid; gap: 8px; }
        .quiz-option {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 16px;
            color: var(--q-text);
            background: #ffffff;
            border: 1px solid rgba(11,18,48,.09);
            transition: transform .25s cubic-bezier(.34,1.56,.64,1), border-color .2s ease, background .2s ease;
        }
        .quiz-option:hover { transform: translateX(4px); border-color: #2f7de1; background: #f3f8ff; }
        .quiz-option:active { transform: scale(.985); }
        .quiz-explanation {
            margin: 0;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 15px;
            line-height: 1.6;
            color: #454c60;
            background: #f3f4f7;
        }
        .quiz-explanation.ok  { color: #16306b; background: rgba(47,125,225,.09); box-shadow: inset 3px 0 0 #2f7de1; }
        .quiz-explanation.bad { color: #7a1f23; background: rgba(229,72,77,.09);  box-shadow: inset 3px 0 0 #e5484d; }

        /* ---------- INTERACTIVE QUIZ ---------- */
        .quiz-option[role="button"] { cursor: pointer; display: flex; align-items: center; gap: 10px; }
        .quiz-option[role="button"]:focus-visible { outline: 2px solid #2f7de1; outline-offset: 2px; }
        .quiz-option .qo-mark { margin-left: auto; flex: none; width: 22px; height: 22px; display: none; }
        .quiz-block[data-done] .quiz-option { cursor: default; transform: none; }
        .quiz-block[data-done] .quiz-option:hover { transform: none; border-color: rgba(11,18,48,.09); background: #fff; }
        .quiz-block[data-busy] .quiz-option { pointer-events: none; opacity: .7; }
        .quiz-option.is-correct { border-color: #2f7de1 !important; background: #eaf2fd !important; color: #16306b; font-weight: 600; }
        .quiz-option.is-correct .qo-mark { display: block; color: #2f7de1; }
        .quiz-option.is-wrong { border-color: #e5484d !important; background: #fdeeee !important; color: #9c2b2f; animation: qo-shake .35s ease; }
        .quiz-option.is-wrong .qo-mark { display: block; color: #e5484d; }
        .quiz-block[data-done] .quiz-option:not(.is-correct):not(.is-wrong) { opacity: .55; }
        .quiz-result {
            display: flex; align-items: center; gap: 8px; margin: 0 0 10px;
            font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700;
        }
        .quiz-result.ok  { color: #2f7de1; }
        .quiz-result.bad { color: #d23a3f; }
        .quiz-result .xp-pill { padding: 2px 10px; border-radius: 999px; background: #fff3c4; color: #8a6100; font-size: 13px; }
        .quiz-block .quiz-explanation { animation: qo-in .3s ease both; }
        .quiz-summary {
            display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
            margin-top: 6px; padding: 14px 18px; border-radius: 16px;
            background: #f3f7ff; border: 1px solid rgba(47,125,225,.18);
            font-family: 'Space Grotesk', sans-serif; font-size: 15px; color: #16306b;
        }
        .quiz-summary .qs-bar { flex: 1 1 140px; height: 8px; border-radius: 999px; background: rgba(47,125,225,.15); overflow: hidden; }
        .quiz-summary .qs-bar i { display: block; height: 100%; width: 0; border-radius: 999px; background: #2f7de1; transition: width .4s cubic-bezier(.32,.72,0,1); }
        .quiz-summary b { font-weight: 700; }
        .quiz-summary.complete { background: #eaf2fd; border-color: rgba(47,125,225,.3); color: #16306b; }
        .quiz-summary.complete .qs-bar i { background: #2f7de1; }
        .xp-toast {
            position: fixed; left: 50%; bottom: 36px; z-index: 80; transform: translate(-50%, 0);
            padding: 10px 20px; border-radius: 999px; background: #0d0d0d; color: #ffe58a;
            font: 700 15px 'Space Grotesk', sans-serif; box-shadow: 0 12px 32px -8px rgba(0,0,0,.4);
            animation: xp-rise 1.6s cubic-bezier(.32,.72,0,1) both; pointer-events: none;
        }
        @keyframes xp-rise { 0% { opacity: 0; transform: translate(-50%, 16px) scale(.9); } 15% { opacity: 1; transform: translate(-50%, 0) scale(1); } 80% { opacity: 1; } 100% { opacity: 0; transform: translate(-50%, -14px); } }
        @keyframes qo-shake { 25% { transform: translateX(-5px); } 55% { transform: translateX(5px); } 85% { transform: translateX(-2px); } }
        @keyframes qo-in { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }
        @media (prefers-reduced-motion: reduce) { .quiz-option.is-wrong, .quiz-block .quiz-explanation, .xp-toast { animation: none; } .xp-toast { opacity: 1; } .quiz-summary .qs-bar i { transition: none; } }

        /* ---------- CTA ---------- */
        .quest-card .cta-wrap { margin-top: 6px !important; }

        @media (max-width: 720px) {
            .quest-hero { padding: 26px 22px; border-radius: 20px; }
            .quest-head { padding: 18px 20px 0 22px; }
            .quest-body { padding: 14px 20px 22px 24px; }
            .quest-card .section-heading { font-size: 22px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .quest-card { animation: none; transition: none; }
            .quest-card:hover, .quest-card:hover .quest-icon { transform: none; }
            .quiz-option { transition: none; }
        }

        /* ── "Code it yourself" button ── */
        .code-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 14px 26px 14px 20px;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 14px;
            background: linear-gradient(180deg, #1d2a4d 0%, #0e1530 100%);
            color: #f4f7ff;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            text-decoration: none;
            cursor: pointer;
            overflow: hidden;
            isolation: isolate;
            box-shadow:
                0 1px 0 rgba(255,255,255,.18) inset,
                0 10px 24px -8px rgba(14,21,48,.55),
                0 0 0 0 rgba(90,159,212,0);
            transition:
                transform 0.35s cubic-bezier(.34,1.56,.64,1),
                box-shadow 0.35s ease,
                border-color 0.35s ease;
        }
        /* soft blue/yellow glow that follows the Python logo colors */
        .code-btn::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background:
                radial-gradient(120px 60px at 0% 0%, rgba(90,159,212,.35), transparent 70%),
                radial-gradient(120px 60px at 100% 100%, rgba(255,212,59,.22), transparent 70%);
            opacity: .7;
            transition: opacity 0.35s ease;
        }
        /* light sweep */
        .code-btn::after {
            content: "";
            position: absolute;
            top: 0; bottom: 0;
            left: -60%;
            width: 40%;
            z-index: -1;
            background: linear-gradient(105deg, transparent, rgba(255,255,255,.22), transparent);
            transform: skewX(-18deg);
            transition: left 0.7s cubic-bezier(.22,1,.36,1);
        }
        .code-btn .python-logo,
        .code-btn .networking-logo,
        .code-btn .cybersecurity-logo {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            transition: transform 0.45s cubic-bezier(.34,1.56,.64,1);
        }
        .code-btn-arrow {
            margin-left: 2px;
            opacity: .55;
            transform: translateX(-4px);
            transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), opacity 0.25s ease;
        }
        .code-btn:hover {
            transform: translateY(-2px);
            border-color: rgba(90,159,212,.55);
            box-shadow:
                0 1px 0 rgba(255,255,255,.22) inset,
                0 16px 32px -10px rgba(14,21,48,.6),
                0 0 24px -4px rgba(90,159,212,.45);
        }
        .code-btn:hover::before { opacity: 1; }
        .code-btn:hover::after  { left: 130%; }
        .code-btn:hover .python-logo,
        .code-btn:hover .networking-logo,
        .code-btn:hover .cybersecurity-logo { transform: rotate(-10deg) scale(1.12); }
        .code-btn:hover .code-btn-arrow { opacity: 1; transform: translateX(0); }
        /* responds on press, not release */
        .code-btn:active {
            transform: scale(0.97);
            transition-duration: 0.1s;
        }
        .code-btn:focus-visible {
            outline: 2px solid #5a9fd4;
            outline-offset: 3px;
        }
        @media (prefers-reduced-motion: reduce) {
            .code-btn, .code-btn::after, .code-btn .python-logo, .code-btn .networking-logo, .code-btn .cybersecurity-logo, .code-btn-arrow { transition: none; }
            .code-btn:hover, .code-btn:hover .python-logo, .code-btn:hover .networking-logo, .code-btn:hover .cybersecurity-logo { transform: none; }
        }
    </style>
</head>

<body>

@php
    // These come from PlanetController::viewModuleLesson(). Sensible
    // defaults so this view never breaks if hit without them.
    $slug   = $slug   ?? 'programming';
    $module = $module ?? 'm1';
    $lesson = $lesson ?? 'lesson01';

    // lessonView may be explicitly null (controller couldn't find a
    // matching file under either naming style) — leave it null rather
    // than guessing a string, so the check below shows the fallback.
    $lessonView = $lessonView ?? (\App\Services\CourseProgressService::viewBase($slug)
        ? \App\Services\CourseProgressService::viewBase($slug) . '.' . strtoupper($module) . ".{$lesson}"
        : null);
@endphp

{{--
    ┌─────────────────────────────────────────────────────────────┐
    │  CURRENT LESSON — injected by Blade on every page load.     │
    │  Kept in sync by loadLesson() on every client-side swap.    │
    │  The Python editor reads this to know which lesson to mark  │
    │  complete when the student gets the coding challenge right.  │
    └─────────────────────────────────────────────────────────────┘
--}}
<script>
    window.CURRENT_LESSON = {
        module: @json($module),
        lesson: @json($lesson),
    };
</script>


<div class="container">

    {{-- =========================================================
         SIDEBAR — the remote control. It only ever fires
         window.TechLab.loadLesson(module, lesson).
    ========================================================== --}}

    @include('components.programming-sidebar')


    {{-- =========================================================
         MAIN AREA — the TV. It never rebuilds itself, it just
         swaps what's showing on #lesson-stage.
    ========================================================== --}}

    <main class="main-area">

        <header class="header">

            <div class="header-left">

                <div
                    class="back-icon"
                    onclick="window.history.back()"
                    title="Go back"
                >
                    &#9664;
                </div>

                <div class="lesson-title" id="lesson-title-text">
                    Loading…
                </div>

            </div>


            <div class="header-right">

                <a id="ask-astro-link" href="/chat" class="icon" title="Ask Astro about this lesson"
                   style="text-decoration:none;width:auto;padding:0 12px;font-size:13px;font-weight:600;white-space:nowrap;">
                    🤖 Ask Astro
                </a>

                <button type="button" class="icon fs-btn" title="Full screen" aria-label="Full screen" onclick="toggleFullscreen()">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4h6v6M10 20H4v-6M20 4l-7 7M4 20l7-7"/></svg>
                </button>

            </div>

        </header>


        <div class="content">

            {{--
                THIS is the TV screen. Its id is what all the JS
                below targets when a sidebar button is pressed.
                It's server-rendered on first load (no flash / no
                extra round trip), then swapped client-side after.
            --}}
            <div class="lesson-content" id="lesson-stage">

                @if($lessonView && view()->exists($lessonView)){!! \App\Services\LessonQuizService::strip(view($lessonView)->render()) !!}@endif

                @unless(view()->exists($lessonView))
                    <div class="empty-lesson">
                        <div>
                            <h1>Lesson not found</h1>
                            <p style="margin-top: 10px;">
                                The selected lesson file does not exist yet.
                            </p>
                        </div>
                    </div>
                @endunless

                <div class="nav-arrows">

                    <div
                        class="nav-arrow"
                        onclick="goToPreviousLesson()"
                        title="Previous lesson"
                    >
                        &#9664;
                    </div>

                    <div
                        class="nav-arrow"
                        onclick="goToNextLesson()"
                        title="Next lesson"
                    >
                        &#9654;
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>


<script>
(function () {

    const slug  = @json($slug);
    // Networking and security lessons show console commands, not Python.
    const CODE_LANG = slug === 'programming' ? 'python' : 'bash';
    let current = { module: @json($module), lesson: @json($lesson) };

    const stage    = document.getElementById('lesson-stage');
    const titleEl  = document.getElementById('lesson-title-text');

    // Ordered list used only for the prev/next arrows.
    const lessonOrder = @json(collect(\App\Services\CourseProgressService::order($slug))->map(fn ($i) => ['module' => $i['module'], 'lesson' => $i['lesson']])->values());

    // Turn every plain <pre class="code-block"> in the lesson into the same
    // highlighted, copy-able box the chat uses. A block that follows an
    // "Output:" line is program output, so it stays uncoloured.
    function upgradeCodeBlocks() {
        if (!window.CodeHighlight) return;
        stage.querySelectorAll('pre.code-block').forEach(function (pre) {
            const code = pre.textContent.replace(/\n$/, '');
            const prev = pre.previousElementSibling;
            const isOutput = prev && /^\s*output\b/i.test(prev.textContent);
            const wrap = document.createElement('div');
            wrap.innerHTML = CodeHighlight.blockHtml(code, isOutput ? 'output' : CODE_LANG);
            const box = wrap.firstElementChild;
            box.style.marginBottom = '22px';
            pre.replaceWith(box);
        });
    }


    // Turn the plain lesson (h1, objective, then h2 + paragraphs...) into a
    // gamified layout: a hero with level/XP chips, then one card per section.
    // Works on any lesson because it only relies on .lesson-heading,
    // .lesson-objective and .section-heading — no per-lesson markup.
    const QUEST_ICONS = {
        brief:     '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5z"/><path d="M8 7h8M8 11h6"/>',
        astro:     '<path d="M12 3c3.5 2 5.5 5.5 5.5 9l-2 3h-7l-2-3c0-3.5 2-7 5.5-9z"/><circle cx="12" cy="10" r="1.6"/><path d="M9.5 18l-1.5 3M14.5 18l1.5 3"/>',
        code:      '<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M8 10l3 2-3 2M13 15h3"/>',
        practice:  '<path d="M13 2L4 14h7l-1 8 9-12h-7z"/>',
        lab:       '<circle cx="6" cy="7" r="2.2"/><circle cx="18" cy="7" r="2.2"/><circle cx="12" cy="18" r="2.2"/><path d="M8 8l3 8M16 8l-3 8M8.2 7h7.6"/>',
        diagram:   '<rect x="3" y="4" width="7" height="6" rx="1.5"/><rect x="14" y="14" width="7" height="6" rx="1.5"/><path d="M10 7h4a3 3 0 0 1 3 3v4"/>',
        quiz:      '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.7.4-1 .9-1 1.7M12 17h.01"/>',
        star:      '<path d="M12 3l2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.5 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"/>'
    };

    // Every lesson follows one fixed flow, whatever order its file is written in:
    //   1 Mission Brief · 2 Astro's Explanation · 3 Lab (code example) · 4 Quiz · 5 Coding Exercise
    // Anything else (challenge, rubric...) is folded into stage 5 as a sub-section.
    function questKind(title) {
        const t = title.toLowerCase().trim();
        // Networking: the hands-on simulator is the last stage (like the Python coding exercise).
        if (t.indexOf('relay lab') !== -1)    return { rank: 5, kind: 'lab', icon: 'lab', tag: 'Relay Lab' };
        // Security: "Defend it yourself" runs the Citadel Sim, also the last stage.
        if (t.indexOf('defense lab') !== -1)  return { rank: 5, kind: 'lab', icon: 'lab', tag: 'Defense Lab' };
        // Networking: the crew speaks (Astro is the captain, Rivet the engineer, Volt the shield tech).
        if (t.indexOf('rivet') !== -1)        return { rank: 2, kind: 'astro', icon: 'astro', tag: 'Rivet Says' };
        if (t.indexOf('volt') !== -1)         return { rank: 2, kind: 'astro', icon: 'astro', tag: 'Volt Says' };
        if (t.indexOf('crew') !== -1)         return { rank: 2, kind: 'astro', icon: 'astro', tag: 'Crew Briefing' };
        if (t.indexOf('astro') !== -1)        return { rank: 2, kind: 'astro', icon: 'astro', tag: 'Astro Says' };
        if (t.indexOf('diagram') !== -1)      return { rank: 3, kind: 'diagram', icon: 'diagram', tag: 'Field Diagram' };
        if (t.indexOf('code example') !== -1) return { rank: 3, kind: 'code',  icon: 'code',  tag: 'Lab' };
        if (t.indexOf('quiz') !== -1)         return { rank: 4, kind: 'quiz',  icon: 'quiz',  tag: 'Knowledge Check' };
        if (t.indexOf('explanation') !== -1 || t === 'brief' || t.indexOf('brief') !== -1)
                                              return { rank: 1, kind: 'brief', icon: 'brief', tag: 'Mission Brief' };
        return { rank: 5, kind: 'practice', icon: 'practice', tag: 'Coding Exercise' };
    }

    function upgradeSections() {
        const frag = stage.querySelector('.lesson-fragment');
        if (!frag || frag.dataset.quest === '1') return;
        frag.dataset.quest = '1';

        const nodes     = Array.from(frag.children);
        const heading   = nodes.find(function (n) { return n.classList.contains('lesson-heading'); });
        const objective = nodes.find(function (n) { return n.classList.contains('lesson-objective'); });

        // 1. Split the lesson into sections (a heading + everything up to the next heading).
        const sections = [];
        const loose = [];
        const ctas  = [];       // "Code it yourself" blocks, wherever the lesson file put them
        let cur = null;
        nodes.forEach(function (node) {
            if (node === heading || node === objective) return;
            if (node.classList.contains('section-heading')) {
                cur = { h: node, info: questKind(node.textContent), nodes: [] };
                sections.push(cur);
            } else if (node.classList.contains('cta-wrap') && cur && cur.info.rank !== 5) {
                ctas.push(node);
            } else if (cur) {
                cur.nodes.push(node);
            } else {
                loose.push(node);
            }
        });

        // Stage 5 always exists when there is a button, and always ends with it.
        if (ctas.length) {
            const last = sections.filter(function (sec) { return sec.info.rank === 5; }).pop();
            if (last) {
                ctas.forEach(function (n) { last.nodes.push(n); });
            } else {
                sections.push({ h: null, info: questKind('coding exercise'), nodes: ctas });
            }
        }

        // 2. Group by stage rank, in the fixed order. Stage 5 gathers all the "other" sections.
        const groups = {};
        sections.forEach(function (sec) {
            (groups[sec.info.rank] = groups[sec.info.rank] || []).push(sec);
        });
        const ranks = Object.keys(groups).map(Number).sort(function (a, b) { return a - b; });

        // 3. Hero
        const hero = document.createElement('header');
        hero.className = 'quest-hero';
        const mod = (frag.dataset.module || '').replace(/\D/g, '');
        const les = parseInt((frag.dataset.lesson || '').replace(/\D/g, ''), 10);
        const top = document.createElement('div');
        top.className = 'quest-hero-top';
        top.innerHTML =
            '<span class="quest-chip">Level ' + (mod || '?') + '.' + (les || '?') + '</span>' +
            '<span class="quest-chip">' + ranks.length + ' Stages</span>' +
            '<span class="quest-chip xp">+' + LESSON_XP + ' XP</span>';
        hero.appendChild(top);
        if (heading)   hero.appendChild(heading);
        if (objective) hero.appendChild(objective);

        // 4. One card per stage
        const out = [hero].concat(loose);
        ranks.forEach(function (rank, idx) {
            const list = groups[rank];
            const info = list[0].info;

            const card = document.createElement('section');
            card.className = 'quest-card';
            card.dataset.kind = info.kind;
            card.style.setProperty('--i', idx + 1);

            // Title: the first section's own heading, unless stage 5 is made of "other" sections.
            let title = list[0].h;
            const merged = rank === 5 && info.kind !== 'lab' && (!title || !/coding exercise/i.test(title.textContent));
            if (merged) {
                title = document.createElement('h2');
                title.className = 'section-heading';
                title.textContent = 'Coding Exercise';
            }

            const head = document.createElement('div');
            head.className = 'quest-head';
            head.innerHTML =
                '<span class="quest-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
                QUEST_ICONS[info.icon] + '</svg></span>' +
                '<div><span class="quest-kicker">Stage ' + (idx + 1) + ' &middot; ' + info.tag + '</span></div>';
            head.lastElementChild.appendChild(title);

            const body = document.createElement('div');
            body.className = 'quest-body';
            list.forEach(function (sec, i) {
                if (sec.h && (i > 0 || merged)) {   // extra sections become sub-headings
                    sec.h.className = 'quest-sub';
                    body.appendChild(sec.h);
                }
                sec.nodes.forEach(function (n) { body.appendChild(n); });
            });

            card.appendChild(head);
            card.appendChild(body);
            out.push(card);
        });

        frag.replaceChildren.apply(frag, out);
    }

    // ── Interactive quiz ───────────────────────────────────────────────
    // Options become buttons. The first answer to a question is checked (and scored) on the server;
    // the right answer + explanation only come back after answering, and a reload restores the results.
    const QUIZ_CSRF = @json(csrf_token());
    const LESSON_XP = {{ \App\Services\StudentDashboardService::XP_PER_LESSON }};
    const QUIZ_XP   = {{ \App\Services\StudentDashboardService::XP_PER_QUIZ_QUESTION }};
    const QO_OK  = '<svg class="qo-mark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>';
    const QO_BAD = '<svg class="qo-mark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>';

    function xpToast(xp) {
        const t = document.createElement('div');
        t.className = 'xp-toast';
        t.setAttribute('role', 'status');
        t.textContent = '+' + xp + ' XP';
        document.body.appendChild(t);
        setTimeout(function () { t.remove(); }, 1700);
    }

    function upgradeQuiz(moduleId, lessonId) {
        const blocks = Array.from(stage.querySelectorAll('.quiz-block'));
        if (!blocks.length) return;

        const base = '/student/planet/' + slug + '/quiz/' + moduleId + '/' + lessonId;
        const last = blocks[blocks.length - 1];
        let perQ = 10, earned = 0, done = 0;

        const summary = document.createElement('div');
        summary.className = 'quiz-summary';
        summary.setAttribute('aria-live', 'polite');
        summary.innerHTML = '<span class="qs-text"></span><span class="qs-bar"><i></i></span>';
        last.after(summary);

        function paintSummary() {
            summary.querySelector('.qs-text').innerHTML = done === blocks.length
                ? 'Quiz complete: <b>' + Math.round(earned / perQ) + '/' + blocks.length + '</b> correct · <b>+' + earned + ' XP</b>'
                : 'Answer each question to earn <b>' + perQ + ' XP</b> for every correct one · <b>' + done + '/' + blocks.length + '</b> answered';
            summary.querySelector('.qs-bar i').style.width = (done / blocks.length * 100) + '%';
            summary.classList.toggle('complete', done === blocks.length);
        }

        function show(block, r, animate) {
            block.dataset.done = '1';
            block.removeAttribute('data-busy');
            block.querySelectorAll('.quiz-option').forEach(function (li) {
                li.setAttribute('aria-disabled', 'true');
                li.tabIndex = -1;
                if (li.dataset.letter === r.correctChoice) { li.classList.add('is-correct'); li.insertAdjacentHTML('beforeend', QO_OK); }
                else if (li.dataset.letter === r.choice)   { li.classList.add('is-wrong');   li.insertAdjacentHTML('beforeend', QO_BAD); }
            });
            const res = document.createElement('p');
            res.className = 'quiz-result ' + (r.correct ? 'ok' : 'bad');
            res.innerHTML = r.correct
                ? 'Correct!' + (r.xp ? ' <span class="xp-pill">+' + r.xp + ' XP</span>' : '')
                : 'Not quite. The answer is ' + r.correctChoice + '.';
            const exp = document.createElement('p');
            exp.className = 'quiz-explanation ' + (r.correct ? 'ok' : 'bad');
            exp.innerHTML = r.explanation;   // written by us in the lesson file, not user input
            block.appendChild(res);
            if (r.explanation) block.appendChild(exp);
            if (animate && r.correct && r.xp) xpToast(r.xp);
            done++; earned += (r.correct ? (r.xp || perQ) : 0);
            paintSummary();
        }

        blocks.forEach(function (block) {
            const q = block.dataset.q;
            block.querySelectorAll('.quiz-option').forEach(function (li) {
                const m = li.textContent.trim().match(/^([A-D])\./);
                if (!m) return;
                li.dataset.letter = m[1];
                li.setAttribute('role', 'button');
                li.tabIndex = 0;

                async function pick() {
                    if (block.dataset.done || block.dataset.busy) return;
                    block.dataset.busy = '1';
                    try {
                        const res = await fetch(base, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': QUIZ_CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ q: Number(q), choice: m[1] })
                        });
                        const r = await res.json().catch(function () { return {}; });
                        if (!res.ok || !r.ok) throw new Error('bad');
                        if (!block.isConnected) return;
                        show(block, r, r.first !== false);
                    } catch (e) {
                        block.removeAttribute('data-busy');
                        if (!block.querySelector('.quiz-result')) {
                            const err = document.createElement('p');
                            err.className = 'quiz-result bad';
                            err.textContent = 'Could not check that answer. Try again.';
                            block.appendChild(err);
                            setTimeout(function () { err.remove(); }, 2500);
                        }
                    }
                }
                li.addEventListener('click', pick);
                li.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(); } });
            });
        });

        paintSummary();

        // Restore what this student already answered.
        fetch(base, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (st) {
                if (!st || !summary.isConnected) return;
                perQ = st.perQuestion || perQ;
                blocks.forEach(function (block) {
                    const a = st.answered && st.answered[block.dataset.q];
                    if (a && !block.dataset.done) show(block, { choice: a.choice, correct: a.correct, correctChoice: a.correctChoice, explanation: a.explanation, xp: a.xp }, false);
                });
                paintSummary();
            })
            .catch(function () {});
    }

    function syncTitleAndSidebar(moduleId, lessonId) {
        const heading = stage.querySelector('h1, .lesson-heading');
        if (titleEl) titleEl.textContent = heading ? heading.textContent.trim() : 'Untitled lesson';

        // "Ask Astro about this lesson" -> chat with this lesson pre-connected as a source.
        const askLink = document.getElementById('ask-astro-link');
        const num = String(lessonId).match(/(\d+)$/);
        if (askLink && num) {
            askLink.href = '/chat?source=' + encodeURIComponent(slug + '/' + String(moduleId).toUpperCase() + '/lesson-' + num[1]);
        }

        document.querySelectorAll('.lesson-item').forEach(function (a) {
            a.classList.toggle(
                'active',
                a.dataset.module === moduleId && a.dataset.lesson === lessonId
            );
        });
    }

    // THIS is the core function. Pressing a sidebar "channel button"
    // (or the prev/next arrows) always ends up calling this. It never
    // touches window.location — it fetches the fragment and drops it
    // straight into the TV screen.
    async function loadLesson(moduleId, lessonId, opts) {
        opts = opts || {};
        const pushState = opts.pushState !== false;

        const pageUrl     = `/student/planet/${slug}/${moduleId}/${lessonId}`;
        const fragmentUrl = `${pageUrl}/fragment`;

        stage.style.opacity = '0.35';

        try {
            const res = await fetch(fragmentUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (res.status === 403) {
                // Server says this lesson is still locked (the sidebar is display only).
                stage.innerHTML =
                    '<div class="empty-lesson"><div><h1>Lesson locked 🔒</h1>' +
                    '<p style="margin-top:10px;">' + (slug === 'networking' ? 'Pass the previous lesson\'s Relay Lab to unlock this one.' : slug === 'cybersecurity' ? 'Clear the previous lesson\'s Defense Lab to unlock this one.' : 'Complete the previous lesson\'s coding challenge to unlock this one.') + '</p></div></div>';
                return;
            }

            if (!res.ok) throw new Error('not found');

            const html = await res.text();

            stage.innerHTML = html;
            upgradeCodeBlocks();
            upgradeSections();
            upgradeQuiz(moduleId, lessonId);
            current = { module: moduleId, lesson: lessonId };

            // ── Keep CURRENT_LESSON in sync so the Python editor always
            //    knows which lesson it is displaying, even after a
            //    client-side swap via the sidebar or prev/next arrows.
            window.CURRENT_LESSON = { module: moduleId, lesson: lessonId };

            syncTitleAndSidebar(moduleId, lessonId);

            // Tell the sidebar to move its active highlight.
            if (window.TechLab && typeof window.TechLab.setActiveLesson === 'function') {
                window.TechLab.setActiveLesson(moduleId, lessonId);
            }

            if (pushState) {
                history.pushState({ module: moduleId, lesson: lessonId }, '', pageUrl);
            }
        } catch (err) {
            stage.innerHTML =
                '<div class="empty-lesson"><div><h1>Lesson not found</h1>' +
                '<p style="margin-top:10px;">Could not load this lesson.</p></div></div>';
        } finally {
            stage.style.opacity = '1';
        }
    }

    function goToPreviousLesson() {
        const idx = lessonOrder.findIndex(function (l) {
            return l.module === current.module && l.lesson === current.lesson;
        });
        if (idx > 0) {
            const prevLesson = lessonOrder[idx - 1];
            // Check if the previous lesson is unlocked before navigating
            if (window.TechLab && typeof window.TechLab.getLessonStatus === 'function') {
                const status = window.TechLab.getLessonStatus(prevLesson.module, prevLesson.lesson);
                if (status.unlocked) {
                    loadLesson(prevLesson.module, prevLesson.lesson);
                }
            } else {
                // Fallback: navigate anyway if TechLab not available
                loadLesson(prevLesson.module, prevLesson.lesson);
            }
        }
    }

    function goToNextLesson() {
        const idx = lessonOrder.findIndex(function (l) {
            return l.module === current.module && l.lesson === current.lesson;
        });
        if (idx !== -1 && idx < lessonOrder.length - 1) {
            const nextLesson = lessonOrder[idx + 1];
            // Check if the next lesson is unlocked before navigating
            if (window.TechLab && typeof window.TechLab.getLessonStatus === 'function') {
                const status = window.TechLab.getLessonStatus(nextLesson.module, nextLesson.lesson);
                if (status.unlocked) {
                    loadLesson(nextLesson.module, nextLesson.lesson);
                }
            } else {
                // Fallback: navigate anyway if TechLab not available
                loadLesson(nextLesson.module, nextLesson.lesson);
            }
        }
    }

    // Back/forward browser buttons should also just re-tune the TV,
    // not reload the page.
    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.module && e.state.lesson) {
            loadLesson(e.state.module, e.state.lesson, { pushState: false });
        }
    });

    // Expose the one function the sidebar is allowed to call.
    // The sidebar knows NOTHING else about how this panel works.
    window.TechLab = window.TechLab || {};
    window.TechLab.loadLesson = loadLesson;

    window.goToPreviousLesson = goToPreviousLesson;
    window.goToNextLesson = goToNextLesson;

    window.toggleFullscreen = function () {
        var on = !document.body.classList.contains('is-fullscreen');
        document.body.classList.toggle('is-fullscreen', on);
        try {
            if (on && !document.fullscreenElement && document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (!on && document.fullscreenElement && document.exitFullscreen) {
                document.exitFullscreen();
            }
        } catch (e) {}
    };

    // Esc (browser leaves fullscreen) -> leave our fullscreen layout too.
    document.addEventListener('fullscreenchange', function () {
        if (!document.fullscreenElement) {
            document.body.classList.remove('is-fullscreen');
        }
    });

    // Sync the header title + sidebar highlight for whatever lesson
    // was rendered server-side on first page load.
    document.addEventListener('DOMContentLoaded', function () {
        if (window.CodeHighlight) CodeHighlight.wire();
        upgradeCodeBlocks();
        upgradeSections();
        upgradeQuiz(current.module, current.lesson);
        syncTitleAndSidebar(current.module, current.lesson);

        // Tell the sidebar script (which may have already run) about
        // the initial active lesson.
        if (window.TechLab && typeof window.TechLab.setActiveLesson === 'function') {
            window.TechLab.setActiveLesson(current.module, current.lesson);
        }
    });

})();
</script>

<script src="{{ asset('js/code-highlight.js') }}?v={{ filemtime(public_path('js/code-highlight.js')) }}"></script>

<button type="button" class="fs-exit" title="Exit full screen" aria-label="Exit full screen" onclick="toggleFullscreen()">
    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5"/></svg>
</button>

</body>
</html>