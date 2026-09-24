# NetSim (vendored for TechLab)

Upstream: https://github.com/michael-borck/netsim (MIT, see LICENSE), copied at commit c3075e7.

TechLab changes (small, keep the diff minimal when re-syncing upstream):
- `src/embed.ts` (new): `?lab=<id>` loading, per-lab autosave, postMessage to the host page.
- `src/store.ts`: boot() loads the embedded lab; checkStep() reports progress to the host.
- `vite.config.ts`: build output goes to `../../public/netsim-app`.
- `public/labs/*.json`: one lab per TechLab lesson. Student-facing text uses Windows commands.
- Windows mode: embedded labs (`?lab=`) call `setShellOs('windows')` (`src/os.ts`), so every device console
  runs `src/commands/windows/` (ipconfig, netsh, route, tracert, nslookup, Test-NetConnection, netsh advfirewall,
  New-NetNat...) instead of the Linux shell. Both dialects drive the same engine, so lab checks are unchanged.
  Adapters show as "Ethernet", "Ethernet 2" (engine names stay eth0, eth1). Firewall rules carry an optional
  `comment` (the Windows rule name). `techlab.windows.test.ts` solves every lab with Windows commands only.
- `src/components/Palette.tsx` / `src/components/LabPanel.tsx`: when a lab is loaded, the left column
  becomes a "Mission" / "Devices" tab pair instead of floating the lab steps over the canvas — the
  device map stays fully visible instead of being covered by a large instructions box.

Build: `cd resources/netsim-app && npm install && npm run build` (separate from the root npm scripts).
