import type { IpDevice } from '../../engine/ipdevice';
import type { FilterHook, FilterRule, Proto, RuleMatch } from '../../engine/firewall';
import { cidrToString, ipToString, networkOf, parseCidr, parseIp } from '../../engine/ip';
import { dhcpAdapters, gatewayOf } from './ipconfig';
import { adapterName, findAdapter, maskToPrefix, parseNetOrAny, prefixToMask, splitKv } from './util';

type Out = (s: string) => void;

const OK = 'Ok.\n\n';
const STATEFUL = '@stateful';

export function runNetsh(device: IpDevice, args: string[], out: Out): void {
  const lower = args.map((a) => a.toLowerCase());
  if (lower[0] === 'interface' || lower[0] === 'int') {
    netshInterface(device, args.slice(1), out);
    return;
  }
  if (lower[0] === 'advfirewall') {
    netshAdvfirewall(device, args.slice(1), out);
    return;
  }
  out(
    'The following commands are available in NetSim:\n' +
      '  netsh interface ip set address "Ethernet" static <ip> <mask> [<gateway>]\n' +
      '  netsh interface ip set address "Ethernet" dhcp\n' +
      '  netsh interface ip add|delete address "Ethernet" <ip> [<mask>]\n' +
      '  netsh interface ip set dns "Ethernet" static <dns-server>\n' +
      '  netsh interface ip show config\n' +
      '  netsh interface show interface\n' +
      '  netsh interface set interface "Ethernet" admin=enabled|disabled\n' +
      '  netsh interface ipv4 set interface "Ethernet" forwarding=enabled|disabled\n' +
      '  netsh advfirewall set allprofiles state on|off\n' +
      '  netsh advfirewall set allprofiles firewallpolicy blockinbound,allowoutbound\n' +
      '  netsh advfirewall show allprofiles\n' +
      '  netsh advfirewall firewall add rule name=<n> dir=in|out action=allow|block [protocol=TCP|UDP|ICMPv4|any]\n' +
      '        [localport=<p>] [remoteport=<p>] [remoteip=<ip|cidr|any>] [localip=<ip|cidr|any>]\n' +
      '  netsh advfirewall firewall delete rule name=<n>\n' +
      '  netsh advfirewall firewall show rule name=all|<n>\n' +
      '  netsh advfirewall reset\n',
  );
}

// ---------------- netsh interface ----------------

function netshInterface(device: IpDevice, args: string[], out: Out): void {
  const l = args.map((a) => a.toLowerCase());
  // netsh interface show interface
  if (l[0] === 'show' && (l[1] === 'interface' || l[1] === undefined)) {
    let s = '\nAdmin State    State          Type             Interface Name\n';
    s += '-------------------------------------------------------------------------\n';
    for (const i of device.interfaces) {
      const admin = i.up ? 'Enabled' : 'Disabled';
      const state = i.up && i.link ? 'Connected' : 'Disconnected';
      s += `${admin.padEnd(15)}${state.padEnd(15)}${'Dedicated'.padEnd(17)}${adapterName(device, i)}\n`;
    }
    out(s + '\n');
    return;
  }
  // netsh interface set interface "Ethernet" admin=enabled|disabled
  if (l[0] === 'set' && l[1] === 'interface') {
    const { pos, kv } = splitKv(args.slice(2));
    const iface = findAdapter(device, kv.name ?? kv.interface ?? pos[0]);
    if (!iface) {
      out('The interface name is not registered with the router. (Try: netsh interface show interface)\n\n');
      return;
    }
    const admin = (kv.admin ?? pos[1] ?? '').toLowerCase();
    if (admin === 'enabled' || admin === 'enable') iface.up = true;
    else if (admin === 'disabled' || admin === 'disable') iface.up = false;
    else {
      out('Usage: netsh interface set interface "Ethernet" admin=enabled|disabled\n\n');
      return;
    }
    out('\n');
    return;
  }
  if (l[0] === 'ip' || l[0] === 'ipv4') {
    netshIp(device, args.slice(1), out);
    return;
  }
  out('The following command was not found: interface ' + args.join(' ') + '.\n\n');
}

