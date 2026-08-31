{{-- Lesson: Thinking in Code — Chapter 1 interactive lesson.
     Pixel-art design system (Press Start 2P / VT323, quantized flat palette,
     hard-step borders). Stage is a tile grid; Alpine drives the mission:
     program array -> sequential run -> goal check. Instant state changes,
     no animation between steps yet. --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thinking in Code · TechLab</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet" />
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('mission', () => ({
        COLS: 8,
        ROWS: 5,
        start: { col: 1, row: 1, facing: 0 },   // facing: 0=E 90=S 180=W 270=N
        goal: { col: 7, row: 1 },               // scrap pile tile
        state: { col: 1, row: 1, facing: 0 },
        program: [],
        maxSlots: 16,
        status: 'idle',                         // idle | win | miss
        blocks: [
          { id: 'drive', label: 'DRIVE' },
          { id: 'left', label: 'TURN L' },
        ],

        get atGoal() {
          return this.state.col === this.goal.col && this.state.row === this.goal.row;
        },

        addBlock(id) {
          if (this.program.length < this.maxSlots) this.program.push(id);
        },
        removeBlock(i) {
          this.program.splice(i, 1);
        },

        turnLeft() {
          this.state.facing = (this.state.facing + 270) % 360;
        },
        driveForward() {
          const d = { 0: [1, 0], 90: [0, 1], 180: [-1, 0], 270: [0, -1] }[this.state.facing];
          const col = this.state.col + d[0];
          const row = this.state.row + d[1];
          if (col >= 0 && col < this.COLS && row >= 0 && row < this.ROWS) {
            this.state.col = col;
            this.state.row = row;
          }
        },

        /* Execute the program sequentially, instantly. */
        runProgram() {
          for (const b of this.program) {
            if (b === 'drive') this.driveForward();
            else if (b === 'left') this.turnLeft();
          }
          this.status = this.atGoal ? 'win' : 'miss';
        },

        resetAll() {
          this.state = { ...this.start };
          this.program = [];
          this.status = 'idle';
        },

        blockLabel(id) {
          return { drive: 'DRIVE', left: 'TURN L' }[id] ?? id.toUpperCase();
        },
        statusText() {
          if (this.status === 'win') return '> MISSION CLEAR! ASTRO MADE IT TO THE SCRAP PILE.';
          if (this.status === 'miss') return '> ASTRO IS NOT AT THE SCRAP PILE. TRY AGAIN.';
          return '> TAP BLOCKS, THEN PRESS CHECK.';
        },
      }));
    });
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    /* ---- Pixel design tokens: quantized flat palette ---- */
    :root {
      --px-navy:  #141329;
      --px-panel: #201e42;
      --px-deep:  #0b0a18;
      --px-blue:  #73b6ff;
      --px-violet:#9b6bff;
      --px-cyan:  #5be1ff;
      --px-ink:   #eef0ff;
    }

    .pixel {
      background: var(--px-navy);
      color: var(--px-ink);
      font-family: 'VT323', monospace;
      font-size: 20px;
      line-height: 1.45;
      image-rendering: pixelated;
    }
    .pixel img, .pixel svg, .pixel canvas {
      image-rendering: pixelated;
      shape-rendering: crispEdges;
    }
    .pixel h1, .pixel h2, .pixel button, .pixel .px-type {
      font-family: 'Press Start 2P', monospace;
      line-height: 1.6;
    }

    .px-panel {
      background: var(--px-panel);
      border: 3px solid var(--px-blue);
      outline: 3px solid var(--px-deep);
      border-radius: 0;
      padding: 16px;
    }

    .lesson {
      max-width: 720px;
      margin: 0 auto;
      padding: 24px 16px 48px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .mission-title {
      margin: 0;
      font-size: 13px;
      color: var(--px-cyan);
      letter-spacing: 1px;
    }
    .mission-title .px-cursor { color: var(--px-violet); }

    /* ---- Stage area: tile ground + floating sprite layer ---- */
    #stage {
      background: var(--px-navy);
      border-style: solid;
      border-color: var(--px-violet);
    }
    .tile-grid {
      position: relative;
      display: grid;
      grid-template-columns: repeat(8, 1fr);
      gap: 2px;
      padding: 8px;
      background: var(--px-deep);
    }
    .tile { aspect-ratio: 1; position: relative; border-radius: 0; }
    .t-floor {
      background: var(--px-navy);
      box-shadow: inset 0 0 0 2px rgba(32, 30, 66, .6);
    }
    .t-path { background: var(--px-panel); }
    .t-path::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 8px; height: 8px;
      background: var(--px-blue);
    }
    .t-crystal { background: var(--px-navy); }
    .t-crystal::before {
      content: '';
      position: absolute;
      left: 50%; bottom: 20%;
      width: 10px; height: 10px;
      margin-left: -5px;
      background: var(--px-cyan);
      box-shadow:
        0 -8px 0 var(--px-cyan),
        10px 4px 0 var(--px-violet),
        -10px 4px 0 var(--px-violet),
        0 8px 0 var(--px-violet),
        0 16px 0 var(--px-blue);
    }
    .t-glitch { background: var(--px-panel); }
    .t-glitch::before,
    .t-glitch::after {
      content: '';
      position: absolute;
      width: 6px; height: 6px;
    }
    .t-glitch::before {
      left: 15%; top: 20%;
      background: var(--px-cyan);
      box-shadow:
        14px 8px 0 var(--px-violet),
        26px -4px 0 var(--px-cyan),
        8px 22px 0 var(--px-ink),
        30px 18px 0 var(--px-violet);
    }
    .t-glitch::after {
      right: 18%; bottom: 16%;
      background: var(--px-violet);
      box-shadow:
        -12px 6px 0 var(--px-cyan),
        6px 12px 0 var(--px-ink),
        -20px -2px 0 var(--px-cyan);
    }

    /* ---- Astro sprite: absolute layer, positioned per state ---- */
    .astro-layer {
      position: absolute;
      inset: 8px;                    /* matches .tile-grid padding */
      pointer-events: none;
      z-index: 2;
    }
    .astro-bob {
      position: absolute;
      width: 12.5%;
      height: 20%;
      transition: none;
    }
    .astro-sprite {
      position: absolute;
      left: 50%;
      bottom: -3px;
      width: 150%;
      transform: translateX(-50%);
      filter: drop-shadow(3px 3px 0 var(--px-deep));
      animation: astro-bob .9s steps(2, jump-none) infinite alternate;
    }
    @keyframes astro-bob {
      from { margin-top: 0; }
      to   { margin-top: -7px; }
    }
    /* Facing tick: hard triangle orbiting the helmet, snaps per direction */
    .facing-tick {
      position: absolute;
      top: -9px;
      left: 50%;
      width: 0; height: 0;
      margin-left: -5px;
      border-left: 5px solid transparent;
      border-right: 5px solid transparent;
      border-bottom: 7px solid var(--px-cyan);
      transform-origin: 5px 16px;
    }

    /* ---- Parked ship ---- */
    .ship-token {
      background: var(--px-navy);
      box-shadow: inset 0 0 0 2px rgba(32, 30, 66, .6);
      position: relative;
      z-index: 2;
      pointer-events: none;
    }
    .ship-token { grid-column: 7 / span 2; grid-row: 1; }
    .ship-sprite {
      position: absolute;
      left: 50%;
      bottom: -2px;
      width: 92%;
      transform: translateX(-50%);
      filter: drop-shadow(3px 3px 0 var(--px-deep));
    }

    /* ---- Scrap pile: goal tile ---- */
    .scrap-token {
      grid-column: 8;
      grid-row: 2;
      background: var(--px-panel);
      box-shadow: inset 0 0 0 3px var(--px-cyan);
      position: relative;
      z-index: 2;
    }
    .scrap-sprite {
      position: absolute;
      left: 50%;
      bottom: -2px;
      width: 96%;
      transform: translateX(-50%);
      filter: drop-shadow(2px 2px 0 var(--px-deep));
    }
    .scrap-twinkle {
      animation: scrap-twinkle .8s steps(2, jump-none) infinite alternate;
    }
    @keyframes scrap-twinkle {
      from { opacity: 0; }
      to   { opacity: 1; }
    }

    /* ---- Program slots ---- */
    #program-slots {
      list-style: none;
      margin: 0 0 14px;
      padding: 0;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    #program-slots li {
      display: flex;
      align-items: center;
      gap: 12px;
      min-height: 44px;
      padding: 6px 12px;
      border: 3px dashed var(--px-blue);
      color: var(--px-blue);
      background: var(--px-navy);
      font-size: 20px;
      cursor: pointer;
    }
    #program-slots li.filled {
      border-style: solid;
      border-color: var(--px-violet);
      color: var(--px-ink);
    }
    #program-slots li .slot-num {
      font-family: 'Press Start 2P', monospace;
      font-size: 10px;
      color: var(--px-cyan);
    }
    #program-slots li .slot-x {
      margin-left: auto;
      color: var(--px-violet);
      font-family: 'Press Start 2P', monospace;
      font-size: 10px;
    }
    .slots-empty {
      border-color: var(--px-blue);
      justify-content: center;
      color: var(--px-blue);
      cursor: default;
      font-family: 'Press Start 2P', monospace;
      font-size: 9px;
      letter-spacing: 1px;
    }

    /* ---- Buttons + palette ---- */
    #program-controls { display: flex; gap: 14px; flex-wrap: wrap; }
    .px-btn {
      font-family: 'Press Start 2P', monospace;
      font-size: 11px;
      padding: 12px 18px;
      border-radius: 0;
      border: 3px solid var(--px-deep);
      box-shadow: 4px 4px 0 var(--px-deep);
      cursor: pointer;
      transition: none;
    }
    .px-btn-check { background: var(--px-cyan); color: var(--px-deep); }
    .px-btn-reset { background: var(--px-panel); color: var(--px-ink); border-color: var(--px-ink); }
    .px-btn:active {
      transform: translate(4px, 4px);
      box-shadow: 0 0 0 var(--px-deep);
    }

    #block-palette {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      min-height: 96px;
      align-content: flex-start;
    }
    .px-block {
      min-width: 118px;
      text-align: center;
      font-family: 'Press Start 2P', monospace;
      font-size: 10px;
      padding: 14px 14px;
      border-radius: 0;
      border: 3px solid var(--px-deep);
      box-shadow: 4px 4px 0 var(--px-deep);
      cursor: pointer;
    }
    .px-block-drive { background: var(--px-blue); color: var(--px-deep); }
    .px-block-left { background: var(--px-violet); color: var(--px-ink); }
    .px-block:active { transform: translate(4px, 4px); box-shadow: 0 0 0 var(--px-deep); }
    .px-block:focus-visible { outline: 3px solid var(--px-cyan); outline-offset: 2px; }

    /* ---- Status line ---- */
    .status {
      margin: 14px 0 0;
      font-size: 19px;
      letter-spacing: 1px;
    }
    .status.is-win { color: var(--px-cyan); }
    .status.is-miss { color: var(--px-violet); }
    .status.is-idle { color: var(--px-muted, #98a2d4); }

    @media (max-width: 560px) {
      .tile-grid { gap: 1px; }
      .tile { aspect-ratio: auto; height: 34px; }
      .astro-sprite { width: 170%; }
    }
    @media (prefers-reduced-motion: reduce) {
      .astro-sprite, .scrap-twinkle { animation: none; }
      .astro-bob { transition: none; }
    }
  </style>
</head>
<body class="pixel" x-data="mission">

  <main class="lesson">

    <header class="mission px-panel">
      <h1 class="mission-title">TAP THE BLOCKS TO WRITE A PROGRAM. GET ASTRO TO THE SCRAP PILE.<span class="px-cursor">_</span></h1>
    </header>

    {{-- Stage area: tile ground + Astro positioned by state --}}
    @php
      $stageMap = [
        '..C...SS',
        '.PPGP..X',
        'C...P..P',
        '..C.PPPP',
        '.....G..',
      ];
    @endphp
    <section id="stage" class="stage px-panel" aria-label="Stage area">
      <div class="tile-grid" role="img"
           aria-label="Alien planet ground: a walkable path leads from Astro across the field, past crystal outcrops and glitch-static patches, to a scrap metal pile beside his parked ship">
        @foreach ($stageMap as $ri => $row)
          @foreach (str_split($row) as $ci => $cell)
            @continue(($ri === 0 && $ci >= 6) || ($ri === 1 && $ci === 7))
            @if ($cell === 'P')
              <div class="tile t-path"></div>
            @elseif ($cell === 'C')
              <div class="tile t-crystal"></div>
            @elseif ($cell === 'G')
              <div class="tile t-glitch"></div>
            @else
              <div class="tile t-floor"></div>
            @endif
          @endforeach
        @endforeach

        <div class="astro-layer" aria-hidden="true">
          <div class="astro-bob"
               :style="`left:${state.col * 12.5}%; top:${state.row * 20}%;`">
            <svg class="astro-sprite" viewBox="0 0 10 13">
              <g shape-rendering="crispEdges">
                <rect x="4" y="0" width="1" height="1" fill="#5be1ff"/>
                <rect x="4" y="1" width="1" height="1" fill="#9b6bff"/>
                <rect x="3" y="2" width="4" height="1" fill="#eef0ff"/>
                <rect x="2" y="3" width="6" height="1" fill="#eef0ff"/>
                <rect x="2" y="4" width="1" height="1" fill="#eef0ff"/>
                <rect x="3" y="4" width="4" height="1" fill="#73b6ff"/>
                <rect x="7" y="4" width="1" height="1" fill="#eef0ff"/>
                <rect x="2" y="5" width="1" height="1" fill="#eef0ff"/>
                <rect x="3" y="5" width="1" height="1" fill="#5be1ff"/>
                <rect x="4" y="5" width="3" height="1" fill="#73b6ff"/>
                <rect x="7" y="5" width="1" height="1" fill="#eef0ff"/>
                <rect x="2" y="6" width="6" height="1" fill="#eef0ff"/>
                <rect x="1" y="7" width="2" height="3" fill="#9b6bff"/>
                <rect x="7" y="7" width="2" height="3" fill="#9b6bff"/>
                <rect x="3" y="7" width="4" height="1" fill="#eef0ff"/>
                <rect x="3" y="8" width="4" height="2" fill="#eef0ff"/>
                <rect x="4" y="8" width="2" height="1" fill="#5be1ff"/>
                <rect x="3" y="10" width="2" height="2" fill="#eef0ff"/>
                <rect x="5" y="10" width="2" height="2" fill="#eef0ff"/>
                <rect x="3" y="12" width="2" height="1" fill="#201e42"/>
                <rect x="5" y="12" width="2" height="1" fill="#201e42"/>
              </g>
            </svg>
            <span class="facing-tick" :style="`transform: rotate(${state.facing}deg)`"></span>
          </div>
        </div>

        <div class="ship-token" aria-hidden="true">
          <svg class="ship-sprite" viewBox="0 0 14 8" role="img" aria-label="Astro's parked ship">
            <g shape-rendering="crispEdges">
              <rect x="1" y="3" width="10" height="3" fill="#eef0ff"/>
              <rect x="0" y="4" width="1" height="1" fill="#73b6ff"/>
              <rect x="4" y="2" width="4" height="1" fill="#73b6ff"/>
              <rect x="5" y="2" width="1" height="1" fill="#5be1ff"/>
              <rect x="12" y="3" width="1" height="1" fill="#9b6bff"/>
              <rect x="9" y="1" width="2" height="2" fill="#9b6bff"/>
              <rect x="11" y="1" width="1" height="2" fill="#eef0ff"/>
              <rect x="2" y="6" width="1" height="2" fill="#201e42"/>
              <rect x="9" y="6" width="1" height="2" fill="#201e42"/>
              <rect x="1" y="7" width="2" height="1" fill="#0b0a18"/>
              <rect x="8" y="7" width="2" height="1" fill="#0b0a18"/>
              <rect x="1" y="5" width="10" height="1" fill="#201e42"/>
            </g>
          </svg>
        </div>

        <div class="scrap-token" aria-hidden="true">
          <svg class="scrap-sprite" viewBox="0 0 10 8" role="img" aria-label="Scrap metal pile">
            <g shape-rendering="crispEdges">
              <rect x="0" y="6" width="10" height="2" fill="#201e42"/>
              <rect x="1" y="5" width="8" height="1" fill="#34305c"/>
              <rect x="2" y="3" width="2" height="2" fill="#eef0ff"/>
              <rect x="4" y="2" width="2" height="4" fill="#9b6bff"/>
              <rect x="6" y="4" width="3" height="2" fill="#eef0ff"/>
              <rect x="3" y="4" width="1" height="1" fill="#73b6ff"/>
              <g class="scrap-twinkle">
                <rect x="2" y="2" width="1" height="1" fill="#5be1ff"/>
                <rect x="7" y="3" width="1" height="1" fill="#5be1ff"/>
                <rect x="5" y="1" width="1" height="1" fill="#eef0ff"/>
              </g>
            </g>
          </svg>
        </div>
      </div>
    </section>

    {{-- Program area: dynamic slots + controls --}}
    <aside id="program-area" class="program-area px-panel" aria-label="Your program">
      <ol id="program-slots" class="program-slots">
        <li class="slots-empty" x-show="program.length === 0">&gt; PROGRAM EMPTY — TAP BLOCKS BELOW</li>
        <template x-for="(blockId, i) in program" :key="i">
          <li class="filled" @click="removeBlock(i)" title="Tap to remove">
            <span class="slot-num" x-text="String(i + 1).padStart(2, '0')"></span>
            <span x-text="blockLabel(blockId)"></span>
            <span class="slot-x">X</span>
          </li>
        </template>
      </ol>
      <div id="program-controls" class="program-controls">
        <button type="button" id="check-btn" class="px-btn px-btn-check" @click="runProgram()">CHECK</button>
        <button type="button" id="reset-btn" class="px-btn px-btn-reset" @click="resetAll()">RESET</button>
      </div>
      <p class="status" :class="'is-' + status" x-text="statusText()" data-status></p>
    </aside>

    {{-- Block palette: tap to append to the program --}}
    <section id="block-palette" class="block-palette px-panel" aria-label="Block palette">
      <template x-for="b in blocks" :key="b.id">
        <button type="button" class="px-block" :class="'px-block-' + b.id"
                :data-block="b.id" @click="addBlock(b.id)"
                x-text="b.label"></button>
      </template>
    </section>

  </main>

</body>
</html>
