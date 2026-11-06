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
      <div className="app">
        <Toolbar />
        <div className="main">
          {/* TechLab: the side column runs the full height, so the terminal only
              shares space with the map and never squeezes the lab instructions. */}
          <Palette />
          <div
            className={`workspace${hasTerminals ? ' with-dock' : ''}`}
            style={hasTerminals ? { gridTemplateRows: `minmax(0, 1fr) ${dockHeight}px` } : undefined}
          >
            <div className="work-top">
              <div className="canvas">
                <Flow />
              </div>
              {selected && <Inspector />}
            </div>
            <TerminalDock height={dockHeight} onResize={setDockHeight} />
          </div>
        </div>
      </div>
    </ReactFlowProvider>
  );
}