function netshIp(device: IpDevice, args: string[], out: Out): void {
  const l = args.map((a) => a.toLowerCase());
  const verb = l[0];
  const noun = l[1];

  if (verb === 'show' && (noun === 'config' || noun === 'addresses' || noun === 'address')) {
    let s = '';
    for (const i of device.interfaces) {
      s += `\nConfiguration for interface "${adapterName(device, i)}"\n`;
      s += `    DHCP enabled:                         ${dhcpAdapters.has(i) ? 'Yes' : 'No'}\n`;
      if (i.ip) {
        s += `    IP Address:                           ${ipToString(i.ip.addr)}\n`;
        s += `    Subnet Prefix:                        ${cidrToString(networkOf(i.ip.addr, i.ip.prefix), i.ip.prefix)} (mask ${prefixToMask(i.ip.prefix)})\n`;
      }
      const gw = gatewayOf(device, i);
      if (gw !== null) s += `    Default Gateway:                      ${ipToString(gw)}\n`;
      s += `    Statically Configured DNS Servers:    ${device.nameserver !== null ? ipToString(device.nameserver) : 'None'}\n`;
    }
    out(s + '\n');
    return;
  }

  if (verb === 'set' && noun === 'interface') {
    const { pos, kv } = splitKv(args.slice(2));
    const iface = findAdapter(device, kv.interface ?? kv.name ?? pos[0]);
    if (!iface) {
      out('Element not found. (Use the adapter name, e.g. "Ethernet".)\n\n');
      return;
    }
    const fwd = (kv.forwarding ?? '').toLowerCase();
    if (fwd !== 'enabled' && fwd !== 'disabled') {
      out('Usage: netsh interface ipv4 set interface "Ethernet" forwarding=enabled|disabled\n\n');
      return;
    }
    // NetSim models forwarding per device, so this switches the whole device.
    device.forwarding = fwd === 'enabled';
    out(OK);
    return;
  }

  if ((verb === 'set' || verb === 'add' || verb === 'delete') && noun === 'address') {
    const { pos, kv } = splitKv(args.slice(2));
    const iface = findAdapter(device, kv.name ?? kv.interface ?? pos.shift());
    if (!iface) {
      out('The filename, directory name, or volume label syntax is incorrect.\n(NetSim: the adapter name was not found — use "Ethernet", "Ethernet 2", ...)\n\n');
      return;
    }
    if (verb === 'set') {
      const source = (kv.source ?? pos[0] ?? '').toLowerCase();
      if (source === 'dhcp') {
        iface.ip = null;
        device.staticRoutes = device.staticRoutes.filter((r) => !(r.prefix === 0 && r.ifaceName === iface.name));
        out('DHCP is now enabled on this adapter. Run ipconfig /renew to get an address.\n\n');
        return;
      }
      const rest = source === 'static' ? pos.slice(1) : pos;
      const addrWord = kv.address ?? kv.addr ?? rest[0];
      const maskWord = kv.mask ?? rest[1];
      const gwWord = kv.gateway ?? rest[2];
      const parsed = parseAddrMask(addrWord, maskWord);
      if ('error' in parsed) {
        out(parsed.error);
        return;
      }
      iface.ip = { addr: parsed.addr, prefix: parsed.prefix };
      dhcpAdapters.delete(iface);
      if (gwWord !== undefined) {
        device.staticRoutes = device.staticRoutes.filter((r) => !(r.prefix === 0 && r.ifaceName === iface.name));
        if (gwWord.toLowerCase() !== 'none') {
          const gw = parseIp(gwWord);
          if (gw === null) {
            out(`The default gateway "${gwWord}" is not a valid IPv4 address.\n\n`);
            return;
          }
          const err = device.addRoute(0, 0, gw, iface.name);
          if (err) out(`Warning: could not add the default gateway (${err}).\n`);
        }
      }
      out('\n');
      return;
    }
    if (verb === 'add') {
      const parsed = parseAddrMask(kv.address ?? kv.addr ?? pos[0], kv.mask ?? pos[1]);
      if ('error' in parsed) {
        out(parsed.error);
        return;
      }
      if (iface.ip) {
        if (iface.ip.addr === parsed.addr) {
          out('The object already exists.\n\n');
          return;
        }
        out(
          `NetSim supports one IPv4 address per adapter. "${adapterName(device, iface)}" already has ${ipToString(iface.ip.addr)}.\n` +
            `Replace it with: netsh interface ip set address "${adapterName(device, iface)}" static <ip> <mask>\n\n`,
        );
        return;
      }
      iface.ip = { addr: parsed.addr, prefix: parsed.prefix };
      dhcpAdapters.delete(iface);
      out('\n');
      return;
    }
    // delete address
    const target = parseIp((kv.address ?? kv.addr ?? pos[0] ?? '').split('/')[0]);
    if (target === null || !iface.ip || iface.ip.addr !== target) {
      out('Element not found.\n\n');
      return;
    }
    iface.ip = null;
    dhcpAdapters.delete(iface);
    out('\n');
    return;
  }

  if ((verb === 'set' || verb === 'add') && (noun === 'dns' || noun === 'dnsservers' || noun === 'dnsserver')) {
    const { pos, kv } = splitKv(args.slice(2));
    const iface = findAdapter(device, kv.name ?? kv.interface ?? pos.shift());
    if (!iface) {
      out('Element not found. (Use the adapter name, e.g. "Ethernet".)\n\n');
      return;
    }
    const source = (kv.source ?? '').toLowerCase() || (['static', 'dhcp'].includes((pos[0] ?? '').toLowerCase()) ? pos.shift()!.toLowerCase() : 'static');
    if (source === 'dhcp') {
      out('DNS will come from DHCP on the next ipconfig /renew.\n\n');
      return;
    }
    const word = kv.address ?? kv.addr ?? pos[0];
    if (word && word.toLowerCase() === 'none') {
      device.nameserver = null;
      out('\n');
      return;
    }
    const ns = parseIp(word ?? '');
    if (ns === null) {
      out(`The DNS server address "${word ?? ''}" is not valid.\n\n`);
      return;
    }
    device.nameserver = ns;
    out('\n');
    return;
  }

  out(`The following command was not found: interface ip ${args.join(' ')}.\n\n`);
}

