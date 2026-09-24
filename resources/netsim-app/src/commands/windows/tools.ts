import type { IpDevice } from '../../engine/ipdevice';
import { cidrToString, ipToString, networkOf, parseCidr, parseIp } from '../../engine/ip';
import type { Running } from '../shell';
import { resolveHost } from '../resolve';
import { runNc } from '../nc';
import { adapterName, adapterNameOf, maskToPrefix, prefixToMask, psParams, winMac } from './util';

type Out = (s: string) => void;
const BAR = '===========================================================================\n';

// ---------------- route ----------------

export function runRoute(device: IpDevice, args: string[], out: Out): void {
  const a = args.filter((x) => x.toLowerCase() !== '-p' && x.toLowerCase() !== '-4');
  const verb = (a[0] ?? '').toLowerCase();
  if (verb === 'print' || verb === '') {
    out(formatRoutePrint(device));
    return;
  }
  if (verb === 'add' || verb === 'delete' || verb === 'change') {
    const dest = parseIp(a[1] ?? '');
    if (dest === null) {
      out(`Route: bad destination address ${a[1] ?? ''}\n`);
      return;
    }
    let prefix = 32;
    let rest = a.slice(2);
    if ((rest[0] ?? '').toLowerCase() === 'mask') {
      const p = maskToPrefix(rest[1] ?? '');
      if (p === null) {
        out(`Route: bad mask ${rest[1] ?? ''}\n`);
        return;
      }
      prefix = p;
      rest = rest.slice(2);
    } else if (dest === 0) {
      prefix = 0;
    }
    if (networkOf(dest, prefix) !== dest) {
      out('The route addition failed: The specified mask parameter is invalid. (Destination & Mask) != Destination.\n');
      return;
    }
    if (verb === 'delete') {
      const err = device.delRoute(dest, prefix);
      out(err ? 'The route deletion failed: Element not found.\n' : ' OK!\n');
      return;
    }
    const gw = parseIp(rest[0] ?? '');
    if (gw === null) {
      out('Route: bad gateway address ' + (rest[0] ?? '(missing)') + '\n');
      return;
    }
    if (verb === 'change') device.delRoute(dest, prefix);
    const hop = device.lookupRoute(gw);
    if (!hop || hop.kind !== 'connected') {
      out(
        'The route addition failed: Either the interface index is wrong or the gateway does not lie on the same network as the interface. Check the IP Address Table for the machine.\n',
      );
      return;
    }
    const err = device.addRoute(dest, prefix, gw);
    if (err) {
      out(/exists/i.test(err) ? 'The route addition failed: The object already exists.\n' : `The route addition failed: ${err}\n`);
      return;
    }
    out(' OK!\n');
    return;
  }
  out(
    'Manipulates network routing tables.\n\n' +
      'ROUTE PRINT\n' +
      'ROUTE ADD <destination> MASK <netmask> <gateway>\n' +
      'ROUTE DELETE <destination> [MASK <netmask>]\n' +
      'ROUTE CHANGE <destination> MASK <netmask> <gateway>\n\n' +
      'Example: route add 10.1.0.0 mask 255.255.255.0 192.168.1.1\n' +
      '         route add 0.0.0.0 mask 0.0.0.0 192.168.1.1   (default gateway)\n',
  );
}

