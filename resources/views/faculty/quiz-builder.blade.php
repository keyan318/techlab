@extends('layouts.faculty-shell')

@php
  $miniBoss ??= null;
  // Where Back (and Publish) return to: the course page's section, or the Mini bosses tab.
  $backUrl = $miniBoss ? route('faculty.exams.index', $crew).'#boss-m'.$miniBoss : ($isFinal ? route('faculty.exams.index', $crew).'#final-exam' : route('faculty.course', $crew).'#quizzes');
@endphp
@section('title', $isFinal ? 'Final exam builder' : ($miniBoss ? 'Mini boss builder' : 'Quiz builder'))

@push('head')
<style>
  /* The shell caps pages at 860px and centres text; the builder needs room for the toolbar,
     and with the reference panel open it is a split screen that uses the whole width. */
  .wrap:has(.gf) { max-width: 1400px; text-align: left; }
  @media (min-width: 1100px) { .wrap:has(.gf-body.with-drawer) { max-width: none; padding-left: 24px; padding-right: 24px; } }

  .gf {
    --gf-card: rgba(255,255,255,.045); --gf-border: rgba(255,255,255,.12); --gf-field: rgba(255,255,255,.06);
    --gf-line: rgba(255,255,255,.3); --gf-text: var(--text, #eef0ff); --gf-muted: var(--muted, #9aa3c7);
    --gf-accent: #73b6ff; --gf-link: #8cc4ff; --gf-bar: #73b6ff; --gf-good: #8bd83a; --gf-bad: #ff6b81;
    --gf-shadow: 0 1px 3px rgba(0,0,0,.35);
    color: var(--gf-text);
  }
  html[data-theme="light"] .gf {
    --gf-card: #fff; --gf-border: #dadce0; --gf-field: #f1f3f4; --gf-line: #80868b;
    --gf-text: #202124; --gf-muted: #5f6368; --gf-link: #1a66d2; --gf-good: #188038; --gf-bad: #c5221f;
    --gf-shadow: 0 1px 2px rgba(60,64,67,.15), 0 1px 3px 1px rgba(60,64,67,.08);
  }
  .gf.final { --gf-bar: #ffc400; }

  /* ── Top bar ── */
  .gf-top { display: flex; flex-wrap: wrap; align-items: center; gap: 12px 18px; padding: 4px 0 14px; border-bottom: 1px solid var(--gf-border); }
  .gf-back { color: var(--gf-muted); font-size: .88rem; text-decoration: none; }
  .gf-back:hover { color: var(--gf-text); }
  .gf-tabs { display: flex; gap: 4px; margin: 0 auto; }
  .gf-tab { position: relative; padding: 8px 16px; border: 0; background: none; color: var(--gf-muted); font: 600 .95rem/1 inherit; cursor: pointer; border-radius: 8px; }
  .gf-tab:hover { color: var(--gf-text); background: var(--gf-field); }
  .gf-tab[aria-selected="true"] { color: var(--gf-link); }
  .gf-tab[aria-selected="true"]::after { content: ''; position: absolute; left: 10px; right: 10px; bottom: -15px; height: 3px; border-radius: 3px 3px 0 0; background: var(--gf-link); }
  .gf-actions { display: flex; align-items: center; gap: 10px; }
  .gf-chip { font-size: .75rem; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: var(--gf-field); color: var(--gf-muted); }
  .gf-chip.live { background: rgba(139,216,58,.15); color: var(--gf-good); }

  /* ── Buttons ── */
  .gf-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; border: 1px solid var(--gf-border); background: transparent; color: var(--gf-text); font: 600 .88rem/1 inherit; cursor: pointer; text-decoration: none; }
  .gf-btn:hover { background: var(--gf-field); }
  .gf-btn.solid { background: var(--gf-accent); border-color: var(--gf-accent); color: #06122a; }
  .gf-btn.solid:hover { filter: brightness(1.06); }
  .gf-btn:disabled { opacity: .5; cursor: not-allowed; }
  .gf-btn.sm { padding: 6px 10px; font-size: .8rem; }
  .gf-link { border: 0; background: none; padding: 0; color: var(--gf-link); font: 600 .8rem/1.4 inherit; cursor: pointer; }
  .gf-link:hover { text-decoration: underline; }
  .gf-icon { display: inline-grid; place-items: center; width: 40px; height: 40px; border: 0; border-radius: 50%; background: none; color: var(--gf-muted); cursor: pointer; }
  .gf-icon:hover { background: var(--gf-field); color: var(--gf-text); }
  .gf-icon:disabled { opacity: .35; cursor: not-allowed; background: none; }
  .gf-icon svg, .gf-btn svg { width: 20px; height: 20px; }

  /* ── Layout: drawer · form · toolbar ── */
  .gf-body { display: grid; grid-template-columns: minmax(0, 1fr); gap: 20px; padding-top: 20px; align-items: start; }
  @media (min-width: 1100px) {
    .gf-body { grid-template-columns: minmax(0, 1fr) 56px; }
    /* Split screen: the material on the left half, the form on the right. */
    .gf-body.with-drawer { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) 56px; }
  }
  .gf-main { width: 100%; max-width: 770px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; min-width: 0; }
  .gf-main fieldset { border: 0; margin: 0; padding: 0; min-width: 0; display: flex; flex-direction: column; gap: 12px; }

  /* ── Cards ── */
  .gf-card { position: relative; background: var(--gf-card); border: 1px solid var(--gf-border); border-radius: 8px; box-shadow: var(--gf-shadow); padding: 22px 24px; }
  .gf-head { border-top: 10px solid var(--gf-bar); padding-top: 18px; }
  .gf-q { cursor: pointer; transition: box-shadow .15s; }
  .gf-q.active { cursor: default; }
  .gf-q.active::before { content: ''; position: absolute; left: -1px; top: -1px; bottom: -1px; width: 6px; border-radius: 8px 0 0 8px; background: #4285f4; }
  .gf-head.active::before { display: none; }

  /* ── Fields: grey fill, underline that lights up on focus ── */
  .gf-in { width: 100%; padding: 10px 2px 8px; border: 0; border-bottom: 1px solid var(--gf-border); background: transparent; color: var(--gf-text); font: inherit; font-size: .95rem; outline: none; }
  .gf-in:hover { border-bottom-color: var(--gf-line); }
  .gf-in:focus { border-bottom: 2px solid var(--gf-link); padding-bottom: 7px; }
  .gf-in::placeholder { color: var(--gf-muted); }
  .gf-in.title { font-size: 2rem; font-weight: 500; line-height: 1.2; }
  .gf-in.prompt { background: var(--gf-field); padding: 14px 14px 12px; border-radius: 4px 4px 0 0; font-size: 1rem; resize: vertical; min-height: 54px; }
  .gf-in:disabled { opacity: .8; }
  .gf-sel { padding: 12px 14px; border: 1px solid var(--gf-border); border-radius: 4px; background: var(--gf-card); color: var(--gf-text); font: inherit; font-size: .92rem; min-width: 190px; }
  .gf-sel:focus { outline: 2px solid var(--gf-link); outline-offset: 1px; }

  .gf-row { display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-start; }
  .gf-opt { display: flex; align-items: center; gap: 12px; margin-top: 6px; }
  .gf-mark { flex: none; display: grid; place-items: center; width: 22px; height: 22px; padding: 0; border: 2px solid var(--gf-line); background: none; cursor: pointer; color: transparent; }
  .gf-mark.round { border-radius: 50%; }
  .gf-mark.square { border-radius: 3px; }
  .gf-mark.num { border: 0; color: var(--gf-muted); font-size: .85rem; width: auto; min-width: 22px; }
  .gf-mark.on { border-color: var(--gf-good); background: var(--gf-good); color: #fff; }
  .gf-mark.num.on { background: none; color: var(--gf-good); font-weight: 700; }
  .gf-mark svg { width: 14px; height: 14px; }
  .gf-opt .gf-in { flex: 1; }
  .gf-opt-add { color: var(--gf-muted); font-size: .92rem; }

  .gf-source { display: flex; align-items: center; gap: 8px; margin-top: 16px; color: var(--gf-muted); font-size: .85rem; }
  .gf-source svg { width: 16px; height: 16px; flex: none; }
  .gf-source .gf-in { font-size: .85rem; padding: 6px 2px; }
  .gf-tag { display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; padding: 4px 10px; border-radius: 999px; background: var(--gf-field); color: var(--gf-muted); font-size: .78rem; }

  .gf-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 4px; margin-top: 18px; padding-top: 10px; border-top: 1px solid var(--gf-border); }
  .gf-divider { width: 1px; height: 30px; background: var(--gf-border); margin: 0 10px; }
  .gf-switch { display: inline-flex; align-items: center; gap: 10px; font-size: .9rem; cursor: pointer; }
  .gf-switch input { position: absolute; opacity: 0; width: 1px; height: 1px; }
  .gf-switch .track { position: relative; width: 36px; height: 14px; border-radius: 999px; background: var(--gf-line); transition: background .15s; }
  .gf-switch .track::after { content: ''; position: absolute; top: -3px; left: -2px; width: 20px; height: 20px; border-radius: 50%; background: #f1f1f1; box-shadow: 0 1px 3px rgba(0,0,0,.4); transition: transform .15s, background .15s; }
  .gf-switch input:checked + .track { background: rgba(66,133,244,.5); }
  .gf-switch input:checked + .track::after { transform: translateX(20px); background: #4285f4; }
  .gf-switch input:focus-visible + .track { outline: 2px solid var(--gf-link); outline-offset: 3px; }

  /* An inactive question shows a quiet preview of itself. */
  .gf-preview-prompt { font-size: 1rem; font-weight: 500; }
  .gf-preview-opt { display: flex; align-items: center; gap: 10px; margin-top: 10px; color: var(--gf-muted); font-size: .92rem; }
  .gf-preview-opt.right { color: var(--gf-good); font-weight: 600; }

  .gf-note { font-size: .82rem; color: var(--gf-muted); line-height: 1.5; }
  .gf-err { padding: 12px 16px; border-radius: 8px; border: 1px solid rgba(255,107,129,.4); background: rgba(255,107,129,.1); color: var(--gf-bad); font-size: .9rem; }
  .gf-ok { color: var(--gf-good); font-size: .85rem; font-weight: 600; }
  .gf-final-note { margin-top: 16px; padding: 12px 14px; border-radius: 8px; border: 1px solid rgba(255,196,0,.4); background: rgba(255,196,0,.09); font-size: .88rem; line-height: 1.55; }
  .gf-final-note b { color: #c99700; }
  html:not([data-theme="light"]) .gf-final-note b { color: #ffc400; }

  /* ── Settings ── */
  .gf-set-h { font-size: 1rem; font-weight: 600; }
  .gf-set .gf-in { max-width: 260px; }

  /* ── Floating toolbar ── */
  .gf-tools { display: flex; gap: 2px; padding: 4px; background: var(--gf-card); border: 1px solid var(--gf-border); border-radius: 8px; box-shadow: var(--gf-shadow); }
  @media (min-width: 1100px) { .gf-tools { flex-direction: column; position: sticky; top: 90px; } }
  @media (max-width: 1099px) {
    .gf-tools { position: fixed; z-index: 30; left: 50%; bottom: 14px; transform: translateX(-50%); }
    .gf-main { padding-bottom: 80px; }
  }

  /* ── Reference panel: one document, as tall as the screen ── */
  .gf-drawer { background: var(--gf-card); border: 1px solid var(--gf-border); border-radius: 8px; box-shadow: var(--gf-shadow); display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
  @media (min-width: 1100px) { .gf-drawer { position: sticky; top: 18px; height: calc(100vh - 36px); } }
  /* Small screens: the panel covers the whole screen. .wrap is its own stacking layer, so it is lifted too, above the app sidebar. */
  @media (max-width: 1099px) {
    .gf-drawer { position: fixed; z-index: 40; inset: 0; border-radius: 0; }
    .wrap:has(.gf-body.with-drawer) { z-index: 200; }
  }
  .gf-drawer-head { display: flex; align-items: center; gap: 4px; padding: 6px 6px 0 12px; border-bottom: 1px solid var(--gf-border); }
  .gf-drawer-head .gf-tab::after { bottom: -1px; }
  .gf-drawer-head .gf-icon { margin-left: auto; }
  .gf-pane { display: flex; flex-direction: column; flex: 1; min-height: 0; }
  .gf-docbar { display: flex; align-items: center; gap: 10px 14px; flex-wrap: wrap; padding: 10px 14px; border-bottom: 1px solid var(--gf-border); }
  .gf-docbar .gf-sel { flex: 1 1 220px; min-width: 0; padding: 8px 10px; }
  .gf-doc-name { flex: 1 1 200px; min-width: 0; display: flex; align-items: center; gap: 10px; }
  .gf-ext { flex: none; display: grid; place-items: center; width: 36px; height: 26px; border-radius: 6px; background: rgba(115,182,255,.16); color: var(--gf-link); font: 700 10px/1 ui-monospace, monospace; text-transform: uppercase; }
  .gf-fname { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .9rem; font-weight: 600; }
  .gf-doc-acts { display: flex; align-items: center; gap: 14px; margin-left: auto; }
  .gf-doc-acts a { color: var(--gf-link); font: 600 .8rem/1.4 inherit; text-decoration: none; }
  .gf-doc-acts a:hover { text-decoration: underline; }
  .gf-viewer { display: block; flex: 1; min-height: 0; width: 100%; border: 0; background: #525659; }
  img.gf-viewer { object-fit: contain; background: #1b1b22; }
  .gf-pane-msg { padding: 24px 18px; }

  [x-cloak] { display: none !important; }
</style>
@endpush

@php
  // Small inline icons (Material-style), so the page needs no icon font.
  $icon = [
    'add' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>',
    'book' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14z"/><path d="M20 17v4H6.5A2.5 2.5 0 0 1 4 18.5"/></svg>',
    'clip' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.4 11.1l-9.2 9.2a6 6 0 0 1-8.5-8.5l9.2-9.2a4 4 0 0 1 5.7 5.7l-9.2 9.2a2 2 0 0 1-2.8-2.8l8.5-8.5"/></svg>',
    'copy' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/></svg>',
    'trash' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 11v6M14 11v6"/></svg>',
    'up' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>',
    'down' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>',
    'close' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>',
    'check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5 9-10"/></svg>',
    'tag' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>',
  ];
@endphp

@section('content')
<div class="gf {{ $isFinal || $miniBoss ? 'final' : '' }}" x-data="quizBuilder()" x-init="init()" @keydown.escape.window="drawer = null">

  {{-- ═══════════ TOP BAR ═══════════ --}}
  <header class="gf-top">
    <a class="gf-back" href="{{ $backUrl }}">← Back to {{ $miniBoss ? 'mini bosses' : 'course' }}</a>

    <nav class="gf-tabs" data-tour="gf-tabs" role="tablist" aria-label="Builder sections">
      <button type="button" class="gf-tab" role="tab" :aria-selected="(tab === 'questions').toString()" @click="tab = 'questions'">Questions</button>
      <button type="button" class="gf-tab" role="tab" :aria-selected="(tab === 'settings').toString()" @click="tab = 'settings'">Settings</button>
    </nav>

    <div class="gf-actions" data-tour="gf-actions">
      <span class="gf-chip" :class="{ live: published }" x-text="published ? 'Published' : 'Draft'"></span>
      <button type="button" class="gf-btn" @click="save(true)" :disabled="saving || locked">Save draft</button>
      <button type="button" class="gf-btn solid" @click="save(false)" :disabled="saving || locked" x-text="saving ? 'Saving…' : 'Publish'"></button>
    </div>
  </header>

  <div class="gf-body" :class="{ 'with-drawer': drawer }">

    {{-- ═══════════ REFERENCE PANEL: the document the questions are written from ═══════════ --}}
    <aside class="gf-drawer" x-show="drawer" x-cloak aria-label="Course reference">
      <div class="gf-drawer-head" role="tablist">
        <button type="button" class="gf-tab" role="tab" :aria-selected="(drawer === 'course').toString()" @click="drawer = 'course'">Course summary</button>
        <button type="button" class="gf-tab" role="tab" :aria-selected="(drawer === 'materials').toString()" @click="openMaterials()">My materials</button>
        <button type="button" class="gf-icon" @click="drawer = null" aria-label="Close the reference panel">{!! $icon['close'] !!}</button>
      </div>

      {{-- Course summary: one PDF of every module's lessons, goals and key terms. --}}
      <div class="gf-pane" x-show="drawer === 'course'">
        @if ($summaryUrl)
          <div class="gf-docbar">
            <div class="gf-doc-name"><span class="gf-ext">pdf</span><span class="gf-fname" title="{{ $summaryName }}">{{ $summaryName }}</span></div>
            <div class="gf-doc-acts">
              <button type="button" class="gf-link" @click="useSource('Course summary')" :disabled="locked || !questions.length">Use as source</button>
              <a href="{{ $summaryUrl }}" target="_blank" rel="noopener">Open in new tab</a>
            </div>
          </div>
          {{-- Loaded only once the tab is first shown, so the PDF isn't built for nothing. --}}
          <template x-if="summaryShown">
            <iframe src="{{ $summaryUrl }}#view=FitH" class="gf-viewer" title="{{ $summaryName }}"></iframe>
          </template>
        @else
          <p class="gf-note gf-pane-msg">This course has no lessons to summarise yet.</p>
        @endif
      </div>

      {{-- My materials: the files this faculty member uploaded to the course, one open at a time. --}}
      <div class="gf-pane" x-show="drawer === 'materials'" x-cloak>
        @if (! collect($modules)->sum(fn ($m) => count($m['materials'])))
          <p class="gf-note gf-pane-msg">
            You haven't uploaded anything yet.
            <a href="{{ route('faculty.course', $crew) }}#modules" style="color:var(--gf-link)">Add materials</a>
            to read them here while you write.
          </p>
        @else
          <div class="gf-docbar">
            <label class="sr-only" for="gf-file">Material to show</label>
            <select id="gf-file" class="gf-sel" @change="view(fileById($event.target.value))">
              @foreach ($modules as $m)
                @continue(empty($m['materials']))
                <optgroup label="{{ $m['title'] }}">
                  @foreach ($m['materials'] as $f)
                    <option value="{{ $f['id'] }}" :selected="viewing?.id === {{ $f['id'] }}">{{ $f['name'] }}</option>
                  @endforeach
                </optgroup>
              @endforeach
            </select>
            <div class="gf-doc-acts" x-show="viewing">
              <button type="button" class="gf-link" @click="useSource(viewing.name)" :disabled="locked || !questions.length">Use as source</button>
              <a :href="viewing?.url" target="_blank" rel="noopener noreferrer" x-text="viewing?.is_link ? 'Open link' : 'Open in new tab'"></a>
            </div>
          </div>
          <template x-if="viewing && !viewing.is_link && viewing.ext === 'pdf'">
            <iframe :src="viewing.preview + '#view=FitH'" class="gf-viewer" :title="viewing.name"></iframe>
          </template>
          <template x-if="viewing && !viewing.is_link && viewing.ext === 'img'">
            <img :src="viewing.preview" :alt="viewing.name" class="gf-viewer">
          </template>
          <template x-if="viewing && viewing.is_link">
            <p class="gf-note gf-pane-msg">Links open in a new tab. <a :href="viewing.url" target="_blank" rel="noopener noreferrer" style="color:var(--gf-link)">Open it →</a></p>
          </template>
          <template x-if="viewing && !viewing.is_link && viewing.ext !== 'img' && viewing.ext !== 'pdf'">
            <p class="gf-note gf-pane-msg">This file type can't be shown here. <a :href="viewing.url" style="color:var(--gf-link)">Download it</a> to read it.</p>
          </template>
        @endif
      </div>
    </aside>

    {{-- ═══════════ THE FORM ═══════════ --}}
    <main class="gf-main">
      <p class="gf-err" x-show="error" x-cloak x-text="error" role="alert"></p>
      @if ($locked)
        <p class="gf-err">A student has already taken this exam, so it can't be changed any more. To let someone sit it again, use "Allow retake" on the course page.</p>
      @endif
      <p class="gf-ok" x-show="savedAt" x-cloak x-text="savedAt" role="status"></p>

      <fieldset :disabled="locked">
        {{-- ── Questions tab ── --}}
        <div x-show="tab === 'questions'" style="display:flex;flex-direction:column;gap:12px">
          <section class="gf-card gf-head" data-tour="gf-head" :class="{ active: active === -1 }" @click="active = -1">
            <input class="gf-in title" type="text" maxlength="255" aria-label="Title" x-model="title"
                   placeholder="{{ $isFinal ? 'Final exam' : 'Untitled quiz' }}">
            <textarea class="gf-in" rows="1" maxlength="2000" aria-label="Instructions for your students" x-model="description"
                      placeholder="Instructions for your students (optional)" style="resize:vertical;margin-top:8px"></textarea>
            @if ($miniBoss)
              <div class="gf-final-note">
                <b>Module {{ $miniBoss }} mini boss.</b> Students unlock it after finishing the module's lessons and take it once, in full screen,
                against the clock. Each right answer is worth {{ \App\Services\MiniBossService::XP_PER_CORRECT }} XP, and they move on whether they pass or not.
                Leaving full screen ends it as a fail with no XP. Afterwards they see only their score. Your changes reach only your class.
              </div>
            @elseif ($isFinal)
              <div class="gf-final-note">
                <b>The Final Boss.</b> Students unlock it after finishing every lesson and take it in full screen, against the clock.
                Passing it earns the certificate. Failing it, running out of time, or leaving full screen sends them back to the
                start of the course: they redo every lesson, then face it again. Afterwards they see their score and their mistakes.
              </div>
            @else
              <p class="gf-note" style="margin-top:12px">One try per student, against the clock. They see their score and mistakes as soon as they hand it in.</p>
            @endif
            <p class="gf-note" style="margin-top:8px">
              <span x-text="questions.length"></span> <span x-text="questions.length === 1 ? 'question' : 'questions'"></span>
              · <span x-text="minutes ? minutes + ' min' : 'no time limit yet'"></span>
              · <button type="button" class="gf-link" @click.stop="tab = 'settings'">Settings</button>
            </p>
          </section>

          <template x-for="(q, qi) in questions" :key="q.uid">
            <article class="gf-card gf-q" :class="{ active: active === qi }" @click="active = qi" :id="'q-card-' + q.uid">

              {{-- The question being edited --}}
              <template x-if="active === qi">
                <div>
                  <div class="gf-row">
                    <textarea class="gf-in prompt" style="flex:1 1 300px" rows="1" maxlength="1000" :id="'q-prompt-' + q.uid"
                              placeholder="Question" :aria-label="'Question ' + (qi + 1)" x-model="q.prompt"></textarea>
                    <select class="gf-sel" :aria-label="'Answer type for question ' + (qi + 1)" x-model="q.type" @change="onTypeChange(q)">
                      <option value="multiple_choice">◉ Multiple choice</option>
                      <option value="checkboxes">☑ Checkboxes</option>
                      <option value="dropdown">▾ Drop-down</option>
                      <option value="true_false">◐ True / False</option>
                    </select>
                  </div>

                  <div style="margin-top:14px">
                    <template x-for="(opt, oi) in q.options" :key="oi">
                      <div class="gf-opt">
                        <button type="button" class="gf-mark"
                                :class="[q.type === 'checkboxes' ? 'square' : (q.type === 'dropdown' ? 'num' : 'round'), isCorrect(q, oi) ? 'on' : '']"
                                @click="toggleCorrect(q, oi)" :aria-pressed="isCorrect(q, oi).toString()"
                                :aria-label="'Mark option ' + (oi + 1) + ' as a correct answer'" title="Mark as the correct answer">
                          <template x-if="q.type === 'dropdown'"><span x-text="(oi + 1) + '.'"></span></template>
                          <template x-if="q.type !== 'dropdown'">{!! $icon['check'] !!}</template>
                        </button>
                        <input class="gf-in" type="text" maxlength="500" :placeholder="'Option ' + (oi + 1)"
                               :aria-label="'Option ' + (oi + 1) + ' for question ' + (qi + 1)"
                               x-model="q.options[oi]" :readonly="q.type === 'true_false'">
                        <button type="button" class="gf-icon" x-show="q.type !== 'true_false' && q.options.length > 2"
                                @click="removeOption(q, oi)" :aria-label="'Remove option ' + (oi + 1)">{!! $icon['close'] !!}</button>
                      </div>
                    </template>
                    <div class="gf-opt" x-show="q.type !== 'true_false' && q.options.length < 10">
                      <span class="gf-mark" :class="q.type === 'checkboxes' ? 'square' : (q.type === 'dropdown' ? 'num' : 'round')" style="opacity:.4" aria-hidden="true">
                        <template x-if="q.type === 'dropdown'"><span x-text="(q.options.length + 1) + '.'"></span></template>
                      </span>
                      <button type="button" class="gf-opt-add gf-link" style="color:var(--gf-muted);font-weight:400;font-size:.92rem" @click="addOption(q)">Add option</button>
                    </div>
                    <p class="gf-note" style="margin-top:10px" x-text="q.type === 'checkboxes' ? 'Tick every correct answer (students must pick exactly those).' : 'Click the circle next to the correct answer.'"></p>
                  </div>

                  <label class="gf-source">
                    {!! $icon['tag'] !!}
                    <span style="flex:none">Based on</span>
                    <input class="gf-in" type="text" maxlength="255" x-model="q.source"
                           placeholder="e.g. Lesson 2.2 · TCP, or a file name (optional, only you see this)">
                  </label>

                  <div class="gf-foot">
                    <button type="button" class="gf-icon" @click.stop="move(qi, -1)" :disabled="qi === 0" aria-label="Move up" title="Move up">{!! $icon['up'] !!}</button>
                    <button type="button" class="gf-icon" @click.stop="move(qi, 1)" :disabled="qi === questions.length - 1" aria-label="Move down" title="Move down">{!! $icon['down'] !!}</button>
                    <button type="button" class="gf-icon" @click.stop="duplicate(qi)" aria-label="Duplicate" title="Duplicate">{!! $icon['copy'] !!}</button>
                    <button type="button" class="gf-icon" @click.stop="remove(qi)" aria-label="Delete" title="Delete">{!! $icon['trash'] !!}</button>
                    <span class="gf-divider" aria-hidden="true"></span>
                    <label class="gf-switch">Required <input type="checkbox" x-model="q.required"><span class="track"></span></label>
                  </div>
                </div>
              </template>

              {{-- Any other question: a quiet preview --}}
              <template x-if="active !== qi">
                <div>
                  <p class="gf-preview-prompt">
                    <span x-text="q.prompt || 'Question'" :style="q.prompt ? '' : 'color:var(--gf-muted)'"></span>
                    <span x-show="q.required" style="color:var(--gf-bad)">*</span>
                  </p>
                  <template x-for="(opt, oi) in q.options" :key="oi">
                    <p class="gf-preview-opt" :class="{ right: isCorrect(q, oi) }">
                      <span class="gf-mark" style="cursor:default;width:16px;height:16px;border-width:2px"
                            :class="[q.type === 'checkboxes' ? 'square' : 'round', isCorrect(q, oi) ? 'on' : '']"></span>
                      <span x-text="opt || ('Option ' + (oi + 1))"></span>
                    </p>
                  </template>
                </div>
              </template>
            </article>
          </template>

          <button type="button" class="gf-btn" style="align-self:center;margin-top:6px" @click="addQuestion()">{!! $icon['add'] !!} Add question</button>
        </div>

        {{-- ── Settings tab ── --}}
        <div x-show="tab === 'settings'" x-cloak style="display:flex;flex-direction:column;gap:12px">
          <section class="gf-card gf-head">
            <p class="gf-in title" style="border:0" x-text="title || '{{ $isFinal ? 'Final exam' : 'Untitled quiz' }}'"></p>
            <p class="gf-note">How students take it. Every quiz is one try, against the clock.</p>
          </section>

          <section class="gf-card gf-set">
            <label class="gf-set-h" for="gf-minutes">Time limit <span style="color:var(--gf-bad)">*</span></label>
            <p class="gf-note">The clock starts when a student presses Start and keeps running if they close the page. When it hits zero, their answers are handed in automatically.</p>
            <div class="gf-row" style="align-items:center;margin-top:8px">
              <input id="gf-minutes" class="gf-in" type="number" min="1" max="600" required x-model.number="minutes" placeholder="Minutes">
              <span class="gf-note">minutes</span>
            </div>
          </section>

          <section class="gf-card gf-set">
            <label class="gf-set-h" for="gf-due">Deadline</label>
            <p class="gf-note">Optional. After this, students who haven't started can't open it any more.</p>
            <input id="gf-due" class="gf-in" style="margin-top:8px" type="datetime-local" x-model="dueAt">
          </section>

          @if ($isFinal || $miniBoss)
            <section class="gf-card gf-set">
              <label class="gf-set-h" for="gf-pass">Pass mark</label>
              <p class="gf-note">{{ $miniBoss ? 'The score that counts as beating the mini boss. Below it, the student still moves on, and you see their score.' : 'The score a student needs to earn the certificate. Below it, they redo the course and try again.' }}</p>
              <div class="gf-row" style="align-items:center;margin-top:8px">
                <input id="gf-pass" class="gf-in" type="number" min="1" max="100" x-model.number="passPercent">
                <span class="gf-note">%</span>
              </div>
            </section>
          @endif
        </div>
      </fieldset>
    </main>

    {{-- ═══════════ FLOATING TOOLBAR ═══════════ --}}
    <nav class="gf-tools" data-tour="gf-tools" aria-label="Builder tools">
      <button type="button" class="gf-icon" @click="tab = 'questions'; addQuestion()" :disabled="locked" title="Add question" aria-label="Add question">{!! $icon['add'] !!}</button>
      <button type="button" class="gf-icon" @click="toggleDrawer('course')" title="Course summary" aria-label="Open the course summary"
              :style="drawer === 'course' ? 'color:var(--gf-link)' : ''">{!! $icon['book'] !!}</button>
      <button type="button" class="gf-icon" @click="toggleDrawer('materials')" title="My materials" aria-label="Open my materials"
              :style="drawer === 'materials' ? 'color:var(--gf-link)' : ''">{!! $icon['clip'] !!}</button>
    </nav>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function quizBuilder() {
    const existing = @json($quiz);
    const isFinal = @js($isFinal);
    const isMiniBoss = @js((bool) $miniBoss);
    let uid = 0;
    const withUid = q => ({ source: '', ...q, source: q.source || '', uid: ++uid });

    return {
      title: existing?.title || (isFinal ? 'Final Exam' : ''),
      description: existing?.description || '',
      minutes: existing?.minutes || (isFinal ? 60 : 15),
      dueAt: existing?.due_at || '',
      passPercent: existing?.pass_percent || {{ \App\Models\CrewQuiz::DEFAULT_PASS_PERCENT }},
      questions: (existing?.questions || []).map(withUid),
      published: !!existing?.published,
      isFinal, locked: @js($locked),
      saveUrl: @js($saveUrl), saveMethod: @js($saveMethod),
      hasSummary: @js((bool) $summaryUrl),
      files: @js(collect($modules)->pluck('materials')->flatten(1)->values()),
      tab: 'questions', active: 0, drawer: null, viewing: null, summaryShown: false,
      saving: false, savedAt: '', error: '',

      init() {
        if (!this.questions.length) this.questions.push(this.blank());
        this.$watch('drawer', d => { if (d === 'course') this.summaryShown = true; });
        // Wide screens open the course summary straight away, side by side with the form.
        if (window.matchMedia('(min-width: 1100px)').matches && this.hasSummary && !this.locked) this.drawer = 'course';
      },

      blank() {
        return withUid({ prompt: '', type: 'multiple_choice', options: ['Option 1'], correct: 0, correct_many: [], required: true, source: '' });
      },
      focusPrompt(q) {
        this.$nextTick(() => {
          document.getElementById('q-card-' + q.uid)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
          document.getElementById('q-prompt-' + q.uid)?.focus();
        });
      },
      /* New questions go right after the one being edited, like Google Forms. */
      addQuestion(q = this.blank()) {
        const at = Math.min(this.active + 1, this.questions.length);
        this.questions.splice(Math.max(at, 0), 0, q);
        this.active = Math.max(at, 0);
        this.focusPrompt(q);
      },
      duplicate(i) {
        const copy = withUid(JSON.parse(JSON.stringify(this.questions[i])));
        this.questions.splice(i + 1, 0, copy);
        this.active = i + 1;
      },
      remove(i) {
        this.questions.splice(i, 1);
        if (!this.questions.length) this.questions.push(this.blank());
        this.active = Math.min(i, this.questions.length - 1);
      },
      move(i, by) {
        const to = i + by;
        if (to < 0 || to >= this.questions.length) return;
        const [q] = this.questions.splice(i, 1);
        this.questions.splice(to, 0, q);
        this.active = to;
      },

      addOption(q) {
        q.options.push('Option ' + (q.options.length + 1));
        this.$nextTick(() => {
          const inputs = document.querySelectorAll('#q-card-' + q.uid + ' .gf-opt input.gf-in');
          inputs[q.options.length - 1]?.select();
        });
      },
      removeOption(q, oi) {
        q.options.splice(oi, 1);
        if (q.correct === oi) q.correct = 0; else if (q.correct > oi) q.correct--;
        q.correct_many = q.correct_many.filter(i => i !== oi).map(i => i > oi ? i - 1 : i);
      },
      isCorrect(q, oi) { return q.type === 'checkboxes' ? q.correct_many.includes(oi) : q.correct === oi; },
      toggleCorrect(q, oi) {
        if (q.type !== 'checkboxes') { q.correct = oi; return; }
        q.correct_many = q.correct_many.includes(oi) ? q.correct_many.filter(i => i !== oi) : [...q.correct_many, oi];
      },
      /* True/False always has exactly those two options; the others keep what's typed. */
      onTypeChange(q) {
        if (q.type === 'true_false') { q.options = ['True', 'False']; if (q.correct > 1) q.correct = 0; }
        else if (q.options.length < 2) q.options.push('Option ' + (q.options.length + 1));
      },

      /* ── Reference panel ── */
      toggleDrawer(which) {
        if (this.drawer === which) { this.drawer = null; return; }
        which === 'materials' ? this.openMaterials() : (this.drawer = which);
      },
      /* Opens straight onto a file (the first one that can be shown here), not an empty viewer. */
      openMaterials() {
        this.drawer = 'materials';
        if (!this.viewing) this.viewing = this.files.find(f => !f.is_link && ['pdf', 'img'].includes(f.ext)) || this.files[0] || null;
      },
      fileById(id) { return this.files.find(f => String(f.id) === String(id)) || null; },
      useSource(text) {
        const q = this.questions[this.active] || this.questions[this.questions.length - 1];
        if (q) q.source = text;
      },
      view(file) {
        if (!file) return;
        if (file.is_link) window.open(file.url, '_blank', 'noopener');
        this.viewing = file;
      },

      /* ── Saving ── */
      check() {
        if (!this.title.trim()) return 'Give it a title first.';
        if (!this.minutes || this.minutes < 1) { this.tab = 'settings'; return 'Set a time limit so students take it seriously.'; }
        for (const [i, q] of this.questions.entries()) {
          if (!q.prompt.trim()) { this.active = i; this.tab = 'questions'; return `Question ${i + 1} has no text yet.`; }
          if (q.options.some(o => !String(o).trim())) { this.active = i; this.tab = 'questions'; return `Fill in every option in question ${i + 1}, or remove the empty ones.`; }
          if (q.type === 'checkboxes' && !q.correct_many.length) { this.active = i; this.tab = 'questions'; return `Tick at least one correct answer in question ${i + 1}.`; }
        }
        return '';
      },

      async save(draft) {
        if (this.saving || this.locked) return;
        this.error = ''; this.savedAt = '';
        const problem = this.check();
        if (problem) { this.error = problem; return; }
        this.saving = true;
        try {
          const res = await fetch(this.saveUrl, {
            method: this.saveMethod, headers: this.headers(),
            body: JSON.stringify({
              title: this.title, description: this.description,
              minutes: this.minutes || null, due_at: this.dueAt || null, draft,
              is_final: this.isFinal, pass_percent: this.isFinal || isMiniBoss ? (this.passPercent || null) : null,
              questions: this.questions.map(q => ({
                prompt: q.prompt, type: q.type, options: q.options, source: q.source || null,
                correct: q.correct, correct_many: q.correct_many, required: q.required,
              })),
            }),
          });
          const body = await res.json().catch(() => ({}));
          if (!res.ok) {
            this.error = body.message && !body.errors ? body.message : (Object.values(body.errors || {}).flat()[0] || body.message || 'That did not save.');
            return;
          }
          // The first save created it; every save after that updates the same one instead of making another.
          if (this.saveMethod === 'POST' && body.id) {
            this.saveUrl = @js(route('faculty.quizzes.update', '__ID__')).replace('__ID__', body.id);
            this.saveMethod = 'PUT';
          }
          this.published = !!body.published;
          this.savedAt = body.published ? 'Published — your students can see it now.' : 'Draft saved.';
          if (!draft) setTimeout(() => { window.location = @js($backUrl); }, 700);
        } catch (e) { this.error = 'Could not save. Check your connection and try again.'; }
        finally { this.saving = false; }
      },

      headers() {
        return {
          'Content-Type': 'application/json', 'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        };
      },
    };
  }
</script>
@endpush