function parseAddrMask(addrWord: string | undefined, maskWord: string | undefined): { addr: number; prefix: number } | { error: string } {
  if (!addrWord) return { error: 'Usage: netsh interface ip set address "Ethernet" static <ip> <mask> [<gateway>]\n\n' };
  if (addrWord.includes('/')) {
    const c = parseCidr(addrWord);
    if (!c) return { error: `"${addrWord}" is not a valid address.\n\n` };
    return c;
  }
  const addr = parseIp(addrWord);
  if (addr === null) return { error: `"${addrWord}" is not a valid IPv4 address.\n\n` };
  if (!maskWord) return { error: 'A subnet mask is required, e.g. 255.255.255.0\n\n' };
  const prefix = maskToPrefix(maskWord);
  if (prefix === null) return { error: `The subnet mask "${maskWord}" is invalid. Masks are 1s then 0s, e.g. 255.255.255.0\n\n` };
  return { addr, prefix };
}

// ---------------- netsh advfirewall ----------------

function filterHooksIn(device: IpDevice): FilterHook[] {
  return device.forwarding ? ['input', 'forward'] : ['input'];
}

export function firewallOn(device: IpDevice): boolean {
  return device.fw.filter.input.declared;
}

function ensureStateful(device: IpDevice): void {
  for (const hook of ['input', 'forward', 'output'] as FilterHook[]) {
    const c = device.fw.filter[hook];
    if (!c.rules.some((r) => r.comment === STATEFUL)) {
      device.fw.addFilterRule(hook, { ctStates: ['established', 'related'] }, 'accept', true, STATEFUL);
    }
  }
}

// Windows evaluates block rules before allow rules, whatever order they were added in.
function sortRules(device: IpDevice): void {
  for (const hook of ['input', 'forward', 'output'] as FilterHook[]) {
    const rank = (r: FilterRule) => (r.comment === STATEFUL ? 0 : r.verdict === 'accept' ? 2 : 1);
    device.fw.filter[hook].rules.sort((a, b) => rank(a) - rank(b));
  }
}

