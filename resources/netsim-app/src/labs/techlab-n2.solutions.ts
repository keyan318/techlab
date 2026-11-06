// One working solution per Networking 2 NetSim lab: [device, command] in the order a student would type them.
// IOS abbreviations are used on purpose, so the console's matching is exercised too.
type Line = [string, string];

const ios = (device: string, ...lines: string[]): Line[] => lines.map((l) => [device, l]);

export const N2_SOLUTIONS: Record<string, Line[]> = {
  'g1-l1': [
    ...ios('deck-rtr', 'enable', 'configure terminal', 'interface g0/0', 'no shutdown', 'int g0/1', 'ip address 192.168.20.1 255.255.255.0', 'no shut', 'end', 'show ip interface brief'),
    ...ios('deck-sw', 'en', 'conf t', 'vlan 10', 'name CREW', 'vlan 20', 'name WORKSHOP', 'exit',
      'interface range g0/1 - 2', 'switchport access vlan 10', 'int g0/3', 'switchport access vlan 20',
      'int g0/7', 'sw access vlan 10', 'int g0/8', 'switchport access vlan 20',
      'interface range g0/1 - 3', 'switchport mode access', 'end', 'show vlan brief'),
  ],
  'g2-l1': [
    ...ios('landing', 'enable', 'conf t', 'router ospf 1', 'network 192.168.10.0 0.0.0.255 area 0', 'network 10.0.12.0 0.0.0.3 area 0'),
    ...ios('citadel', 'en', 'conf t', 'router ospf 1', 'network 10.0.12.0 0.0.0.3 area 0', 'network 10.0.23.0 0.0.0.3 area 0', 'network 192.168.20.0 0.0.0.255 area 0', 'end', 'show ip ospf neighbor'),
    ['astro', 'curl http://192.168.20.10/'],
    ...ios('northwatch', 'en', 'conf t', 'router ospf 1', 'net 10.0.23.0 0.0.0.3 area 0', 'net 192.168.30.0 0.0.0.255 area 0'),
    ...ios('landing', 'passive-interface g0/0', 'end', 'show ip route'),
    ...ios('citadel', 'conf t', 'router ospf 1', 'passive-interface g0/2', 'end', 'show ip protocols'),
    ...ios('northwatch', 'passive-interface g0/1', 'end'),
    ['astro', 'ping 192.168.30.10'],
  ],
  'g2-l2': [
    ...ios('citadel', 'en', 'show ip ospf neighbor', 'show ip ospf interface brief'),
    ...ios('northwatch', 'en', 'show ip ospf int br', 'conf t', 'router ospf 1', 'no network 10.0.23.0 0.0.0.3 area 0', 'network 10.0.23.0 0.0.0.3 area 1', 'end'),
    ...ios('deepwell', 'en', 'conf t', 'router ospf 1', 'network 10.0.34.0 0.0.0.3 area 1', 'network 192.168.40.0 0.0.0.255 area 1', 'end'),
    ...ios('skyport', 'en', 'show ip route ospf', 'show ip ospf database'),
  ],
  'g2-l3': [
    ...ios('landing', 'en', 'show ip route ospf', 'conf t', 'int g0/2', 'ip ospf cost 50', 'exit', 'router ospf 1', 'passive-interface g0/0', 'end'),
    ...ios('northwatch', 'en', 'conf t', 'int g0/0', 'ip ospf cost 50', 'router ospf 1', 'passive-interface g0/2', 'end'),
    ...ios('citadel', 'en', 'conf t', 'router ospf 1', 'default-information originate', 'end'),
    ['astro', 'curl http://198.51.100.10/'],
  ],
  'g3-l1': [
    ...ios('acc', 'enable', 'conf t', 'interface range g0/1 - 2', 'switchport mode access', 'spanning-tree portfast', 'end'),
    ...ios('dist-b', 'en', 'conf t', 'int g0/0', 'no shut', 'int g0/1', 'no shut', 'router ospf 1', 'network 10.1.0.4 0.0.0.3 area 0', 'network 192.168.60.0 0.0.0.255 area 0', 'passive-interface g0/1', 'end'),
    ...ios('core', 'en', 'show ip route', 'conf t', 'no ip route 192.168.60.0 255.255.255.0 10.1.0.2', 'end', 'show ip route ospf'),
    ['pc2', 'curl http://192.168.100.10/'],
  ],
  'g3-l3': [
    ...ios('northwatch', 'en', 'conf t', 'interface g0/1', 'no shutdown', 'router ospf 1', 'network 10.0.34.0 0.0.0.3 area 0', 'end'),
    ...ios('skyport', 'en', 'conf t', 'int g0/1', 'no shut', 'end'),
    ...ios('landing', 'en', 'show ip route ospf', 'conf t', 'int g0/1', 'shutdown', 'do traceroute 192.168.50.10', 'no shutdown', 'end'),
  ],
  'g3-l4': [
    ['pc1', 'ping 192.168.60.1'],
    ...ios('dist-a', 'enable', 'conf t', 'interface g0/1', 'standby 10 ip 192.168.60.1', 'standby 10 priority 110', 'standby 10 preempt', 'end'),
    ...ios('dist-b', 'en', 'conf t', 'int g0/1', 'standby 10 ip 192.168.60.1', 'standby 10 preempt', 'end', 'show standby brief'),
    ...ios('dist-a', 'conf t', 'int g0/1', 'standby 10 track g0/0 20', 'end'),
    ...ios('dist-a', 'conf t', 'int g0/1', 'shutdown'),
    ['pc1', 'ping 192.168.100.10'],
    ...ios('dist-a', 'no shutdown', 'end', 'show standby brief'),
  ],
  'g3-l5': [
    ...ios('nw-sw', 'en', 'show vlan brief', 'conf t', 'int g0/1', 'switchport access vlan 30', 'end'),
    ...ios('citadel', 'en', 'show ip ospf neighbor', 'show ip ospf interface brief'),
    ...ios('nw-b', 'en', 'show ip ospf int br', 'conf t', 'router ospf 1', 'no network 10.0.32.0 0.0.0.3 area 0', 'network 10.0.32.0 0.0.0.3 area 1', 'exit',
      'int g0/1', 'standby 30 ip 192.168.30.1', 'end', 'show standby brief'),
    ['volt', 'curl http://192.168.20.10/'],
  ],
  'g4-l1': [
    ...ios('arc-sw1', 'enable', 'conf t', 'interface g0/8', 'switchport mode trunk', 'end', 'show interfaces trunk'),
    ...ios('arc-sw2', 'en', 'conf t', 'int g0/8', 'sw mode trunk', 'end', 'sh int trunk', 'show vlan brief', 'conf t', 'vlan 20', 'name ARCHIVE', 'exit',
      'int g0/8', 'switchport trunk native vlan 99', 'switchport trunk allowed vlan 10,20,99', 'end'),
    ...ios('arc-sw1', 'conf t', 'int g0/8', 'switchport trunk native vlan 99', 'switchport trunk allowed vlan 10,20,99', 'end'),
  ],
  'g4-l2': [
    ...ios('deck-sw', 'en', 'conf t', 'interface g0/8', 'switchport mode trunk', 'end'),
    ...ios('deck-rtr', 'en', 'conf t', 'interface g0/0', 'no shutdown',
      'interface g0/0.10', 'encapsulation dot1Q 10', 'ip address 192.168.10.1 255.255.255.0',
      'int g0/0.20', 'encapsulation dot1q 20', 'ip add 192.168.20.1 255.255.255.0',
      'int g0/0.30', 'encap dot1q 30', 'ip add 192.168.30.1 255.255.255.0', 'end', 'show ip interface brief'),
  ],
  'g4-l3': [
    ...ios('core-sw', 'enable', 'conf t', 'spanning-tree mode rapid-pvst'),
    ...ios('dist-sw', 'enable', 'conf t', 'spanning-tree mode rapid-pvst'),
    ...ios('old-sw', 'enable', 'conf t', 'spanning-tree mode rapid-pvst', 'do show spanning-tree vlan 10'),
    ...ios('core-sw', 'spanning-tree vlan 1,10 root primary', 'spanning-tree vlan 20 root secondary'),
    ...ios('dist-sw', 'spanning-tree vlan 1,10 root secondary', 'spanning-tree vlan 20 root primary', 'end'),
    ...ios('old-sw', 'do show spanning-tree vlan 20', 'end'),
  ],
  'g4-l4': [
    ...ios('acc1', 'en', 'show spanning-tree', 'conf t', 'interface range g0/1 - 4', 'spanning-tree portfast', 'spanning-tree bpduguard enable', 'end', 'show interfaces status'),
    ...ios('core-sw', 'en', 'conf t', 'interface range g0/1 - 2', 'spanning-tree guard root', 'end', 'show spanning-tree'),
    ...ios('acc2', 'en', 'conf t', 'no spanning-tree vlan 1 priority', 'end'),
  ],
  'g4-l5': [
    ...ios('hangar-sw', 'en', 'conf t', 'interface range g0/1 - 2', 'switchport mode access', 'switchport port-security', 'switchport port-security mac-address sticky', 'exit'),
    ['astro', 'ping 192.168.60.10'],
    ['volt', 'ping 192.168.60.10'],
    ...ios('hangar-sw', 'interface g0/3', 'switchport mode access', 'switchport port-security', 'switchport port-security maximum 2', 'switchport port-security violation restrict',
      'interface range g0/4 - 7', 'shutdown', 'end', 'show port-security'),
  ],
  'g4-l6': [
    ...ios('edge-sw', 'en', 'show interfaces g0/1 switchport', 'conf t', 'interface range g0/1 - 2', 'switchport mode access', 'switchport nonegotiate',
      'int g0/8', 'switchport nonegotiate', 'exit', 'vlan 999', 'name NATIVE-UNUSED', 'exit', 'int g0/8', 'switchport trunk native vlan 999', 'switchport trunk allowed vlan 10', 'exit',
      'vlan 666', 'name PARKING', 'exit', 'interface range g0/3 - 7', 'switchport mode access', 'switchport access vlan 666', 'shutdown', 'end'),
    ...ios('core-sw', 'en', 'conf t', 'vlan 999', 'name NATIVE-UNUSED', 'int g0/8', 'switchport nonegotiate', 'switchport trunk native vlan 999', 'switchport trunk allowed vlan 10', 'end'),
  ],
  'g6-l1': [
    ...ios('citadel-gw', 'enable', 'conf t', 'access-list 110 permit tcp any host 192.168.20.80 eq 80', 'interface g0/1', 'ip access-group 110 in', 'end'),
    ['astro', 'curl http://203.0.113.10/'],
    ...ios('citadel-gw', 'conf t', 'access-list 110 permit tcp any 192.168.20.0 0.0.0.255 established', 'ip access-list extended 110', '5 deny ip host 203.0.113.66 any', 'end', 'show access-lists'),
  ],
  'g6-l2': [
    ['shield', 'netsh advfirewall show allprofiles'],
    ['shield', 'netsh advfirewall set allprofiles state on'],
    ['shield', 'netsh advfirewall set allprofiles firewallpolicy blockinbound,allowoutbound'],
    ['shield', 'netsh advfirewall firewall add rule name=CrewOut dir=in action=allow remoteip=192.168.20.0/24'],
    ['shield', 'netsh advfirewall firewall add rule name=DMZWeb dir=in action=allow protocol=TCP localport=80 localip=172.16.0.10'],
  ],
  'g6-l7': [
    ['astro', 'nslookup codexia-update.net'],
    ['grid-dns', 'Get-DnsServerResourceRecord'],
    ['grid-dns', 'Remove-DnsServerResourceRecord -ZoneName codexia-update.net -Name @ -RRType A -Force'],
    ['grid-dns', 'Add-DnsServerResourceRecordA -ZoneName codexia-update.net -Name @ -IPv4Address 192.168.100.66'],
    ['astro', 'curl http://codexia-update.net/'],
    ...ios('gate', 'en', 'conf t', 'ip access-list extended GRID-DNS', 'permit udp host 192.168.100.53 any eq domain', 'deny udp any any eq domain',
      'deny tcp any any eq 853', 'permit ip any any', 'exit', 'interface g0/0', 'ip access-group GRID-DNS in', 'exit',
      'ip access-list extended GRID-DNS', '35 deny tcp any host 198.51.100.53 eq 443', 'end', 'show access-lists'),
  ],
  'g6-l8': [
    ...ios('hangar-sw', 'en', 'show spanning-tree', 'conf t', 'interface range g0/1 - 6', 'spanning-tree portfast', 'spanning-tree bpduguard enable', 'end'),
    ...ios('citadel', 'en', 'show ip ospf neighbor'),
    ...ios('sky-b', 'en', 'show ip protocols', 'conf t', 'router ospf 1', 'no passive-interface g0/0', 'exit', 'int g0/1', 'standby 50 preempt', 'end'),
    ...ios('sky-a', 'en', 'conf t', 'int g0/1', 'standby 50 track g0/0 20', 'end'),
    ...ios('citadel', 'conf t', 'ip access-list extended EDGE-IN', 'deny ip host 203.0.113.66 any', 'permit ip any any', 'exit', 'interface g0/3', 'ip access-group EDGE-IN in', 'end'),
    ['rivet', 'curl http://203.0.113.10/'],
  ],
};
