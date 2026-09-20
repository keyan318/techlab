# NetSim (vendored for TechLab)

Upstream: https://github.com/michael-borck/netsim (MIT, see LICENSE), copied at commit c3075e7.

TechLab changes (small, keep the diff minimal when re-syncing upstream):
- `src/embed.ts` (new): `?lab=<id>` loading, per-lab autosave, postMessage to the host page.
- `src/store.ts`: boot() loads the embedded lab; checkStep() reports progress to the host.
- `vite.config.ts`: build output goes to `../../public/netsim-app`.
- `public/labs/*.json`: one lab per TechLab lesson.

Build: `cd resources/netsim-app && npm install && npm run build` (separate from the root npm scripts).