export function formatRoutePrint(device: IpDevice): string {
  let s = BAR + 'Interface List\n';
  device.interfaces.forEach((i, idx) => {
    s += `  ${String(idx + 1).padStart(2)}...${winMac(i.mac).replace(/-/g, ' ').toLowerCase()} ......${adapterName(device, i)}\n`;
  });
  s += BAR + '\nIPv4 Route Table\n' + BAR + 'Active Routes:\n';
  s += 'Network Destination        Netmask          Gateway       Interface  Metric\n';
  const rs = device.routes();
  const sorted = [...rs.filter((r) => r.prefix === 0), ...rs.filter((r) => r.prefix !== 0)];
  for (const r of sorted) {
    const iface = device.getInterface(r.ifaceName);
    const ifAddr = iface?.ip ? ipToString(iface.ip.addr) : adapterNameOf(device, r.ifaceName);
    const gw = r.via === null ? 'On-link' : ipToString(r.via);
    const metric = r.kind === 'connected' ? 281 : r.kind === 'rip' ? 20 + (r.metric ?? 1) : 25;
    s += `${ipToString(r.dest).padStart(17)}${prefixToMask(r.prefix).padStart(17)}${gw.padStart(17)}${ifAddr.padStart(16)}${String(metric).padStart(7)}\n`;
  }
  s += BAR + 'Persistent Routes:\n';
  const statics = device.staticRoutes;
  if (!statics.length) s += '  None\n';
  else {
    s += '  Network Address          Netmask  Gateway Address  Metric\n';
    for (const r of statics) {
      s += `${ipToString(r.dest).padStart(17)}${prefixToMask(r.prefix).padStart(17)}${(r.via === null ? 'On-link' : ipToString(r.via)).padStart(17)}${'Default'.padStart(8)}\n`;
    }
  }
  return s + BAR;
}

// ---------------- arp -a ----------------

export function formatArpA(device: IpDevice): string {
  let s = '';
  for (const i of device.interfaces) {
    const entries = [...device.arpTable].filter(([, e]) => e.ifaceName === i.name);
    if (!i.ip) continue;
    s += `\nInterface: ${ipToString(i.ip.addr)} --- 0x${(device.interfaces.indexOf(i) + 1).toString(16)}\n`;
    s += '  Internet Address      Physical Address      Type\n';
    for (const [ip, e] of entries) s += `  ${ipToString(ip).padEnd(22)}${winMac(e.mac).toLowerCase().padEnd(22)}dynamic\n`;
    const bcast = (networkOf(i.ip.addr, i.ip.prefix) | (~(i.ip.prefix === 0 ? 0 : (0xffffffff << (32 - i.ip.prefix)) >>> 0) >>> 0)) >>> 0;
    s += `  ${ipToString(bcast).padEnd(22)}${'ff-ff-ff-ff-ff-ff'.padEnd(22)}static\n`;
  }
  return s || 'No ARP Entries Found.\n';
}

// ---------------- netstat ----------------

export function formatNetstat(device: IpDevice, args: string[]): string {
  const flags = args.join('').toLowerCase();
  const showProc = flags.includes('b');
  const rows: [string, string, string, string, string][] = [];
  const tcp = new Map<number, string>();
  const udp = new Map<number, string>();
  for (const p of device.services.tcp) tcp.set(p, 'svchost.exe');
  for (const p of device.ncTcp.keys()) tcp.set(p, 'ncat.exe');
  if (device.httpServer?.enabled) tcp.set(device.httpServer.port, 'httpd.exe');
  for (const p of device.services.udp) udp.set(p, 'svchost.exe');
  for (const p of device.ncUdp.keys()) udp.set(p, 'ncat.exe');
  if (device.dnsServer?.enabled) udp.set(53, 'dns.exe');
  if (device.dhcpServer?.enabled) udp.set(67, 'dhcpserver.exe');
  for (const [p, proc] of [...tcp].sort((a, b) => a[0] - b[0])) rows.push(['TCP', `0.0.0.0:${p}`, '0.0.0.0:0', 'LISTENING', proc]);
  for (const e of device.ct.list(device.network.now)) {
    if (e.orig.proto !== 'tcp') continue;
    const mine = device.hasIp(e.orig.srcIp) || device.hasIp(e.orig.dstIp);
    if (!mine) continue;
    const local = device.hasIp(e.orig.srcIp) ? `${ipToString(e.orig.srcIp)}:${e.orig.srcPort}` : `${ipToString(e.orig.dstIp)}:${e.orig.dstPort}`;
    const remote = device.hasIp(e.orig.srcIp) ? `${ipToString(e.orig.dstIp)}:${e.orig.dstPort}` : `${ipToString(e.orig.srcIp)}:${e.orig.srcPort}`;
    rows.push(['TCP', local, remote, e.state === 'established' ? 'ESTABLISHED' : 'SYN_SENT', '']);
  }
  for (const [p, proc] of [...udp].sort((a, b) => a[0] - b[0])) rows.push(['UDP', `0.0.0.0:${p}`, '*:*', '', proc]);
  let s = '\nActive Connections\n\n  Proto  Local Address          Foreign Address        State\n';
  for (const [proto, local, remote, state, proc] of rows) {
    s += `  ${proto.padEnd(7)}${local.padEnd(23)}${remote.padEnd(23)}${state}\n`;
    if (showProc && proc) s += ` [${proc}]\n`;
  }
  return s;
}

