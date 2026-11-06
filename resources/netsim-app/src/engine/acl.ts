// TechLab (Networking 2): Cisco IOS access control lists — stateless packet filters on router interfaces.
//
// Each entry is checked top-down (lowest sequence number first); the first match decides, and a packet that
// matches nothing hits the invisible "deny any" at the end. Nothing is remembered between packets: that is
// what "stateless" means, and why `established` (TCP with ACK or RST set) exists to let replies back in.
import type { Ipv4Packet } from './types';
import { ipToString, parseIp, type U32 } from './ip';

export interface AclAddr {
  addr: U32;
  wild: U32; // wildcard: 1 bits = "don't care"
}

export interface AclPort {
  op: 'eq' | 'gt' | 'lt' | 'neq' | 'range';
  a: number;
  b?: number;
}

export interface AclEntry {
  seq: number;
  action: 'permit' | 'deny';
  proto: 'ip' | 'tcp' | 'udp' | 'icmp';
  src: AclAddr | null; // null = any
  sport?: AclPort;
  dst: AclAddr | null;
  dport?: AclPort;
  icmpType?: 'echo' | 'echo-reply';
  established?: boolean;
  matches: number;
}

export interface Acl {
  kind: 'standard' | 'extended';
  entries: AclEntry[];
}

const PORT_NAMES: Record<string, number> = {
  ftp: 21, ssh: 22, telnet: 23, smtp: 25, domain: 53, www: 80, http: 80, pop3: 110, ntp: 123, snmp: 161, https: 443, rdp: 3389,
};

function port(word: string | undefined): number | null {
  if (word === undefined) return null;
  if (/^\d+$/.test(word)) {
    const n = Number(word);
    return n <= 65535 ? n : null;
  }
  return PORT_NAMES[word.toLowerCase()] ?? null;
}

// Reads "any" | "host A.B.C.D" | "A.B.C.D W.W.W.W" (extended) or "A.B.C.D [W.W.W.W]" (standard).
function readAddr(t: string[], i: number, standard: boolean): { v: AclAddr | null; next: number } | null {
  const w = t[i]?.toLowerCase();
  if (w === undefined) return null;
  if (w === 'any') return { v: null, next: i + 1 };
  if (w === 'host') {
    const a = parseIp(t[i + 1] ?? '');
    return a === null ? null : { v: { addr: a, wild: 0 }, next: i + 2 };
  }
  const a = parseIp(t[i]);
  if (a === null) return null;
  const wild = parseIp(t[i + 1] ?? '');
  if (wild === null) return standard ? { v: { addr: a, wild: 0 }, next: i + 1 } : null;
  return { v: { addr: (a & ~wild) >>> 0, wild }, next: i + 2 };
}

function readPort(t: string[], i: number): { v: AclPort | undefined; next: number } | null {
  const op = t[i]?.toLowerCase();
  if (op === 'eq' || op === 'gt' || op === 'lt' || op === 'neq') {
    const a = port(t[i + 1]);
    return a === null ? null : { v: { op, a }, next: i + 2 };
  }
  if (op === 'range') {
    const a = port(t[i + 1]);
    const b = port(t[i + 2]);
    return a === null || b === null ? null : { v: { op, a, b }, next: i + 3 };
  }
  return { v: undefined, next: i };
}

// Parses the part after "permit"/"deny". Returns the entry (without seq/action) or an error message.
export function parseAce(kind: Acl['kind'], words: string[]): Omit<AclEntry, 'seq' | 'action' | 'matches'> | string {
  const t = words.filter((w) => w.toLowerCase() !== 'log');
  if (kind === 'standard') {
    const s = readAddr(t, 0, true);
    if (!s || s.next !== t.length) return 'standard lists match a source only: any | host A.B.C.D | A.B.C.D wildcard';
    return { proto: 'ip', src: s.v, dst: null };
  }
  const proto = t[0]?.toLowerCase();
  if (proto !== 'ip' && proto !== 'tcp' && proto !== 'udp' && proto !== 'icmp') return 'protocol must be ip, tcp, udp or icmp';
  const s = readAddr(t, 1, false);
  if (!s) return 'bad source: any | host A.B.C.D | A.B.C.D wildcard';
  let i = s.next;
  let sport: AclPort | undefined;
  if (proto === 'tcp' || proto === 'udp') {
    const sp = readPort(t, i);
    if (!sp) return 'bad source port';
    sport = sp.v;
    i = sp.next;
  }
  const d = readAddr(t, i, false);
  if (!d) return 'bad destination: any | host A.B.C.D | A.B.C.D wildcard';
  i = d.next;
  let dport: AclPort | undefined;
  let icmpType: AclEntry['icmpType'];
  let established = false;
  if (proto === 'tcp' || proto === 'udp') {
    const dp = readPort(t, i);
    if (!dp) return 'bad destination port';
    dport = dp.v;
    i = dp.next;
  }
  if (proto === 'icmp' && (t[i]?.toLowerCase() === 'echo' || t[i]?.toLowerCase() === 'echo-reply')) {
    icmpType = t[i].toLowerCase() as AclEntry['icmpType'];
    i++;
  }
  if (proto === 'tcp' && t[i]?.toLowerCase() === 'established') {
    established = true;
    i++;
  }
  if (i !== t.length) return `unexpected "${t[i]}"`;
  return { proto, src: s.v, sport, dst: d.v, dport, icmpType, ...(established ? { established } : {}) };
}

