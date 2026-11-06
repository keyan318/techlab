{{-- Shared styles for <x-avatar-frame>, <x-rank-badge> and the leaderboard rows. Included once per page. --}}
@once
<style>
  .af { position: relative; display: inline-block; flex: none; width: var(--af-size); height: var(--af-size); }
  .af > svg { position: absolute; inset: 0; width: 100%; height: 100%; overflow: visible; pointer-events: none; }
  .af-pic { position: absolute; left: 20%; top: 15%; width: 60%; height: 60%; overflow: hidden; display: flex; align-items: flex-end; justify-content: center;
    background: radial-gradient(circle at 50% 35%, var(--af-c1) 0%, var(--af-c0) 75%); color: #fff; font-weight: 700; }
  .af-round .af-pic { border-radius: 50%; }
  .af-sq .af-pic { border-radius: 24%; }
  .af-pic svg { width: 112%; height: 112%; flex: none; }
  .af-pic .af-initial { align-self: center; font-size: calc(var(--af-size) * .26); line-height: 1; }
  .af-num { font: 700 11px/1 'Pixelify Sans', 'Space Grotesk', sans-serif; letter-spacing: .02em; }

  /* motion: the higher the rank, the livelier the frame */
  .af .spin, .rb .spin { transform-box: view-box; transform-origin: 60px 54px; animation: af-spin 14s linear infinite; }
  .af .spin-rev { transform-box: view-box; transform-origin: 60px 54px; animation: af-spin 22s linear infinite reverse; }
  .af .pulse, .rb .pulse { animation: af-pulse 2.6s ease-in-out infinite; }
  .af .twinkle { animation: af-twinkle 2.2s ease-in-out infinite; }
  .af .twinkle:nth-of-type(2n) { animation-delay: .7s; }
  .af .twinkle:nth-of-type(3n) { animation-delay: 1.3s; }
  .af .sweep { stroke-dasharray: 26 214; animation: af-sweep 3.2s linear infinite; }
  .af .drift { animation: af-drift 7s ease-in-out infinite alternate; }
  .af .flicker { animation: af-flicker 1.4s steps(3) infinite; }
  @keyframes af-spin { to { transform: rotate(360deg); } }
  @keyframes af-pulse { 0%, 100% { opacity: .55; } 50% { opacity: 1; } }
  @keyframes af-twinkle { 0%, 100% { opacity: .2; transform: scale(.7); } 50% { opacity: 1; transform: scale(1); } }
  @keyframes af-sweep { to { stroke-dashoffset: -240; } }
  @keyframes af-drift { from { transform: translate(-3px, 1px); } to { transform: translate(3px, -2px); } }
  @keyframes af-flicker { 0% { opacity: .65; } 50% { opacity: 1; } 100% { opacity: .8; } }
  .af .twinkle { transform-box: fill-box; transform-origin: center; }

  /* rank badge */
  .rb { display: inline-flex; align-items: center; gap: .5rem; min-width: 0; vertical-align: middle; }
  .rb-emblem { position: relative; flex: none; width: var(--rb-size); height: calc(var(--rb-size) * 1.1); }
  .rb-emblem svg { position: absolute; inset: 0; width: 100%; height: 100%; overflow: visible; }
  .rb-emblem .spin { transform-origin: 20px 22px; }
  .rb-text { display: flex; min-width: 0; flex-direction: column; line-height: 1.1; }
  .rb-level { font: 700 1em/1 'Pixelify Sans', 'Space Grotesk', sans-serif; color: var(--rb-c2); }
  .rb-name { margin-top: .15em; font-size: .78em; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: var(--rb-c3); white-space: nowrap; }
  .rb-shine { animation: rb-shine 3.4s ease-in-out infinite; }
  @keyframes rb-shine { 0%, 55% { transform: translateX(-46px); } 100% { transform: translateX(46px); } }
  .rb-glow { filter: drop-shadow(0 0 4px var(--rb-c3)); animation: rb-glow 2.4s ease-in-out infinite; }
  @keyframes rb-glow { 0%, 100% { filter: drop-shadow(0 0 2px var(--rb-c3)); } 50% { filter: drop-shadow(0 0 7px var(--rb-c3)); } }
  html[data-theme="light"] .rb-level { color: var(--rb-c0); }
  html[data-theme="light"] .rb-name { color: var(--rb-c1); filter: brightness(.75); }

  /* leaderboard rows: slide in one after another, the top 3 glow in their medal colour */
  .lb-row { animation: lb-in .45s cubic-bezier(.2, .8, .2, 1) both; animation-delay: calc(var(--i, 0) * 55ms); }
  @keyframes lb-in { from { opacity: 0; transform: translateY(8px); } }
  .lb-top { box-shadow: inset 3px 0 0 var(--medal), 0 0 18px -8px var(--medal); }

  @media (prefers-reduced-motion: reduce) {
    .af *, .rb *, .rb, .lb-row { animation: none !important; }
  }
</style>
@endonce
