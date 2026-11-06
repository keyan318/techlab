// TechLab (Networking 2): Cisco interface names over NetSim's engine names.
// Router ports eth0, eth1, ... are GigabitEthernet0/0, 0/1, ...; switch ports eth0, eth1, ...
// are GigabitEthernet0/1, 0/2, ... (switches number from 1, like a Catalyst). Sub-interface
// eth0.10 is GigabitEthernet0/0.10.
import { SubInterface, type Device, type NetInterface } from '../../engine/device';
import { SwitchDevice } from '../../engine/switch';

const isSwitch = (d: Device) => d instanceof SwitchDevice;

function portNumber(d: Device, iface: NetInterface): number {
  const idx = d.ports().indexOf(iface);
  return isSwitch(d) ? idx + 1 : idx;
}

export function iosName(d: Device, iface: NetInterface): string {
  if (iface instanceof SubInterface) return `${iosName(d, iface.parent)}.${iface.name.split('.')[1]}`;
  return `GigabitEthernet0/${portNumber(d, iface)}`;
}

export function iosShort(d: Device, iface: NetInterface): string {
  return iosName(d, iface).replace('GigabitEthernet', 'Gi');
}

export function iosNameOf(d: Device, engineName: string): string {
  const i = d.getInterface(engineName);
  return i ? iosName(d, i) : engineName;
}

const TYPE = /^(g|gi|gig|giga|gigab|gigabi|gigabit|gigabite|gigabitet|gigabiteth|gigabitethe|gigabitether|gigabitethern|gigabitetherne|gigabitethernet|f|fa|fas|fast|faste|fastet|fasteth|fastethe|fastether|fastethern|fastetherne|fastethernet|e|et|eth|ethernet)$/;

// "g0/1", "Gi0/1", "GigabitEthernet0/1", "g 0/1" (pre-joined), "fa0/1", "g0/0.10", or "eth1".
// Returns the port number and optional sub-interface number, or null.
export function parseIfName(text: string): { port: number; sub: number | null } | null {
  const t = text.toLowerCase().replace(/\s+/g, '');
  const raw = t.match(/^eth(\d+)(?:\.(\d+))?$/);
  if (raw) return { port: -1 - Number(raw[1]), sub: raw[2] !== undefined ? Number(raw[2]) : null };
  const m = t.match(/^([a-z]+)(\d+)\/(\d+)(?:\.(\d+))?$/);
  if (!m || !TYPE.test(m[1]) || m[2] !== '0') return null;
  return { port: Number(m[3]), sub: m[4] !== undefined ? Number(m[4]) : null };
}

// The physical port named by `text` (sub-interface part ignored), or null.
export function findPort(d: Device, text: string): NetInterface | null {
  const p = parseIfName(text);
  if (!p) return null;
  const ports = d.ports();
  // Negative = an engine name (eth<n>) typed directly.
  const idx = p.port < 0 ? -1 - p.port : isSwitch(d) ? p.port - 1 : p.port;
  return ports[idx] ?? null;
}

// Expands "g0/1 - 4 , g0/7" / "g0/1-4" into ports.
export function parseRange(d: Device, text: string): NetInterface[] | null {
  const out: NetInterface[] = [];
  for (const part of text.split(',')) {
    const s = part.trim().replace(/\s*-\s*/g, '-');
    const m = s.match(/^(.*?\d+\/)(\d+)(?:-(\d+))?$/);
    if (!m) return null;
    const from = Number(m[2]);
    const to = m[3] !== undefined ? Number(m[3]) : from;
    if (to < from) return null;
    for (let n = from; n <= to; n++) {
      const p = findPort(d, `${m[1]}${n}`);
      if (!p) return null;
      if (!out.includes(p)) out.push(p);
    }
  }
  return out.length ? out : null;
}

// 02:00:00:00:01:0a -> 0200.0000.010a
export function ciscoMac(mac: string): string {
  const h = mac.replace(/[^0-9a-f]/gi, '').toLowerCase().padStart(12, '0');
  return `${h.slice(0, 4)}.${h.slice(4, 8)}.${h.slice(8, 12)}`;
}

// Accepts 0200.0000.010a, 02:00:00:00:01:0a or 02-00-...; returns colon form or null.
export function parseMac(text: string): string | null {
  const h = text.replace(/[.:-]/g, '').toLowerCase();
  if (!/^[0-9a-f]{12}$/.test(h)) return null;
  return h.match(/../g)!.join(':');
}

// "10,20,30-32" -> [10,20,30,31,32]
export function parseVlanList(text: string): number[] | null {
  const out: number[] = [];
  for (const part of text.split(',')) {
    const m = part.trim().match(/^(\d+)(?:-(\d+))?$/);
    if (!m) return null;
    const a = Number(m[1]);
    const b = m[2] !== undefined ? Number(m[2]) : a;
    if (a < 1 || b > 4094 || b < a) return null;
    for (let v = a; v <= b; v++) if (!out.includes(v)) out.push(v);
  }
  return out.sort((x, y) => x - y);
}

// [1,2,3,10] -> "1-3,10"
export function vlanListText(vs: number[]): string {
  const s = [...vs].sort((a, b) => a - b);
  const parts: string[] = [];
  for (let i = 0; i < s.length; ) {
    let j = i;
    while (j + 1 < s.length && s[j + 1] === s[j] + 1) j++;
    parts.push(j > i ? `${s[i]}-${s[j]}` : `${s[i]}`);
    i = j + 1;
  }
  return parts.join(',');
}
