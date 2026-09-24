import { useState } from 'react';
import { ReactFlowProvider } from '@xyflow/react';
import { Flow } from './components/Flow';
import { Palette } from './components/Palette';
import { Inspector } from './components/Inspector';
import { TerminalDock } from './components/TerminalDock';
import { Toolbar } from './components/Toolbar';
import { useStore } from './store';

const DEFAULT_DOCK_HEIGHT = 280;

export default function App() {
  const selected = useStore((s) => s.selected);
  const hasTerminals = useStore((s) => s.terminals.length > 0);
  // TechLab: the terminal dock is drag-resizable (see TerminalDock's handle)
  // so a lab's instructions and the device map stay reachable behind it.
  const [dockHeight, setDockHeight] = useState(DEFAULT_DOCK_HEIGHT);

  return (
    <ReactFlowProvider>
      <div
        className={`app${hasTerminals ? ' with-dock' : ''}`}
        style={hasTerminals ? { gridTemplateRows: `52px 1fr ${dockHeight}px` } : undefined}
      >
        <Toolbar />
        <div className="main">
          <Palette />
          <div className="canvas">
            <Flow />
          </div>
          {selected && <Inspector />}
        </div>
        <TerminalDock height={dockHeight} onResize={setDockHeight} />
      </div>
    </ReactFlowProvider>
  );
}