function addrMatch(a: AclAddr | null, ip: U32): boolean {
  if (!a) return true;
  return ((ip & ~a.wild) >>> 0) === ((a.addr & ~a.wild) >>> 0);
}

function portMatch(p: AclPort | undefined, n: number | null): boolean {
  if (!p) return true;
  if (n === null) return false;
  switch (p.op) {
    case 'eq': return n === p.a;
    case 'neq': return n !== p.a;
    case 'gt': return n > p.a;
    case 'lt': return n < p.a;
    case 'range': return n >= p.a && n <= (p.b ?? p.a);
  }
}

export function aceMatches(e: AclEntry, pkt: Ipv4Packet): boolean {
  const l4 = pkt.l4;
  if (e.proto !== 'ip' && e.proto !== l4.kind) return false;
  if (!addrMatch(e.src, pkt.src) || !addrMatch(e.dst, pkt.dst)) return false;
  const sp = l4.kind === 'tcp' || l4.kind === 'udp' ? l4.srcPort : null;
  const dp = l4.kind === 'tcp' || l4.kind === 'udp' ? l4.dstPort : null;
  if (!portMatch(e.sport, sp) || !portMatch(e.dport, dp)) return false;
  if (e.icmpType && (l4.kind !== 'icmp' || l4.msg.type !== (e.icmpType === 'echo' ? 'echo-request' : 'echo-reply'))) return false;
  if (e.established && !(l4.kind === 'tcp' && (l4.ack || l4.rst))) return false;
  return true;
}

// First match wins; no match is the implicit deny. An ACL that doesn't exist permits everything (as in IOS).
export function aclPermits(acl: Acl | undefined, pkt: Ipv4Packet): boolean {
  if (!acl) return true;
  for (const e of [...acl.entries].sort((a, b) => a.seq - b.seq)) {
    if (aceMatches(e, pkt)) {
      e.matches++;
      return e.action === 'permit';
    }
  }
  return false;
}

function addrText(a: AclAddr | null): string {
  if (!a) return 'any';
  if (a.wild === 0) return `host ${ipToString(a.addr)}`;
  return `${ipToString(a.addr)} ${ipToString(a.wild)}`;
}

function portText(p: AclPort | undefined): string {
  if (!p) return '';
  return p.op === 'range' ? ` range ${p.a} ${p.b}` : ` ${p.op} ${p.a}`;
}

export function aceText(kind: Acl['kind'], e: AclEntry): string {
  if (kind === 'standard') return `${e.action} ${addrText(e.src)}`;
  let s = `${e.action} ${e.proto} ${addrText(e.src)}${portText(e.sport)} ${addrText(e.dst)}${portText(e.dport)}`;
  if (e.icmpType) s += ` ${e.icmpType}`;
  if (e.established) s += ' established';
  return s;
}

export function aclKindFor(name: string): Acl['kind'] {
  const n = Number(name);
  return Number.isInteger(n) && ((n >= 1 && n <= 99) || (n >= 1300 && n <= 1999)) ? 'standard' : 'extended';
}

export function aclToJSON(acl: Acl): { kind: Acl['kind']; entries: { seq: number; text: string }[] } {
  return { kind: acl.kind, entries: acl.entries.map((e) => ({ seq: e.seq, text: aceText(acl.kind, e) })) };
}

export function aclFromJSON(j: { kind: Acl['kind']; entries: { seq: number; text: string }[] }): Acl {
  const acl: Acl = { kind: j.kind, entries: [] };
  for (const { seq, text } of j.entries) {
    const [action, ...rest] = text.split(/\s+/);
    if (action !== 'permit' && action !== 'deny') continue;
    const e = parseAce(j.kind, rest);
    if (typeof e !== 'string') acl.entries.push({ ...e, seq, action, matches: 0 });
  }
  return acl;
}
