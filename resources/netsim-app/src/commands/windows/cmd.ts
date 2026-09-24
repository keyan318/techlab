import type { IpDevice } from '../../engine/ipdevice';
import type { Running, TermIO } from '../shell';
import { runCurl } from '../curl';
import { pingTarget } from '../ping';
import { traceTarget } from '../traceroute';
import { runIpconfig } from './ipconfig';
import { runNetsh } from './netsh';
import {
  formatArpA,
  formatGetNetTcp,
  formatNetstat,
  runAddStaticMapping,
  runGetNetNat,
  runGetStaticMapping,
  runNcat,
  runNewNetNat,
  runNslookup,
  runRemoveNetNat,
  runRoute,
  runTnc,
} from './tools';
import { tokenize } from './util';

const HELP = `NetSim commands (real Windows syntax, curated subset). Commands are not case-sensitive.

  ipconfig [/all]                         show adapters, addresses, gateway
  ipconfig /release | /renew [adapter]    give back / get an address from DHCP
  netsh interface ip set address "Ethernet" static <ip> <mask> [<gateway>]
  netsh interface ip set dns "Ethernet" static <dns-server>
  netsh interface show interface          adapter on/off state
  netsh interface set interface "Ethernet" admin=enabled|disabled
  netsh interface ipv4 set interface "Ethernet" forwarding=enabled   (turn on routing)
  route print | route add <net> mask <mask> <gateway> | route delete <net>
  arp -a                                  neighbour (ARP) cache
  ping [-n count] [-t] <host>             ICMP echo (Ctrl-C to stop -t)
  tracert [-h max] <host>                 trace the route packets take
  nslookup <name> [<server>]              DNS lookup
  curl http://<host>[:port][/path]        fetch a web page
  netstat -an [-b]                        listening ports and connections
  Test-NetConnection <host> -Port <port>  is a TCP port open?  (short: tnc)
  ncat -l [-u] <port> | ncat -z <host> <port>   listen on / test a port
  netsh advfirewall ...                   Windows Defender Firewall (type: netsh advfirewall)
  New-NetNat / Add-NetNatStaticMapping / Get-NetNat / Remove-NetNat   NAT on a router
  Get-NetTCPConnection                    TCP connection table
  hostname | ver | cls | help

Adapters are named "Ethernet", "Ethernet 2", "Ethernet 3", ... (use quotes when the name has a space).
`;

// People who learned Linux commands get pointed at the Windows equivalent.
const LINUX_HINTS: Record<string, string> = {
  ip: 'ipconfig  (show)  |  netsh interface ip set address "Ethernet" static <ip> <mask> [<gateway>]',
  ifconfig: 'ipconfig',
  dhclient: 'ipconfig /renew',
  dig: 'nslookup <name>',
  traceroute: 'tracert <host>',
  iptables: 'netsh advfirewall firewall add rule ...',
  nft: 'netsh advfirewall firewall add rule ...',
  ss: 'netstat -an',
  sysctl: 'netsh interface ipv4 set interface "Ethernet" forwarding=enabled',
  conntrack: 'Get-NetTCPConnection',
  nc: 'ncat  or  Test-NetConnection <host> -Port <port>',
  clear: 'cls',
  ls: 'dir',
};

export class WinShell {
  constructor(public device: IpDevice) {}

