# Hyperframes Composition Brief: TechLab

## Objective
Create a short, Duolingo-energy launch video for TechLab.

## Output
- Composition directory: `brag-output/composition/`
- Rendered video: `brag-output/brag.mp4`
- Format: vertical — 1080x1920
- Duration: 25 seconds

## Source Material
- Project root: `/Users/keyanjaoricrima/techlab`
- Primary files read: `resources/views/landingpage.blade.php`, `resources/views/components/astro-mascot.blade.php`, `resources/views/student/planets/lab.blade.php`, `config/course-catalog.php`, `design-system/techlab/MASTER.md`
- Product name: TechLab
- Tagline / strongest claim: "Level up your coding, networking and cybersecurity"
- Key UI or visual moment to recreate: Astro pixel helmet (16x15 grid, exact colors), XP / Streak / Gem coins, the C code editor (`code-editor.blade.php`: file tab, Run, Terminal, dark syntax colors from `public/js/code-highlight.js`), and NetSim labs for networking (m1-l1) and cybersecurity (c4-l3)
- Copy that must appear verbatim:
  - Level up your / Coding / Networking / Cybersecurity
  - Gamify your learning — XP "Earn per lesson", Streak "Come back daily", Gems "Unlock rewards"
  - Meet your tutor
  - Your turn! / XP counter 0 → 300
  - Code it yourself · Programming · diagnostics.c · Run · Diagnostics: PASS · You got it!
  - Configure it yourself · Networking · astro / relay / rivet · ping -n 3 192.168.1.20
  - Defend it yourself · Cybersecurity · PORT SWEEP DETECTED · OUTER HULL · 21 FTP / 80 Web / 3389 RDP · netsh advfirewall set allprofiles state on · DOOM ATTACK REPELLED
  - +100 XP per lab
  - Start your mission

## Creative Direction
- Tone preset: default (playful)
- Creative direction: Duolingo-style — mascot-led, bouncy springs, chunky 3D buttons with thick bottom borders, rewards popping on the beat
- Interpretation: fast springy entrances (0.3–0.4s, back/elastic eases), hard slide-up scene changes, generous holds on every line
- Angle: learning a serious subject that feels like leveling up in a game
- Hook: Astro drops in and bounces; "Level up your" + word pill cycling Coding → Networking → Cybersecurity
- Outro / punchline: Astro + logo + "Start your mission" button pop on a strong beat
- Avoid: generic SaaS language, abstract filler, Duolingo's own branding (no owl, no their green as brand) — TechLab blue + gold stay the identity

## Visual Identity
- Background: #2f7de1 → #3aa0ff gradient; lab scene #0c0a2a → #1b1750
- Text: #ffffff on blue, #1b1750 on light cards
- Accent: gold #ffcf3f / #f5a623, flame #f0491a, gem #8fe3ff, success green #16a34a
- Display font: Nunito 800/900 (rounded, playful)
- Body font: Nunito 700; code and terminals in Space Mono (the editor's font)
- Visual references: Astro mascot, coin icons from the landing page, lab status bar

## Storyboard
Contract: `brag-output/brag-plan.md`.
1. Hook — 0–4.0s — Astro + cycling skill word
2. Gamify your learning — 3.65–8.0s — 3 reward cards + XP bar
3. Meet your tutor — 7.65–12.5s — typed question, Astro answer, caption
4. Your turn: three labs — 12.15–21.0s — Code / Configure / Defend it yourself, XP counter 0 → 300
5. Outro — 20.65–25.0s — Astro, logo, tagline, CTA

## Audio
- Audio role: upbeat rhythmic bed + motion-matched soft accents
- Music: `assets/music/bed.mp3` (happy-beats-business-moves vol 1, 120 BPM), `data-media-start` 1.02 so beats fall on 0.5s multiples
- Music treatment: volume ~0.5, fade in 0.3s, fade out 1.5s
- Music cue guidance: preset `happy-beats-business-moves-vol-1-by-ende-dot-app.music-cues.json`; strong cues shifted −1.02s → locks at 16.5s (networking pass), 17.0s (cyber alarm), 19.0s (attack repelled), 22.0s (tagline), 22.5s (CTA)
- Audio-reactive treatment: subtle — background glow breathes on the beat grid (deterministic, from the preset beat grid; no FFT extraction helper available in this environment)
- Audio-coupled moments: word pops, card drops, key ticks while typing, tap click, success bell, CTA pop
- SFX: chosen from low-risk picks in `sfx-analysis.md`, copied into `assets/sfx/`

## Hyperframes Instructions
Single `index.html` composition, GSAP paused timeline registered at `window.__timelines["root"]`, scenes as timed `.clip` sections. Run `npx hyperframes check` before render. Keep everything local.