// ---------------- nslookup ----------------

export function runNslookup(device: IpDevice, args: string[], out: Out, done: () => void): Running | null {
  const name = args[0];
  if (!name) {
    out('Usage: nslookup <name> [<server>]\n');
    done();
    return null;
  }
  let ns = device.nameserver;
  if (args[1]) {
    ns = parseIp(args[1]);
    if (ns === null) {
      out(`*** Can't find server address for '${args[1]}':\n`);
      done();
      return null;
    }
  }
  if (ns === null) {
    out('*** Default servers are not available\nServer:  UnKnown\nAddress:  127.0.0.1\n\n(NetSim: no DNS server is set. Use nslookup <name> <server> or netsh interface ip set dns "Ethernet" static <server>.)\n');
    done();
    return null;
  }
  const server = ipToString(ns);
  let cancelled = false;
  device.resolveDns(name, ns, (r) => {
    if (cancelled) return;
    out(`Server:  UnKnown\nAddress:  ${server}\n\n`);
    if (typeof r === 'number') out(`Name:    ${name}\nAddress:  ${ipToString(r)}\n\n`);
    else if (r === null) out(`*** UnKnown can't find ${name}: Non-existent domain\n`);
    else if (r === 'refused') out(`*** UnKnown can't find ${name}: Query refused\n`);
    else out(`DNS request timed out.\n    timeout was 2 seconds.\n*** Request to UnKnown timed-out\n`);
    done();
  });
  return {
    cancel: () => {
      cancelled = true;
      done();
    },
  };
}

// ---------------- Test-NetConnection ----------------

const COMMON_PORTS: Record<string, number> = { http: 80, rdp: 3389, smb: 445, winrm: 5985 };

export function runTnc(device: IpDevice, args: string[], out: Out, done: () => void): Running | null {
  const { pos, params } = psParams(args);
  const host = params.computername ?? params.remoteaddress ?? pos[0];
  if (!host) {
    out('Usage: Test-NetConnection <host> -Port <port>\n');
    done();
    return null;
  }
  let port: number | null = null;
  if (params.port !== undefined) port = Number(params.port);
  else if (params.commontcpport !== undefined) port = COMMON_PORTS[params.commontcpport.toLowerCase()] ?? NaN;
  if (port !== null && (!Number.isInteger(port) || port < 1 || port > 65535)) {
    out('Test-NetConnection : Cannot validate argument on parameter \'Port\'. Use a number from 1 to 65535.\n');
    done();
    return null;
  }

  let cancelled = false;
  const src = device.interfaces.find((i) => i.ip)?.ip;
  const iface = device.interfaces.find((i) => i.ip);
  const header = (ip: number) =>
    `\nComputerName     : ${host}\nRemoteAddress    : ${ipToString(ip)}\n` +
    (port !== null ? `RemotePort       : ${port}\n` : '') +
    `InterfaceAlias   : ${iface ? adapterName(device, iface) : ''}\nSourceAddress    : ${src ? ipToString(src.addr) : ''}\n`;

  const resolver = resolveHost(device, host, (res) => {
    if (cancelled) return;
    if ('error' in res) {
      out(`WARNING: Name resolution of ${host} failed\n\nComputerName   : ${host}\nRemoteAddress  :\nInterfaceAlias :\nSourceAddress  :\nPingSucceeded  : False\n\n`);
      done();
      return;
    }
    const ip = res.ip;
    if (port === null) {
      pingOnce(device, ip, (ok) => {
        if (cancelled) return;
        if (!ok) out(`WARNING: Ping to ${host} failed with status: TimedOut\n`);
        out(header(ip) + `PingSucceeded    : ${ok ? 'True' : 'False'}\n\n`);
        done();
      });
      return;
    }
    const report = (r: 'open' | 'refused' | 'timeout' | 'unreachable') => {
      if (cancelled) return;
      if (r !== 'open') {
        const why =
          r === 'refused'
            ? 'the host answered, but nothing is listening on that port'
            : r === 'timeout'
              ? 'no answer at all — a firewall is probably dropping it'
              : 'there is no route to that host';
        out(`WARNING: TCP connect to (${ipToString(ip)} : ${port}) failed  [${why}]\n`);
      }
      out(header(ip) + `TcpTestSucceeded : ${r === 'open' ? 'True' : 'False'}\n\n`);
      done();
    };
    const err = device.connectTcp(ip, port, 3000, report);
    if (err) {
      out(`WARNING: TCP connect to (${ipToString(ip)} : ${port}) failed  [${err}]\n` + header(ip) + 'TcpTestSucceeded : False\n\n');
      done();
    }
  });
  return {
    cancel: () => {
      cancelled = true;
      resolver.cancel();
      done();
    },
  };
}

