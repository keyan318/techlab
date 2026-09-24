import { describe, expect, it } from 'vitest';
import { Network } from '../../engine/network';
import { createDevice } from '../../engine/factory';
import { FirewallDevice, Host, RouterDevice } from '../../engine/ipdevice';
import { firewallFromJSON, firewallToJSON } from '../../engine/firewall';
import { cidrToString, parseIp } from '../../engine/ip';
import { WinShell } from './cmd';
import { maskToPrefix, prefixToMask, psParams, splitKv, tokenize } from './util';

const ip = (s: string) => parseIp(s)!;
type Dev = Host | FirewallDevice | RouterDevice;

function run(net: Network, dev: Dev, line: string, advance = 60_000): string {
  const out: string[] = [];
  let finished = false;
  new WinShell(dev).exec(line, { write: (s) => out.push(s) }, () => (finished = true));
  net.scheduler.advanceTo(net.scheduler.now + advance);
  expect(finished, line).toBe(true);
  return out.join('');
}

// pc1 (192.168.1.10) — fw (firewall or router) — pc2 (10.0.0.20)
function topo(kind: 'firewall' | 'router' = 'firewall') {
  const net = new Network();
  const pc1 = createDevice(net, 'host') as Host;
  const fw = createDevice(net, kind) as FirewallDevice | RouterDevice;
  const pc2 = createDevice(net, 'host') as Host;
  net.connect(pc1.id, fw.id);
  net.connect(pc2.id, fw.id);
  return { net, pc1, fw, pc2 };
}

function configured(kind: 'firewall' | 'router' = 'firewall') {
  const t = topo(kind);
  run(t.net, t.pc1, 'netsh interface ip set address "Ethernet" static 192.168.1.10 255.255.255.0 192.168.1.1');
  run(t.net, t.fw, 'netsh interface ip set address "Ethernet" static 192.168.1.1 255.255.255.0');
  run(t.net, t.fw, 'netsh interface ip set address name="Ethernet 2" static 10.0.0.1 255.255.255.0');
  run(t.net, t.pc2, 'netsh interface ip set address "Ethernet" static 10.0.0.20 255.255.255.0 10.0.0.1');
  t.pc2.services.tcp.add(80);
  t.pc2.services.tcp.add(22);
  return t;
}

describe('parsing helpers', () => {
  it('splits like cmd.exe, keeping quoted names together', () => {
    expect(tokenize('netsh interface ip set address "Ethernet 2" static 10.0.0.1')).toEqual([
      'netsh', 'interface', 'ip', 'set', 'address', 'Ethernet 2', 'static', '10.0.0.1',
    ]);
    expect(tokenize('set address name="Ethernet 2" source=static')).toEqual(['set', 'address', 'name=Ethernet 2', 'source=static']);
    expect(splitKv(['a', 'Name=X', 'dir=in']).kv).toEqual({ name: 'X', dir: 'in' });
    expect(psParams(['10.0.0.5', '-Port', '80', '-Confirm:$false']).params).toEqual({ port: '80', confirm: '$false' });
  });

  it('converts masks both ways and rejects broken masks', () => {
    expect(maskToPrefix('255.255.255.0')).toBe(24);
    expect(maskToPrefix('255.255.255.128')).toBe(25);
    expect(maskToPrefix('255.0.255.0')).toBeNull();
    expect(prefixToMask(25)).toBe('255.255.255.128');
    expect(prefixToMask(0)).toBe('0.0.0.0');
  });
});

