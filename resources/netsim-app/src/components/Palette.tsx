import { useState, type DragEvent } from 'react';
import { useStore } from '../store';
import type { DeviceKind } from '../engine/device';
import { DeviceIcon } from './DeviceNode';
import { LabPanel } from './LabPanel';

const ITEMS: { kind: DeviceKind; label: string; blurb: string }[] = [
  { kind: 'host', label: 'Host', blurb: 'Linux workstation' },
  { kind: 'server', label: 'Server', blurb: 'DHCP / DNS / web services' },
  { kind: 'switch', label: 'Switch', blurb: 'L2 learning switch' },
  { kind: 'router', label: 'Router', blurb: 'IP forwarding on' },
  { kind: 'firewall', label: 'Firewall', blurb: 'router + nftables ready' },
  { kind: 'ap', label: 'Wi-Fi AP', blurb: 'wireless bridge (SSID)' },
];

function DeviceList() {
  const addDeviceAt = useStore((s) => s.addDeviceAt);
  const nodeCount = useStore((s) => s.nodes.length);

  const onDragStart = (e: DragEvent, kind: DeviceKind) => {
    e.dataTransfer.setData('application/netsim', kind);
    e.dataTransfer.effectAllowed = 'move';
  };

  return (
    <>
      {ITEMS.map((it) => (
        <button
          key={it.kind}
          className={`palette-item kind-${it.kind}`}
          draggable
          onDragStart={(e) => onDragStart(e, it.kind)}
          onClick={() => addDeviceAt(it.kind, { x: 140 + (nodeCount % 5) * 60, y: 120 + (nodeCount % 7) * 50 })}
          title="Drag onto the canvas, or click to add"
        >
          <span className="palette-item-icon">
            <DeviceIcon kind={it.kind} size={22} />
          </span>
          <span className="palette-item-text">
            <span className="palette-item-label">{it.label}</span>
            <span className="palette-item-blurb">{it.blurb}</span>
          </span>
        </button>
      ))}
      <div className="palette-hint">
        Drag between the dots on two devices to cable them. Double-click a host or router for its
        terminal.
      </div>
    </>
  );
}

export function Palette() {
  const lab = useStore((s) => s.lab);
  const results = useStore((s) => s.labResults);
  const [tab, setTab] = useState<'mission' | 'devices'>('mission');

  if (!lab) {
    return (
      <aside className="palette">
        <div className="palette-title">Devices</div>
        <DeviceList />
      </aside>
    );
  }

  // TechLab: in a lab the steps live here, in a tab beside the devices, so they never cover the map.
  const objectives = lab.steps.flatMap((s) => s.objectives);
  const passed = objectives.filter((o) => results[o.id]?.pass).length;

  return (
    <aside className="palette with-lab">
      <div className="side-tabs">
        <button className={`side-tab${tab === 'mission' ? ' active' : ''}`} onClick={() => setTab('mission')}>
          Mission{' '}
          <span className="side-tab-count">
            {passed}/{objectives.length}
          </span>
        </button>
        <button className={`side-tab${tab === 'devices' ? ' active' : ''}`} onClick={() => setTab('devices')}>
          Devices
        </button>
      </div>
      {tab === 'mission' ? (
        <LabPanel />
      ) : (
        <div className="side-devices">
          <DeviceList />
        </div>
      )}
    </aside>
  );
}
