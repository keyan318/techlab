import type { NetInterface } from '../../engine/device';
import type { IpDevice } from '../../engine/ipdevice';
import { parseCidr, parseIp } from '../../engine/ip';

// Engine interfaces stay eth0/eth1/...; Windows calls them "Ethernet", "Ethernet 2", ...
export function adapterName(device: IpDevice, iface: NetInterface): string {
  const idx = device.interfaces.indexOf(iface);
  return idx <= 0 ? 'Ethernet' : `Ethernet ${idx + 1}`;
}

export function adapterNameOf(device: IpDevice, engineName: string): string {
  const iface = device.getInterface(engineName);
  return iface ? adapterName(device, iface) : engineName;
}

// Accepts "Ethernet", "ethernet 2", or the engine name ("eth1").
export function findAdapter(device: IpDevice, word: string | undefined): NetInterface | null {
  if (!word) return null;
  const w = word.trim().toLowerCase();
  for (const i of device.interfaces) {
    if (adapterName(device, i).toLowerCase() === w || i.name.toLowerCase() === w) return i;
  }
  return null;
}

// Splits a command line like cmd.exe: whitespace separates, "double quotes" group,
// and name="Ethernet 2" stays one token with the quotes removed.
export function tokenize(line: string): string[] {
  const out: string[] = [];
  let cur = '';
  let inQ = false;
  let has = false;
  for (const ch of line) {
    if (ch === '"') {
      inQ = !inQ;
      has = true;
    } else if (!inQ && /\s/.test(ch)) {
      if (has) out.push(cur);
      cur = '';
      has = false;
    } else {
      cur += ch;
      has = true;
    }
  }
  if (has) out.push(cur);
  return out;
}

// netsh style: positional words plus key=value pairs (keys lowercased).
export function splitKv(args: string[]): { pos: string[]; kv: Record<string, string> } {
  const pos: string[] = [];
  const kv: Record<string, string> = {};
  for (const a of args) {
    const eq = a.indexOf('=');
    if (eq > 0) kv[a.slice(0, eq).toLowerCase()] = a.slice(eq + 1);
    else pos.push(a);
  }
  return { pos, kv };
}

// PowerShell style: -Name value pairs (names lowercased, leading dash removed).
export function psParams(args: string[]): { pos: string[]; params: Record<string, string> } {
  const pos: string[] = [];
  const params: Record<string, string> = {};
  for (let i = 0; i < args.length; i++) {
    const a = args[i];
    if (a.startsWith('-') && a.length > 1 && !/^-\d/.test(a)) {
      const colon = a.indexOf(':');
      if (colon > 0) {
        params[a.slice(1, colon).toLowerCase()] = a.slice(colon + 1);
        continue;
      }
      const next = args[i + 1];
      if (next !== undefined && !(next.startsWith('-') && next.length > 1 && !/^-\d/.test(next))) {
        params[a.slice(1).toLowerCase()] = next;
        i++;
      } else {
        params[a.slice(1).toLowerCase()] = 'true';
      }
    } else {
      pos.push(a);
    }
  }
  return { pos, params };
}

export function maskToPrefix(mask: string): number | null {
  const m = parseIp(mask);
  if (m === null) return null;
  let prefix = 0;
  let seenZero = false;
  for (let bit = 31; bit >= 0; bit--) {
    const on = ((m >>> bit) & 1) === 1;
    if (on && seenZero) return null;
    if (on) prefix++;
    else seenZero = true;
  }
  return prefix;
}

export function prefixToMask(prefix: number): string {
  const m = prefix === 0 ? 0 : (0xffffffff << (32 - prefix)) >>> 0;
  return [24, 16, 8, 0].map((s) => (m >>> s) & 0xff).join('.');
}

// "10.0.0.5", "10.0.0.0/24", "any" -> network or null (null = any).
export function parseNetOrAny(word: string | undefined): { addr: number; prefix: number } | null | 'bad' {
  if (!word || word.toLowerCase() === 'any') return null;
  const c = parseCidr(word);
  if (c) return c;
  const ip = parseIp(word);
  if (ip !== null) return { addr: ip, prefix: 32 };
  return 'bad';
}

export function winMac(mac: string): string {
  return mac.toUpperCase().replace(/:/g, '-');
}

// ipconfig's dotted leader: "   Subnet Mask . . . . . . : "
export function dots(label: string, width = 38): string {
  let s = `   ${label}`;
  for (let col = s.length; col < width - 1; col++) s += col % 2 === 1 ? '.' : ' ';
  return `${s} : `;
}