describe('addressing: netsh, ipconfig, route', () => {
  it('sets an address and gateway, and ipconfig shows them with adapter names', () => {
    const { net, pc1 } = configured();
    expect(cidrToString(pc1.getInterface('eth0')!.ip!.addr, 24)).toBe('192.168.1.10/24');
    const text = run(net, pc1, 'ipconfig');
    expect(text).toContain('Ethernet adapter Ethernet:');
    expect(text).toMatch(/IPv4 Address[ .]*: 192\.168\.1\.10/);
    expect(text).toMatch(/Subnet Mask[ .]*: 255\.255\.255\.0/);
    expect(text).toMatch(/Default Gateway[ .]*: 192\.168\.1\.1/);
    expect(run(net, pc1, 'ipconfig /all')).toContain('Physical Address');
  });

  it('pings across the router in Windows format, 4 by default', () => {
    const { net, pc1 } = configured();
    const text = run(net, pc1, 'ping 10.0.0.20');
    expect(text).toContain('Pinging 10.0.0.20 with 32 bytes of data:');
    expect(text).toMatch(/Reply from 10\.0\.0\.20: bytes=32 time=\d+ms TTL=\d+/);
    expect(text).toContain('Packets: Sent = 4, Received = 4, Lost = 0 (0% loss)');
    expect(run(net, pc1, 'ping -n 2 10.0.0.20')).toContain('Sent = 2');
    expect(run(net, pc1, 'ping -c 2 10.0.0.20')).toContain('On Windows use -n');
  });

  it('rejects a bad mask and explains masks', () => {
    const { net, pc1 } = topo();
    expect(run(net, pc1, 'netsh interface ip set address "Ethernet" static 192.168.1.10 255.0.255.0')).toContain('is invalid');
    expect(pc1.getInterface('eth0')!.ip).toBeNull();
  });

  it('route add / print / delete', () => {
    const { net, fw } = configured();
    expect(run(net, fw, 'route add 172.16.0.0 mask 255.255.255.0 10.0.0.20')).toContain('OK!');
    expect(fw.routes().some((r) => r.dest === ip('172.16.0.0') && r.prefix === 24)).toBe(true);
    expect(run(net, fw, 'route print')).toMatch(/172\.16\.0\.0\s+255\.255\.255\.0\s+10\.0\.0\.20/);
    expect(run(net, fw, 'route add 172.17.0.0 mask 255.255.255.0 8.8.8.8')).toContain('does not lie on the same network');
    expect(run(net, fw, 'route delete 172.16.0.0 mask 255.255.255.0')).toContain('OK!');
    expect(run(net, fw, 'route delete 172.16.0.0 mask 255.255.255.0')).toContain('Element not found');
  });

  it('tracert lists the router hop then the target', () => {
    const { net, pc1 } = configured();
    const text = run(net, pc1, 'tracert 10.0.0.20');
    expect(text).toContain('Tracing route to 10.0.0.20');
    expect(text).toMatch(/ms\s+192\.168\.1\.1\n/);
    expect(text).toMatch(/ms\s+10\.0\.0\.20\n/);
    expect(text).toContain('Trace complete.');
  });

  it('disables and enables an adapter', () => {
    const { net, pc1 } = configured();
    run(net, pc1, 'netsh interface set interface "Ethernet" admin=disabled');
    expect(pc1.getInterface('eth0')!.up).toBe(false);
    expect(run(net, pc1, 'netsh interface show interface')).toMatch(/Disabled\s+Disconnected\s+Dedicated\s+Ethernet/);
    run(net, pc1, 'netsh interface set interface name="Ethernet" admin=ENABLED');
    expect(pc1.getInterface('eth0')!.up).toBe(true);
  });

  it('turns routing on for a host-based router', () => {
    const { net, pc1 } = topo();
    expect(pc1.forwarding).toBe(false);
    run(net, pc1, 'netsh interface ipv4 set interface "Ethernet" forwarding=enabled');
    expect(pc1.forwarding).toBe(true);
  });

  it('points Linux habits at the Windows command', () => {
    const { net, pc1 } = topo();
    const text = run(net, pc1, 'ip addr');
    expect(text).toContain("is not recognized as an internal or external command");
    expect(text).toContain('ipconfig');
  });
});