function pingOnce(device: IpDevice, ip: number, cb: (ok: boolean) => void): void {
  const id = device.allocIcmpId();
  let settled = false;
  const settle = (ok: boolean) => {
    if (settled) return;
    settled = true;
    device.offIcmp(id);
    cb(ok);
  };
  device.onIcmp(id, (ev) => settle(ev.type === 'reply'));
  const err = device.sendEcho(ip, id, 1);
  if (err) {
    settle(false);
    return;
  }
  device.network.scheduler.schedule(2000, () => settle(false));
}

// ---------------- ncat (Nmap's netcat for Windows) ----------------

export function runNcat(device: IpDevice, args: string[], out: Out, done: () => void): Running | null {
  return runNc(device, args, (s) => out(s.replace(/^nc: /gm, 'Ncat: ').replace(/netsim supports only: nc /g, 'NetSim supports only: ncat ')), done);
}

// ---------------- NAT (PowerShell NetNat cmdlets) ----------------

export function runNewNetNat(device: IpDevice, args: string[], out: Out): void {
  const { params } = psParams(args);
  const name = params.name;
  const prefix = parseCidr(params.internalipinterfaceaddressprefix ?? '');
  if (!name || !prefix) {
    out('New-NetNat : Usage: New-NetNat -Name <name> -InternalIPInterfaceAddressPrefix <network/prefix>\n');
    return;
  }
  if (!device.forwarding) {
    out('New-NetNat : This computer does not route traffic. Turn on forwarding first:\n  netsh interface ipv4 set interface "Ethernet" forwarding=enabled\n');
    return;
  }
  device.fw.ensureIptablesTable('nat');
  const net = { addr: networkOf(prefix.addr, prefix.prefix), prefix: prefix.prefix };
  device.fw.addNatRule('postrouting', { srcNet: net }, { type: 'masquerade' }, false, name);
  out(`\nName                             : ${name}\nInternalIPInterfaceAddressPrefix : ${cidrToString(net.addr, net.prefix)}\nActive                           : True\n\n`);
}

export function runGetNetNat(device: IpDevice, out: Out): void {
  const rules = device.fw.nat.postrouting.rules.filter((r) => r.action.type === 'masquerade');
  if (!rules.length) {
    out('');
    return;
  }
  let s = '';
  for (const r of rules) {
    s += `\nName                             : ${r.comment ?? `Nat${r.handle}`}\nInternalIPInterfaceAddressPrefix : ${r.match.srcNet ? cidrToString(r.match.srcNet.addr, r.match.srcNet.prefix) : 'Any'}\nActive                           : True\n`;
  }
  out(s + '\n');
}

export function runRemoveNetNat(device: IpDevice, args: string[], out: Out): void {
  const { params, pos } = psParams(args);
  const name = (params.name ?? pos[0] ?? '').toLowerCase();
  let n = 0;
  // Removing a NAT also removes its static mappings (they share the NAT's name).
  for (const hook of ['prerouting', 'postrouting'] as const) {
    const c = device.fw.nat[hook];
    const before = c.rules.length;
    c.rules = c.rules.filter((r) => (r.comment ?? '').toLowerCase() !== name);
    n += before - c.rules.length;
  }
  if (!n) out(`Remove-NetNat : No MSFT_NetNat objects found with property 'Name' equal to '${name}'.\n`);
}

