// TechLab: which objectives a student has actually earned (fork addition, MIT like the rest of NetSim).
//
// Upstream, a ✓ meant "this check passed at some point". That credited work nobody did:
// - checks already true in the lab's starting network ("astro can still ping rivet") went green on the first
//   press of Check objectives, before the student changed anything;
// - a ✓ never expired, so undoing an earlier step (or an old autosave) still counted toward the lab pass.
// Here an objective that was true from the start only counts once the rest of its step passes, and the lab
// only passes after every objective has been re-checked against the network as it is now.
import { restoreNetwork, type SaveFile } from '../engine/serialize';
import type { Network } from '../engine/network';
import { runChecks, type CheckResult } from './checker';
import type { LabDef } from './types';

export type LabResults = Record<string, { pass: boolean; detail: string }>;

/** Ids of the objectives that already pass in the lab's untouched network, graded on a throwaway copy. */
export function startingPasses(file: SaveFile): string[] {
  if (!file.lab) return [];
  const net = restoreNetwork(file);
  const passing: string[] = [];
  let done = false;
  runChecks(
    net,
    file.lab.steps.flatMap((s) => s.objectives),
    (r) => {
      if (r.pass) passing.push(r.id);
    },
    () => (done = true),
  );
  // The copy is never drawn, so its clock can jump ahead instead of playing the probes in real time.
  for (let i = 0; !done && i < 100; i++) net.scheduler.advanceTo(net.scheduler.now + 10_000);
  return passing;
}

/** A passing objective counts, unless it was already true at the start and the rest of its step is not done. */
export function isEarned(lab: LabDef, results: LabResults, baseline: readonly string[], id: string): boolean {
  if (!results[id]?.pass) return false;
  if (!baseline.includes(id)) return true;
  const step = lab.steps.find((s) => s.objectives.some((o) => o.id === id));
  return !!step && step.objectives.every((o) => results[o.id]?.pass);
}

export function earnedCount(lab: LabDef, results: LabResults, baseline: readonly string[]): number {
  return lab.steps.flatMap((s) => s.objectives).filter((o) => isEarned(lab, results, baseline, o.id)).length;
}

export function labEarned(lab: LabDef, results: LabResults, baseline: readonly string[]): boolean {
  const total = lab.steps.reduce((n, s) => n + s.objectives.length, 0);
  return total > 0 && earnedCount(lab, results, baseline) === total;
}

/**
 * Checks one step on the live network. Once that step passes and every other step has been checked before,
 * the other steps are checked again too before onDone, so the lab passes (or not) on what the network does
 * now rather than on ✓ or ✗ marks from earlier.
 */
export function checkLabStep(
  net: Network,
  lab: LabDef,
  stepIdx: number,
  results: () => LabResults,
  onResult: (r: CheckResult) => void,
  onDone: () => void,
): void {
  const step = lab.steps[stepIdx].objectives;
  runChecks(net, step, onResult, () => {
    const r = results();
    const others = lab.steps.flatMap((s, i) => (i === stepIdx ? [] : s.objectives));
    const recheck = others.length > 0 && step.every((o) => r[o.id]?.pass) && others.every((o) => r[o.id]);
    if (recheck) runChecks(net, others, onResult, onDone);
    else onDone();
  });
}
