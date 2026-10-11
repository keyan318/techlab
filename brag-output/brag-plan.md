# Brag Plan: TechLab

## What is this app?
A school-style learning app that teaches coding, networking and cybersecurity like a game (XP, streaks, gems) with an AI tutor named Astro.

## The angle
Duolingo-energy: bouncy mascot, chunky cards, rewards popping on the beat. Learning a serious subject, but it feels like leveling up. Specific to TechLab: Astro the pixel astronaut, XP bolt / streak flame / gem coins, and the real "Lab complete +100 XP" moment.

## Hook (0-4s)
Astro drops in and bounces. "Level up your" then the word cycles Coding -> Networking -> Cybersecurity, one per beat-pair (the landing page's own headline).

## Key moments
- "Gamify your learning": three chunky cards land one by one: XP (Earn per lesson), Streak (Come back daily), Gems (Unlock rewards). A progress bar fills as each lands.
- "Meet your tutor": a student types a question, Astro answers. Caption: "Ask a question mid-lesson."
- The labs, one per planet, stacked like a checklist while an XP counter climbs 0 -> 300:
  - Programming, "Code it yourself": the C editor (diagnostics.c, Lesson 1.3) types printf("Diagnostics: PASS\n");, Run, terminal prints Diagnostics: PASS, "You got it!".
  - Networking, "Configure it yourself": NetSim (Lesson 1.1) astro pings rivet through the relay switch, "Reply from 192.168.1.20".
  - Cybersecurity, "Defend it yourself": NetSim (Lesson 4.3) PORT SWEEP DETECTED, firewall on, ports 21 and 3389 locked, 80 kept open, DOOM ATTACK REPELLED.

## Outro / punchline
Astro + TechLab logo, "Level up your coding, networking and cybersecurity", green button "Start your mission" pops on a strong beat.

## User flow worth showing
Ask Astro a question -> do the hands-on lab for each planet (Code / Configure / Defend it yourself) -> pass it, earn 100 XP each.

## Tone
- Preset: default (playful), pushed toward app-store polish
- Creative direction: Duolingo-style - mascot-led, bouncy, chunky 3D buttons, reward pops
- Interpretation: fast springy motion, but every text line holds long enough to read.

## Format: vertical - 1080x1920
## Duration: 25s

## Visual identity (from the project)
- Background: sky blue gradient #2f7de1 / #3aa0ff (Astro's visor blue); lab scene uses the app's night navy #0c0a2a / #1b1750
- Accent: gold #ffcf3f (XP bolt), flame orange #f0491a, gem cyan #8fe3ff, success green
- Text: white / #eaeeff on blue, navy #1b1750 on light cards
- Display font: Nunito (rounded, playful) in place of the site's Space Grotesk, to get the Duolingo feel
- Strongest visual element: Astro pixel helmet + the XP/Streak/Gem coins

## Share copy (draft)
Level up your coding, networking and cybersecurity. TechLab turns it into a game, with Astro as your tutor.

## Audio direction
- Role: dense-ish rhythmic bed with bouncy motion-matched accents
- Music: happy-beats-business-moves vol 1 (120 BPM), offset so beats land on 0.5s multiples
- Music treatment: fade in 0.3s, ~0.5 volume, fade out last 1.5s
- Music cue guidance: bundled preset. Strong cues (music time) 16.02, 17.02, 17.52, 18.02, 21.01, 22.01, 23.02 -> shifted by -1.02s in the composition: networking lab passes at 16.5s (cue 17.52), cyber alarm at 17.0s (cue 18.02), DOOM ATTACK REPELLED at 19.0s (cue 20.02), tagline 22.0s (cue 23.02), CTA 22.5s (cue 23.52). Beat grid every 0.5s used for words and cards.
- Audio-reactive treatment: subtle; the background glow breathes on the beat grid
- SFX posture: moderate, motion-matched (pops, clicks, key ticks, one bell for success)
- Restraint rule: low-risk soft sounds only; nothing sharp or repeated fast

## Storyboard
### Scene 1 - Hook - 4.0s
Astro bounces in; "Level up your" pops; word pill cycles Coding / Networking / Cybersecurity (each held 1s).
Sequential: yes, 3 words, 1s each. Audio: pop per word. Transition: slide-up -> Scene 2
### Scene 2 - Gamify your learning - 4.35s (3.65-8.0)
Heading, then XP, Streak, Gems cards at 4.5 / 5.5 / 6.5, XP bar fills. Hold the full set to 8.0.
Sequential: yes, cards 1s apart (readable). Transition: slide-up -> Scene 3
### Scene 3 - Meet your tutor - 4.85s (7.65-12.5)
Typed student question (key ticks), Astro typing dots, answer pops at 10.0, caption "Ask a question mid-lesson." at 11.0.
Sequential/interaction: typing. Transition: slide-up -> Scene 4
### Scene 4 - Your turn: three labs - 8.85s (12.15-21.0)
"Your turn!" + XP counter (0). Lab cards stack in one by one; each ends with a check, "+100 XP" and a counter bump:
- 12.75 Code it yourself (Programming): printf line types 13.0-13.8, Run 14.0, "Diagnostics: PASS" 14.15, "You got it!" + pass 14.5.
- 14.75 Configure it yourself (Networking): astro / relay / rivet pop 15.0-15.3, ping command typed 15.35, packet out and back 15.8-16.4, "Reply from 192.168.1.20" 16.3, pass 16.5 (beat-locked).
- 16.75 Defend it yourself (Cybersecurity): "PORT SWEEP DETECTED · OUTER HULL" alarm 17.0 (beat-locked), volt / relay / outer-hull + ports 21 FTP / 80 Web / 3389 RDP, sweep ripples 17.5, netsh firewall command 17.8, shield 18.0, 21 locked 18.1, 3389 locked 18.3, 80 open 18.5, "DOOM ATTACK REPELLED" + 300 XP + confetti 19.0 (beat-locked).
Interaction: typing, Run press, ping, port lockdown. Transition: slide-up -> Scene 5
### Scene 5 - Outro - 4.35s (20.65-25)
Astro bounce 21.0, logo + TechLab 21.5, tagline 22.0, "Start your mission" button 22.5 (beat-locked).

**Music mood:** upbeat
**Audio summary:** Happy bed with soft pops on every reveal, key ticks while typing, one bright bell on lab success, a final pop on the button.

## Grounding notes
Real copy used: Level up your Coding / Networking and Cybersecurity, Gamify your learning, XP Earn per lesson, Streak Come back daily, Gems Unlock rewards, Meet your tutor, Configure it yourself, Signal received. Lab complete +100 XP, Next lesson, Start your mission.
Lab content comes from real lessons: C course Lesson 1.3 "Anatomy of a Console" (diagnostics.c, Diagnostics: PASS, editor toast "You got it!"), networking lab m1-l1 (astro, rivet, relay, ping), cybersecurity lab c4-l3 (volt, relay, outer-hull, ports 21/80/3389, PORT SWEEP DETECTED, netsh advfirewall, DOOM ATTACK REPELLED). XP per lesson is 100 (StudentDashboardService::XP_PER_LESSON).
Invented demo content (UI stand-ins, not claims): the student question "What's an IP address?", Astro's short answer, the "C:\>" prompt and the trimmed ping reply line.