export function runAddStaticMapping(device: IpDevice, args: string[], out: Out): void {
  const { params } = psParams(args);
  const nat = params.natname;
  const proto = (params.protocol ?? '').toLowerCase();
  const extPort = Number(params.externalport);
  const intIp = parseIp(params.internalipaddress ?? '');
  const intPort = Number(params.internalport ?? params.externalport);
  if (!nat || (proto !== 'tcp' && proto !== 'udp') || !Number.isInteger(extPort) || intIp === null || !Number.isInteger(intPort)) {
    out(
      'Add-NetNatStaticMapping : Usage: Add-NetNatStaticMapping -NatName <nat> -Protocol TCP|UDP -ExternalIPAddress 0.0.0.0/0\n' +
        '    -ExternalPort <port> -InternalIPAddress <ip> -InternalPort <port>\n',
    );
    return;
  }
  if (!device.fw.nat.postrouting.rules.some((r) => (r.comment ?? '').toLowerCase() === nat.toLowerCase())) {
    out(`Add-NetNatStaticMapping : No NAT named '${nat}'. Create it first with New-NetNat.\n`);
    return;
  }
  const ext = params.externalipaddress ? parseCidr(params.externalipaddress) ?? (parseIp(params.externalipaddress) !== null ? { addr: parseIp(params.externalipaddress)!, prefix: 32 } : null) : null;
  const match: import('../../engine/firewall').RuleMatch = { proto, dport: { from: extPort, to: extPort } };
  if (ext && ext.prefix > 0 && ext.addr !== 0) match.dstNet = ext;
  device.fw.ensureIptablesTable('nat');
  device.fw.addNatRule('prerouting', match, { type: 'dnat', addr: intIp, port: intPort }, false, nat);
  out(
    `\nNatName            : ${nat}\nProtocol           : ${proto.toUpperCase()}\nExternalIPAddress  : ${params.externalipaddress ?? '0.0.0.0'}\nExternalPort       : ${extPort}\nInternalIPAddress  : ${ipToString(intIp)}\nInternalPort       : ${intPort}\nActive             : True\n\n`,
  );
}

export function runGetStaticMapping(device: IpDevice, out: Out): void {
  let s = '';
  for (const r of device.fw.nat.prerouting.rules) {
    if (r.action.type !== 'dnat') continue;
    s += `\nNatName            : ${r.comment ?? ''}\nProtocol           : ${(r.match.proto ?? 'any').toUpperCase()}\nExternalPort       : ${r.match.dport?.from ?? ''}\nInternalIPAddress  : ${ipToString(r.action.addr)}\nInternalPort       : ${r.action.port ?? r.match.dport?.from ?? ''}\n`;
  }
  out(s ? s + '\n' : '');
}

// ---------------- Get-NetTCPConnection ----------------

export function formatGetNetTcp(device: IpDevice): string {
  let s = '\nLocalAddress      LocalPort RemoteAddress     RemotePort State\n';
  s += '------------      --------- -------------     ---------- -----\n';
  const tcp = new Set<number>([...device.services.tcp, ...device.ncTcp.keys()]);
  if (device.httpServer?.enabled) tcp.add(device.httpServer.port);
  for (const p of [...tcp].sort((a, b) => a - b)) s += `${'0.0.0.0'.padEnd(18)}${String(p).padEnd(10)}${'0.0.0.0'.padEnd(18)}${'0'.padEnd(11)}Listen\n`;
  for (const e of device.ct.list(device.network.now)) {
    if (e.orig.proto !== 'tcp') continue;
    const st = e.state === 'established' ? 'Established' : 'SynSent';
    s += `${ipToString(e.orig.srcIp).padEnd(18)}${String(e.orig.srcPort).padEnd(10)}${ipToString(e.orig.dstIp).padEnd(18)}${String(e.orig.dstPort).padEnd(11)}${st}\n`;
  }
  return s;
}