function setState(device: IpDevice, on: boolean): void {
  const fw = device.fw;
  if (on) {
    const fresh = !fw.tables.filter;
    fw.tables.filter = true;
    for (const hook of ['input', 'forward', 'output'] as FilterHook[]) fw.filter[hook].declared = true;
    if (fresh) {
      // Windows' default: block unsolicited inbound, allow outbound.
      fw.filter.input.policy = 'drop';
      fw.filter.forward.policy = device.forwarding ? 'drop' : 'accept';
      fw.filter.output.policy = 'accept';
    }
    ensureStateful(device);
    sortRules(device);
  } else {
    for (const hook of ['input', 'forward', 'output'] as FilterHook[]) fw.filter[hook].declared = false;
  }
}

function policyText(device: IpDevice): string {
  const inb = device.fw.filter.input.policy === 'drop' ? 'BlockInbound' : 'AllowInbound';
  const outb = device.fw.filter.output.policy === 'drop' ? 'BlockOutbound' : 'AllowOutbound';
  return `${inb},${outb}`;
}

function netshAdvfirewall(device: IpDevice, args: string[], out: Out): void {
  const l = args.map((a) => a.toLowerCase());
  const profiles = ['allprofiles', 'currentprofile', 'domainprofile', 'privateprofile', 'publicprofile'];

  if (l[0] === 'reset') {
    device.fw.flush();
    out(OK);
    return;
  }
  if (l[0] === 'show' && profiles.includes(l[1] ?? '')) {
    const title = l[1] === 'allprofiles' ? 'All Profiles' : 'Current Profile';
    let s = `\n${title} Settings:\n----------------------------------------------------------------------\n`;
    s += `State                                 ${firewallOn(device) ? 'ON' : 'OFF'}\n`;
    s += `Firewall Policy                       ${policyText(device)}\n`;
    if (device.forwarding) {
      s += `Routed (forwarded) traffic            ${device.fw.filter.forward.policy === 'drop' ? 'Block' : 'Allow'} unless a rule allows it\n`;
    }
    out(s + '\n' + OK);
    return;
  }
  if (l[0] === 'set' && profiles.includes(l[1] ?? '')) {
    const setting = l[2];
    const value = l[3] ?? '';
    if (setting === 'state') {
      if (value !== 'on' && value !== 'off') {
        out('Usage: netsh advfirewall set allprofiles state on|off\n\n');
        return;
      }
      setState(device, value === 'on');
      out(OK);
      return;
    }
    if (setting === 'firewallpolicy') {
      const [inb, outb] = value.split(',');
      const inPol = inb === 'blockinbound' || inb === 'blockinboundalways' ? 'drop' : inb === 'allowinbound' ? 'accept' : null;
      const outPol = outb === 'blockoutbound' ? 'drop' : outb === 'allowoutbound' ? 'accept' : null;
      if (!inPol || !outPol) {
        out('Usage: netsh advfirewall set allprofiles firewallpolicy blockinbound|allowinbound,allowoutbound|blockoutbound\n\n');
        return;
      }
      device.fw.tables.filter = true;
      device.fw.filter.input.policy = inPol;
      if (device.forwarding) device.fw.filter.forward.policy = inPol;
      device.fw.filter.output.policy = outPol;
      out(OK);
      return;
    }
    out(`NetSim supports: set ${l[1]} state on|off, set ${l[1]} firewallpolicy <inbound>,<outbound>\n\n`);
    return;
  }
  if (l[0] === 'firewall') {
    netshFirewallRules(device, args.slice(1), out);
    return;
  }
  out('The following command was not found: advfirewall ' + args.join(' ') + '.\n\n');
}

interface NamedRule {
  name: string;
  hook: FilterHook;
  rule: FilterRule;
}

