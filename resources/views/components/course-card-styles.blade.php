{{-- The course card look (pixel-art banner, COURSE eyebrow, title, blurb, level pill), shared by the
     student course picker (student/planets/courses) and the faculty Courses page (faculty/courses). --}}
<style>
  /* Course cards, Codedex-style: a pixel-art banner on top, then COURSE eyebrow, title, blurb and a
     level pill. The art is dark, so the card stays dark (and its text light) in both themes. */
  .course-card {
    --accent: #73b6ff;
    position: relative; display: flex; flex-direction: column; overflow: hidden; isolation: isolate;
    color: #eef1ff; background: #0d1028; text-decoration: none;
    border: 2px solid #252b52; border-radius: 6px;
    box-shadow: 0 5px 0 #1a1f40, 0 18px 36px -20px rgba(0,0,0,.8);
    transition: transform .25s cubic-bezier(.32,.72,0,1), box-shadow .25s cubic-bezier(.32,.72,0,1), border-color .2s ease;
  }
  a.course-card:hover { transform: translateY(-4px); border-color: var(--accent); box-shadow: 0 9px 0 #1a1f40, 0 26px 44px -18px color-mix(in srgb, var(--accent) 45%, transparent); }
  a.course-card:active { transform: translateY(1px); box-shadow: 0 2px 0 #1a1f40; transition-duration: .08s; }
  a.course-card:focus-visible { outline: 3px solid var(--accent); outline-offset: 3px; }
  .course-banner { position: relative; aspect-ratio: 5 / 2; overflow: hidden; border-bottom: 2px solid #252b52; }
  .course-art { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center bottom; image-rendering: pixelated; transition: transform .5s cubic-bezier(.32,.72,0,1); }
  a.course-card:hover .course-art { transform: scale(1.04); }
  .course-banner > svg { position: absolute; right: 18px; bottom: -14px; width: 96px; height: 96px; opacity: .9; }
  .course-banner .scene-python-space { position: absolute; inset: 0; width: 100%; height: 100%; right: auto; bottom: auto; opacity: 1; }
  .ps-star { animation: ps-twinkle 3s steps(2, end) infinite; }
  @keyframes ps-twinkle { 50% { opacity: .25; } }
  .course-eyebrow { font-family: 'Space Mono', monospace; font-size: 11.5px; letter-spacing: .18em; text-transform: uppercase; color: #9aa3d0; }
  .course-title { font-family: 'Space Grotesk', sans-serif; font-size: 1.45rem; line-height: 1.2; letter-spacing: -.01em; font-weight: 700; color: #fff; }
  .course-blurb { font-size: .95rem; line-height: 1.5; color: #c3c9ea; }
  .course-pill { display: inline-flex; align-items: center; gap: 9px; padding: 7px 14px; border-radius: 99px;
    font-family: 'Space Mono', monospace; font-size: 11.5px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #dfe4ff; background: #1b2042; }
  .course-bars { display: inline-flex; align-items: flex-end; gap: 2px; height: 11px; }
  .course-bars i { width: 3px; background: #48507e; }
  .course-bars i:nth-child(1) { height: 4px; } .course-bars i:nth-child(2) { height: 7px; } .course-bars i:nth-child(3) { height: 11px; }
  .course-bars i.on { background: var(--accent); }
  .course-pct { font-size: 12.5px; font-weight: 600; color: #c3c9ea; }
  .course-bar { height: 4px; background: #1b2042; overflow: hidden; }
  .course-bar > i { display: block; height: 100%; background: var(--accent); transition: width .6s cubic-bezier(.32,.72,0,1); }
  .course-go { display: inline-flex; align-items: center; gap: 6px; font-family: 'Space Grotesk', sans-serif; font-size: .9rem; font-weight: 600; color: var(--accent); white-space: nowrap; }
  .course-go svg { transition: transform .3s cubic-bezier(.32,.72,0,1); }
  a.course-card:hover .course-go svg { transform: translateX(4px); }
  /* Coming soon / locked: same shape, quieter. Not a link, so no hover lift. */
  .course-card.is-soon { cursor: default; border-style: dashed; box-shadow: none; }
  .course-card.is-soon .course-banner { filter: saturate(.7); }
  .course-card.is-closed .course-art { filter: grayscale(.6) brightness(.6); }
  .soon-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 99px; font-size: 12px; font-weight: 600; color: #ffe08a; background: rgba(245,192,74,.12); border: 1px solid rgba(245,192,74,.3); }
  .soon-pill i { width: 6px; height: 6px; border-radius: 50%; background: #f5c04a; animation: soon-pulse 2.4s ease-in-out infinite; }
  @keyframes soon-pulse { 50% { opacity: .35; } }
  .soon-glyph { position: absolute; inset: 0; display: grid; place-items: center; font-family: 'Space Mono', monospace; font-size: 40px; letter-spacing: .05em; color: rgba(255,255,255,.55); text-shadow: 0 0 24px rgba(155,107,255,.6); }
  .captain-line { display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: rgb(190 198 235); margin-top: 2px; }
  .captain-line svg { flex: none; opacity: .8; }
  @media (prefers-reduced-motion: reduce) {
    .course-card, .course-art, .course-go svg, .course-bar > i { transition: none; }
  }
</style>
