import { useEffect, useRef } from 'react';
import type { PointerEvent as ReactPointerEvent } from 'react';
import { useStore } from '../store';
import { sim } from '../sim';
import { IpDevice } from '../engine/ipdevice';
import { getSession } from '../terminals';

// TechLab: keep the terminal usable but never let it swallow the whole screen —
// the student still needs to see the lab instructions and the device map above it.
const MIN_DOCK_HEIGHT = 140;
const TOOLBAR_HEIGHT = 52;
const MIN_MAIN_HEIGHT = 160;

function TerminalPane({ deviceId, active }: { deviceId: string; active: boolean }) {
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const device = sim.net.devices.get(deviceId);
    if (!(device instanceof IpDevice) || !ref.current) return;
    const session = getSession(device, {
      onAfterCommand: () => useStore.getState().refresh(),
    });
    if (!session.opened) {
      session.term.open(ref.current);
      session.opened = true;
    } else if (session.term.element && session.term.element.parentElement !== ref.current) {
      ref.current.appendChild(session.term.element);
    }
    session.fit.fit();
  }, [deviceId]);

  useEffect(() => {
    if (!active) return;
    const device = sim.net.devices.get(deviceId);
    if (!(device instanceof IpDevice)) return;
    const session = getSession(device);
    session.fit.fit();
    session.term.focus();
  }, [active, deviceId]);

  return <div ref={ref} className="term-host" style={{ display: active ? 'block' : 'none' }} />;
}

export function TerminalDock({ height, onResize }: { height: number; onResize: (h: number) => void }) {
  const terminals = useStore((s) => s.terminals);
  const activeTerminal = useStore((s) => s.activeTerminal);
  const setActiveTerminal = useStore((s) => s.setActiveTerminal);
  const closeTerminal = useStore((s) => s.closeTerminal);
  useStore((s) => s.uiPulse); // pick up device renames in tab labels

  const drag = useRef<{ startY: number; startHeight: number } | null>(null);

  // Re-fit the visible terminal whenever the dock is resized, so xterm's row/col
  // count matches the new pixel height instead of clipping or leaving a gap.
  useEffect(() => {
    if (!activeTerminal) return;
    const device = sim.net.devices.get(activeTerminal);
    if (!(device instanceof IpDevice)) return;
    getSession(device).fit.fit();
  }, [height, activeTerminal]);

  const onHandlePointerDown = (e: ReactPointerEvent<HTMLDivElement>) => {
    e.preventDefault();
    drag.current = { startY: e.clientY, startHeight: height };
    e.currentTarget.setPointerCapture(e.pointerId);
    document.body.style.cursor = 'ns-resize';
    document.body.style.userSelect = 'none';
  };

  const onHandlePointerMove = (e: ReactPointerEvent<HTMLDivElement>) => {
    if (!drag.current) return;
    const maxHeight = Math.max(MIN_DOCK_HEIGHT, window.innerHeight - TOOLBAR_HEIGHT - MIN_MAIN_HEIGHT);
    const delta = drag.current.startY - e.clientY; // dragging the handle up grows the dock
    const next = Math.min(maxHeight, Math.max(MIN_DOCK_HEIGHT, drag.current.startHeight + delta));
    onResize(next);
  };

  const endDrag = (e: ReactPointerEvent<HTMLDivElement>) => {
    drag.current = null;
    e.currentTarget.releasePointerCapture(e.pointerId);
    document.body.style.cursor = '';
    document.body.style.userSelect = '';
  };

  if (!terminals.length) return null;

  return (
    <div className="terminal-dock">
      <div
        className="terminal-dock-handle"
        onPointerDown={onHandlePointerDown}
        onPointerMove={onHandlePointerMove}
        onPointerUp={endDrag}
        onPointerCancel={endDrag}
        title="Drag up or down to resize the terminal"
      >
        <span className="terminal-dock-grip" />
      </div>
      <div className="terminal-tabs">
        {terminals.map((id) => {
          const name = sim.net.devices.get(id)?.name ?? id;
          return (
            <div
              key={id}
              className={`terminal-tab${id === activeTerminal ? ' active' : ''}`}
              onClick={() => setActiveTerminal(id)}
            >
              <span>{name}</span>
              <button
                className="tab-close"
                onClick={(e) => {
                  e.stopPropagation();
                  closeTerminal(id);
                }}
                title="Close terminal"
              >
                ×
              </button>
            </div>
          );
        })}
      </div>
      <div className="terminal-body">
        {terminals.map((id) => (
          <TerminalPane key={id} deviceId={id} active={id === activeTerminal} />
        ))}
      </div>
    </div>
  );
}