  exec(line: string, io: TermIO, done: () => void): Running | null {
    const argv = tokenize(line.trim());
    if (argv.length === 0) {
      done();
      return null;
    }
    const [raw, ...args] = argv;
    const cmd = raw.toLowerCase().replace(/\.exe$/, '');
    const write = io.write.bind(io);
    const d = this.device;

    switch (cmd) {
      case 'help':
      case '/?':
        write(HELP);
        break;
      case 'hostname':
        write(d.name + '\n');
        break;
      case 'ver':
        write('\nMicrosoft Windows [Version 10.0.22631] (NetSim)\n');
        break;
      case 'ipconfig':
        return runIpconfig(d, args, write, done);
      case 'netsh':
        runNetsh(d, args, write);
        break;
      case 'route':
        runRoute(d, args, write);
        break;
      case 'arp':
        if (args[0]?.toLowerCase() === '-a' || args[0]?.toLowerCase() === '/a' || args.length === 0) write(formatArpA(d));
        else write('NetSim supports: arp -a\n');
        break;
      case 'netstat':
        write(formatNetstat(d, args));
        break;
      case 'ping':
        return this.ping(args, write, done);
      case 'tracert':
        return this.tracert(args, write, done);
      case 'nslookup':
        return runNslookup(d, args, write, done);
      case 'curl':
        return runCurl(d, args, write, done);
      case 'test-netconnection':
      case 'tnc':
        return runTnc(d, args, write, done);
      case 'ncat':
        return runNcat(d, args, write, done);
      case 'new-netnat':
        runNewNetNat(d, args, write);
        break;
      case 'get-netnat':
        runGetNetNat(d, write);
        break;
      case 'remove-netnat':
        runRemoveNetNat(d, args, write);
        break;
      case 'add-netnatstaticmapping':
        runAddStaticMapping(d, args, write);
        break;
      case 'get-netnatstaticmapping':
        runGetStaticMapping(d, write);
        break;
      case 'get-nettcpconnection':
        write(formatGetNetTcp(d));
        break;
      default: {
        const hint = LINUX_HINTS[cmd];
        write(`'${raw}' is not recognized as an internal or external command,\noperable program or batch file.\n`);
        if (hint) write(`(This computer runs Windows. Try: ${hint})\n`);
        else write("(Type 'help' to see the commands NetSim understands.)\n");
      }
    }
    done();
    return null;
  }

  private ping(args: string[], write: (s: string) => void, done: () => void): Running | null {
    let count: number | null = 4;
    let target: string | null = null;
    for (let i = 0; i < args.length; i++) {
      const a = args[i].toLowerCase();
      if (a === '-n' || a === '/n') {
        count = Number(args[++i]);
        if (!Number.isInteger(count) || count <= 0) {
          write('Bad value for option -n, valid range is from 1 to 4294967295.\n');
          done();
          return null;
        }
      } else if (a === '-t' || a === '/t') {
        count = null;
      } else if (a === '-c') {
        write('Option -c is Linux. On Windows use -n, e.g. ping -n 3 <host>\n');
        done();
        return null;
      } else if (a.startsWith('-') || a.startsWith('/')) {
        write(`Bad option ${args[i]}.\nNetSim supports: ping [-n count] [-t] <host>\n`);
        done();
        return null;
      } else {
        target = args[i];
      }
    }
    if (!target) {
      write('\nUsage: ping [-t] [-n count] target_name\n');
      done();
      return null;
    }
    return pingTarget(this.device, target, count, write, done, 'windows');
  }

  private tracert(args: string[], write: (s: string) => void, done: () => void): Running | null {
    let maxHops = 30;
    let target: string | null = null;
    for (let i = 0; i < args.length; i++) {
      const a = args[i].toLowerCase();
      if (a === '-h' || a === '/h') {
        maxHops = Number(args[++i]);
        if (!Number.isInteger(maxHops) || maxHops <= 0) {
          write('Bad value for option -h.\n');
          done();
          return null;
        }
      } else if (a === '-d' || a === '/d') {
        // numeric output is all NetSim prints anyway
      } else if (a.startsWith('-') || a.startsWith('/')) {
        write(`Bad option ${args[i]}.\nNetSim supports: tracert [-d] [-h maximum_hops] <host>\n`);
        done();
        return null;
      } else {
        target = args[i];
      }
    }
    if (!target) {
      write('\nUsage: tracert [-d] [-h maximum_hops] target_name\n');
      done();
      return null;
    }
    return traceTarget(this.device, target, maxHops, write, done, 'windows');
  }
}
