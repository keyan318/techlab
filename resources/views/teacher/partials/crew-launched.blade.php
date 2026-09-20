{{-- Crew-launched hero (spaceship, crew code, stats). Removed from the dashboard but kept here for reuse. Needs $crew, $quizCount, $submissionCount. --}}
{{-- CREW LAUNCHED --}}
<span class="eyebrow">Captain · Crew Launched</span>
<h1>Your <span class="accent">spaceship</span> is ready</h1>
<p class="sub">Share the crew code with your students. When they board, your squad's shared planet begins to grow.</p>

<div class="ship-stage">
    <div class="orbit"></div>
    <div class="dock"></div>
    <svg class="ship" viewBox="0 0 120 220" aria-hidden="true">
      <defs>
        <linearGradient id="hull" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#eaf2ff"/><stop offset="100%" stop-color="#9fb4e6"/></linearGradient>
        <linearGradient id="flame" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#fff3b0"/><stop offset="60%" stop-color="#ff9b3d"/><stop offset="100%" stop-color="#ff4d6d"/></linearGradient>
      </defs>
      <g class="thruster"><path d="M48 168 q12 34 12 46 q0 -12 12 -46 z" fill="url(#flame)"/></g>
      <path d="M60 8 q32 42 32 96 q0 44 -32 56 q-32 -12 -32 -56 q0 -54 32 -96z" fill="url(#hull)" stroke="#7c8cc8" stroke-width="2"/>
      <circle cx="60" cy="74" r="15" fill="#5be1ff" stroke="#1b3a5c" stroke-width="2"/>
      <path d="M28 120 q-22 8 -22 44 q22 -12 32 -22z" fill="#9b6bff"/>
      <path d="M92 120 q22 8 22 44 q-22 -12 -32 -22z" fill="#9b6bff"/>
    </svg>
  </div>

<div class="code-card">
  <div class="k">Crew code — give this to your students</div>
  <div class="code-row">
    <span class="code-val" id="codeVal">{{ $crew->code }}</span>
    <button class="copy" id="copyBtn" type="button">Copy</button>
  </div>
  <p class="note">Crew name: <strong>{{ $crew->name }}</strong> · {{ $crew->roster->count() }} aboard</p>
</div>

<div class="stats">
  <div class="stat"><div class="n">{{ $crew->roster->where('pivot.role', '!=', 'teacher')->count() }}</div><div class="l">Astronauts</div></div>
  <div class="stat"><div class="n">{{ $crew->modules->count() }}</div><div class="l">Modules</div></div>
  <div class="stat"><div class="n">{{ $quizCount }}</div><div class="l">Quizzes</div></div>
  <div class="stat"><div class="n">{{ $submissionCount }}</div><div class="l">Submissions</div></div>
</div>

<p style="margin-top:22px"><a class="mbtn solid" href="{{ route('teacher.crew') }}" style="display:inline-block">Manage crew, modules &amp; quizzes →</a></p>

@push('scripts')
<script>
const copyBtn = document.getElementById('copyBtn');
if (copyBtn) {
  copyBtn.addEventListener('click', () => {
    const code = document.getElementById('codeVal').textContent.trim();
    navigator.clipboard?.writeText(code).then(() => {
      copyBtn.textContent = 'Copied!';
      setTimeout(() => (copyBtn.textContent = 'Copy'), 1600);
    });
  });
}
</script>
@endpush