function namedRules(device: IpDevice): NamedRule[] {
  const list: NamedRule[] = [];
  const seen = new Set<string>();
  for (const hook of ['input', 'output', 'forward'] as FilterHook[]) {
    for (const rule of device.fw.filter[hook].rules) {
      if (rule.comment === STATEFUL) continue;
      const name = rule.comment ?? `Rule #${rule.handle}`;
      // An inbound rule on a router lives on both input and forward; list it once.
      const key = `${name}|${hook === 'forward' ? 'input' : hook}`;
      if (seen.has(key)) continue;
      seen.add(key);
      list.push({ name, hook, rule });
    }
  }
  return list;
}

function portText(p: { from: number; to: number } | undefined): string {
  if (!p) return 'Any';
  return p.from === p.to ? String(p.from) : `${p.from}-${p.to}`;
}

function netText(n: { addr: number; prefix: number } | undefined): string {
  if (!n) return 'Any';
  return n.prefix === 32 ? ipToString(n.addr) : cidrToString(n.addr, n.prefix);
}

function parsePort(word: string | undefined): { from: number; to: number } | null | 'bad' {
  if (!word || word.toLowerCase() === 'any') return null;
  const m = word.match(/^(\d+)(?:-(\d+))?$/);
  if (!m) return 'bad';
  const from = Number(m[1]);
  const to = m[2] ? Number(m[2]) : from;
  if (from < 1 || to > 65535 || from > to) return 'bad';
  return { from, to };
}

