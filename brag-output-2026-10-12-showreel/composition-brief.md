# Hyperframes Composition Brief: TechLab Showreel

## Objective
A 25s landscape showreel/launch video for TechLab where real app screens are the footage.

## Output
- Composition directory: `brag-output-2026-10-12-showreel/composition/`
- Rendered video: `brag-output-2026-10-12-showreel/brag.mp4`
- Format: landscape — 1920x1080, 30fps
- Duration: 25s

## Source Material
- Project root: /Users/keyanjaoricrima/techlab
- Files read: `resources/views/landingpage.blade.php`, `components/astro-mascot.blade.php`, `student/dashboard`, `student/planets/code-editor.blade.php`, `config/course-structure.php`, `public/citadel-sim/js/app.js`, `StudentDashboardService.php`
- Real screens (captured 1920x1080 from a local SQLite copy, fictional student): `assets/shots/*.png`
- Real art: `assets/img/{programming-city-pixel,networking-nebula,cybersecurity-citadel}.png`, `xp.svg`, `streak.svg`, `gem.svg` (from the landing page DOM), `astro-glove.svg` (app cursor); pinwheel traced to rects from `public/apple-touch-icon.png`; Astro sprite from `astro-mascot.blade.php`
- Copy that must appear verbatim: "Level up your", "Coding", "Networking", "Cybersecurity", "Start your mission", "Ask a question mid-lesson.", "Code it yourself", "Configure it yourself", "Defend it yourself", "You got it!", "ALL CLEAR · Attack repelled", "+100 XP"

## Creative Direction
- Tone preset: cinematic (+ app-store polish); direction: motion designer's showreel
- Hook: pinwheel assembles, "This is TechLab"
- Outro: landing hero rebuilt, glove cursor presses "Start your mission", end card
- Avoid: generic SaaS language, abstract filler, waveform visuals, flashing text

## Visual Identity
- Background #06061a + purple nebula; brand blue #2f7de1; logo #73b6ff; planet accents #73b6ff/#5be1ff/#9b6bff; gold #ffcf3f; text #eaeeff; navy #1b1750
- Fonts: Space Grotesk 700 display, Pixelify Sans pixel heads, Inter body, Space Mono labels (all local woff2)

## Storyboard
See `brag-plan.md` (creative contract). Scenes: 1 Logo hook 0–2.25 · 2 Planets 2.25–6.9 · 3 Rewards 6.9–10.6 · 4 Astro 10.6–14.0 · 5 Labs 14.0–19.75 · 6 Outro 19.75–25.

## Audio
- VO: `assets/vo/take-a.mp3` (ElevenLabs) at 0.5s, track 3
- Music: `assets/music/bed.mp3` (vol 1, 120 BPM), media-start 1.02; ~0.3 under VO, lift to ~0.55 at 22.4, fade out over last 1.8s
- Cue guidance: `assets/music/happy-beats-business-moves-vol-1-by-ende-dot-app.music-cues.json`; beat-locks at 15.0, 16.0, 17.0, 18.0, 19.0, 22.0 (video time); beat-grid every 0.5s
- Audio-reactive: subtle glow breathing on the beat grid
- SFX: Kenney picks (low HF risk first, per `sfx-analysis.md`) + locally synthesized whoosh/swish/boom/riser/sparkle; fire on the same frame as the motion
