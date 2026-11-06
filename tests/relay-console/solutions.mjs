// One working solution per Relay Console lab, typed the way a student would (abbreviations included).
export const SOLUTIONS = {
  'g1-l2': [
    'enable', 'dir flash:', 'more flash:earth-offer.txt',
    'submit A=IaaS', 'submit B=PaaS', 'submit C=SaaS', 'submit D=hybrid',
    'show ip route', 'conf t', 'ip route 10.200.0.0 255.255.0.0 172.31.0.2', 'end', 'ping 10.200.1.10',
    'more flash:shared-responsibility.txt', 'submit OS=crew', 'submit DATA=crew',
  ],
  'g1-l3': [
    'en', 'more flash:fleet-addresses.txt', 'submit A=2001:db8:c0::10', 'submit B=2001:db8:0:a1::1',
    'conf t', 'ipv6 unicast-routing', 'int g0/0', 'ipv6 address 2001:db8:c0de:10::1/64', 'end', 'show ipv6 interface brief',
    'more flash:visitors.txt', 'submit ULA=SHIP-3', 'submit LINK=SHIP-1',
  ],
  'g2-l4': [
    'enable', 'more flash:peering.txt', 'submit EBGP=203.0.113.1', 'submit IBGP=10.0.0.2',
    'conf t', 'router bgp 65010', 'neighbor 203.0.113.1 remote-as 64500', 'nei 10.0.0.2 remote-as 65010',
    'network 192.168.0.0 mask 255.255.0.0', 'end', 'show ip bgp summary',
    'show ip bgp neighbors 10.0.0.2 advertised-routes', 'conf t', 'router bgp 65010', 'neighbor 10.0.0.2 next-hop-self', 'end',
    'sh ip bgp nei 10.0.0.2 advertised-routes',
  ],
  'g2-l5': [
    'en', 'show ip bgp', 'submit BEST=203.0.113.1', 'submit REASON=as-path',
    'conf t', 'route-map MOON-FIRST permit 10', 'set local-preference 200', 'exit',
    'router bgp 65010', 'neighbor 198.18.0.1 route-map MOON-FIRST in', 'end', 'clear ip bgp * soft', 'show ip bgp',
    'submit HIJACK=66666', 'conf t', 'ip prefix-list NO-GRID seq 5 deny 192.168.0.0/16 le 32',
    'ip prefix-list NO-GRID seq 10 permit 0.0.0.0/0 le 32', 'router bgp 65010', 'neighbor 203.0.113.1 prefix-list NO-GRID in',
    'end', 'clear ip bgp * soft', 'sh ip bgp', 'show ip bgp 10.99.0.0', 'submit EXPORT=no',
  ],
  'g2-l6': [
    'en', 'show ip eigrp topology', 'submit SUCCESSOR=10.1.1.2', 'submit FS=10.1.2.2',
    'more flash:link-survey.txt', 'submit METRIC=76800',
    'conf t', 'router eigrp 100', 'variance 2', 'end', 'show ip route 192.168.60.0',
  ],
  'g3-l2': [
    'en', 'show ip route ospf', 'submit SUMMARY=10.30.0.0/22', 'conf t', 'router ospf 1', 'area 1 range 10.30.0.0 255.255.252.0', 'end',
    'sh ip ro ospf', 'more flash:design-review.txt', 'submit SPOF=citadel', 'submit AVAIL=99.99',
  ],
  'g5-l1': [
    'en', 'more flash:traffic-survey.txt', 'submit VOICE=EF', 'submit VIDEO=AF41', 'submit BULK=CS1',
    'conf t', 'class-map match-any VOICE', 'match protocol rtp audio', 'exit', 'class-map match-any VIDEO', 'match protocol rtp video', 'exit',
    'policy-map MARK', 'class VOICE', 'set dscp ef', 'exit', 'class VIDEO', 'set dscp af41', 'exit', 'exit',
    'interface g0/1', 'service-policy input MARK', 'end', 'show policy-map interface g0/1', 'show running-config',
  ],
  'g5-l2': [
    'en', 'show policy-map interface g0/2', 'conf t', 'policy-map RADIO-OUT', 'class VOICE', 'priority percent 20',
    'class VIDEO', 'bandwidth percent 30', 'class BULK', 'police 1000000 conform-action transmit exceed-action drop',
    'class class-default', 'fair-queue', 'exit', 'exit', 'interface g0/2', 'service-policy output RADIO-OUT', 'end',
    'sh policy-map int g0/2', 'conf t', 'policy-map SHAPE-EARTH', 'class class-default', 'shape average 50000000', 'exit', 'exit',
    'int g0/3', 'service-policy output SHAPE-EARTH', 'end', 'submit BULK=police', 'submit EARTH=shape',
  ],
  'g5-l3': [
    'en', 'show mpls forwarding-table', 'submit OUT17=22', 'submit ACTION16=pop', 'show ip vrf',
    'conf t', 'ip vrf GRID', 'rd 64500:65010', 'route-target both 64500:65010', 'exit',
    'interface g0/3', 'ip vrf forwarding GRID', 'ip address 172.20.0.1 255.255.255.252', 'end', 'show ip vrf', 'show ip route vrf GRID',
    'show mpls traffic-eng tunnels brief', 'submit AVOID=P2',
  ],
  'g5-l4': [
    'en', 'show policy-map interface g0/1', 'more flash:link-graph.txt', 'submit PROBLEM=global synchronization',
    'conf t', 'policy-map EARTH-OUT', 'class class-default', 'random-detect dscp-based', 'end', 'show policy-map interface g0/1',
    'show ip cef exact-route 192.168.10.10 198.51.100.10', 'show ip cef exact-route 192.168.30.10 198.51.100.10',
    'submit PERPACKET=reordering', 'submit SPREAD=per-flow',
  ],
  'g6-l3': [
    'en', 'show alerts', 'more flash:drill-memo.txt', 'submit ANOMALY=A2', 'submit FALSEPOS=A4', 'submit SENSOR=NIDS',
    'show sensor mode', 'conf t', 'sensor mode inline', 'signature 2001219 action deny-attacker-inline', 'end', 'show alerts',
    'conf t', 'signature 1000123 exclude source 192.168.20.5', 'end',
  ],
  'g6-l4': [
    'en', 'more flash:vpn-plan.txt', 'conf t', 'crypto isakmp policy 10', 'encryption aes 256', 'hash sha256', 'authentication pre-share', 'group 14', 'exit',
    'crypto isakmp key Gr1d-H0lds-2026 address 203.0.113.50',
    'crypto ipsec transform-set GRID-TS esp-aes 256 esp-sha256-hmac', 'exit',
    'access-list 120 permit ip 192.168.30.0 0.0.0.255 192.168.50.0 0.0.0.255',
    'crypto map GRID-MAP 10 ipsec-isakmp', 'set peer 203.0.113.50', 'set transform-set GRID-TS', 'match address 120', 'exit',
    'interface g0/2', 'crypto map GRID-MAP', 'end', 'ping 192.168.50.10 source g0/1', 'show crypto isakmp sa', 'show crypto ipsec sa', 'submit WRAP=GRE',
  ],
  'g6-l5': [
    'en', 'show clock', 'show crypto pki certificates', 'submit PROBLEM=expired', 'submit ISSUER=GRID-ROOT-CA',
    'conf t', 'crypto pki trustpoint SKYPORT-TP', 'enrollment url http://10.0.0.20:80', 'subject-name CN=vpn.skyport.grid', 'revocation-check ocsp', 'exit',
    'crypto pki enroll SKYPORT-TP', 'end', 'show crypto pki certificates', 'more flash:connect-log.txt', 'submit TRUST=no', 'submit PORT=443',
  ],
  'g6-l6': [
    'en', 'more flash:radius.txt', 'conf t', 'aaa new-model', 'radius server GRID-RAD', 'address ipv4 192.168.100.20 auth-port 1812 acct-port 1813', 'key Rad1us-Gr1d', 'exit',
    'aaa authentication dot1x default group radius', 'aaa authorization network default group radius', 'dot1x system-auth-control',
    'interface g0/5', 'switchport mode access', 'authentication port-control auto', 'dot1x pae authenticator',
    'interface g0/6', 'switchport mode access', 'authentication port-control auto', 'dot1x pae authenticator', 'end',
    'show authentication sessions', 'more flash:wifi-audit.txt', 'submit MODE=WPA3-Enterprise', 'submit EAP=EAP-TLS',
  ],
};