describe('Windows Defender Firewall (netsh advfirewall)', () => {
  it('a host firewall blocks unsolicited inbound but lets replies back in', () => {
    const { net, pc1, pc2 } = configured();
    run(net, pc2, 'netsh advfirewall set allprofiles state on');
    expect(run(net, pc1, 'Test-NetConnection 10.0.0.20 -Port 80')).toContain('TcpTestSucceeded : False');
    // pc2 can still reach out (stateful: the reply to its own ping is allowed back in).
    expect(run(net, pc2, 'ping -n 1 192.168.1.10')).toContain('Received = 1');
    run(net, pc2, 'netsh advfirewall firewall add rule name="Web" dir=in action=allow protocol=TCP localport=80');
    expect(run(net, pc1, 'tnc 10.0.0.20 -Port 80')).toContain('TcpTestSucceeded : True');
    expect(run(net, pc1, 'tnc 10.0.0.20 -Port 22')).toContain('a firewall is probably dropping it');
  });

  it('block rules beat allow rules regardless of order', () => {
    const { net, pc1, pc2 } = configured();
    run(net, pc2, 'netsh advfirewall set allprofiles state on');
    run(net, pc2, 'netsh advfirewall firewall add rule name=AllowWeb dir=in action=allow protocol=TCP localport=80');
    run(net, pc2, 'netsh advfirewall firewall add rule name=BanAstro dir=in action=block remoteip=192.168.1.10');
    expect(run(net, pc1, 'tnc 10.0.0.20 -Port 80')).toContain('TcpTestSucceeded : False');
    run(net, pc2, 'netsh advfirewall firewall delete rule name=BanAstro');
    expect(run(net, pc1, 'tnc 10.0.0.20 -Port 80')).toContain('TcpTestSucceeded : True');
  });

  it('on a firewall appliance, inbound policy guards routed traffic and fw-policy sees it', () => {
    const { net, pc1, fw } = configured();
    expect(run(net, pc1, 'tnc 10.0.0.20 -Port 22')).toContain('True');
    run(net, fw, 'netsh advfirewall set allprofiles firewallpolicy blockinbound,allowoutbound');
    expect(fw.fw.filter.forward.policy).toBe('drop');
    expect(fw.fw.filter.input.policy).toBe('drop');
    expect(run(net, pc1, 'tnc 10.0.0.20 -Port 22')).toContain('False');
    run(net, fw, 'netsh advfirewall firewall add rule name="DMZ web" dir=in action=allow protocol=TCP localport=80 localip=10.0.0.20');
    expect(run(net, pc1, 'tnc 10.0.0.20 -Port 80')).toContain('True');
    expect(run(net, pc1, 'tnc 10.0.0.20 -Port 22')).toContain('False');
    const shown = run(net, fw, 'netsh advfirewall firewall show rule name=all');
    expect(shown).toContain('Rule Name:                            DMZ web');
    expect(shown).toContain('LocalPort:                            80');
  });

  it('a router gets Windows defaults (block inbound) when the firewall is first turned on', () => {
    const { net, fw } = configured('router');
    expect(run(net, fw, 'netsh advfirewall show allprofiles')).toContain('OFF');
    run(net, fw, 'netsh advfirewall set allprofiles state on');
    expect(fw.fw.filter.input.policy).toBe('drop');
    expect(fw.fw.filter.forward.policy).toBe('drop');
    expect(run(net, fw, 'netsh advfirewall show allprofiles')).toContain('BlockInbound,AllowOutbound');
    run(net, fw, 'netsh advfirewall set allprofiles state off');
    expect(fw.fw.filter.input.declared).toBe(false);
  });

  it('rule names survive a save and reload', () => {
    const { net, pc2 } = configured();
    run(net, pc2, 'netsh advfirewall set allprofiles state on');
    run(net, pc2, 'netsh advfirewall firewall add rule name="Web" dir=in action=allow protocol=TCP localport=80');
    const j = firewallToJSON(pc2.fw)!;
    pc2.fw.flush();
    firewallFromJSON(pc2.fw, JSON.parse(JSON.stringify(j)));
    expect(run(net, pc2, 'netsh advfirewall firewall show rule name=Web')).toContain('Rule Name:                            Web');
  });

  it('rejects ports without a protocol', () => {
    const { net, pc2 } = configured();
    expect(run(net, pc2, 'netsh advfirewall firewall add rule name=x dir=in action=block localport=80')).toContain(
      'Ports need protocol=TCP or protocol=UDP',
    );
  });
});

describe('NAT, DNS, netstat', () => {
  it('New-NetNat masquerades an inside network', () => {
    const { net, fw } = configured('router');
    expect(run(net, fw, 'New-NetNat -Name CitadelNat -InternalIPInterfaceAddressPrefix 192.168.1.0/24')).toContain(
      'InternalIPInterfaceAddressPrefix : 192.168.1.0/24',
    );
    const rule = fw.fw.nat.postrouting.rules[0];
    expect(rule.action.type).toBe('masquerade');
    expect(rule.comment).toBe('CitadelNat');
    run(net, fw, 'Add-NetNatStaticMapping -NatName CitadelNat -Protocol TCP -ExternalIPAddress 0.0.0.0/0 -ExternalPort 8080 -InternalIPAddress 192.168.1.10 -InternalPort 80');
    expect(fw.fw.nat.prerouting.rules[0].action).toEqual({ type: 'dnat', addr: ip('192.168.1.10'), port: 80 });
    run(net, fw, 'Remove-NetNat -Name CitadelNat -Confirm:$false');
    expect(fw.fw.nat.postrouting.rules.length + fw.fw.nat.prerouting.rules.length).toBe(0);
  });

  it('nslookup reports a missing DNS server helpfully', () => {
    const { net, pc1 } = configured();
    expect(run(net, pc1, 'nslookup portal.ship')).toContain('Default servers are not available');
  });

  it('netstat -an lists listening ports', () => {
    const { net, pc2 } = configured();
    expect(run(net, pc2, 'netstat -an')).toMatch(/TCP\s+0\.0\.0\.0:80\s+0\.0\.0\.0:0\s+LISTENING/);
  });

  it('ipconfig /release then /renew fails cleanly with no DHCP server', () => {
    const { net, pc1 } = configured();
    run(net, pc1, 'ipconfig /release');
    expect(pc1.getInterface('eth0')!.ip).toBeNull();
    expect(run(net, pc1, 'ipconfig /renew')).toContain('unable to contact your DHCP server');
  });
});
