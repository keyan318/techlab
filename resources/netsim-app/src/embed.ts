// TechLab embed hooks (fork addition, MIT like the rest of NetSim).
//
// When TechLab shows NetSim inside a lesson it loads /netsim-app/app.html?lab=<id>.
// We then (1) load labs/<id>.json instead of the demo, (2) keep each lab's
// autosave separate, and (3) tell the parent page how the student is doing via
// postMessage, so TechLab can award XP and unlock the next lesson.
// Outside an iframe (or without ?lab=) NetSim behaves exactly as upstream.

// Lab ids are plain slugs; anything else is ignored so the param can't be
// used to fetch arbitrary paths.
const raw = new URLSearchParams(location.search).get('lab');

export const embedLab: string | null = raw && /^[a-z0-9-]{1,64}$/.test(raw) ? raw : null;

export const embedded: boolean = embedLab !== null && window.parent !== window;

export type HostMessage =
  | { source: 'netsim'; type: 'lab-progress'; lab: string; passed: number; total: number }
  | { source: 'netsim'; type: 'lab-passed'; lab: string; passed: number; total: number }
  | { source: 'netsim'; type: 'lab-failed'; lab: string; failing: string[] };

// Same-origin only: TechLab serves this app from its own domain, so the
// parent's origin is ours. No wildcard target.
export function postToHost(msg: HostMessage): void {
  if (!embedded) return;
  try {
    window.parent.postMessage(msg, location.origin);
  } catch {
    // Parent unreachable; the lab still works standalone.
  }
}

export function autosaveKeyFor(base: string): string {
  return embedLab ? `${base}:lab:${embedLab}` : base;
}

// Embedded in TechLab: mark the page so CSS can hide upstream chrome (the "← home" link).
if (embedded) document.documentElement.classList.add('embedded');
