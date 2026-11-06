// TechLab: a live probe on the network proves the *configuration* is right, but not that the student
// typed the command themselves — a typo'd `ping` that never left the sender still leaves the network
// perfectly pingable, so a "configure it yourself" objective could pass on a broken terminal command.
// A "defend it yourself" objective is different: its ping/curl/etc. often comes from a scripted
// attacker or outside tester (e.g. `doom-scan`, `rogue-pc`) the student never opens a console on, and
// that one *should* stay a pure network-state check.
//
// Rather than hand-flag every objective in every lab file, this reads the same lesson text the student
// reads: when a step's body says "From <device>, run `<command>`" (or similar — "Test from X:", "from
// `X`, ..."), the objective naming that device and check type is marked `requireOwnCommand`, so grading
// also requires IpDevice.recordActivity to show the student's own command observed the expected result.
// Every other objective (the scripted-probe kind) keeps the old network-state-only behavior.
import type { Check, LabDef } from './types';

const VERB_BY_KIND: Partial<Record<Check['type'], RegExp>> = {
  ping: /\bping\b/i,
  tcp: /\b(Test-NetConnection|tnc|nc|ncat)\b/i,
  dns: /\b(nslookup|dig)\b/i,
  http: /\b(curl|Invoke-WebRequest|iwr)\b/i,
};

function escapeRegExp(s: string): string {
  return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// True when `body` tells the student, by name, to run a `kind`-shaped command from `device` — scoped to
// the text between that "from <device>" mention and the next one (if any), so a step naming several
// devices doesn't credit the wrong one.
export function studentTypesIt(body: string, device: string, kind: Check['type']): boolean {
  const verb = VERB_BY_KIND[kind];
  if (!verb) return false;
  const fromRe = new RegExp(`\\bfrom\\s+\`?${escapeRegExp(device)}\`?\\b`, 'gi');
  while (fromRe.exec(body)) {
    const after = body.slice(fromRe.lastIndex);
    const nextFrom = /\bfrom\s+`?[a-zA-Z][\w-]*`?\b/i.exec(after);
    const scope = nextFrom ? after.slice(0, nextFrom.index) : after;
    const codeSpans = scope.match(/`[^`]+`/g) ?? [];
    if (codeSpans.some((c) => verb.test(c))) return true;
  }
  return false;
}

/** Mutates `lab` in place, flagging the ping/tcp/dns/http checks the lesson text asks the student to run
 * themselves. Called wherever a lab file is freshly loaded (store.ts's adoptSave and its embed-refresh
 * path), so a faculty rewrite of a step's body keeps this in sync automatically. */
export function attachRequireOwnCommand(lab: LabDef): LabDef {
  for (const step of lab.steps) {
    for (const o of step.objectives) {
      const c = o.check;
      if (c.type === 'ping' || c.type === 'tcp' || c.type === 'dns' || c.type === 'http') {
        c.requireOwnCommand = studentTypesIt(step.body, c.from, c.type);
      }
    }
  }
  return lab;
}
