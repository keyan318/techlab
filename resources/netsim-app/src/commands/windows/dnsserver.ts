// TechLab (Networking 2): the PowerShell DnsServer cmdlets for a NetSim DNS server, so records can be
// managed from the console as well as the Inspector panel (lesson 6.7 sinkholes a malicious domain).
//   Get-DnsServerResourceRecord [-ZoneName <zone>]
//   Add-DnsServerResourceRecordA -ZoneName <zone> -Name <name|@> -IPv4Address <ip>
//   Remove-DnsServerResourceRecord -ZoneName <zone> -Name <name|@> -RRType A [-Force]
import { normalizeName, type IpDevice } from '../../engine/ipdevice';
import { ipToString, parseIp } from '../../engine/ip';

function params(args: string[]): Record<string, string> {
  const out: Record<string, string> = {};
  for (let i = 0; i < args.length; i++) {
    const a = args[i];
    if (!a.startsWith('-')) continue;
    const key = a.slice(1).toLowerCase();
    const next = args[i + 1];
    if (next !== undefined && !next.startsWith('-')) {
      out[key] = next;
      i++;
    } else {
      out[key] = 'true';
    }
  }
  return out;
}

function fqdn(name: string, zone: string): string {
  const z = normalizeName(zone);
  return name === '@' || !name ? z : `${normalizeName(name)}.${z}`;
}

function needServer(d: IpDevice, write: (s: string) => void): boolean {
  if (d.dnsServer?.enabled) return true;
  write(`Failed to contact the DNS server on ${d.name}: the DNS Server role is not running on this computer.\n`);
  return false;
}

export function runGetDnsRecords(d: IpDevice, args: string[], write: (s: string) => void): void {
  if (!needServer(d, write)) return;
  const zone = params(args).zonename;
  let s = '\nHostName                  RecordType Type       Timestamp            TimeToLive      RecordData\n';
  s += '--------                  ---------- ----       ---------            ----------      ----------\n';
  for (const [name, addr] of [...d.dnsServer!.records].sort()) {
    if (zone && !(name === normalizeName(zone) || name.endsWith('.' + normalizeName(zone)))) continue;
    s += `${name.padEnd(26)}${'A'.padEnd(11)}${'1'.padEnd(11)}${'0'.padEnd(21)}${'01:00:00'.padEnd(16)}${ipToString(addr)}\n`;
  }
  write(s + '\n');
}

export function runAddDnsA(d: IpDevice, args: string[], write: (s: string) => void): void {
  if (!needServer(d, write)) return;
  const p = params(args);
  const ip = parseIp(p.ipv4address ?? '');
  if (!p.zonename || !p.name || ip === null) {
    write('Usage: Add-DnsServerResourceRecordA -ZoneName <zone> -Name <name or @> -IPv4Address <address>\n');
    return;
  }
  const name = fqdn(p.name, p.zonename);
  if (d.dnsServer!.records.has(name)) {
    write(`Add-DnsServerResourceRecordA : Failed to create resource record ${name}: it already exists.\nRemove the old record first with Remove-DnsServerResourceRecord.\n`);
    return;
  }
  d.dnsServer!.records.set(name, ip);
}

export function runRemoveDnsRecord(d: IpDevice, args: string[], write: (s: string) => void): void {
  if (!needServer(d, write)) return;
  const p = params(args);
  if (!p.zonename || !p.name) {
    write('Usage: Remove-DnsServerResourceRecord -ZoneName <zone> -Name <name or @> -RRType A -Force\n');
    return;
  }
  if (p.rrtype && p.rrtype.toUpperCase() !== 'A') {
    write('NetSim DNS servers hold A records only. Use -RRType A.\n');
    return;
  }
  const name = fqdn(p.name, p.zonename);
  if (!d.dnsServer!.records.delete(name)) {
    write(`Remove-DnsServerResourceRecord : Failed to find resource record ${name}.\n`);
    return;
  }
  if (p.force !== 'true') write(`Removed ${name}. (Real Windows asks to confirm; -Force skips the question.)\n`);
}
