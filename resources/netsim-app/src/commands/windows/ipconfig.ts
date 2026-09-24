import type { NetInterface } from '../../engine/device';
import type { IpDevice } from '../../engine/ipdevice';
import { ipToString } from '../../engine/ip';
import { runDhclient } from '../dhclient';
import type { Running } from '../shell';
import { adapterName, dots, findAdapter, prefixToMask, winMac } from './util';

type Out = (s: string) => void;

// Adapters that got their address from DHCP (ipconfig /all shows "DHCP Enabled").
export const dhcpAdapters = new WeakSet<NetInterface>();

export function gatewayOf(device: IpDevice, iface: NetInterface): number | null {
  const r = device.staticRoutes.find((x) => x.prefix === 0 && x.ifaceName === iface.name);
  return r?.via ?? null;
}

function adapterBlock(device: IpDevice, iface: NetInterface, all: boolean): string {
  let s = `\nEthernet adapter ${adapterName(device, iface)}:\n\n`;
  if (!iface.up) {
    return s + `${dots('Media State')}Media disconnected (adapter disabled)\n`;
  }
  if (!iface.link) {
    s += `${dots('Media State')}Media disconnected\n`;
    if (!all) return s;
  }
  s += `${dots('Connection-specific DNS Suffix')}\n`;
  if (all) {
    s += `${dots('Description')}NetSim Virtual Ethernet Adapter\n`;
    s += `${dots('Physical Address')}${winMac(iface.mac)}\n`;
    s += `${dots('DHCP Enabled')}${dhcpAdapters.has(iface) ? 'Yes' : 'No'}\n`;
  }
  if (iface.ip) {
    s += `${dots('IPv4 Address')}${ipToString(iface.ip.addr)}${all ? '(Preferred)' : ''}\n`;
    s += `${dots('Subnet Mask')}${prefixToMask(iface.ip.prefix)}\n`;
  } else if (iface.link) {
    s += `${dots('IPv4 Address')}(none — set one with netsh or ipconfig /renew)\n`;
  }
  const gw = gatewayOf(device, iface);
  s += `${dots('Default Gateway')}${gw !== null ? ipToString(gw) : ''}\n`;
  if (all) {
    s += `${dots('DNS Servers')}${device.nameserver !== null ? ipToString(device.nameserver) : ''}\n`;
  }
  return s;
}

export function formatIpconfig(device: IpDevice, all: boolean): string {
  let s = '\nWindows IP Configuration\n';
  if (all) {
    s += `\n${dots('Host Name')}${device.name}\n`;
    s += `${dots('IP Routing Enabled')}${device.forwarding ? 'Yes' : 'No'}\n`;
  }
  for (const i of device.interfaces) s += adapterBlock(device, i, all);
  return s;
}

export function runIpconfig(device: IpDevice, args: string[], out: Out, done: () => void): Running | null {
  const flag = (args[0] ?? '').toLowerCase();
  const adapterArg = args.slice(1).join(' ') || undefined;

  if (flag === '' || flag === '/all') {
    out(formatIpconfig(device, flag === '/all'));
    done();
    return null;
  }
  if (flag === '/flushdns') {
    out('\nWindows IP Configuration\n\nSuccessfully flushed the DNS Resolver Cache.\n');
    done();
    return null;
  }
  if (flag === '/displaydns') {
    out('\nWindows IP Configuration\n\n    (NetSim does not cache DNS answers; every lookup asks the DNS server.)\n');
    done();
    return null;
  }
  if (flag === '/release' || flag === '/renew') {
    let targets = device.interfaces.filter((i) => i.up && i.link);
    if (adapterArg) {
      const a = findAdapter(device, adapterArg);
      if (!a) {
        out(`\nThe operation failed as no adapter is in the state permissible for\nthis operation. (No adapter named "${adapterArg}".)\n`);
        done();
        return null;
      }
      targets = [a];
    }
    if (flag === '/release') {
      for (const i of targets) {
        i.ip = null;
        device.staticRoutes = device.staticRoutes.filter((r) => !(r.prefix === 0 && r.ifaceName === i.name));
        dhcpAdapters.delete(i);
      }
      out(formatIpconfig(device, false));
      done();
      return null;
    }
    return renew(device, targets, out, done);
  }
  out(
    `\nError: unrecognized or incomplete command line.\n\nUSAGE:\n    ipconfig [/all | /release [adapter] | /renew [adapter] | /flushdns | /displaydns]\n`,
  );
  done();
  return null;
}

// Runs the DHCP exchange on each adapter in turn, then prints the result the Windows way.
function renew(device: IpDevice, targets: NetInterface[], out: Out, done: () => void): Running | null {
  let current: Running | null = null;
  let cancelled = false;
  let idx = 0;
  const failures: string[] = [];

  const next = () => {
    if (cancelled) return;
    if (idx >= targets.length) {
      for (const f of failures) out(f);
      out(formatIpconfig(device, false));
      done();
      return;
    }
    const iface = targets[idx++];
    let log = '';
    current = runDhclient(device, [iface.name], (t) => (log += t), () => {
      if (cancelled) return;
      if (/bound to/.test(log)) {
        dhcpAdapters.add(iface);
        // Real Windows hides the exchange; the lessons teach it, so NetSim shows one labelled line.
        const offer = log.match(/DHCPOFFER of (\S+) from (\S+)/);
        out(
          `\n[NetSim] DHCP on "${adapterName(device, iface)}": DISCOVER -> OFFER ${offer ? `${offer[1]} from ${offer[2]} ` : ''}-> REQUEST -> ACK\n`,
        );
      } else {
        failures.push(
          `\nAn error occurred while renewing interface ${adapterName(device, iface)} : unable to contact your DHCP server. Request has timed out.\n`,
        );
      }
      next();
    });
  };

  if (!targets.length) {
    out('\nThe operation failed as no adapter is in the state permissible for\nthis operation.\n');
    done();
    return null;
  }
  next();
  return {
    cancel: () => {
      cancelled = true;
      current?.cancel();
      done();
    },
  };
}