function netshFirewallRules(device: IpDevice, args: string[], out: Out): void {
  const verb = (args[0] ?? '').toLowerCase();
  const noun = (args[1] ?? '').toLowerCase();
  const { kv } = splitKv(args.slice(2));
  if (noun !== 'rule') {
    out('Usage: netsh advfirewall firewall add|delete|show rule name=<name> ...\n\n');
    return;
  }

  if (verb === 'show') {
    const want = (kv.name ?? 'all').toLowerCase();
    const dir = (kv.dir ?? '').toLowerCase();
    const rules = namedRules(device).filter(
      (r) =>
        (want === 'all' || r.name.toLowerCase() === want) &&
        (!dir || (dir === 'in' ? r.hook !== 'output' : r.hook === 'output')),
    );
    if (!rules.length) {
      out('No rules match the specified criteria.\n\n');
      return;
    }
    let s = '';
    for (const { name, hook, rule } of rules) {
      const m = rule.match;
      const inbound = hook !== 'output';
      s += `\nRule Name:                            ${name}\n`;
      s += '----------------------------------------------------------------------\n';
      s += `Enabled:                              Yes\n`;
      s += `Direction:                            ${inbound ? 'In' : 'Out'}${hook === 'forward' ? ' (routed traffic)' : ''}\n`;
      s += `Profiles:                             Domain,Private,Public\n`;
      s += `LocalIP:                              ${netText(inbound ? m.dstNet : m.srcNet)}\n`;
      s += `RemoteIP:                             ${netText(inbound ? m.srcNet : m.dstNet)}\n`;
      s += `Protocol:                             ${m.proto ? (m.proto === 'icmp' ? 'ICMPv4' : m.proto.toUpperCase()) : 'Any'}\n`;
      if (m.proto === 'tcp' || m.proto === 'udp') {
        s += `LocalPort:                            ${portText(inbound ? m.dport : m.sport)}\n`;
        s += `RemotePort:                           ${portText(inbound ? m.sport : m.dport)}\n`;
      }
      s += `Action:                               ${rule.verdict === 'accept' ? 'Allow' : 'Block'}\n`;
    }
    out(s + (firewallOn(device) ? '' : '\n(The firewall is OFF, so these rules are not enforced. Turn it on: netsh advfirewall set allprofiles state on)\n') + OK);
    return;
  }

  if (verb === 'delete') {
    const name = kv.name;
    if (!name) {
      out('Usage: netsh advfirewall firewall delete rule name=<name>\n\n');
      return;
    }
    const dir = (kv.dir ?? '').toLowerCase();
    let n = 0;
    for (const hook of ['input', 'forward', 'output'] as FilterHook[]) {
      if (dir === 'in' && hook === 'output') continue;
      if (dir === 'out' && hook !== 'output') continue;
      const c = device.fw.filter[hook];
      const before = c.rules.length;
      const doomed = (r: FilterRule) =>
        r.comment !== STATEFUL && (r.comment ?? `Rule #${r.handle}`).toLowerCase() === name.toLowerCase();
      c.rules = c.rules.filter((r) => !doomed(r));
      n += before - c.rules.length;
    }
    if (!n) {
      out('No rules match the specified criteria.\n\n');
      return;
    }
    out(`\nDeleted ${namedCount(n, device)} rule(s).\n${OK}`);
    return;
  }

  if (verb === 'add') {
    const name = kv.name;
    const dir = (kv.dir ?? '').toLowerCase();
    const action = (kv.action ?? '').toLowerCase();
    if (!name || (dir !== 'in' && dir !== 'out') || (action !== 'allow' && action !== 'block')) {
      out(
        'A specified value is not valid.\nUsage: netsh advfirewall firewall add rule name=<name> dir=in|out action=allow|block\n' +
          '      [protocol=TCP|UDP|ICMPv4|any] [localport=<p>] [remoteport=<p>] [remoteip=<ip|cidr|any>] [localip=<ip|cidr|any>]\n\n',
      );
      return;
    }
    const protoWord = (kv.protocol ?? 'any').toLowerCase();
    let proto: Proto | undefined;
    let icmpType: RuleMatch['icmpType'];
    if (protoWord === 'tcp' || protoWord === '6') proto = 'tcp';
    else if (protoWord === 'udp' || protoWord === '17') proto = 'udp';
    else if (protoWord.startsWith('icmpv4')) {
      proto = 'icmp';
      const t = protoWord.split(':')[1];
      if (t === '8') icmpType = 'echo-request';
      else if (t === '0') icmpType = 'echo-reply';
    } else if (protoWord !== 'any') {
      out(`The protocol "${kv.protocol}" is not supported. Use TCP, UDP, ICMPv4 or any.\n\n`);
      return;
    }
    const lp = parsePort(kv.localport);
    const rp = parsePort(kv.remoteport);
    if (lp === 'bad' || rp === 'bad') {
      out('A specified port value is not valid. Use a number (80) or a range (8000-8080).\n\n');
      return;
    }
    if ((lp || rp) && proto !== 'tcp' && proto !== 'udp') {
      out("A specified port value is not valid.\nPorts need protocol=TCP or protocol=UDP.\n\n");
      return;
    }
    const rip = parseNetOrAny(kv.remoteip);
    const lip = parseNetOrAny(kv.localip);
    if (rip === 'bad' || lip === 'bad') {
      out('A specified IP address is not valid. Use an address (10.0.0.5), a network (10.0.0.0/24) or any.\n\n');
      return;
    }
    const verdict = action === 'allow' ? 'accept' : 'drop';
    const m: RuleMatch = {};
    if (proto) m.proto = proto;
    if (icmpType) m.icmpType = icmpType;
    if (dir === 'in') {
      if (lp) m.dport = lp;
      if (rp) m.sport = rp;
      if (rip) m.srcNet = rip;
      if (lip) m.dstNet = lip;
      for (const hook of filterHooksIn(device)) device.fw.addFilterRule(hook, { ...m }, verdict, false, name);
    } else {
      if (lp) m.sport = lp;
      if (rp) m.dport = rp;
      if (rip) m.dstNet = rip;
      if (lip) m.srcNet = lip;
      device.fw.addFilterRule('output', m, verdict, false, name);
    }
    if (firewallOn(device)) ensureStateful(device);
    sortRules(device);
    out(OK);
    if (!firewallOn(device)) out('(Rule saved, but the firewall is OFF. Turn it on: netsh advfirewall set allprofiles state on)\n');
    return;
  }

  out('Usage: netsh advfirewall firewall add|delete|show rule name=<name> ...\n\n');
}

// Inbound rules on a router are stored twice (input + forward); report them once.
function namedCount(removed: number, device: IpDevice): number {
  return device.forwarding ? Math.max(1, Math.ceil(removed / 2)) : removed;
}
