// Compact Windows-flavoured tables for the Inspector side panel.
import type { IpDevice } from '../../engine/ipdevice';
import type { FilterHook } from '../../engine/firewall';
import { cidrToString, ipToString } from '../../engine/ip';
import { adapterNameOf, prefixToMask } from './util';

export function panelRoutes(device: IpDevice): string {
  const rs = device.routes();
  if (!rs.length) return '';
  let s = 'Destination      Netmask          Gateway          Adapter\n';
  for (const r of [...rs.filter((x) => x.prefix === 0), ...rs.filter((x) => x.prefix !== 0)]) {
    s += `${ipToString(r.dest).padEnd(17)}${prefixToMask(r.prefix).padEnd(17)}${(r.via === null ? 'On-link' : ipToString(r.via)).padEnd(17)}${adapterNameOf(device, r.ifaceName)}\n`;
  }
  return s;
}

export function panelArp(device: IpDevice): string {
  let s = '';
  for (const [ip, e] of device.arpTable) {
    s += `${ipToString(ip).padEnd(17)}${e.mac.toLowerCase().replace(/:/g, '-').padEnd(19)}${adapterNameOf(device, e.ifaceName)}\n`;
  }
  return s;
}

export function panelFirewall(device: IpDevice): string {
  const fw = device.fw;
  const on = fw.filter.input.declared;
  let s = `State: ${on ? 'ON' : 'OFF'}   Policy: ${fw.filter.input.policy === 'drop' ? 'BlockInbound' : 'AllowInbound'},${
    fw.filter.output.policy === 'drop' ? 'BlockOutbound' : 'AllowOutbound'
  }\n`;
  const seen = new Set<string>();
  for (const hook of ['input', 'forward', 'output'] as FilterHook[]) {
    for (const r of fw.filter[hook].rules) {
      if (r.comment === '@stateful') continue;
      const name = r.comment ?? `Rule #${r.handle}`;
      const dir = hook === 'output' ? 'Out' : 'In';
      if (seen.has(name + dir)) continue;
      seen.add(name + dir);
      const m = r.match;
      const proto = m.proto ? (m.proto === 'icmp' ? 'ICMPv4' : m.proto.toUpperCase()) : 'Any';
      const port = m.dport;
      const remote = hook === 'output' ? m.dstNet : m.srcNet;
      s += `${dir.padEnd(4)}${(r.verdict === 'accept' ? 'Allow' : 'Block').padEnd(6)}${proto.padEnd(7)}${(port ? (port.from === port.to ? String(port.from) : `${port.from}-${port.to}`) : 'Any').padEnd(12)}${(remote ? cidrToString(remote.addr, remote.prefix) : 'Any').padEnd(19)}${name}\n`;
    }
  }
  const nat = [...fw.nat.postrouting.rules, ...fw.nat.prerouting.rules];
  for (const r of nat) {
    if (r.action.type === 'masquerade') s += `NAT  ${r.comment ?? ''}  inside ${r.match.srcNet ? cidrToString(r.match.srcNet.addr, r.match.srcNet.prefix) : 'any'}\n`;
    else if (r.action.type === 'dnat') s += `NAT  ${r.comment ?? ''}  port ${r.match.dport?.from ?? '?'} -> ${ipToString(r.action.addr)}:${r.action.port ?? r.match.dport?.from ?? ''}\n`;
  }
  return s;
}
