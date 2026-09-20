import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  base: './',
  build: {
    // TechLab: build straight into the Laravel public dir (served at /netsim-app/).
    outDir: '../../public/netsim-app',
    emptyOutDir: true,
    rollupOptions: {
      // Two pages: the marketing/landing page at / and the app at /app.html.
      input: {
        main: 'index.html',
        app: 'app.html',
      },
    },
  },
  test: {
    environment: 'node',
    include: ['src/**/*.test.ts'],
    // ExFAT volumes on macOS grow AppleDouble "._foo" companion files;
    // they are not source code.
    exclude: ['**/node_modules/**', '**/._*'],
  },
});
