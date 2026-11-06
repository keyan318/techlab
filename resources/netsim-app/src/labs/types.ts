// The lab format: a starting topology (the SaveFile part) plus ordered steps
// with machine-checkable objectives and quiz questions. Objectives are
// expressed as observable network facts — the checker probes the live
// simulation with real packets, so "grading" is visible on the canvas.

export type Check =
  | { type: 'iface-ip'; device: string; iface: string; cidr?: string; inSubnet?: string }
  | { type: 'default-route'; device: string; via?: string }
  | { type: 'forwarding'; device: string; expect: boolean }
  | { type: 'fw-policy'; device: string; hook: 'input' | 'forward' | 'output'; policy: 'accept' | 'drop' }
  | { type: 'ping'; from: string; toDevice?: string; toAddr?: string; expect: 'success' | 'fail'; requireOwnCommand?: boolean }
  | {
      type: 'tcp';
      from: string;
      toDevice?: string;
      toAddr?: string;
      port: number;
      expect: 'open' | 'refused' | 'filtered';
      requireOwnCommand?: boolean;
    }
  | { type: 'dns'; from: string; name: string; expect: 'resolves' | 'nxdomain' | 'blocked'; addr?: string; requireOwnCommand?: boolean }
  | { type: 'http'; from: string; url: string; expect: 'ok' | 'fail'; contains?: string; requireOwnCommand?: boolean }
  // TechLab (Networking 2)
  | { type: 'ospf-neighbor'; device: string; neighbor: string; expect: 'full' | 'none' }
  | { type: 'route'; device: string; dest: string; source?: 'ospf' | 'static' | 'connected'; expect: 'present' | 'absent'; paths?: number }
  // "What if": cut one interface for a moment, follow the routing, put it back. Nothing stays changed.
  | { type: 'survives'; from: string; toDevice?: string; toAddr?: string; cut: { device: string; iface: string } }
  | { type: 'path'; from: string; toDevice?: string; toAddr?: string; through: string; expect: 'through' | 'avoids' }
  | { type: 'stp-root'; vlan: number; device: string }
  | { type: 'stp-mode'; device: string; mode: 'pvst' | 'rapid-pvst' | 'mst' }
  | { type: 'stp-port'; device: string; iface: string; vlan: number; state: 'forwarding' | 'blocking' | 'err-disabled' | 'root-inconsistent' }
  | { type: 'iface-config'; device: string; iface: string; is: IfaceExpect }
  | { type: 'fhrp-active'; device: string; group: number; proto?: 'hsrp' | 'vrrp' }
  | { type: 'fw-rule'; device: string; name: string }
  | { type: 'acl'; device: string; name: string; has: string };

// Every listed property must hold for an iface-config check to pass.
export interface IfaceExpect {
  mode?: 'access' | 'trunk';
  accessVlan?: number;
  native?: number;
  allowed?: number[];
  nonegotiate?: boolean;
  portfast?: boolean;
  bpduguard?: boolean;
  rootguard?: boolean;
  portSecurity?: boolean;
  psMax?: number;
  psViolation?: 'shutdown' | 'restrict' | 'protect';
  psSticky?: boolean;
  ospfCost?: number;
  ospfPassive?: boolean;
  shutdown?: boolean;
  dot1q?: number;
  fhrpPreempt?: boolean;
  fhrpTrack?: string; // engine name of a tracked interface
  aclIn?: string;
  aclOut?: string;
}

export interface Objective {
  id: string;
  label: string;
  check: Check;
}

export interface QuizQ {
  q: string;
  options: string[];
  answer: number;
}

export interface LabStep {
  title: string;
  body: string; // plain text; `backticks` render as code
  objectives: Objective[];
  quiz?: QuizQ[];
}

export interface LabDef {
  id: string;
  title: string;
  blurb: string;
  steps: LabStep[];
}

export interface LabProgress {
  step: number;
  results: Record<string, { pass: boolean; detail: string }>;
  quiz: Record<string, number>;
  // TechLab: objectives already true in the starting network (see labs/grading.ts).
  baseline?: string[];
}
